<?php
if(realpath('.') === __DIR__) die(__FILE__);


class Comments

{
	const
		SPAM_IP = __DIR__.'/../db/badIP.json',
		MAX_LEN = 1500,
		MAX_ON_PAGE = 10,
		MAX_ENTRIES = 10000,
		TO_EMAIL = 1,
		CAPTCHA_4_USERS = false,
		TRY_AGAIN = '<button class="core note pointer" onclick="commFns.refresh(null, {hash:\'#comments_name\'});">Попробовать ещё раз</button>',
		T_DISABLED = '<div id="comm_off" class="core warning">Комменты отключены!!!</div>',
		T_EMPTY = "<p class='center' style='margin:20px 0;'>Комментариев пока нет.</p>",
		T_SUCCESS_SEND = "Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email",
		T_FAIL_SEND = "<div class=\"error\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>",
		T_SUCCESS_REMOVE = 'Комментарий успешно удалён',
		T_FAIL_REMOVE = 'Невозможно удалить комментарий. Возможно, у вас недостаточно прав';

	protected
		$err = [],
		# Путь к файлу комментариев
		$path =  \DIR . "comments.json",
		# Arr with comments
		$file;

	public
		$Title = 'Добавить комментарий',
		$separator='|-|';


	###
	function __construct()

	{
		global $FileInfo, $Data;

		# При Аякс-запросе открываем сессию
		if(!headers_sent() && !isset($_SESSION)) session_start();

		$_SESSION['captcha'] = \H::realIP();
		$this->userR = @$_SESSION['user'] && self::CAPTCHA_4_USERS == false;

		$this->p_name= $Data['title'];
		// $this->p_name= php\classes\Navigate::skipNum($FileInfo->getFilename());

		// $this->data = \DIR . 'data.json';

		$this->file = \H::json($this->path);

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


		$moder_panel= ' <a href="mailto:'.$email.'" rel="nofollow">'.$email.'</a> <span style="float:right;">IP: '.$IP.' &nbsp; | <img src="/assets/images/icons/edit.png" onclick="commFns.Edit.open('.$num.')" alt="EDIT" title="Редактировать" class="pointer green" /> | <img src="/assets/images/icons/del.png" onclick="commFns.Edit.del('.$num.')" alt="DEL" title="Удалить" class="pointer red" /></span>' ;

		$res= '<div id="ent_page'.$num.'" class="container entry"><div class="head_entry"><span class="uname">'.$num.' '.$name.' &nbsp; CMS: ' . $cms . '</span> <span style="font-size:0.7em;">( '.$time.' )</span>' . "\n" . (!\ADMIN? '': '<div class="core bar">' . $moder_panel . '</div>');
		$res.= '</div><div class="entry_mess">' . self::smiles(self::BBcode($mess));

		if(trim($answer)) {
			$res.= '<div class="entry_answer"><p style="font-weight:bold;">'. OWNER['name'] .':</p>'.self::smiles(self::BBcode($answer)).'</div>';
		}

		$res.= '</div></div>';
		return $res;
	}


	//
	function check_no_comm()

