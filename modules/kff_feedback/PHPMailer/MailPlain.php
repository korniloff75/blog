<?php
# https://github.com/PHPMailer/PHPMailer
# https://medium.com/@shpaginkirill/вменяемая-инструкция-к-phpmailer-отправка-писем-и-файлов-на-почту-b462f8ff9b5c


if (version_compare(PHP_VERSION, '7.0', '<') ) exit("Извини, брат, с пыхом ниже 7 - не судьба!\n");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/src/Exception.php';
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';
// require_once "{$_SERVER['DOCUMENT_ROOT']}/modules/kff_basic/integration_system.php";
if(!defined('DR'))
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/system/global.dat";
}


class MailPlain extends PHPMailer

{
	const
		T_SUCCESS_SEND = "Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email",
		T_FAIL_SEND = "<div class=\"error\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>",
		DATE_FORMAT = "Y-m-d H:i:s";

	public
		# Custom params
		$cfg = [
			'emails' => null,
			'smtp' => [
				'host' => null,
				'username' => null,
				'password' => null,
			],
			'tg' => [
				"token" => null,
				"chat_id" => null,
			]
		],

		//* str || aray with emails
		$to_emails = null,

		# Common
		$Mailer = 'smtp',
		$CharSet    = 'UTF-8',

		$attach = null,

		$SMTP = [
			"on" => true,
			"isHTML" => true,
		];

	private $log;


	public function __construct($subject, $message, $from_mail, $from_name = null)

	{
		global $kff, $log;

		$this->log = &$log;

		$this->setLanguage('ru', __DIR__ . '/language/');
		parent::__construct(true);

		# 4 test
		if(!empty($GLOBALS['test'])) $this->to_emails = 'support@js-master.ru';

		$this->validated = $this->valid([
			'name' => $from_name,
			'email' => $from_mail,
			'subject' => $subject,
			'message' => $message
		]);

		if(empty($this->validated)) {
			die("Отправляемые данные не прошли серверную валидацию. Попробуйте ещё раз.");
		}

		$this->FromName = $this->validated['name'];
		$this->From = $this->validated['email'];
		$this->Subject = $this->validated['subject'];
		$this->isHTML($this->SMTP['isHTML']);

		$this->validated['messageNL'] = $this->validated['message'] = "<pre>{$this->validated['message']}</pre>\n"
		. "\nTime - " . date(self::DATE_FORMAT)
		. "\nEmail - {$this->validated['email']}"
		. "\nIP - " . $kff::realIP()
		. ($_REQUEST['tg'] ? "\nTelegram - {$_REQUEST['tg']}" : "");


	} // __construct


	protected function valid(array $input_vars)

	{
		# Прогоняем данные через фильтры
		return filter_var_array($input_vars, [
			'name' => [
				'filter' => FILTER_SANITIZE_SPECIAL_CHARS
			],
			'email' => [
				'filter' => FILTER_SANITIZE_EMAIL
			],
			'subject' => [
				'filter' => FILTER_SANITIZE_SPECIAL_CHARS
			],
			'message' => [
				'filter' => FILTER_DEFAULT
			],
		]);
	}


	# MailPlain::save($this->validated);
	# in handler.php
	/**
	 * Save input data in DB
	 */
	public static function save(array $data)
	{
		$answer = '';
		$m_save = [
			date(self::DATE_FORMAT) => [
				$data['name'], $data['email'], $data['subject'], $data['message'], $answer
			]
		];

		// \H::json('email.json', $m_save);

		file_put_contents('email.json', json_encode($m_save, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), LOCK_EX);
	}


	/**
	 * collect body of main message
	 * in developing ...
	 * now use in the comments
	 */
	public static function collectMessage($arr)

	{
		$message = '';

		foreach($arr as $name=>$value) {
			if(
				!trim($value)
				|| in_array($name, ['time', 'email', 'IP'], 1)
			) continue;

			if(in_array($name, ['Post', 'Ответ'], 1))
				$name = "===============\n" . $name . ": \n";
			elseif(is_string($name))
				$name = $name . ": ";
			else $name = '';

			$message .= $name . $value . " \n";
		}

		return $message;
	}


	/**
	 * Telegram API
	 *
	 * https://api.telegram.org/bot1028281410:AAHBV_yuvrXcjNNg4FvSfuR1-2vjKNVfwys/getUpdates
	 *
	 * # To Me
	 * MailPlain::toTG(673976740);
	 * # To Cat
	 * MailPlain::toTG(677430081);
	 *
	 * Отправка GET-запросом
	 * $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$tg_chat_id}&parse_mode=html&text={$tg_txt}","r");
	 */
	public function toTG(
		$text = null,
		$chat_id = null
	)
	{
		global $log;
		// require_once __DIR__."/../tg.class.php";

		$log->add('token= ' . $this->cfg['tg']['token']);

		$tg = new TG(
			$this->cfg['tg']['token']
		);
		$chat_id = $chat_id ?? $this->cfg['tg']['chat_id'];

		return $tg->apiRequest([
			'chat_id' => $chat_id,
			'parse_mode' => 'html',
			'text' => $text,
		]);

	}


