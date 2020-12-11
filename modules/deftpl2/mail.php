<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

$mailToAdminStorage = new EngineStorage('module.mail.to.admin');
$emails = $mailToAdminStorage->get('emails');
$fromallform = $mailToAdminStorage->get('fromallform');


if($MODULE_URI == '/'){

echo '<div style="margin-bottom:20px;overflow:hidden;">'.file_get_contents('./data/pages/page_'.$URI[1].'.dat').'</div>
<form class="form-mail" name="form_mail_module" action="/'.$URI[1].'/add" method="post"  onsubmit="if(document.getElementById(\'roscomnadzor\').checked){this.submit();}else{alert(\'Нужно дать согласие на обработку персональных данных\'); return false;}">
<div id="form-mail">
<div id="form-name">
<p>Ваше имя</p>
<input type="text" name="name" value="" size="26" required>
</div>
<div id="form-email">
<p>Ваш email (Нужен для ответа)</p>
<input type="text" name="email" value="" size="26" required>
</div>
<div id="form-textarea">
<p>Содержимое письма</p>
<TEXTAREA NAME="text" ROWS="5" COLS="50" required></TEXTAREA>
</div>';

if($fromallform != 1){
echo'<div id="form-captcha">
<img border="1" id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha" onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;">
<p>Введите символы с картинки</p>
<input class="captcha-text" type="text" name="captcha_form_mail_module" value="" size="10" required>
<p style="font-size:12px; opacity: 0.7;clear:both;">Для обновления символов нажмите на картинку</p>
</div>';
}

echo'<p><input type="checkbox" name="roscomnadzor" value="ok" id="roscomnadzor"> <label for="roscomnadzor">Я согласен на <a href="/fz152" target="_blank">обработку моих персональных данных</a></label></p>
<div id="form-button"><input type="submit" name="" value="Отправить"></div>
</div>
</form>';

}elseif($MODULE_URI == '/add'){

	$Page->clear(); // Очистили страницу
	
	if(md5(strtolower($_POST['captcha_form_mail_module']).$Config->ticketSalt) != $_COOKIE['captcha'] && $fromallform == false){
		echo '<p class="msg-error">Символы с картинки введены неверно</p><p class="userlink"><a href="/'.($Page->isIndexPage()?'':$URI[1]).'">Вернуться назад</a></p>';
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
			echo'<p class="msg-success">Сообщение успешно отправлено</p><p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
		}else{
			echo'<p class="msg-error">Сообщение не отправлено</p><p class="userlink"><a href="/'.$URI[1].'">Повторить отправку сообщения</a></p>';
		}
		
	}
	setcookie('captcha','',time(),'/');
	
}
?>