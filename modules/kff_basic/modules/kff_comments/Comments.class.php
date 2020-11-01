<?php
if(realpath('.') === __DIR__) die(__FILE__);

global $kff, $log;
// var_dump($kff);
require_once $kff::$dir. "/traits/Paginator.trait.php";

class Comments extends BlogKff
{
	use Paginator;

	const
		MAX_LEN = 1500,
		MAX_ON_PAGE = 10,
		MAX_ENTRIES = 10000,
		TO_EMAIL = 0, // ! 1
		CAPTCHA_4_USERS = false,
		TRY_AGAIN = '<button class="core note pointer" onclick="commFns.refresh(null, {hash:\'#comments_name\'});">Попробовать ещё раз</button>',
		T_DISABLED = '<div id="comm_off" class="core warning uk-text-center">Комменты отключены</div>',
		T_EMPTY = "<p class='center' style='margin:20px 0;'>Комментариев пока нет.</p>",
		T_SUCCESS_SEND = "Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email",
		T_FAIL_SEND = "<div class=\"core warning\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>",
		T_SUCCESS_REMOVE = 'Комментарий успешно удалён',
		T_FAIL_REMOVE = 'Невозможно удалить комментарий. Возможно, у вас недостаточно прав';

	private
		$err = [],
		# Путь к файлу комментариев
		$path,
		# Arr with comments
		$file,
		$Title = 'Добавить комментарий',
		$separator='|-|';

	public
		$artData;

	static
		$SPAM_IP,
		$captcha;


	###
	function __construct(array $artData)

	{
		global $act;

		$this->path= self::$storagePath. "/{$artData['oldCatId']}/{$artData['id']}_comments.json";

		$this->file = new DbJSON($this->path);

		self::$SPAM_IP = self::$dir.'/db/badIP.json';

		// var_dump($act);

		// if($act==='comments' && $this->_InputController()) die;

		// *При Аякс-запросе открываем сессию
		// if(!headers_sent() && !isset($_SESSION)) session_start();

		// $this->artData= self::getArtData();
		self::$artDB= self::$artDB ?? self::getArtDB();
		// $this->artData= $artData;
		$this->artData= &self::$map->get($artData['ind'][0])['items'][$artData['ind'][1]];

		// self::$log->add(__METHOD__,null,['$this->artData'=>$this->artData]);

		if($_REQUEST['act']==='comments' &&  $this->_InputController()) die;


		// ?
		self::$captcha = self::realIP();

		// ?
		$this->p_name= $this->artData['title'];

		// var_dump($this->file);

	} // __construct


############################
	private function Create_comment_box($num,$time, $name, $mess, $Site, $email, $IP,$answer='',$cms=NULL)
############################
	{
		# Формируем тело комментария
		if(strlen($Site)>5) { # fixSait
			$Site= preg_match('#^\s*?(//|http)#i', $Site)? $Site: 'http://' . trim($Site);
			$name='<a href="'.$Site.'" title="'.$Site.'" rel="nofollow" target="_blank">'.$name.'</a>';
		}


		$moder_panel= ' <a href="mailto:'.$email.'" rel="nofollow">'.$email.'</a> <span style="float:right;">IP: '.$IP.' &nbsp; <span uk-icon="icon: file-edit" onclick="commFns.Edit.open('.$num.')" title="Редактировать" style="cursor:pointer; color:green;" ></span> <span uk-icon="icon: close" onclick="commFns.Edit.del('.$num.')" title="Удалить" style="cursor:pointer; color:red;" /></span></span>' ;

		$res= '<div id="ent_page'.$num.'" class="container entry"><div class="head_entry"><span class="uname">'.$num.' '.$name.' &nbsp; CMS: ' . $cms . '</span> <span style="font-size:0.7em;">( '.$time.' )</span>' . "\n" . (!self::is_adm()? '': '<div class="core bar">' . $moder_panel . '</div>');
		$res.= '</div><div class="entry_mess">' . self::smiles(self::BBcode($mess));

		if(trim($answer)) {
			$res.= '<div class="entry_answer"><p style="font-weight:bold;">'. self::$cfg['admin']['name'] .':</p>'.self::smiles(self::BBcode($answer)).'</div>';
		}

		$res.= '</div></div>';
		return $res;
	}


	//*
	function check_no_comm()

	{ # return true - комменты
		return !filter_var(self::$artDB->get('enable-comments'), FILTER_VALIDATE_BOOLEAN);
	}



############################
	function c_Enabled_Comm($bool)

