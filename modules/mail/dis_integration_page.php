<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

$mailToAdminStorage = new EngineStorage('module.mail.to.admin');
$emails = $mailToAdminStorage->get('emails');
$fromallform = $mailToAdminStorage->get('fromallform');


if($MODULE_URI == '/'){

$return = '
<form name="form_mail_module" action="/'.$URI[1].'/add" method="post"  onsubmit="if(document.getElementById(\'roscomnadzor\').checked){this.submit();}else{alert(\'Нужно дать согласие на обработку персональных данных\'); return false;}">
<p>Ваше имя<br><input type="text" name="name" value="" size="26" required></p>
<p>Ваш email (Нужен для ответа)<br><input type="text" name="email" value="" size="26" required></p>
<p>Содержимое письма<br><TEXTAREA NAME="text" ROWS="5" COLS="50" required></TEXTAREA></p>';

if($fromallform != 1){
$return.= '<p style="line-height:1;"><img border="1" id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha" onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;">
<br><span style="font-size:12px; opacity: 0.7;">Для обновления символов нажмите на картинку</span></p>
<p>Введите символы с картинки<br><input type="text" name="captcha_form_mail_module" value="" size="10"  autocomplete="off" required></p>';
}

$return.= '<p><input type="checkbox" name="roscomnadzor" value="ok" id="roscomnadzor"> <label for="roscomnadzor">Я согласен на <a href="/fz152" target="_blank">обработку моих персональных данных</a></label></p>
<p><input type="submit" name="" value="Отправить"></p>
</form>';

}elseif($MODULE_URI == '/add'){

	$Page->clear(); // Очистили страницу
	if(md5(strtolower($_POST['captcha_form_mail_module']).$Config->ticketSalt) != $_COOKIE['captcha'] && $fromallform == false){
		$return.=  '<p>Символы с картинки введены неверно</p><p class="p_link_back"><a href="/'.($Page->isIndexPage()?'':$URI[1]).'">Вернуться назад</a></p>';
	}else{

		$sit = htmlspecialchars($_SERVER['HTTP_HOST']);
		$txt = "На сайте $sit, было написано письмо\n\n\n\n";
		$valid = false;
		foreach($_POST as $key => $value){
			if($value != ''){
				$txt.= ucfirst(htmlspecialchars($key)).": ".htmlspecialchars($value)."\n\n\n";
				$valid = true;
			}
		}
		
		if($valid && $emails !== false){
			addmail($emails, "С сайта $sit написано письмо", $txt);
			$return.= '<p>Форма успешно отправлена</p><p><a href="/">Перейти на главную страницу</a></p>';
		}else{
			$return.= '<p>Форма не отправлена</p><p><a href="/">Перейти на главную страницу</a></p>';
		}
		
	}
	setcookie('captcha','',time(),'/');
	
}else{
	header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
}


return $return;
?>