	{ # return true - комменты отключены
		global $Data;
		return isset($Data['comments']) && !$Data['comments'];
	}



############################
	function Enabled_Comm($bool)
############################
	{
		global $H, $Data;

		// $data = \H::json(\DIR . 'data.json');

		if (!\ADMIN) die("<p class='core warning'>У тебя нет прав для данного действия!</p>");

		if ($bool === 'false')
			$comments = ['comments'=>0];
		elseif ($bool === 'true')
			$comments = ['comments'=>1];

		\H::json(\DIR . 'data.json', $comments);

		$this->read();
		die;
	}



############################
	function Edit_Comm()
############################
	{
		$ind = $_REQUEST['num'] - 1;

		@list($u_date, $u_name, $u_mess, $u_home, $u_email, $u_ip, $u_otvet, $u_CMS) = $this->file[$ind];
		// var_dump($this->path, $this->file);
		// @list($u_date,$u_name,$u_mess,$u_home, $u_email, $u_ip, $u_otvet, $u_CMS) = $this->file{$_POST['id']};

		#com_ed - node with formEdit
		require('formEdit.php');

		die;
	}



############################
	function Save_Edit()
############################
	{ # call Ajax
		global $H;
		$path=$this->path;

		if (!file_exists($path)) die('<div class="core warning">Файл с комментариями не обнаружен по адресу</div>' . $path);

  	# загружаем файл в массив
		$e= $_POST['entry'];
		$o= trim($_POST['otvet']);
		$ind = $_POST['ind'];

		# строим массив с новыми значениями
 		$arr = [
			$_POST['dt'],  # дата и время
			$_POST['name'] ?? '', # имя пользователя
			$e,  # текст сообщения
			$_POST['homepage'] ?? '',
			$_POST['email'],
			$_POST['ip'],
			$o,
		];
		if(isset($_POST['CMS'])) $arr[] = $_POST['CMS'];

		# присваиваем нужной строке новый комментарий
		$this->file[$ind] = $arr;

		# блокируем файл и производим запись обновлённого массива
		if (!\ADMIN || !\H::json($path, [$ind => $arr]))
			echo '<div class="core warning">Невозможно записать новые данные!</div>';

		// var_dump($GLOBALS['sendToMail']);

		if(self::TO_EMAIL == true && $_POST['sendToMail'])
		{
			$subject = "Ответ администрации сайта " . \HOST;

			$name = $_POST['name'] ?? 'Гость';

			self::sendMail([
				"Уважаемый(ая) " . $name . "!\nАдминистрация сайта " . \HOST
				. " ответила на Ваш комментарий на странице - " . $this->p_name,
				'Комментарий' => $e,
				'Ответ' => $o,
				'email' => $_POST['email'],
				'name' => $name
			], $subject, $_POST['email']);
		}

		$this->read();
		die;
	}



############################
	function write()
	{ # call Ajax
		############################
		global $H, $user, $com_count;
		// $this->err=[];

		# Невидимая каптча
		# compare without types
		if ($_REQUEST['keyCaptcha'] != $_SESSION['captcha'])
			$this->err["Невидимая каптча"] = [
				$_REQUEST['keyCaptcha'], $_SESSION['captcha'], $_REQUEST['keyCaptcha'] != $_SESSION['captcha']
			];

		# Если превышен лимит строк
		if ($_POST['dataCount'] > self::MAX_ENTRIES)
			$this->err[] = 'Превышено максимальное количество комментариев - ' . self::MAX_ENTRIES;

		if(strlen(trim(@$_POST['entry'])) < 3)
			$this->err[]= "Нет сообщения.";


		if(empty($_POST['email']))
			$this->err[] = "Не указан email";

		if(\ADMIN)
			$_POST = array_merge($_POST, [
				'name' => $_POST['name'] ? $_POST['name'] : \OWNER['name'],
				'homepage' => \BASE_URL
			]);

		$arr= [
			"time" => date(\CF['date']['format']),
			"name" => $_POST['name'],
			"Post" => @$_POST['entry'],
			"Site"=>@$_POST['homepage'],
			"email"=>@$_POST['email'],
			"IP"=>\H::realIP(),
			"Ответ"=>"",
			"CMS"=>@$_POST['CMS'],
		];

		/* var_dump(
			$_POST,
			\OWNER['name']
		); */


		if(empty($arr['IP']))
			$this->err[]= "Нет IP-адреса.";

		# Проверяем на наличие в базе
		if(file_exists(self::SPAM_IP))
		{
			if(in_array($arr['IP'], \H::json(self::SPAM_IP)))
				$this->err[] = 'Попался, товарищ спамер!';
		}


		# Check ERRORS
		if (count($this->err))
		{
			echo '<pre class="core warning">';
			array_walk($this->err, function($i) {
				echo "<p>$i</p>\n";
			});
			echo '</pre>';
			echo self::TRY_AGAIN;
			die;
		}


		# Если указан, то отсылаем на мыло
		if(self::TO_EMAIL == true)
		{
			$subject = "Комментарий со страницы $this->p_name - ". ($_REQUEST['curpage'] ?? \HOST);
			self::sendMail($arr, $subject);
		}
		// var_dump($arr);

		$this->file[] = array_values($arr);

		if (!\H::json($this->path, $this->file, 'rewrite'))
		{
			echo '<div class="core warning">Невозможно добавить новый Post!</div>';
			\H::$tmp['comm'] = [
				'path' => $this->path,
				'file' => $this->file,
			];
			\H::log([
				'echo \'$this->path = \' . self::$tmp[\'comm\'][\'path\']',
				'echo \'$this->file = \'',
				'print_r(self::$tmp[\'comm\'][\'file\'])',
			], __FILE__, __LINE__);
		}


		$this->read();
		die;

	}  //write()


	function Del_Comm()

	{
		global $H;
		$ind = $_REQUEST['num'] - 1;

		echo "<h2>Del_Comm</h2>" . __FILE__ . ' : ' . __LINE__ .  "<pre>\n";
		echo $ind . "\n";
		var_dump($this->file[$ind]);
		echo '<hr>';
		echo '</pre>';

		$this->file = array_values(array_diff_key($this->file, [$ind => 0]));

		// if (!$kff['file']['json']([], $this->path, ['str' => $_POST['id']])) {
		if (!\ADMIN || !\H::json($this->path, $this->file, 'rewrite'))
			$this->err[] = self::T_FAIL_REMOVE;
		else
			echo self::T_SUCCESS_REMOVE;

		$this->read();
		die;
	}



	public static function sendMail($arr, $subject, $to_emails = null)

