<?php

	
if(!isset($URI[2])){
	if($User->authorized){
		$page->clear();// Очистили страницу
		$page->title = 'Профиль пользователя';
		$page->name = 'Привет '.$User->login;
		if(file_exists(DR.'/'.$Config->userAvatarDir.'/'.$User->login.'.jpg')){
			$avatar_file = '/'.$Config->userAvatarDir.'/'.$User->login.'.jpg?'.filemtime(DR.'/'.$Config->userAvatarDir.'/'.$User->login.'.jpg');
		}else{
			$avatar_file = '/modules/users/avatar.png';
		}
		$return = '
		<div class="user_profile" id="user_profile">
			<p class="p_avatar"><img src="'.$avatar_file.'" alt="avatar" id="avatar"></p>
			<p class="p_info">Зарегистрирован '.human_time(time() - $User->timeRegistration).' назад</p>
			<p class="p_info">Оставлено '.$User->numPost.' '.numDec($User->numPost, array('сообщение', 'сообщения', 'сообщений')).'</p>
			
			<p class="p_link_cfg"><a href="/'.$URI[1].'/avatar">Загрузить аватар</a></p>
			<p class="p_link_cfg"><a href="/'.$URI[1].'/cfg">Настройки профиля</a></p>
			<p class="p_link_exit"><a href="/'.$URI[1].'/exit">Выход из профиля</a></p>
		</div>
		
		';
	}else{
		
		//$page->clear();// Очистили страницу
		$page->title = 'Вход';
		$page->name = 'Вход';
		
		$return = '<form name="forma" action="/'.$URI[1].'/in" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="add">
		<div class="user_form user_auth_form">
			<p class="p_login">Логин:<br><input type="text" name="login" value="" required></p>
			<p class="p_password">Пароль:<br><input type="password" name="password" value="" required></p>
			<p class="p_captcha_img" style="line-height:1;"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;">
				<br><span style="font-size:12px; opacity: 0.7;">Для обновления символов нажмите на картинку</span>
			</p>
			<p class="p_captcha_input">Символы с картинки:<br><input type="text" name="captcha" value="" size="10"  autocomplete="off" required></p>
			<p class="p_submit"><input type="submit" name="" value="Отправить"></p>	
			<p class="p_link_reg"><a href="/'.$URI[1].'/reg">Зарегистрироваться</a></p>
			<p class="p_link_newpassword"><a href="/'.$URI[1].'/newpassword">Не помню пароль</a></p>
		</div>
		</form>';
	}
	
	
	
}elseif($URI[2]=='avatar'){
	
	if($User->authorized){
		$Page->clear();// Очистили страницу
		$Page->title = 'Профиль пользователя';
		$Page->name = 'Загрузка аватара';
		if(!$Config->userAvatarUpload){
			$return = '<p>Загрузка аватаров недоступна</p>
				<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
		}elseif($Config->userAvatarMinNumPost > $User->numPost){
			$return = '<p>Пока что вам недоступна загрузка аватаров. Общайтесь на сайте, будьте актывным, и со временем у вас появится возможность загружать аватары.</p>
				<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
		}elseif(md5($_POST['ticket'].$Config->ticketSalt) == $_COOKIE['ticket']){
			
			$errorUpLoad = false;
			$errorReport = 'Неизвестная ошибка';

			if(isset($_FILES['img'])){
				
				if($_FILES['img']['error'] == '0'){
					
					if(($Config->userAvatarSize * 1000) <= $_FILES['img']['size']){
						$errorUpLoad = true;
						$errorReport = 'Превышен лимит файла в '.$Config->userAvatarSize.' кб.';
					}else{
						if(!file_exists(DR.'/'.$Config->userAvatarDir)){
							mkdir(DR.'/'.$Config->userAvatarDir);
						}

						$img_file = move_uploaded_file($_FILES['img']['tmp_name'], DR.'/'.$Config->userAvatarDir.'/'.$User->login);
						$img = imagecreatefromjpeg(DR.'/'.$Config->userAvatarDir.'/'.$User->login);
						if($img == false){
							$errorUpLoad = true;
							$errorReport = 'Ошибка при сохранении картинки';
						}elseif(imagejpeg($img, DR.'/'.$Config->userAvatarDir.'/'.$User->login.'.jpg') == false){
							$errorUpLoad = true;
							$errorReport = 'Ошибка при сохранении картинки';
						}
						imagedestroy($img);
						unlink(DR.'/'.$Config->userAvatarDir.'/'.$User->login);
						
					}

				}else{
					if($_FILES['img']['error'] == '1') System::notification('Ошибка при загрузке пользовательского ('.$User->login.') аватара, размер принятого файла превысил максимально допустимый размер, который задан настройками сервера', 'r');
					elseif($_FILES['img']['error'] == '3') System::notification('Ошибка при загрузке пользовательского ('.$User->login.') аватара, загружаемый файл был получен только частично', 'r');
					elseif($_FILES['img']['error'] == '4') System::notification('Ошибка при загрузке пользовательского ('.$User->login.') аватара, файл не был загружен', 'r');
					elseif($_FILES['img']['error'] == '6') System::notification('Ошибка при загрузке пользовательского ('.$User->login.') аватара, отсутствует временная папка', 'r');
					elseif($_FILES['img']['error'] == '7') System::notification('Ошибка при загрузке пользовательского ('.$User->login.') аватара, не удалось записать файл на диск', 'r');
					else System::notification('Ошибка при загрузке пользовательского ('.$User->login.') аватара, неизвестная ошибка', 'r');
					$errorUpLoad = true;
					$errorReport = 'Ошибка при загрузке файлов';
				}
			}else{
				System::notification('Ошибка при загрузке автара пользователем '.$User->login.', файл не получен', 'r');
				$errorUpLoad = true;
				$errorReport = 'Файл не получен';
			}

			if($errorUpLoad){
				$Page->name = 'Ошибка';
				$return = '<p>'.$errorReport.'</p>';
			}else{
				System::notification('Загружен аватар пользователем '.$User->login.', расположение: /'.$Config->userAvatarDir.'/'.$User->login.'.jpg', 'g');
				$return = '<p>Аватар успешно загружен</p>';
			}

			$return.= '<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться к профилю</a></p>';
	
			
		}else{
			$ticket = random(255);
			setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
			$return = '<form name="forma" action="/'.$URI[1].'/avatar" enctype="multipart/form-data" method="post">
			<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
			<div class="user_form user_cfg_form">
				<p class="p_upload">Выберите изображение (макс. '.$Config->userAvatarSize.' кб.):<br>
					Допускается только JPEG формат<br>
					<input type="file" name="img"></p>
				<p class="p_sumit"><input type="submit" name="" value="Загрузить"></p>
				<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>	
			</div>
			</form>';
		}
		
		
		
	}else{
		$Page->clear();// Очистили страницу
		$Page->title = 'Перенаправление';
		$Page->name = 'Перенаправление';
		$return = '<p>Перенаправление на страницу авторизации</p>
		<script type="text/javascript">
			setTimeout("window.location.href = \"/'.$URI[1].'\";", 1000);
		</script>';
	}
	
}elseif($URI[2]=='cfg'){
	
	if($User->authorized){
		$page->clear();// Очистили страницу
		$page->title = 'Профиль пользователя';
		$page->name = 'Настройки';
		if(md5($_POST['ticket'].$Config->ticketSalt) == $_COOKIE['ticket']){
			
			$return = '<p>Настройки успешно сохранены</p>
			<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться к профилю</a></p>';

			$listEmailsUsers = System::listEmailsUsers();
			if(($key = array_search($User->email, $listEmailsUsers)) !== false){
				unset($listEmailsUsers[$key]); // Удалили найденый элемент массива
			}

			if ($_POST['password'] != ''){
				$salt = random(255);
				$User->salt = $salt;
				$User->password = cipherPass($_POST['password'], $salt);
				$User->newPasswordChecksum = random(100);
				System::notification('Пользователь '.$User->login.' изменил свой пароль', 'g');
				setcookie('user_password',cipherPass($_POST['password'], $User->salt),time()+32000000,'/');
			}
			
			if($Config->userEmailChange && $User->email != $_POST['email']){
				if($_POST['email'] == ''){
					$page->name = 'Ошибка';
					$return = '<p>Email не введен</p>
					<p class="p_link_back"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false){
					$page->name = 'Ошибка';
					$return = '<p>Email введен некорректно</p>
					<p class="p_link_back"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}elseif(in_array(strtolower($_POST['email']), $listEmailsUsers)){
					$page->name = 'Ошибка';
					$return = '<p>Пользователь с таким email уже существует</p>
					<p class="p_link_back"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}elseif(System::isBadMailDomain($_POST['email']) && $Config->userEmailFilterList){
					$page->name = 'Ошибка';
					$return = '<p>Такой email иметь запрещено</p>
					<p class="p_link_back"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}else{

					System::notification('Пользователь '.$User->login.' изменил свой email '.$User->email.' на '.$_POST['email'].'', 'g');

					// Обновление емайла
					$User->email = htmlspecialchars(substr(strtolower($_POST['email']), 0, 255));
					$User->emailChecked = 0;
					$User->emailChecksum = random(16);
					
					$listEmailsUsers[] = $User->email;
					System::updateListEmailsUsers($listEmailsUsers);
					if ($Config->userEmailChecked){
						$return.= '<p>Авторизация была сброшена, т.к. необходимо подтвердить новый email.</p>
						<p><a href="/'.$URI[1].'/emailchecked">Подтвердить новый email</a></p>';

					}
				}
			}

			$User->save();
			
			
		}else{
			$ticket = random(255);
			setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
			$return = '<form name="forma" action="/'.$URI[1].'/cfg" method="post">
			<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
			<div class="user_form user_cfg_form">';
				if ($Config->userEmailChange) $return.= '<p class="p_email">Email:<br><input type="text" name="email" value="'.$User->email.'" placeholder="Обязательно для заполнения"></p>';
				$return.= '<p class="p_password">Пароль:<br><input type="password" name="password" value="" placeholder="Оставьте пустым что-бы не менять"></p>
				<p class="p_sumit"><input type="submit" name="" value="Сохранить"></p>
				<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>	
			</div>
			</form>';
		}
		
		
		
	}else{
		$page->clear();// Очистили страницу
		$page->title = 'Перенаправление';
		$page->name = 'Перенаправление';
		$return = '<p>Перенаправление на страницу авторизации</p>
		<script type="text/javascript">
			setTimeout("window.location.href = \"/'.$URI[1].'\";", 1000);
		</script>';
	}
	
}elseif($URI[2]=='in'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Вход';
	
	if(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		$return = '<p>Цифры с картинки введены неверно</p><p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
	}else{
		if(($CUser = User::getConfig($_POST['login'])) !== false){
			$AUser = new User($_POST['login'], cipherPass($_POST['password'], $CUser->salt));
			if($AUser->authorized){
				
				if($Config->userEmailChecked && $AUser->emailChecked || !$Config->userEmailChecked){
					$page->name = 'Вход';
					$return = '<p>Вы успешно авторизованны</p><p><a href="/'.$URI[1].'">Перейти к профилю</a> или <a href="/">вернуться на главную страницу</a></p>';
					System::notification('Выполнена авторизация пользователя '.$AUser->login, 'g');
				}else{
					$page->name = 'Ошибка';
					$return = '<p>Вы еще не подтвердили свой email</p>
					<p><a href="/'.$URI[1].'/emailchecked">Отослать письмо с подтверждением еще раз</a></p>
					<p><a href="/">Вернуться на главную страницу</a></p>';
					System::notification('Выполнена авторизация пользователя '.$AUser->login.', но из за неподтвержденного email авторизация была сброшена.', 'r');
				}
				setcookie('user_login',$_POST['login'],time()+32000000,'/');
				setcookie('user_password',cipherPass($_POST['password'], $CUser->salt),time()+32000000,'/');
				
			}else{
				$page->name = 'Ошибка';
				$return = '<p>Пароль введен неверно</p>
				<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
				System::notification('Ошибка при авторизации пользователя '.$_POST['login'].', введен ошибочный пароль', 'r');
			}
		}else{
			$page->name = 'Ошибка';
			$return = '<p>Логин введен неверно</p>
			<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
			System::notification('Ошибка при авторизации пользователя '.$_POST['login'].', пользователя не существует', 'r');
		}
	}
	setcookie('captcha','','0','/');
	
	
}elseif($URI[2]=='emailchecked'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Подтверждение email адреса';
	$page->name = 'Подтверждение email адреса';
	
	$ticket = random(255);
			setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
			$return = '<form name="forma" action="/'.$URI[1].'/emailchecked2" method="post">
			<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
			<div class="user_form user_emailchecked_form">
				<p class="p_info">Введите данные от своего аккаунта и мы отправим вам ссылку для подтверждения email адреса.</p>
				<p class="p_login">Логин:<br><input type="text" name="login" value="" required></p>
				<p class="p_email">Email:<br><input type="text" name="email" value="" required></p>
				<p class="p_captcha_img" style="line-height:1;"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;">
					<br><span style="font-size:12px; opacity: 0.7;">Для обновления символов нажмите на картинку</span>
				</p>
				<p class="p_captcha_input">Символы с картинки:<br><input type="text" name="captcha" value="" autocomplete="off" required></p>
				<p class="p_sumit"><input type="submit" name="" value="Отправить"></p>
				<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>	
			</div>
			</form>';
	
	
}elseif($URI[2]=='emailchecked2' && $Config->userEmailChecked){
	
	$page->clear();// Очистили страницу
	$page->title = 'Подтверждение email адреса';
	$page->name = 'Подтверждение email адреса';
	
	if(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		$return = '<p>Цифры с картинки введены неверно</p>
		<p class="p_link_back"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}elseif (md5($_POST['ticket'].$Config->ticketSalt) != $_COOKIE['ticket']){
		$return = '<p>Ошибка безопасности</p>
		<p class="p_link_back"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}elseif (($CUser = User::getConfig($_POST['login'])) !== false){
		
		if($CUser->emailChecked){
			$return = '<p>У этого аккаунта email уже подтвержден</p>
			<p class="p_link_back"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
		}elseif ($CUser->email == $_POST['email']){
			$CUser->emailChecksum = random(16);
			User::setConfig($CUser->login, $CUser);
			$return = '<p>На указанный email выслана ссылка по которой нужно перейти</p>
			<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
			$txt = "Для подтверждения вашего email перейдите по ссылке ниже\n\n\n";
			$txt.= $Config->protocol."://".SERVER."/".$URI[1]."/echeck/".$CUser->login."/".$CUser->emailChecksum;
			addmail($CUser->email, "Ссылка для подтверждения email", $txt, $Config->adminEmail);
			System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для подтверждения email', 'g');

		}else{
			$return = '<p>Указанный email отличается от того, на который был зарегистрирован этот аккаунт.</p>
			<p class="p_link_back"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
		}
	}else{
		$return = '<p>Пользователь с таким логином не найден</p>
		<p class="p_link_back"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}
	setcookie('captcha','','0','/');
	
}elseif($URI[2]=='newpassword'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Восстановление доступа';
	$page->name = 'Восстановление доступа';
	
	$ticket = random(255);
	setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
	if($Config->userNewPassword){
		$return = '<form name="forma" action="/'.$URI[1].'/newpassword2" method="post">
		<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
		<div class="user_form user_newpassword_form">
			<p class="p_info">Все пароли на нашем сайте хранятся в зашифрованном виде, мы не сможем его расшифровать и выслать вам. 
			Однако мы можем сгенерировать для вас новый пароль и выслать его вам на email, который был указан в вашем аккаунте.</p> 
			<p class="p_info">Введите данные от своего аккаунта и мы отправим вам ссылку для генерации нового пароля.</p>
			<p class="p_login">Логин:<br><input type="text" name="login" value="" required></p>
			<p class="p_email">Email:<br><input type="text" name="email" value="" required></p>
			<p class="p_captcha_img" style="line-height:1;"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;">
				<br><span style="font-size:12px; opacity: 0.7;">Для обновления символов нажмите на картинку</span>
			</p>
			<p class="p_captcha_input">Символы с картинки:<br><input type="text" name="captcha" value="" size="10"  autocomplete="off" required></p>
			<p class="p_submit"><input type="submit" name="" value="Отправить"></p>
			<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>
		</div>
		
		</form>';
	}else{
		$return = '<p>К сожалению, в данное время мы не можем восcтановить ваш пароль</p>
		<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
	}
		
	
	
}elseif($URI[2]=='newpassword2' && $Config->userNewPassword){
	
	$page->clear();// Очистили страницу
	$page->title = 'Восстановление доступа';
	$page->name = 'Восстановление доступа';
	
	if(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		$return = '<p>Цифры с картинки введены неверно</p>
		<p class="p_link_back"><a href="/'.$URI[1].'/newpassword">Вернуться назад</a></p>';
	}elseif (md5($_POST['ticket'].$Config->ticketSalt) != $_COOKIE['ticket']){
		$return = '<p>Ошибка безопасности</p>
		<p class="p_link_back"><a href="/'.$URI[1].'/newpassword">Вернуться назад</a></p>';
	}elseif (($CUser = User::getConfig($_POST['login'])) !== false){
		if ($CUser->email == $_POST['email']){
			$CUser->newPasswordChecksum = random(100);
			User::setConfig($CUser->login, $CUser);

			$return = '<p>На указанный email выслана ссылка по которой нужно перейти</p>
			<p class="p_link_back"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
			$txt = "На сайте ".SERVER." был сделан запрос на генерацию нового пароля от аккаунта ".$CUser->login.".\n\n\n";
			$txt.= "Для генерации нового пароля перейдите по ссылке ниже.\n";
			$txt.= "Если вы не запрашивали генерацию нового пароля, то просто проигнорируйте это письмо. Если подобные письма приходять слишком часто, то обратитесь к администратору сайта.\n\n\n";
			$txt.= $Config->protocol."://".SERVER."/".$URI[1]."/npcheck/".$CUser->login."/".$CUser->newPasswordChecksum;
			addmail($CUser->email, "Ссылка для генерации нового пароля", $txt, $Config->adminEmail);
			System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для генерации нового пароля', 'g');

		}else{
			$return = '<p>Указанный email отличается от того, на который был зарегистрирован этот аккаунт.</p>
			<p class="p_link_back"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
		}
	}else{
		$return = '<p>Пользователь с таким логином не найден</p>
		<p class="p_link_back"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}
	setcookie('captcha','','0','/');
	
}elseif($URI[2]=='exit'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Выход';
	$page->name = 'Выход';
	
	$return = '<p>Вы успешно вышли из системы</p>
	<p><a href="/">Перейти на главную страницу</a></p>';
	setcookie('user_login','','0','/');
	setcookie('user_password','','0','/');
	
	if($User->authorized) System::notification('Выполнена деавторизация пользователя '.$User->login, 'g');
	
}elseif($URI[2]=='reg'){
	$ticket = random(255);
	setcookie('ticket',md5($ticket.$Config->ticketSalt),time()+32000000,'/');
	
	$page->clear();// Очистили страницу
	$page->title = 'Регистрация';
	$page->name = 'Регистрация';
	if($Config->registration){
		$return = '<form name="forma" action="/'.$URI[1].'/addreg" method="post" onsubmit="if(document.getElementById(\'roscomnadzor\').checked){this.submit();document.getElementById(\'ticket\').value=\''.$ticket.'\';}else{alert(\'Нужно дать согласие на обработку персональных данных\'); return false;}">
		<INPUT TYPE="hidden" NAME="act" VALUE="add">
		<INPUT TYPE="hidden" NAME="ticket" id="ticket" VALUE="noInput">
		<div class="user_form user_reg_form">
			<p class="p_info">Внимание! Логин может содержать только символы латинского алфавита и цифры. 
			'.($Config->userEmailChecked?'На указанный email придет письмо для подтверждения регистрации.':'').'</p>
			<p class="p_login">Логин:<br><input type="text" name="login" value="" required ></p>
			<p class="p_password">Пароль:<br><input type="password" name="password" value="" required ></p>
			<p class="p_email">Email:<br><input type="text" name="email" value="" required ></p>
			<p class="p_captcha_img" style="line-height:1;"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;">
				<br><span style="font-size:12px; opacity: 0.7;">Для обновления символов нажмите на картинку</span>
			</p>
			<p class="p_captcha_input">Символы с картинки:<br><input type="text" name="captcha" value="" size="10"  autocomplete="off" required ></p>
			<p class="p_roscomnadzor"><input type="checkbox" name="roscomnadzor" value="ok" id="roscomnadzor"> <label for="roscomnadzor">Я согласен на <a href="/fz152" target="_blank">обработку моих персональных данных</a></label></p>
			<p class="p_submit"><input type="submit" name="" value="Зарегистрироваться"></p>	
		</div>
		</form>';
	}else{
		$return = '<p>Регистрация на сайте временно приостановлена</p>';
	}
	
	
	
}elseif($URI[2]=='addreg'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Регистрация';
	
	if(!$Config->registration){
		$page->name = 'Ошибка';
		$return = '<p>Регистрация на сайте временно приостановлена</p>';
	}elseif(in_array(IP, $Config->ipBan)){
		$page->name = 'Ошибка';
		$return = '<p>Регистрация с вашего ip временно приостановлена</p>';
				$return.= '<p class="p_link_back"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		$return = '<p>Цифры с картинки введены неверно</p>';
		$return.= '<p class="p_link_back"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(md5($_POST['ticket'].$Config->ticketSalt) != $_COOKIE['ticket']){
		$page->name = 'Ошибка';
		$return = '<p>Проверка безопасности неудачна</p>';
		$return.= '<p class="p_link_back"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false){
		$page->name = 'Ошибка';
		$return = '<p>Введенный емейл некорректен</p>
		<p class="p_link_back"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(in_array(strtolower($_POST['email']), System::listEmailsUsers())){
		$page->name = 'Ошибка';
		$return = '<p>Пользователь с таким email уже существует</p>';
		$return.= '<p class="p_link_back"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(System::isBadMailDomain($_POST['email']) && $Config->userEmailFilterList){
		$page->name = 'Ошибка';
		$return = '<p>Такой email иметь запрещено</p>';
		$return.= '<p class="p_link_back"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}else{
		$salt = random(255);
		if(($ers = User::registration($_POST['login'], $_POST['password'], $salt, $_POST['email'])) == 0){
			System::notification('Зарегистрирован пользователь '.$_POST['login'].'', 'g');
			$page->name = 'Регистрация';
			if($Config->userEmailChecked){
				$CUser = User::getConfig($_POST['login']);
				$return = '<p>Для завершения регистрации подтвердите ваш email, для этого необходимо перейти по ссылке которую мы отправили вам на email.</p>
				<p><a href="/'.$URI[1].'">Перейти к профилю</a></p>';
				$txt = "Для подтверждения вашего email перейдите по ссылке ниже\n\n\n";
				$txt.= $Config->protocol."://".SERVER."/".$URI[1]."/echeck/".$CUser->login."/".$CUser->emailChecksum;
				addmail($CUser->email, "Ссылка для подтверждения email", $txt, $Config->adminEmail);
				System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для подтверждения email', 'g');
			}else{
				$return = '<p>Вы успешно зарегистрированы</p>';
				$return.= '<p><a href="/'.$URI[1].'">Перейти к профилю</a></p>';
			}
			setcookie('user_login',$_POST['login'],time()+32000000,'/');
			setcookie('user_password',cipherPass($_POST['password'], $salt),time()+32000000,'/');
		}else{
			$page->name = 'Ошибка';
			$errmsg = 'Неизвестная ошибка';
			if ($ers == 1){$errmsg = 'Пользователь с похожим логином уже существует';}
			if ($ers == 2){$errmsg = 'Ошибка при сохранении параметров';}
			if ($ers == 3){$errmsg = 'Не все поля формы заполнены';}
			if ($ers == 4){$errmsg = 'Логин содержит недопустимые символы';}
			if ($ers == 5){$errmsg = 'Слишком длинный логин, пароль или емайл';}
			System::notification('Ошибка при регистрации пользователя '.$_POST['login'].' - '.$errmsg, 'r');
			$return = '<p>'.$errmsg.'</p><p class="p_link_back"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
		}
		
	}
	
	setcookie('captcha','','0','/');
	
}elseif($URI[2]=='echeck' && $Config->userEmailChecked){
	$page->clear();// Очистили страницу
	$page->title = 'Подтверждение email адреса';
	$page->name = 'Подтверждение email адреса';
	if(($CUser = User::getConfig($URI[3])) !== false){
		if(!$CUser->emailChecked && $CUser->emailChecksum != '' && $CUser->emailChecksum == $URI[4]){
			$CUser->emailChecked = 1;

			if(User::setConfig($CUser->login, $CUser)){

				// запись в список емайлов // для 5.1.7 и ниже
				$listEmailsUsers = System::listEmailsUsers();
				if(($key = array_search(strtolower($CUser->email), $listEmailsUsers)) === false){
					$listEmailsUsers[] = strtolower($CUser->email);
					System::updateListEmailsUsers($listEmailsUsers);
				}/////////////////////////////////////////////
				

				$return = '<p>Ваш email адрес успешно подтвержден</p>
				<p><a href="/'.$URI[1].'">Перейти к профилю</a></p>';
				System::notification('Подтвержден email '.$CUser->email.' пользователя '.$CUser->login, 'g');
			}else{
				$return = '<p>Ошибка записи настроек, обратитесь к администратору.</p>
				<p><a href="/">Перейти на главную страницу</a></p>';
				System::notification('Ошибка записи настроек при подтверждении емайла '.$CUser->email.' пользователя '.$CUser->login, 'r');
			}
		}else{
			$return = '<p>Ошибка контрольной суммы. Возможно вы допустили ошибку при копировании ссылки или email уже был подтвержден ранее.</p>
				<p><a href="/">Перейти на главную страницу</a></p>';
		}
	}else{ 
		$return = '<p>Не подтверждено. Возможно вы допустили ошибку при копировании ссылки или email уже был подтвержден ранее.</p>
				<p><a href="/">Перейти на главную страницу</a></p>';
	}
	
	
}elseif($URI[2]=='npcheck' && $Config->userNewPassword){
	$page->clear();// Очистили страницу
	$page->title = 'Восстановление доступа';
	$page->name = 'Восстановление доступа';
	if(($CUser = User::getConfig($URI[3])) !== false){
		if($CUser->newPasswordChecksum != '' && $CUser->newPasswordChecksum == $URI[4]){
			$newPassword = random(rand(5, 15));
			$CUser->password = cipherPass($newPassword, $CUser->salt);
			$CUser->newPasswordChecksum = '';

			$txt = "Ваш новый пароль от аккаунта ".$CUser->login.": ".$newPassword."\n\n\n";
			$txt.= "Сайт: ".SERVER;
			addmail($CUser->email, "Ваш новый пароль", $txt, $Config->adminEmail);
			System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для генерации нового пароля', 'g');

			if(User::setConfig($CUser->login, $CUser)){

				$return = '<p>Ваш новый пароль успешно сгенерирован и был выслан вам на email</p>
				<p><a href="/'.$URI[1].'">Перейти к профилю</a></p>';
				System::notification('Сгенерирован новый пароль для пользователя '.$CUser->login, 'g');
			}else{
				$return = '<p>Ошибка записи настроек, обратитесь к администратору.</p>
				<p><a href="/">Перейти на главную страницу</a></p>';
				System::notification('Ошибка записи настроек при генерировании нового пароля для пользователя '.$CUser->login, 'r');
			}
		}else{
			$return = '<p>Ошибка контрольной суммы. Возможно вы допустили ошибку при копировании ссылки.</p>
				<p><a href="/">Перейти на главную страницу</a></p>';
		}
	}else{ 
		$return = '<p>Ошибка контрольной суммы. Возможно вы допустили ошибку при копировании ссылки.</p>
				<p><a href="/">Перейти на главную страницу</a></p>';
	}
	
	
}elseif($URI[2]=='ban'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			$ticket = random(255);
			setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
			
			$return = '<form name="forma" action="/'.$URI[1].'/addban/'.$URI[3].'" method="post">
			<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
			<div class="user_form user_ban_form" id="user_ban_form">
				<p class="p_info">Причина блокировки:<br><TEXTAREA NAME="cause" ROWS="20" COLS="100" style="height:100px;">'.$CUser->causeBan.'</TEXTAREA></p>
				<p class="p_time_ban">На какое время:<br>
				<SELECT NAME="time">
						<OPTION VALUE="none" selected>Выберите варианты
						<OPTION VALUE="0">Не блокировать
						<OPTION VALUE="3600">1 час
						<OPTION VALUE="86400">1 день
						<OPTION VALUE="259200">3 дня
						<OPTION VALUE="604800">1 неделя
						<OPTION VALUE="2628000">1 месяц
						<OPTION VALUE="7884000">3 месяца
						<OPTION VALUE="15768000">6 месяцев
						<OPTION VALUE="31536000">1 год
						<OPTION VALUE="3153600000">Навсегда
				</SELECT>
				</p>
				<p class="p_submit"><input type="submit" name="" value="Отправить"></p>	
			</div>
			</form>
			<p class="p_link_back"><a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>';
			
		}else{
			$page->name = 'Ошибка';
			$return = '<p>Пользователь не найден</p>';
				$return.= '<p><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		$return = '<p><a href="/">Перейти на главную страницу</a></p>';
	}
	
}elseif($URI[2]=='addban'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			if(md5($_POST['ticket'].$Config->ticketSalt) == $_COOKIE['ticket']){
				
				$CUser->causeBan = htmlspecialchars($_POST['cause']);// Причина 
				if($_POST['time'] != 'none'){
					$CUser->timeBan =  time() + (is_numeric($_POST['time'])?(int)$_POST['time']:0);// Время
				}
				
				if(User::setConfig($CUser->login, $CUser)){
					if($_POST['time'] == 'none'){
						$return = '<p>Ошибка при сохранении настроек</p>
						<p><a href="/'.$URI[1].'/'.$CUser->login.'">Перейти к профилю пользователя</a></p>';
						System::notification('Ошибка при блокировке пользователя '.$CUser->login.', не указано время блокировки. Автор блокировки пользователь '.$User->login, 'r');
					}elseif($_POST['time'] == '0'){
						$return = '<p>Пользователь успешно разблокирован</p>
						<p><a href="/'.$URI[1].'/'.$CUser->login.'">Перейти к профилю пользователя</a></p>';
						System::notification('Разблокирован пользователь '.$CUser->login.', автор разблокировки пользователь '.$User->login, 'g');
					}else{
						$return = '<p>Пользователь успешно заблокирован</p>
						<p><a href="/'.$URI[1].'/'.$CUser->login.'">Перейти к профилю пользователя</a></p>';
						System::notification('Заблокирован пользователь '.$CUser->login.', автор блокировки пользователь '.$User->login, 'g');
					}
				}else{
					$return = '<p>Ошибка при сохранении настроек</p>';
					System::notification('Ошибка при сохранении конфигурации пользователя '.$CUser->login.' во время блокировки пользователем '.$User->login, 'r');
				}
			}else{
				$return = '<p>Ошибка при сохранении настроек</p>';
				System::notification('Ошибка при сохранении конфигурации пользователя '.$CUser->login.' во время блокировки пользователем '.$User->login.'. Провалена проверка безопасности.', 'r');
			}
		}else{
			$page->name = 'Ошибка';
			$return = '<p>Пользователь не найден</p>';
			$return.= '<p><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		$return = '<p><a href="/">Перейти на главную страницу</a></p>';
	}
	
}elseif($URI[2]=='ipban'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака IP пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			if(in_array($CUser->ip, $Config->ipBan)){
				$return.= '<p>Этот IP адрес уже заблокирован</p>';
				$return.= '<p><a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>';

			}else{
				$ticket = random(100);
				setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
				$return.= '<p>Подтвердите блокировку IP пользователя</p>';
				$return.= '<p><a href="/'.$URI[1].'/ipban2/'.$CUser->login.'/'.$ticket.'">Блокировать IP пользователя</a></p>';
				$return.= '<p><a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>';
			}
			
			
		}else{
			$page->name = 'Ошибка';
			$return = '<p>Пользователь не найден</p>';
				$return.= '<p><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		$return = '<p><a href="/">Перейти на главную страницу</a></p>';
	}
	
}elseif($URI[2]=='ipban2'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака IP пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			if(md5($URI[4].$Config->ticketSalt) == $_COOKIE['ticket'] && !in_array($CUser->ip, $Config->ipBan)){
				
				
				$Config->ipBan[] = $CUser->ip; 
			
				if(System::saveConfig($Config)){
					$return = '<p>IP адрес пользователя успешно заблокирован</p>
					<p><a href="/'.$URI[1].'/'.$CUser->login.'">Перейти к профилю пользователя</a></p>';
					System::notification('Заблокирован IP '.$CUser->ip.' пользователя '.$CUser->login.' пользователем '.$User->login, 'g');
				}else{
					$return = '<p>Ошибка при сохранении настроек</p>';
					System::notification('Ошибка при сохранении конфигурации системы', 'r');
				}


				
			}else{
				$return = '<p>Ошибка при сохранении настроек</p>';
				$return.= '<p><a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>';
				System::notification('Ошибка при блокировки IP пользователя '.$CUser->login.' во время блокировки пользователем '.$User->login.'. Провалена проверка безопасности.', 'r');
			}
			
		}else{
			$page->name = 'Ошибка';
			$return = '<p>Пользователь не найден</p>';
			$return.= '<p><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		$return = '<p><a href="/">Перейти на главную страницу</a></p>';
	}
	
}else{
	
	if($User->authorized){
		if(($CUser = User::getConfig($URI[2])) !== false){
			$page->clear();// Очистили страницу
			$page->title = 'Профиль пользователя';
			$page->name = $CUser->login;
			if(file_exists(DR.'/'.$Config->userAvatarDir.'/'.$CUser->login.'.jpg')){
				$avatar_file = '/'.$Config->userAvatarDir.'/'.$CUser->login.'.jpg?'.filemtime(DR.'/'.$Config->userAvatarDir.'/'.$CUser->login.'.jpg');
			}else{
				$avatar_file = '/modules/users/avatar.png';
			}

			$return = '
			<div class="user_profile">
				<p class="p_avatar"><img src="'.$avatar_file.'" alt="avatar" id="avatar"></p>
				<p class="p_info">Зарегистрирован: '.human_time(time() - $CUser->timeRegistration).' назад</p>
				<p class="p_info">Активность: '.human_time(time() - $CUser->timeActive).' назад</p>
				<p class="p_info">Оставлено '.$CUser->numPost.' '.numDec($CUser->numPost, array('сообщение', 'сообщения', 'сообщений')).'</p>';
				
				// Если забанен пользователь
				if ($CUser->timeBan > time()){
					$return.= '<p class="user_ban_info" style="color:red;">Пользователь заблокирован за нарушение правил сайта</p>';

				}

				if($User->preferences > 1 || $status == 'admin'){
					$return.= '<p class="p_link_ban_user"><a href="/'.$URI[1].'/ban/'.$CUser->login.'#user_ban_form">Блокировать пользователя</a></p>';
					$return.= '<p class="p_link_ban_ip"><a href="/'.$URI[1].'/ipban/'.$CUser->login.'">Блокировать IP пользователя</a></p>';
				}
				
				
			$return.= '</div>';
			
		}else{
			$page->clear();// Очистили страницу
			$page->title = 'Профиль пользователя';
			$page->name = 'Ошибка';
			
			$return = '<p>Пользователь не найден</p><p><a href="/">Перейти на главную страницу</a></p>';
				
		}
		
		
		
	}else{
		$page->clear();// Очистили страницу
		$page->title = 'Профиль пользователя';
		$page->name = 'Ошибка';
		
		$return = '<p>Только зарегистрированные пользователи могут просматривать профили других пользователей. Пожалуйста <a href="/'.$URI[1].'/reg">зарегистрируйтесь</a> или <a href="/'.$URI[1].'">войдите</a> в систему.</p>';
	}
}
return $return;
?>