	{
		ob_clean();
		if (!self::is_adm()) die("<p class='core warning'>У тебя нет прав для данного действия!</p>");

		$bool= filter_var($bool,FILTER_VALIDATE_BOOLEAN);

		// \H::json(\DIR . 'data.json', $comments);
		self::$artDB->set(['enable-comments'=>$bool]);

		// *Обновляем карту

		$map= self::getBlogMap();
		$ind= self::$artDB->get('ind');

		$newData= [$ind[0]=>[
			'items'=>[$ind[1]=>self::$artDB->get()]
		]];
		$map->set($newData);

		$this->read();
		ob_end_flush();
		die;
	}



############################
	function c_Edit_Comm()

	{
		ob_clean();
		$ind = $_REQUEST['num'] - 1;

		@list($u_date, $u_name, $u_mess, $u_home, $u_email, $u_ip, $u_otvet, $u_CMS) = $this->file->get($ind);
		// var_dump($this->path, $this->file);
		// @list($u_date,$u_name,$u_mess,$u_home, $u_email, $u_ip, $u_otvet, $u_CMS) = $this->file{$_POST['id']};

		#com_ed - node with formEdit
		require('formEdit.php');

		ob_end_flush();
		die;
	}



############################
	function c_Save_Edit_Comm($form)

	{ # call Ajax
		if(!self::is_adm()) return;

		ob_clean();

		$path= &$this->path;

		if (!file_exists($path)) die('<div class="core warning">Файл с комментариями не обнаружен по адресу</div>' . $path);

  	# загружаем файл в массив
		$e= $form['entry'];
		$o= trim($form['otvet']);
		$ind = $form['ind'];

		# строим массив с новыми значениями
 		$arr = [
			$form['dt'],  # дата и время
			$form['name'] ?? '', # имя пользователя
			$e,  # текст сообщения
			$form['homepage'] ?? '',
			$form['email'],
			$form['ip'],
			$o,
		];
		if(isset($form['CMS'])) $arr[] = $form['CMS'];

		self::$log->add(__METHOD__,null,['$form'=>$form, '$arr'=>$arr]);

		// !
		// die;

		# присваиваем нужной строке новый комментарий
		$this->file->set([$ind=>$arr]) ;

		# блокируем файл и производим запись обновлённого массива
		// if (!self::is_adm() || !\H::json($path, [$ind => $arr]))
			// echo '<div class="core warning">Невозможно записать новые данные!</div>';

		// var_dump($GLOBALS['sendToMail']);

		if(self::TO_EMAIL == true && filter_var($form['sendToMail'], FILTER_VALIDATE_BOOLEAN))
		{
			$subject = "Ответ администрации сайта " . \HOST;

			$name = $_POST['name'] ?? 'Гость';

			self::sendMail([
				"Уважаемый(ая) " . $name . "!\nАдминистрация сайта " . \HOST
				. " ответила на Ваш комментарий на странице - " . $this->artData['title'],
				'Комментарий' => $e,
				'Ответ' => $o,
				'email' => $_POST['email'],
				'name' => $name
			], $subject, $_POST['email']);
		}

		echo $this->read();
		ob_end_flush();
		die;
	}



############################
	function c_Write_Comm($form)
	{ # call Ajax

		ob_clean();

		// $opts= &$this->opts;

		# Невидимая каптча
		# compare without types
		if ($form['keyCaptcha'] != self::realIP())
			$this->err["Невидимая каптча"] = [
				$form['keyCaptcha'], self::$captcha, $form['keyCaptcha'] != self::realIP()
			];

		# Если превышен лимит строк
		$entryLen= strlen($form['entry']);
		if ($entryLen > self::MAX_ENTRIES)
			$this->err[] = 'Превышено максимальное количество комментариев - ' . self::MAX_ENTRIES;

		if($entryLen < 3)
			$this->err[]= "Нет сообщения.";

		if(empty($form['email']))
			$this->err[] = "Не указан email";

		if(self::is_adm())
			$form = array_merge([
				'name' => self::$cfg['admin']['name'],
				'homepage' => \BASE_URL
			], $form);

		$arr= [
			"time" => date(self::DATETIME_FORMAT),
			"name" => $form['name'],
			"Post" => @$form['entry'],
			"Site"=> @$form['homepage'],
			"email"=> @$form['email'],
			"IP"=> self::realIP(),
			"Ответ"=> "",
			"CMS"=> @$form['CMS'],
		];


		if(empty($arr['IP']))
			$this->err[]= "Нет IP-адреса.";

		# Проверяем на наличие в базе
		if(file_exists(self::$SPAM_IP))
		{
			if(in_array($arr['IP'], (new DbJSON(self::$SPAM_IP))->get()))
				$this->err[] = 'Попался, товарищ спамер!';
		}

		self::$log->add(__METHOD__,null,['$arr'=>$arr]);


		# Check ERRORS
		if (count($this->err))
		{
			self::$log->add(__METHOD__,null,['ERRORS'=>$this->err]);
			echo '<pre class="core warning">';
			array_walk($this->err, function(&$i) {
				echo "<p>$i</p>\n";
			});
			echo '</pre>';
			echo self::TRY_AGAIN;
			ob_end_flush();
			die;
		}


		# Если указан, то отсылаем на мыло
		if(self::TO_EMAIL == true)
		{
			$subject = "Комментарий со страницы {$this->artData['title']} - ". ($_REQUEST['curpage'] ?? \HOST);
			self::$log->add(__METHOD__,null,['$this->artData'=>$this->artData]);

			// !
			// return;

			self::sendMail($arr, $subject);
		}
		// var_dump($arr);

		$this->file->push(array_values($arr));

		if (!$this->file->save())
		{
			echo '<div class="core warning">Невозможно добавить новый Post!</div>';
			self::$log->add(__METHOD__,null,[$this->file]);
		}


		echo $this->read();
		ob_end_flush();
		die;

	}  //write()