	public function uploads()
	{
		foreach($_FILES as $fd) {
			if($fd['error'] !== UPLOAD_ERR_OK)
				continue;

			$blacklist = ['phtml', 'php\d?', '.html?'];
			if(count(array_filter($blacklist, function($i) use($fd) {
				return preg_match('/\.' . $i . '$/i',$fd['name']);
			})))
			{
				throw new phpmailerException ("Недопустимый тип файла - {$fd['name']}");
				continue;
			}

			if($fd["size"] > ($maxSize = self::getMaxSizeUpload()))
			{
				throw new phpmailerException ("Размер файла {$fd['name']} превышает $maxSize байт");
				continue;
			}

			$this->AddAttachment($fd['tmp_name'], $fd['name']);
		}
	}


	public static function getMaxSizeUpload ()
	{
		return min(self::sizeToBytes(ini_get('post_max_size')), self::sizeToBytes(ini_get('upload_max_filesize')));
	}

	protected static function sizeToBytes ($sSize)
	{
		$sSuffix = strtoupper(substr($sSize, -1));
	   if (!in_array($sSuffix,array('P','T','G','M','K')))
		 return (int)$sSize;

	   $iValue = substr($sSize, 0, -1);
	   switch ($sSuffix) {
			case 'P':
				$iValue *= 1024;
			case 'T':
				$iValue *= 1024;
			case 'G':
				$iValue *= 1024;
			case 'M':
				$iValue *= 1024;
			case 'K':
				$iValue *= 1024;
				break;
	   }
	   return (int)$iValue;
	}


	/**
	 * *Готовим данные к отправке
	 */
	protected function _prepToSend()
	{
		$this->Body = $this->validated['message'];
		$this->AltBody = strip_tags($this->validated['messageNL']);

		// *Проверка данных для SMTP
		$is_SMTP = (
			($this->Host = $this->cfg['smtp']['host'])
			&& ($this->Username = $this->cfg['smtp']['username'])
			&& ($this->Password = $this->cfg['smtp']['password'])
		);

		if ( $this->SMTP['on'] = $is_SMTP )
		{
			if($this->SMTP['isHTML']) {
				$this->validated['message'] = nl2br($this->validated['message']);
			}

			$this->IsSMTP();
			$this->Port = 465;
			$this->SMTPAuth = true;
			$this->SMTPSecure = "ssl";

			$this->SMTPDebug = $GLOBALS['status'] === 'admin' ? 2 : 0;
			$this->setFrom($this->Username);
			// $this->setFrom($this->validated['email'], $this->validated['name']);
		}
		else
		{
			$this->IsMail();
		}

		// *Проверка данных для Telegram
		$is_TG = (
			// ($this->cfg['tg']['token'] = $this->cfg['tg']['token'] ?? file_get_contents(__DIR__.'/../token.json'))
			!empty($this->cfg['tg']['token'])
			&& !empty($this->cfg['tg']['chat_id'])
		);

		$this->cfg['tg']['on'] = $is_TG;

		$this->log->add(__METHOD__,null,['$is_SMTP'=>$is_SMTP, '$is_TG'=>$is_TG, '$this->cfg'=>$this->cfg]);
	}


	/**
	 * Запускается после создания экземпляра
	 */
	public function TrySend()

	{
		$this->_prepToSend();

		$to = $this->to_emails ?? $this->cfg['emails'];
		if(is_string($to))
		{
			$to = array_map(function(&$i){
				return trim($i);
			}, explode(',',$to));
		}
		elseif(!is_array($to) || empty($to))
		{
			if(defined('OWNER'))
				$to = \OWNER['email'];
			else die('Нет адреса получателя ' . __FILE__ . __LINE__);
		}

		foreach($to as $email) {
			$this->AddAddress($email);
		}

		if(defined('ADM_EMAIL'))
			$this->AddBCC(ADM_EMAIL, 'Developer');

		// var_dump($_FILES);
		if(count($_FILES))
			$this->uploads();

		# Send to Telegram
		if( $this->cfg['tg']['on'] )
		{
			$textToTG = "<b>{$this->validated['subject']}</b>\n {$this->validated['messageNL']}";

			$response = $this->toTG($textToTG);

			file_put_contents(__DIR__ . '/response.json', json_encode($response, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), LOCK_EX);
			/* var_dump(
				$response
				// , $this->validated
			); */
		}

		if(isset($_REQUEST['NoSendEmail'])) return;


		try {
			$this->Send();
			return true;

		} catch (phpmailerException $e) {
			if($this->SMTP['isHTML'])
				try {
					$this->isHTML(false);
					$this->Send();
					return true;
				} catch (phpmailerException $e) {
					// var_dump($e);
					echo 'Mailer Error: ' . $this->ErrorInfo;
					return false;
					// return $e->xdebug_message;
				}
		} catch (Exception $e) {
		// } catch (phpmailerException $e) {
			// var_dump($e);
			$this->log->add(__METHOD__,null,['$this->cfg'=>$this->cfg, '$e'=>$e]);
			return false;
		}
	} // TrySend
} // MailPlain



/** EXAMPLE

$subject = $_REQUEST['subject'] . ' - Обратная связь с ' . HOST;
$message = $_REQUEST['name'] . " пишет: \n\n{$_REQUEST['message']}";

$mailPlain = new MailPlain ($subject, $message, $_REQUEST['email'], $_REQUEST['name']);

if($send_succ = $this->TrySend())
{
	# Success
	echo "Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email";
}
else
{
	# Fail
	echo "Ваше сообщение не было доставлено.";
}
*/