	{
		require_once 'php/modules/PHPMailer/MailPlain.php';

		$message = MailPlain::collectMessage($arr);
		$email = $_REQUEST['email'];

		$mailPlain = new MailPlain ($subject, $message, $email, $arr['name']);

		if($send_succ = $mailPlain->TrySend())
		{
			# Success
			echo self::T_SUCCESS_SEND;
			// updateCaptcha();
		}
		elseif(!isset($_REQUEST['NoSendEmail']))
			echo self::T_FAIL_SEND;
		else return;

		if(\ADMIN) var_dump($send_succ);
	}



############################
	function read()
############################
	{

		global $user, $H,
		#
		$pager;
		$comments='';

		// ob_start();

		# default
		$pager_def= ['data_count' => 0, 'paginator' => '', 'fragm' => []];

		if ($pager= \H::paginator($this->file, self::MAX_ON_PAGE, 'p_comm', 'reverse', '#comments_header'))
		{
			#####
			/* echo "<pre>";
			var_dump($pager);
			var_dump($kff['paginator']($this->file, self::MAX_ON_PAGE, 'p_comm', 1, '#comments_header'));
			// var_dump($kff['file']['json'](NULL, $this->path));
			echo "</pre>"; */
			#####

			foreach($pager['fragm'] as $i => $ent) {
				/* echo '<h3>$ent</h3><pre>';
				print_r( $ent);
				echo '</pre>'; */

				if (\ADMIN && count($ent) <= 3) {
					echo "<h1>fucking URL</h1>";
					var_dump($ent);
				}

				$num = $pager['data_count'] + self::MAX_ON_PAGE - $pager['lp'] - $i ; # nE!

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
			$pager = $pager_def;
		}


		# Rendering comments
		$m_path = Path::fromRootStat(__DIR__);
		?>

		<link rel="stylesheet" href="/<?=$m_path?>/style.css">

		<?php
		/*===============<Enabled comments. Start code source>=================
		#########################*/
		if (\ADMIN && SENIOR):

		?>

		<div class="clear admin">

			<h5 class="center" style="display: inline;"> COMMENTS</h5>

			<p>\ADMIN= <?=\ADMIN?></p>
			<p>this->path= <?=$this->path?></p>
			<!-- <p>this->file= <? #print_r($this->file)?></p> -->

			<hr>
			<p>this->p_name= <?=$this->p_name?></p>
			<p>check_no_comm(this->p_name)= <? var_dump($this->check_no_comm($this->p_name))?></p>

			<hr>
			<p>urldecode($_SERVER['QUERY_STRING']) <?var_dump(urldecode($_SERVER['QUERY_STRING']) )?></p>
		</div>

		<?php
		endif;
		if(\ADMIN) :
		?>

		<div class="core bar">
			<label class="button" style="margin-left:50px;"><input style="width:30px;" onchange="commFns.en_com.call(this)" <?=!$this->check_no_comm($this->p_name) ?'checked="checked"':''?> type="checkbox"> Включить комментарии на этой странице</label>
		</div>

		<?php
		endif;
		// echo $this->js_vars();
		# VIEW comments block
		require_once('entries.php');
		require_once('form.php');

		?>

		<script type="text/javascript">
		//== define vars 4 frontEND;
			window.comm_vars = <?= $this->js_vars(); ?>;
		// console.log('comm_vars = ', comm_vars);
		</script>
		<?#= $this->js_vars(); ?>

		<script type="text/javascript" src="/<?=$m_path?>/comments.js"></script>

		<?php
	} # /read()


	function js_vars()

	{
		global $user, $pager;

		return json_encode([
			'adm' => \ADMIN,
			'email' => \ADM_EMAIL,
			// 'refPage' => $_REQUEST['page'],
			'p_name' => $this->p_name,
			'check_no_comm' => $this->check_no_comm($this->p_name),
			'name_request' => 'p_comm',
			'MAX_LEN' => self::MAX_LEN,
			'captcha' =>  $_SESSION['captcha'] ?? null,
			// 'pageName' => getPageName(),
			'dataCount' => $pager['data_count'],

			'cms' => $user ? $_SESSION['auth']['data'][4] : ($_POST['homepage'] ?? '')
		], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

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

} ### END class Comments ###


###########
$Comments = $Comments ?? new Comments;
###########

/*============ Сохранение, редактирование и удаление комментариев по AJAX-запросу ===========*/

$s_method = $_REQUEST['s_method'] ?? null;

switch($s_method) {
	# Добавляем новый коммент
	case 'write': $Comments->write(); break;
	# Выводим форму редактирования коммента
	case 'edit_comm': $Comments->Edit_Comm(); break;
	# Сохраняем отредактированный коммент
	case 'save_edit':
		$Comments->Save_Edit();
		break;
	# Удаляем коммент
	case 'del': $Comments->Del_Comm(); break;
	# включение/отключение комментариев
	case 'enable_comm': $Comments->Enabled_Comm($_POST['enable_comm']); break;
	default :
	# Выводим существующие комменты
	if (isset($Comments) && (!$Comments->check_no_comm($Comments->p_name) || \ADMIN)) {
		// var_dump(\ADMIN);
		$Comments->read();
	}
}