	function c_Del_Comm($num)

	{
		if(!self::is_adm()) return;

		ob_clean();

		$ind = $num - 1;

		echo "<h2>Del_Comm</h2>" . __FILE__ . ' : ' . __LINE__ .  "<pre>\n";
		echo $ind . "\n";

		echo '<hr>';
		echo '</pre>';

		self::$log->add(__METHOD__,null,['$ind'=>$ind]);

		$this->file->clear($ind);

		$this->read();
		ob_end_flush();
		die;
	}



	public static function sendMail($arr, $subject, $to_emails = null)

	{
		require_once DR.'/'. self::$internalModulesPath. '/kff_feedback/PHPMailer/MailPlain.php';

		$message = MailPlain::collectMessage($arr);

		// !
		// self::$log->add(__METHOD__,null,['self::$cfgDB->admin'=>self::$cfgDB->admin, 'self::$cfgDB'=>self::$cfgDB]);
		// return;

		$mailPlain = new MailPlain ($subject, $message, $arr['email'], $arr['name']);

		$mailPlain->cfg= (new DbJSON(
			// *Path to internal module folder
			DR.'/' . self::$internalModulesPath.'/kff_feedback/cfg.db.dat'
		))->get();

		if($send_succ = $mailPlain->TrySend())
		{
			# Success
			echo self::T_SUCCESS_SEND;
			// updateCaptcha();
		}
		elseif(!isset($_REQUEST['NoSendEmail']))
			echo self::T_FAIL_SEND;
		else return;

		if(self::is_adm()) var_dump($send_succ);
	}



############################
	function read()
############################
	{
		$comments='';

		// ob_start();

		# default
		$pager_def= ['data_count' => 0, 'html' => '', 'fragm' => []];

		$this->Paginator(self::MAX_ON_PAGE, 'p_comm', 'reverse', '#comments_header');

		if ($this->paginator)
		{
			foreach($this->paginator['fragm'] as $i => $ent) {
				/* echo '<h3>$ent</h3><pre>';
				print_r( $ent);
				echo '</pre>'; */

				if (self::is_adm() && count($ent) <= 3) {
					echo "<h1>fucking URL</h1>";
					var_dump($ent);
				}

				$num = $this->paginator['data_count'] + self::MAX_ON_PAGE - $this->paginator['lp'] - $i ; # nE!

				list($time, $name, $mess, $Site, $email, $IP,$answer) = $ent;

				/* echo '<h3>$time</h3><pre>';
				var_dump( $ent);
				echo '</pre>'; */

				$name = strlen($name) ? $name : "Гость";
				$cms = !empty($ent[7]) ? $ent[7] : 'Не указана...';
				$mess = !empty($mess) ? $mess : "<p class='core warning'>No post</p>"; # Для модерации

				$comments.= $this->Create_comment_box($num, $time, $name, $mess, $Site, $email, $IP,$answer,$cms);

			}
				// echo $comments;
		} // file_exists($this->path)
		else
		{
			$this->paginator = $pager_def;
		}


		# Rendering comments
		$m_path = self::getPathFromRoot(__DIR__);
		?>

		<link rel="stylesheet" href="/<?=$m_path?>/style.css">

		<?php
		/*===============<Enabled comments. Start code source>=================
		#########################*/
		if (self::is_adm()):

		?>

		<div class="clear admin">

			<h5 class="center" style="display: inline;"> COMMENTS</h5>

			<p>self::is_adm()= <?=self::is_adm()?></p>
			<p>this->path= <?=$this->path?></p>

			<hr>
			<p>this->p_name= <?=$this->artData['title']?></p>
			<p>check_no_comm(this->p_name)= <? var_dump($this->check_no_comm($this->artData['title']))?></p>

		</div>

		<?php
		endif;

		// echo $this->js_vars();
		# VIEW comments block
		require_once 'entries.php';
		require_once 'form.php';

		?>

		<script type="text/javascript">
		//== define vars 4 frontEND;
			window.comm_vars = <?= $this->js_vars(); ?>;
		// console.log('comm_vars = ', comm_vars);
		</script>

		<script type="text/javascript" src="/<?=$m_path?>/comments.js"></script>

		<?php
	} # /read()


