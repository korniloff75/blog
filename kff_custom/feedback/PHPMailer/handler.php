<?php
require_once __DIR__ . '/MailPlain.php';

/* var_dump(
	$_REQUEST
); */

$test = 1;

// ?
$index_my_addon_path = $_SERVER['DOCUMENT_ROOT'].'/kff_custom/index_my_addon.php';
if(file_exists($index_my_addon_path) && !$test)
	require_once $index_my_addon_path;
else
{
	// note Глушим Логгер в продакшне
	// todo Доработать
	class fixLog
	{
		public function add($txt)
		{
			trigger_error('Заглушка сработала! - ' . $txt);
		}
	}
	$log = new fixLog();

}


$subject = "{$_REQUEST['subject']} - feedback from " . $_SERVER['HTTP_HOST'];
$message = "{$_REQUEST['name']} пишет: \n{$_REQUEST['message']}";

// *Достаём настройки из админки
$cfg = json_decode(
	file_get_contents(DR.'/data/storage/module.mail.to.admin/cfg.dat'),1
);

// *init MailPlain
$mailPlain = new MailPlain ($subject, $message, $_REQUEST['email'], $_REQUEST['name']);

$mailPlain->cfg = &$cfg;

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
elseif($send_succ = $mailPlain->TrySend())
{
	# Success
	echo "<div class=\"success\">Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email или Telegram. </div>";
}
else
{
	# Fail
	echo "<div class=\"error\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>";

}
