<?php
/* var_dump(
	$_REQUEST
); */

// ?
define(
	'INDEX_MY_ADDON_PATH',
	(file_exists("{$_SERVER['DOCUMENT_ROOT']}/kff_custom") ? "{$_SERVER['DOCUMENT_ROOT']}/kff_custom" : __DIR__.'/..') . "/index_my_addon.php"
);


if(file_exists(INDEX_MY_ADDON_PATH))
	require_once INDEX_MY_ADDON_PATH;

if(!$log)
{
	// note Глушим Логгер в продакшне
	class fixLog
	{
		public function add($txt, $e_type=null, $dump=[])
		{
			$o='';
			if(count($dump)) foreach($dump as $i)
			{
				ob_start();
				var_dump($i);
				$o.= ob_get_clean();
			}
			trigger_error(('Заглушка - ' . $txt . $o), $e_type ?? E_USER_NOTICE);
		}
	}
	$log = new fixLog();

}


require_once __DIR__ . '/MailPlain.php';


$subject = "{$_REQUEST['subject']} - feedback from " . $_SERVER['HTTP_HOST'];
$message = "{$_REQUEST['name']} пишет: \n{$_REQUEST['message']}";

// *init MailPlain
$mailPlain = new MailPlain ($subject, $message, $_REQUEST['email'], $_REQUEST['name']);

// *Достаём настройки из админки
$mailPlain->cfg = json_decode(
	file_get_contents(DR.'/data/storage/module.mail.to.admin/cfg.dat'),1
);
// $mailPlain->log->add('CFG',null,[$mailPlain->cfg]);

MailPlain::save($mailPlain->validated);

/* Optional

$mailPlain->Username = "fb@js-master.ru";
$mailPlain->Password = "1975kp@1975";
$mailPlain->Host = "web01-cp.marosnet.net";
# In constant OWNER['email'] must contains string or array with emails
# If OWNER['email'] don't defined - can use
$mailPlain->to_emails = [
	// aray with emails
],
*/

if(@$_REQUEST['captcha'] != $kff::realIP())
{
	echo $_REQUEST['captcha'] . '<br>';
	echo $kff::realIP() . '<br>';
	// echo $_SERVER['REMOTE_ADDR'] . '<br>';
	// echo $_SESSION['captcha'] . '<br>';
	echo "Невидимая каптча не пройдена. Попробуйте ещё раз.";
}
elseif($mailPlain->TrySend())
{
	# Success
	echo "<div class=\"success\">Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email или Telegram. </div>";
}
else
{
	# Fail
	echo "<div class=\"error\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>";

}