	function js_vars()

	{
		global $Config, $user;

		return DbJSON::toJSON([
			'adm' => self::is_adm(),
			'email' => $Config->adminEmail,
			// 'refPage' => $_REQUEST['page'],
			'p_name' => $this->artData['title'],
			'check_no_comm' => $this->check_no_comm(),
			'name_request' => 'p_comm',
			'MAX_LEN' => self::MAX_LEN,
			'captcha' =>  self::$captcha ?? null,
			// 'pageName' => getPageName(),
			'dataCount' => $this->paginator['data_count'],

			'cms' => '',
		]);

	}


	public static function smiles($txt)

	{
		$smArr = array_map(function($i) {
			return " <img src=\"/assets/images/smiles/sm2/".$i.".gif\" class=\"none\" alt=':p' border='0'> ";
		}, [
			":p"=>"s1", ":)"=>"s2", ":a"=>"s3", ":o"=>"s4", ":s"=>"s5",
			":r"=>"s6", ":v"=>"s7", ":h"=>"s8", ";)"=>"s9", ":m"=>"s10"
		]);

		return strtr($txt, $smArr);
	}


	public static function BBcode($texto)

	{
		# [br] - in MAIL.php
		$a = [
			"/\[br\]/", "/\[i\](.*?)\[\/i\]/is", "/\[b\](.*?)\[\/b\]/is", "/\[u\](.*?)\[\/u\]/is", "/\[u\](.*?)\[\/u\]/is", "/\[img\](.*?)\[\/img\]/is",
			"/\[url=[\"|\']?(.*?)[\"|\']?\](.*?)\[\/url\]/is", '/\[url\](.*?)\[\/url\]/is',
			"/\[size=(.*?)\](.*?)\[\/size\]/is",
			"/\[color=(.*?)\](.*?)\[\/color\]/is",

			# new
			"~:\)~", "~;\)~", "~\)\)~", "~:\(~", "~o_O~", "~:\*~"
		];
		$b = [
			'<br>', "<i>$1</i>", "<b>$1</b>", "<u>$1</u>", "<strike>$1</strike>", "<img src=\"$1\" />",
			"<a href=\"$1\" target=\"_blank\" rel=\"nofollow\">$2</a>", "<a href=\"$1\" target=\"_blank\" rel=\"nofollow\">$1</a>",
			'<font size=$1>$2</font>',
			'<font color=$1>$2</font>',

			# new
			'<i class="fa sm-good"></i>', '<i class="fa sm-wink"></i>', '<i class="fa sm-trol"></i>', '<i class="fa sm-frow"></i>', '<i class="fa sm-roll"></i>', '<i class="fa sm-kiss"></i>'
		];
		$texto = preg_replace($a, $b, $texto);
		// $texto = nl2br($texto);
		return $texto;
	}

	function Render()
	{
		?>
		<section id="comments">
			<link rel="stylesheet" href="/<?=self::getPathFromRoot(__DIR__);?>/style.css">

			<?=$this->read()?>

		</section>
		<?php
	}

	public function __destruct()
	{
		return;
		if(ob_get_level()>1)
			ob_get_flush();
	}

} ### END class Comments ###

