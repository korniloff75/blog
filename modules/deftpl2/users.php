<?php	
if(!isset($URI[2])){
	if($User->authorized){
		$page->clear();// Очистили страницу
		$page->title = 'Профиль пользователя';
		$page->name = 'Привет '.$User->login;
		
		echo '<div class="user_profile_">
		<div class="user_avatar">';
		if(file_exists('./modules/users/images/'.$User->login.'.jpg')){
		echo '<img src="/modules/users/images/'.$User->login.'.jpg" alt="">';
		}else{
		echo '<img src="/modules/'.$Config->template.'/images/user.png" alt="">';	
		}
		echo '</div>
		<div class="user_profile">
			<p>Зарегистрирован '.human_time(time() - $User->timeRegistration).' назад</p>
			<p>Оставлено '.$User->numPost.' '.numDec($User->numPost, array('сообщение', 'сообщения', 'сообщений')).'</p>			
			</div>
		</div>
			<p class="userlink"><a href="/'.$URI[1].'/cfg">Настройки профиля</a></p>
		    <p class="userlink"><a href="/'.$URI[1].'/exit">Выход из профиля</a></p>		
		';
	}else{
		
		//$page->clear();// Очистили страницу
		$page->title = 'Вход';
		$page->name = 'Вход';
		
		echo '<form name="forma" action="/'.$URI[1].'/in" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="add1">
		<div class="user_auth_form_">
		<div class="userform">
			<div class="login">Логин<br><input type="text" name="login" value="" required ></div>
			<div class="password">Пароль<br><input type="password" name="password" value="" required ></div>
			<div class="captcha_"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;"></div>
			<div class="simvoly">Введите символы с картинки<br><input type="text" name="captcha" value="" size="10" required ></div>
			<div style="font-size:12px; opacity: 0.7;clear:both;">Для обновления символов нажмите на картинку</div>
			<div class="submit"><input type="submit" name="" value="Отправить"></div>
            </div>
		<p class="userlink"><a href="/'.$URI[1].'/reg">Зарегистрироваться</a></p>
        <p class="userlink"><a href="/'.$URI[1].'/changepassword">Не помню пароль</a></p>		
		</div>
		</form>';
	}
	
	
	
}elseif($URI[2]=='cfg'){
	
	if($User->authorized){
		$page->clear();// Очистили страницу
		$page->title = 'Профиль пользователя';
		$page->name = 'Настройки';
		if(md5($_POST['ticket'].$Config->ticketSalt) == $_COOKIE['ticket']){
			
			echo '<p class="msg-success">Настройки успешно сохранены.</p> 
			<p class="userlink"><a href="/'.$URI[1].'">Вернуться к профилю</a></p>';

			$listEmailsUsers = System::listEmailsUsers();
			if(($key = array_search($User->email, $listEmailsUsers)) !== false){
				unset($listEmailsUsers[$key]); // Удалили найденый элемент массива
			}

			if ($_POST['password'] != ''){
				$salt = random(255);
				$User->salt = $salt;
				$User->password = cipherPass($_POST['password'], $salt);
				$User->changePasswordChecksum = random(100);
				System::notification('Пользователь '.$User->login.' изменил свой пароль', 'g');
				setcookie('user_password',cipherPass($_POST['password'], $User->salt),time()+32000000,'/');
			}
			
			if($Config->userEmailChange && $User->email != $_POST['email']){
				if($_POST['email'] == ''){
					$page->name = 'Ошибка';
					echo '<p class="msg-error">Email не введен.</p> 
					<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false){
					$page->name = 'Ошибка';
					echo '<p class="msg-error">Email введен некорректно.</p> 
					<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}elseif(in_array(strtolower($_POST['email']), $listEmailsUsers)){
					$page->name = 'Ошибка';
					echo '<p class="msg-error">Пользователь с таким email уже существует.</p> 
					<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}elseif(System::isBadMailDomain($_POST['email']) && $Config->userEmailFilterList){
					$page->name = 'Ошибка';
					echo '<p class="msg-error">Такой email иметь запрещено.</p> 
					<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
				}else{

					System::notification('Пользователь '.$User->login.' изменил свой email '.$User->email.' на '.$_POST['email'].'', 'g');

					// Обновление емайла
					$User->email = htmlspecialchars(substr(strtolower($_POST['email']), 0, 255));
					$User->emailChecked = 0;
					$User->emailChecksum = random(16);
					
					$listEmailsUsers[] = $User->email;
					System::updateListEmailsUsers($listEmailsUsers);
					if ($Config->userEmailChecked){
						echo '<p class="msg-error">Авторизация была сброшена, т.к. необходимо подтвердить новый email.,/p> 
						<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Подтвердить новый email</a></p>';

					}
				}
			}

			$User->save();
			
			
		}else{
			$ticket = random(255);
			setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
			echo '<form name="forma" action="/'.$URI[1].'/cfg" method="post">
			<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
			<div class="user_cfg_form_">
			    <div class="userform">';
				if ($Config->userEmailChange) echo '<div class="email">Email<br><input type="text" name="email" value="'.$User->email.'" placeholder="Обязательно для заполнения"></div>';
				echo '<div class="password">Пароль<br><input type="password" name="password" value="" placeholder="Оставьте пустым что-бы не менять"></div>
				<div class="submit"><input type="submit" name="" value="Сохранить"></div>
				</div>
			</div>
			</form>
			<form style="padding:2px 0;" name="upload" action="/'.$URI[1].'/upload2" enctype="multipart/form-data" method="POST">
			<div class="user_cfg_form_">
			<div class="userform">
            <h3>Загрузка аватара</h3>
			<p><input type="file" name="userfile"></p>
			<button type="submit">Загрузить аватар</button>
			<p style="font-size:13px;color:#bbb;line-height:1.2;">(Размер аватара не более<br>200 х 200 пикселей)</p>
            </div>
			<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>
			</div>
			</form>		
			
			';
		}
		
		
		
	}else{
		$page->clear();// Очистили страницу
		$page->title = 'Перенаправление';
		$page->name = 'Перенаправление';
		echo '<p class="msg">Перенаправление на страницу авторизации</p>
		<script type="text/javascript">
			setTimeout("window.location.href = \"/'.$URI[1].'\";", 1000);
		</script>';
	}
	
}elseif($URI[2]=='upload2'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Загрузка аватара';
	$page->name = 'Загрузка аватара';
	
	
	
	
$uploaddir = 'modules/users/images/';
$apend = $User->login.'.jpg'; 
$uploadfile = "$uploaddir$apend"; 

if($_FILES['userfile']['size'] == 0){
echo '<p class="msg-error">Выберите файл</p>
<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';	
}else{ 
if($_FILES['userfile']['type'] == 'image/gif' || $_FILES['userfile']['type'] == 'image/jpeg' || $_FILES['userfile']['type'] == 'image/png'){ 
  
  if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)){ 

   $size = getimagesize($uploadfile); 

     if ($size[0] <= 200 && $size[1] <= 200) 
     { 

     echo '<p class="msg-success">Файл загружен</p>
	 <p class="userlink"><a href="/'.$URI[1].'">Вернуться в профиль</a></p>'; 
     } else {
    echo'<p class="msg-error">Загружаемое изображение превышает допустимые размеры</p>
<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>'; 
     unlink($uploadfile); 

     } 
   }else{
   echo'<p class="msg-error">Файл не загружен, вернитеcь и попробуйте еще раз</p>
<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
   } 
} else { 
echo '<p class="msg-error">Файлы с таким расширением загружать запрещено!</p>
<p class="userlink"><a href="/'.$URI[1].'/cfg">Вернуться назад</a></p>';
} 	
} 	
	
	
	
}elseif($URI[2]=='in'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Вход';
	
	if(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Цифры с картинки введены неверно.</p>  
		<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
	}else{
		if(($CUser = User::getConfig($_POST['login'])) !== false){
			$AUser = new User($_POST['login'], cipherPass($_POST['password'], $CUser->salt));
			if($AUser->authorized){
				
				if($Config->userEmailChecked && $AUser->emailChecked || !$Config->userEmailChecked){
					$page->name = 'Вход';
					echo '<p class="msg-success">Вы успешно авторизованны</p>
					<p class="userlink"><a href="/'.$URI[1].'">Перейти к профилю</a> <a href="/">Вернуться на главную страницу</a></p>';
					System::notification('Выполнена авторизация пользователя '.$AUser->login, 'g');
				}else{
					$page->name = 'Ошибка';
					echo '<p class="msg-error">Вы еще не подтвердили свой email</p>
					<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Отослать письмо с подтверждением еще раз</a> <a href="/">Вернуться на главную страницу</a></p>';
					System::notification('Выполнена авторизация пользователя '.$AUser->login.', но из за неподтвержденного email авторизация была сброшена.', 'r');
				}
				setcookie('user_login',$_POST['login'],time()+32000000,'/');
				setcookie('user_password',cipherPass($_POST['password'], $CUser->salt),time()+32000000,'/');
				
			}else{
				$page->name = 'Ошибка';
				echo '<p class="msg-error">Пароль введен неверно.</p> 
				<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
				System::notification('Ошибка при авторизации пользователя '.$_POST['login'].', введен ошибочный пароль', 'r');
			}
		}else{
			$page->name = 'Ошибка';
			echo '<p class="msg-error">Логин введен неверно.</p> 
			<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
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
			echo '<form name="forma" action="/'.$URI[1].'/emailchecked2" method="post">
			<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
			<div class="user_cfg_form_">
				<p>Введите данные от своего аккаунта и мы отправим вам ссылку для подтверждения email адреса.</p>
				<div class="userform">
			    <div class="login">Логин<br><input type="text" name="login" value="" required ></div>
			    <div class="email">Email<br><input type="text" name="email" value="" required ></div>
			    <div class="captcha_"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;"></div>
			    <div class="simvoly">Введите символы с картинки<br><input type="text" name="captcha" value="" size="10" required ></div>
			    <div style="font-size:12px; opacity: 0.7;clear:both;">Для обновления символов нажмите на картинку</div>
			    <div class="submit"><input type="submit" name="" value="Отправить"></div>
                </div>
				<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>	
			</div>
			</form>';
	
	
}elseif($URI[2]=='emailchecked2' && $Config->userEmailChecked){
	
	$page->clear();// Очистили страницу
	$page->title = 'Подтверждение email адреса';
	$page->name = 'Подтверждение email адреса';
	
	if(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Цифры с картинки введены неверно</p>
		<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}elseif (md5($_POST['ticket'].$Config->ticketSalt) != $_COOKIE['ticket']){
		echo '<p class="msg-error">Ошибка безопасности</p>
		<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}elseif (($CUser = User::getConfig($_POST['login'])) !== false){
		
		if($CUser->emailChecked){
			echo '<p class="msg-error">У этого аккаунта email уже подтвержден</p>
			<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
		}elseif ($CUser->email == $_POST['email']){
			$CUser->emailChecksum = random(16);
			User::setConfig($CUser->login, $CUser);
			echo '<p class="msg-report">На указанный email выслана ссылка по которой нужно перейти</p>
			<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
			$txt = "Для подтверждения вашего email перейдите по ссылке ниже\n\n\n";
			$txt.= "http://".SERVER."/".$URI[1]."/echeck1/".$CUser->login."/".$CUser->emailChecksum;
			addmail($CUser->email, "Ссылка для подтверждения email", $txt, $Config->adminEmail);
			System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для подтверждения email', 'g');

		}else{
			echo '<p class="msg-error">Указанный email отличается от того, на который был зарегистрирован этот аккаунт</p>
			<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
		}
	}else{
		echo '<p class="msg-error">Пользователь с таким логином не найден</p>
		<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}
	setcookie('captcha','','0','/');
	
}elseif($URI[2]=='changepassword'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Восстановление доступа';
	$page->name = 'Восстановление доступа';
	
	$ticket = random(255);
	setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
	if($Config->userNewPassword){
		echo '<form name="forma" action="/'.$URI[1].'/changepassword2" method="post">
		<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
		<div class="user_cfg_form_">
			<p>Все пароли на нашем сайте хранятся в зашифрованном виде, мы не сможем его расшифровать и выслать вам. 
			Однако мы можем сгенерировать для вас новый пароль и выслать его вам на email, который был указан в вашем аккаунте.</p> 
			<p>Введите данные от своего аккаунта и мы отправим вам ссылку для генерации нового пароля.</p>		
			<div class="userform">
			<div class="login">Логин<br><input type="text" name="login" value="" required ></div>
			<div class="email">Email<br><input type="text" name="email" value="" required ></div>
			<div class="captcha_"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;"></div>
			<div class="simvoly">Введите символы с картинки<br><input type="text" name="captcha" value="" size="10" required ></div>
			<div style="font-size:12px; opacity: 0.7;clear:both;">Для обновления символов нажмите на картинку</div>
			<div class="submit"><input type="submit" name="" value="Отправить"></div>
            </div>
			<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>	
		</div>
		</form>';
	}else{
		echo '<p class="msg-report">К сожалению, в данное время мы не можем восcтановить ваш пароль</p>
		<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
	}
		
	
	
}elseif($URI[2]=='changepassword2' && $Config->userNewPassword){
	
	$page->clear();// Очистили страницу
	$page->title = 'Восстановление доступа';
	$page->name = 'Восстановление доступа';
	
	if(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Цифры с картинки введены неверно</p>
		<p class="userlink"><a href="/'.$URI[1].'/changepassword">Вернуться назад</a></p>';
	}elseif (md5($_POST['ticket'].$Config->ticketSalt) != $_COOKIE['ticket']){
		echo '<p class="msg-error">Ошибка безопасности</p>
		<p class="userlink"><a href="/'.$URI[1].'/changepassword">Вернуться назад</a></p>';
	}elseif (($CUser = User::getConfig($_POST['login'])) !== false){
		if ($CUser->email == $_POST['email']){
			$CUser->changePasswordChecksum = random(100);
			User::setConfig($CUser->login, $CUser);

			echo '<p class="msg-report">На указанный email выслана ссылка по которой нужно перейти</p>
			<p class="userlink"><a href="/'.$URI[1].'">Вернуться назад</a></p>';
			$txt = "На сайте ".SERVER." был сделан запрос на генерацию нового пароля от аккаунта ".$CUser->login.".\n\n\n";
			$txt.= "Для генерации нового пароля перейдите по ссылке ниже\n";
			$txt.= "Если вы не запрашивали генерацию нового пароля, то просто проигнорируйте это письмо. Если подобные письма приходять слишком часто, то обратитесь к администратору сайта.\n\n\n";
			$txt.= "http://".SERVER."/".$URI[1]."/npcheck/".$CUser->login."/".$CUser->changePasswordChecksum;
			addmail($CUser->email, "Ссылка для генерации нового пароля", $txt, $Config->adminEmail);
			System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для генерации нового пароля', 'g');

		}else{
			echo '<p class="msg-error">Указанный email отличается от того, на который был зарегистрирован этот аккаунт</p>
			<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
		}
	}else{
		echo '<p class="msg-error">Пользователь с таким логином не найден</p>
		<p class="userlink"><a href="/'.$URI[1].'/emailchecked">Вернуться назад</a></p>';
	}
	setcookie('captcha','','0','/');
	
}elseif($URI[2]=='exit'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Выход';
	$page->name = 'Выход';
	
	echo '<p class="msg-success">Вы успешно вышли из системы.</p> 
	<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
	setcookie('user_login','','0','/');
	setcookie('user_password','','0','/');
	
	if($User->authorized) System::notification('Выполнена деавторизация пользователя '.$User->login, 'g');
	
}elseif($URI[2]=='reg'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Регистрация';
	$page->name = 'Регистрация';
	if($Config->registration){
		echo '<form name="forma" action="/'.$URI[1].'/addreg1" method="post" onsubmit="if(document.getElementById(\'roscomnadzor\').checked){this.submit();}else{alert(\'Нужно дать согласие на обработку персональных данных\'); return false;}">
		<INPUT TYPE="hidden" NAME="act" VALUE="add1">
		<div class="user_reg_form_">
			<p>Внимание! Логин может содержать только символы латинского алфавита и цифры.</p>
		    <div class="userform">
			<div class="login">Логин<br><input type="text" name="login" value="" required ></div>
			<div class="password">Пароль<br><input type="password" name="password" value="" required ></div>
			<div class="email">Email<br><input type="text" name="email" value="" required ></div>
			<div class="captcha_"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;"></div>
			<div class="simvoly">Введите символы с картинки<br><input type="text" name="captcha" value="" size="10" required ></div>
			<div style="font-size:12px; opacity: 0.7;clear:both;">Для обновления символов нажмите на картинку</div>
			<div class="roscomnadzor"><input type="checkbox" name="roscomnadzor" value="ok" id="roscomnadzor"> <label for="roscomnadzor"> Я согласен на <a href="/fz152" target="_blank">обработку моих персональных данных</a></label></div>
			<div class="submit"><input type="submit" name="" value="Зарегистрироваться"></div>
            </div>			
		</div>
		</form>';
	}else{
		echo '<p class="msg">Регистрация на сайте временно приостановлена</p>';
	}
	
	
	
}elseif($URI[2]=='addreg1'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Регистрация';
	
	if(!$Config->registration){
		$page->name = 'Ошибка';
		echo '<p class="msg">Регистрация на сайте временно приостановлена</p>';
	}elseif(in_array(IP, $Config->ipBan)){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Регистрация с вашего ip временно приостановлена.</p> 
		<p class="userlink"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Цифры с картинки введены неверно.</p> 
		<p class="userlink"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Введенный емейл некорректен</p>
		<p class="userlink"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(in_array(strtolower($_POST['email']), System::listEmailsUsers())){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Пользователь с таким email уже существует.</p> 
		<p class="userlink"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}elseif(System::isBadMailDomain($_POST['email']) && $Config->userEmailFilterList){
		$page->name = 'Ошибка';
		echo '<p class="msg-error">Такой email иметь запрещено.</p> 
		<p class="userlink"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
	}else{
		$salt = random(255);
		if(($ers = User::registration($_POST['login'], $_POST['password'], $salt, $_POST['email'])) == 0){
			$page->name = 'Регистрация';
			if($Config->userEmailChecked){
				$CUser = User::getConfig($_POST['login']);
				echo '<p class="msg-report">Для завершения регистрации подтвердите ваш email, для этого необходимо перейти по ссылке которую мы отправили вам на email.</p>
				<p class="userlink"><a href="/'.$URI[1].'">Перейти к профилю</a></p>';
				$txt = "Для подтверждения вашего email перейдите по ссылке ниже\n\n\n";
				$txt.= "http://".SERVER."/".$URI[1]."/echeck1/".$CUser->login."/".$CUser->emailChecksum;
				addmail($CUser->email, "Ссылка для подтверждения email", $txt, $Config->adminEmail);
				System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для подтверждения email', 'g');
			}else{
				echo '<p class="msg-success">Вы успешно зарегистрированы.</p> 
				<p class="userlink"><a href="/'.$URI[1].'">Перейти к профилю</a>';
			}
			setcookie('user_login',$_POST['login'],time()+32000000,'/');
			setcookie('user_password',cipherPass($_POST['password'], $salt),time()+32000000,'/');
			System::notification('Зарегистрирован пользователь '.$_POST['login'].'', 'g');
		}else{
			$page->name = 'Ошибка';
			$errmsg = 'Неизвестная ошибка';
			if ($ers == 1){$errmsg = 'Пользователь с похожим логином уже существует';}
			if ($ers == 2){$errmsg = 'Ошибка при сохранении параметров';}
			if ($ers == 3){$errmsg = 'Не все поля формы заполнены';}
			if ($ers == 4){$errmsg = 'Логин содержит недопустимые символы';}
			if ($ers == 5){$errmsg = 'Слишком длинный логин, пароль или емайл';}
			System::notification('Ошибка при регистрации пользователя '.$_POST['login'].' - '.$errmsg, 'r');
			echo '<p class="msg-error">'.$errmsg.'</p><p class="userlink"><a href="/'.$URI[1].'/reg">Вернуться назад</a></p>';
		}
		
	}
	
	setcookie('captcha','','0','/');
	
}elseif($URI[2]=='echeck1' && $Config->userEmailChecked){
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
				

				echo '<p class="msg-success">Ваш email адрес успешно подтвержден</p>
				<p class="userlink"><a href="/'.$URI[1].'">Перейти к профилю</a></p>';
				System::notification('Подтвержден email '.$CUser->email.' пользователя '.$CUser->login, 'g');
			}else{
				echo '<p class="msg-error">Ошибка записи настроек, обратитесь к администратору.</p>
				<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
				System::notification('Ошибка записи настроек при подтверждении емайла '.$CUser->email.' пользователя '.$CUser->login, 'r');
			}
		}else{
			echo '<p class="msg-error">Ошибка контрольной суммы. Возможно вы допустили ошибку при копировании ссылки или email уже был подтвержден ранее.</p>
				<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
		}
	}else{ 
		echo '<p class="msg-error">Не подтверждено. Возможно вы допустили ошибку при копировании ссылки или email уже был подтвержден ранее.</p>
				<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
	}
	
	
}elseif($URI[2]=='npcheck' && $Config->userNewPassword){
	$page->clear();// Очистили страницу
	$page->title = 'Восстановление доступа';
	$page->name = 'Восстановление доступа';
	if(($CUser = User::getConfig($URI[3])) !== false){
		if($CUser->changePasswordChecksum != '' && $CUser->changePasswordChecksum == $URI[4]){
			$changePassword = random(rand(5, 15));
			$CUser->password = cipherPass($changePassword, $CUser->salt);
			$CUser->changepasswordChecksum = '';

			$txt = "Ваш новый пароль от аккаунта ".$CUser->login.": ".$changePassword."\n\n\n";
			$txt.= "Сайт: ".SERVER;
			addmail($CUser->email, "Ваш новый пароль", $txt, $Config->adminEmail);
			System::notification('На email пользователя '.$CUser->login.' отправлена ссылка для генерации нового пароля', 'g');

			if(User::setConfig($CUser->login, $CUser)){

				echo '<p class="msg-success">Ваш новый пароль успешно сгенерирован и был выслан вам на email</p>
				<p class="userlink"><a href="/'.$URI[1].'">Перейти к профилю</a></p>';
				System::notification('Сгенерирован новый пароль для пользователя '.$CUser->login, 'g');
			}else{
				echo '<p class="msg-error">Ошибка записи настроек, обратитесь к администратору.</p>
				<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
				System::notification('Ошибка записи настроек при генерировании нового пароля для пользователя '.$CUser->login, 'r');
			}
		}else{
			echo '<p class="msg-error">Ошибка контрольной суммы. Возможно вы допустили ошибку при копировании ссылки.</p>'.$CUser->changePasswordChecksum.'
				<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
		}
	}else{ 
		echo '<p class="msg-error">Ошибка контрольной суммы. Возможно вы допустили ошибку при копировании ссылки.</p>
				<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
	}
	
	
}elseif($URI[2]=='ban'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			$ticket = random(255);
			setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
			
			echo '<div class="user_ban_form">
			<form name="forma" action="/'.$URI[1].'/addban/'.$URI[3].'" method="post">
			<INPUT TYPE="hidden" NAME="ticket" VALUE="'.$ticket.'">
			<div id="user_ban_form">
			     <div class="contact-form">
                 <label> <span>Причина блокировки:</span>
                 <textarea class="message" name="cause">'.$CUser->causeBan.'</textarea>
                 </label>
			     <label> <span>На какое время:</span>
                 <select class="select" name="time">
						<option value="0">Не блокировать
						<option value="3600" selected>1 час
						<option value="86400">1 день
						<option value="259200">3 дня
						<option value="604800">Неделя
						<option value="2628000">Месяц
				 </select>
                 </label>
			     <button type="submit" name="">Отправить</button>
		         </div>	
			
			</form>
			<p class="userlink"><a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>
			</div>';
			
		}else{
			$page->name = 'Ошибка';
			echo '<p class="msg-error">Пользователь не найден.</p>
			<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		echo '<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
	}
	
}elseif($URI[2]=='addban'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			if(md5($_POST['ticket'].$Config->ticketSalt) == $_COOKIE['ticket']){
				
				$CUser->causeBan = htmlspecialchars($_POST['cause']);// Причина 
				$CUser->timeBan =  time() + (is_numeric($_POST['time'])?(int)$_POST['time']:0);// Время
				
				if(User::setConfig($CUser->login, $CUser)){
					echo '<p class="msg-success">Пользователь успешно заблокирован</p>
					<p class="userlink"><a href="/'.$URI[1].'/'.$CUser->login.'">Перейти к профилю пользователя</a></p>';
					System::notification('Заблокирован пользователь '.$CUser->login.', автор блокировки пользователь '.$User->login, 'g');
				}else{
					echo '<p class="msg-error">Ошибка при сохранении настроек1</p>';
					System::notification('Ошибка при сохранении конфигурации пользователя '.$CUser->login.' во время блокировки пользователем '.$User->login, 'r');
				}
				
			}else{
				echo '<p class="msg-error">Ошибка при сохранении настроек2</p>';
				System::notification('Ошибка при сохранении конфигурации пользователя '.$CUser->login.' во время блокировки пользователем '.$User->login.'. Провалена проверка безопасности.', 'r');
			}
			
		}else{
			$page->name = 'Ошибка';
			echo '<p class="msg-error">Пользователь не найден.</p> 
			<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		echo '<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
	}
	
}elseif($URI[2]=='ipban'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака IP пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			if(in_array($CUser->ip, $Config->ipBan)){
				echo '<p class="msg">Этот IP адрес уже заблокирован</p>';
				echo '<p class="userlink"><a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>';

			}else{
				$ticket = random(100);
				setcookie('ticket',md5($ticket.$Config->ticketSalt),0,'/');
				echo '<p class="msg-report">Подтвердите блокировку IP пользователя</p>';
				echo '<p class="userlink"><a href="/'.$URI[1].'/ipban21/'.$CUser->login.'/'.$ticket.'">Блокировать IP пользователя</a> <a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>';
				
			}
			
			
		}else{
			$page->name = 'Ошибка';
			echo '<p class="msg-error">Пользователь не найден.</p> 
			<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		echo '<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
	}
	
}elseif($URI[2]=='ipban21'){
	
	$page->clear();// Очистили страницу
	$page->title = 'Блокировака IP пользователя';
	
	if($User->authorized && $User->preferences > 1 || $status == 'admin'){
		if(($CUser = User::getConfig($URI[3])) !== false){
			
			$page->name = $CUser->login;
			
			if(md5($URI[4].$Config->ticketSalt) == $_COOKIE['ticket'] && !in_array($CUser->ip, $Config->ipBan)){
				
				
				$Config->ipBan[] = $CUser->ip; 
			
				if(System::saveConfig($Config)){
					echo '<p class="msg-success">IP адрес пользователя успешно заблокирован</p>
					<p class="userlink"><a href="/'.$URI[1].'/'.$CUser->login.'">Перейти к профилю пользователя</a></p>';
					System::notification('Заблокирован IP '.$CUser->ip.' пользователя '.$CUser->login.' пользователем '.$User->login, 'g');
				}else{
					echo '<p class="msg-error">Ошибка при сохранении настроек3</p>';
					System::notification('Ошибка при сохранении конфигурации системы', 'r');
				}


				
			}else{
				echo '<p class="msg-error">Ошибка при сохранении настроек4</p>';
				echo '<p class="userlink"><a href="/'.$URI[1].'/'.$CUser->login.'">Вернуться к профилю пользователя</a></p>';
				System::notification('Ошибка при блокировки IP пользователя '.$CUser->login.' во время блокировки пользователем '.$User->login.'. Провалена проверка безопасности.', 'r');
			}
			
		}else{
			$page->name = 'Ошибка';
			echo '<p class="msg-error">Пользователь не найден.</p> 
			<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';	
		}
	}else{
		$page->name = 'Ошибка';
		echo '<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
	}
	
}else{
	
	if($User->authorized){
		if(($CUser = User::getConfig($URI[2])) !== false){
			$page->clear();// Очистили страницу
			$page->title = 'Профиль пользователя';
			$page->name = $CUser->login;
			

			echo '<div class="user_profile_">
		    <div class="user_avatar">';
		    if(file_exists('./modules/users/images/'.$CUser->login.'.jpg')){
		    echo '<img src="/modules/users/images/'.$CUser->login.'.jpg" alt="">';
		    }else{
		    echo '<img src="/modules/'.$Config->template.'/images/user.png" alt="">';	
		    }
		    echo'</div>
		    <div class="user_profile">
				<p>Зарегистрирован: '.human_time(time() - $CUser->timeRegistration).' назад</p>
				<p>Активность: '.human_time(time() - $CUser->timeActive).' назад</p>
				<p>Оставлено '.$CUser->numPost.' '.numDec($CUser->numPost, array('сообщение', 'сообщения', 'сообщений')).'</p>
		    </div>
			</div>';	
				// Если забанен пользователь
				if ($CUser->timeBan > time()){
					echo '<p class="msg-error">Пользователь заблокирован за нарушение правил сайта</p>';

				}

				if($User->preferences > 1 || $status == 'admin'){
					echo '<p class="userlink"><a href="/'.$URI[1].'/ban/'.$CUser->login.'#user_ban_form">Блокировать пользователя</a></p>
					<p class="userlink"><a href="/'.$URI[1].'/ipban/'.$CUser->login.'">Блокировать IP пользователя</a></p>';
				}
						
		}else{
			$page->clear();// Очистили страницу
			$page->title = 'Профиль пользователя';
			$page->name = 'Ошибка';
			
			echo '<p class="msg-error">Пользователь не найден.</p> 
			<p class="userlink"><a href="/">Перейти на главную страницу</a></p>';
				
		}
		
		
		
	}else{
		$page->clear();// Очистили страницу
		$page->title = 'Профиль пользователя';
		$page->name = 'Ошибка';
		
		echo '<p class="msg-report">Только зарегистрированные пользователи<br>могут просматривать профили других пользователей.</p> 
		<p class="userlink"><a href="/'.$URI[1].'/reg">Зарегистрироваться</a> <a href="/'.$URI[1].'">Авторизоваться</a></p>';
	}
}

?>