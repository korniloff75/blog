<?php
require('../system/global.dat');
require('./include/start.dat');
if($status=='admin'){

?>
<script type="text/javascript">
function dell(login){
return '<div class="a">Подтвердите удаление пользователя <i>' + login + '</i></div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'users.php?act=dell&amp;login='+login+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
function ipdell(ip){
return '<div class="a">Подтвердите удаление IP <i>' + ip + '</i></div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'users.php?act=ipdell&amp;ip='+ip+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php
$menu_page = '<div class="menu_page">
			<a class="link" href="users.php?act=adduser">Зарегистрировать пользователя</a>
			<a class="link" href="users.php?act=ip">Заблокированные IP</a>
			<a class="link" href="users.php?act=dellold">Удаление неактивных</a>
			<a class="link" href="users.php?act=cfg">Настройки пользователей</a>
		</div>';

	if($act=='index'){
		echo'<div class="header">
			<h1>Управление пользователями</h1>
		</div>
		'.$menu_page.'
		<div class="content">';
		
		$users = System::listUsers();
		$users = array_reverse($users);//перевернули масив для вывода в обратном порядке 
		$nom = count($users);
		
		if($nom == 0){
			echo'<div class="msg">Нет зарегистрированных пользователей</div>';
		}else{
			
			
			echo'
			<div class="row">
				<form action="users.php?act=search" method="post">
				<input style="width: 250px;" type="text" name="q" value="" placeholder="Поиск пользователей">
				<input type="submit" name="" value="Поиск"> 
				</form>
			</div>
			<table class="tables">
			<tr>
				<td class="tables_head" colspan="2">Логин</td>
				<td class="tables_head" style="text-align: center;">Сообщений</td>
				<td class="tables_head" style="text-align: center;">Активность</td>
				<td class="tables_head" style="text-align: center;">Регистрация</td>
				<td class="tables_head" style="text-align: center;">Почтовый ящик</td>
				<td class="tables_head" style="text-align: right;">&nbsp;</td>
			</tr>';
			
			//определили количество страниц
			$navigation = 50;
			$kol_page = ceil($nom / $navigation); 
			
			//проверка правильности переменной с номером страницы
			if(isset($_GET['nom_page'])){$nom_page = $_GET['nom_page'];}else{ $nom_page = 1; }
			if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $kol_page){ $nom_page = 1; }
			
			//начало навигации
			$i = ($nom_page - 1) * $navigation;
			$var = $i + $navigation;
			
			while($i < $var){
				if($i < $nom){
					if(($CUser = User::getConfig($users[$i])) != false){
						
						echo'<tr>
							<td class="img"><img src="include/user.svg" alt=""></td>
							<td><a href="users.php?act=user&amp;login='.$users[$i].'&amp;nom_page='.$nom_page.'">'.$users[$i].'</a>'.($CUser->timeBan > time()?' (<span class="r">бан</span>)':'').'</td>
							<td style="text-align: center;">'.$CUser->numPost.'</td>
							<td style="text-align: center;">'.human_time(time() - $CUser->timeActive).' назад</td>
							
							<td style="text-align: center;">'.date("d.m.Y H:i", $CUser->timeRegistration).'</td>
							<td style="text-align: center;">'.($CUser->emailChecked?'<span class="g">':'<span class="r">').$CUser->email.'</span></td>
							
							<td><a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\''.$users[$i].'\'));">Удалить</a></td>
						</tr>';
					}else{
						echo'<tr>
							<td>&nbsp;</td>
							<td style="color: red;">Error</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>';
					}
				}
				++$i;
			}
			echo'</table>';
			
			//навигация по номерам страниц
			if($kol_page > 1){//Если количество страниц больше 1, то показываем навигацию
				echo'<div style="margin-top: 25px;">';
				echo'Страницы: ';
				
				for($i = 1; $i <= $kol_page; ++$i){
					if($nom_page == $i){
						echo'<b>('.$i.')</b> ';
					}else{
						echo'<a href="?nom_page='.$i.'">'.$i.'</a> ';
					}
				}
				echo'</div>';
			}
			//конец навигации
		}
			
		echo'</div>';
	}
	
	if($act=='cfg')
	{
		echo'<div class="header">
			<h1>Настройка пользователей</h1>
		</div>
		<div class="menu_page"><a href="users.php?">&#8592; Вернуться назад</a></div>
		<div class="content">
		
		<form name="settingform" action="users.php?act=addcfg" method="post">
		
		<table class="tblform">
		<tr>
			<td>Регистрация пользователей:</td>
			<td>
				<select name="registration">
					<option value="1" '.($Config->registration == '1'?'selected':'').'>Разрешить регистрироваться новым пользователям
					<option value="0" '.($Config->registration == '0'?'selected':'').'>Запретить регистрироваться новым пользователем
				</select>
			</td>
		</tr>


		<tr>
			<td>Подтверждение email адресов:</td>
			<td>
				<select name="userEmailChecked">
					<option value="0" '.($Config->userEmailChecked == '0'?'selected':'').'>Выключить подтверждение email адресов
					<option value="1" '.($Config->userEmailChecked == '1'?'selected':'').'>Включить подтверждение email адресов
				</select>
			</td>
		</tr>
		<tr>
			<td>Изменение email пользователями:</td>
			<td>
				<select name="userEmailChange">
					<option value="0" '.($Config->userEmailChange == '0'?'selected':'').'>Запретить изменение email адресов
					<option value="1" '.($Config->userEmailChange == '1'?'selected':'').'>Разрешить изменение email адресов
				</select>
			</td>
		</tr>
		<tr>
			<td>Использование быстрых email:</td>
			<td>
				<select name="userEmailFilterList">
					<option value="1" '.($Config->userEmailFilterList == '1'?'selected':'').'>Запретить использование сервисов быстрых email
					<option value="0" '.($Config->userEmailFilterList == '0'?'selected':'').'>Разрешить использование сервисов быстрых email
				</select>
			</td>
		</tr>
		
		<tr>
			<td>Восстановление доступа:</td>
			<td>
				<select name="userNewPassword">
					<option value="1" '.($Config->userNewPassword == '1'?'selected':'').'>Разрешить восстанавливать забытый пароль
					<option value="0" '.($Config->userNewPassword == '0'?'selected':'').'>Запретить восстанавливать забытый пароль
				</select>
			</td>
		</tr>

		<tr>
			<td>Привила при блокировки IP:</td>
			<td>
				<select name="ipBanRule">
					<option value="0" '.($Config->ipBanRule == '0'?'selected':'').'>Запрет активности, сайт доступен только для просмотра
					<option value="1" '.($Config->ipBanRule == '1'?'selected':'').'>Полный запрет доступа к сайту
				</select>
			</td>
		</tr>

		<tr>
			<td>Загрузка аватаров пользователетями:</td>
			<td>
				<select name="userAvatarUpload">
					<option value="0" '.($Config->userAvatarUpload == '0'?'selected':'').'>Запретить загружать аватары
					<option value="1" '.($Config->userAvatarUpload == '1'?'selected':'').'>Разрешить загружать аватары
				</select>
			</td>
		</tr>
		<tr>
			<td>Максимальный размер аватара в килобайтах:</td>
			<td>
				<input type="text" name="userAvatarSize" value="'.$Config->userAvatarSize.'">
			</td>
		</tr>

		<tr>
			<td>Папка для загрузки аватаров пользователей:</td>
			<td>
				<input type="text" name="userAvatarDir" value="'.$Config->userAvatarDir.'">
				<br><span class="comment">У папки должны быть права разрешающие запись</span>
			</td>
		</tr>
		<tr>
			<td>Количество сообщений для загрузки аватара:</td>
			<td>
				<input type="text" name="userAvatarMinNumPost" value="'.$Config->userAvatarMinNumPost.'">
				<br><span class="comment">Какое минимально количество сообщений должен оставить пользователь на сайте, чтобы у него появилась возможность загружать аватары.</span>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="" value="Сохранить"></td>
		</tr>
		</table>
		</form>
		</div>
			';

	}

	if($act=='addcfg')
	{
		$Config->registration = (int) htmlspecialchars(specfilter($_POST['registration']));
		$Config->userEmailChecked = (int) htmlspecialchars(specfilter($_POST['userEmailChecked']));
		$Config->userEmailChange = (int) htmlspecialchars(specfilter($_POST['userEmailChange']));
		$Config->userEmailFilterList = (int) htmlspecialchars(specfilter($_POST['userEmailFilterList']));
		$Config->userNewPassword = (int) htmlspecialchars(specfilter($_POST['userNewPassword']));
		$Config->ipBanRule = (int) htmlspecialchars(specfilter($_POST['ipBanRule']));

		$Config->userAvatarUpload = (int) htmlspecialchars(specfilter($_POST['userAvatarUpload']));
		$Config->userAvatarSize = (int) htmlspecialchars(specfilter($_POST['userAvatarSize']));
		$Config->userAvatarDir = htmlspecialchars(specfilter($_POST['userAvatarDir']));
		$Config->userAvatarMinNumPost = (int) htmlspecialchars(specfilter($_POST['userAvatarMinNumPost']));
		
		if(System::saveConfig($Config)){
			echo '<div class="msg">Настройки успешно сохранены</div>';
			System::notification('Изменена конфигурация системы через настройки пользователей', 'g');
		}else{
			echo'<div class="msg">Ошибка при сохранении настроек</div>';
			System::notification('Ошибка при сохранении конфигурации системы через настройки пользователей', 'r');
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?act=cfg\';', 3000);
</script>
<?php
	}

	if($act=='adell')
	{
		if(($CUser = User::getConfig($_GET['login'])) != false){
			if(file_exists(DR.'/'.$Config->userAvatarDir.'/'.$CUser->login.'.jpg')){
				unlink(DR.'/'.$Config->userAvatarDir.'/'.$CUser->login.'.jpg');
			}
			echo '<div class="msg">Аватар успешно удален</div>';
			System::notification('Удален аватар пользователя '.$CUser->login.'', 'g');
		}else{
			echo'<div class="msg">Пользователь не найден</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?act=user&login=<?php echo $CUser->login;?>&nom_page=<?php echo isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1;?>\';', 3000);
</script>
<?php
	}

	if($act=='user')
	{
		if(($CUser = User::getConfig($_GET['login'])) != false){
			if(file_exists(DR.'/'.$Config->userAvatarDir.'/'.$CUser->login.'.jpg')){
				$avatar_file = '/'.$Config->userAvatarDir.'/'.$CUser->login.'.jpg';
			}else{
				$avatar_file = '/modules/users/avatar.png';
			}
			echo'<div class="header">
				<h1>Управление пользователями</h1>
			</div>
			
			<div class="menu_page"><a href="users.php?nom_page='.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">&#8592; Вернуться назад</a></div> 
			
			<div class="content">
				<div class="row">
					<h2>'.$CUser->login.'</h2>
					<p><img src="'.$avatar_file.'" alt="avatar" id="avatar"></p>
					<p>Профиль зарегистрирован: '.human_time(time() - $CUser->timeRegistration).' назад ('.date("d.m.Y H:i", $CUser->timeRegistration).')</p>
					<p>Последняя активность: '.human_time(time() - $CUser->timeActive).' назад ('.date("d.m.Y H:i", $CUser->timeActive).')</p>
					<p>Последний используемый IP: '.$CUser->ip.' (<a class="" href="users.php?act=ip&amp;ip='.$CUser->ip.'">Заблокировать этот ip адрес</a>)</p>
					<p>Последний используемый UA: '.$CUser->ua.'</p>
					<p>Электронная Почта: '.$CUser->email.' ('.($CUser->emailChecked?'<span class="g">Подтверждено</span>':'<span class="r">Не подтверждено</span>').')</p>
					<p>Оставлено сообщений: '.$CUser->numPost.'</p>
					<p>Преференции: ';
					if($CUser->preferences == 0) echo'Нет';
					if($CUser->preferences == 1) echo'Удалять сообщения других пользователей';
					if($CUser->preferences == 2) echo'Удалять сообщения и блокировать других пользователей';
					echo'</p>';
					
					
					if($CUser->timeBan > time()){
						echo'<p class="r">Заблокирован до: '.date("d.m.Y H:i", $CUser->timeBan).'</p>';
						echo'<p class="r">Причина блокировки: '.($CUser->causeBan == ''?'Не указано':$CUser->causeBan).'</p>';
					}
					
					echo'
				</div>
				<p><a class="" href="users.php?act=edit&amp;login='.$CUser->login.'&amp;nom_page='.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">Редактировать профиль</a></p>
				<p><a class="" href="users.php?act=ban&amp;login='.$CUser->login.'&amp;nom_page='.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">Управление блокировкой</a></p>
				<p><a class="" href="users.php?act=adell&amp;login='.$CUser->login.'&amp;nom_page='.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">Удалить аватар пользователя</a></p>
				
			</div>';
			
			
		}else{
			echo'<div class="msg">Пользователь не найден</div>';
		}
	}
	
	
	
	if($act=='edit')
	{
		if(($CUser = User::getConfig($_GET['login'])) != false){
			
			echo'<div class="header">
				<h1>Управление пользователями</h1>
			</div>
			
			<div class="menu_page"><a href="users.php?act=user&amp;login='.$CUser->login.'&amp;nom_page='.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">&#8592; Вернуться назад</a></div>
			
			<div class="content">
			 
			<form name="form_name" action="users.php?" method="post">
			<INPUT TYPE="hidden" NAME="act" VALUE="addedit">
			<INPUT TYPE="hidden" NAME="nom_page" VALUE="'.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">
			<INPUT TYPE="hidden" NAME="login" VALUE="'.$_GET['login'].'">
			
			<table class="tblform">
			<tr>
				<td>Логин:</td>
				<td><input type="text" name="new_cfg_login" value="'.$CUser->login.'"><br><span class="comment">Допускаются только латинские символы, цифры, тире и подчеркивания</span></td>
			</tr>
			<tr>
				<td>Email:</td>
				<td><input type="text" name="new_cfg_email" value="'.$CUser->email.'"></td>
			</tr>
			<tr>
				<td>Количество сообщений:</td>
				<td><input type="text" name="new_cfg_numPost" value="'.$CUser->numPost.'"></td>
			</tr>
			<tr>
				<td>Преференции:</td>
				<td>
					<SELECT NAME="new_cfg_preferences">
						<OPTION VALUE="0" '.($CUser->preferences == 0?'selected':'').'>Нет
						<OPTION VALUE="1" '.($CUser->preferences == 1?'selected':'').'>Удалять сообщения других пользователей
						<OPTION VALUE="2" '.($CUser->preferences == 2?'selected':'').'>Удалять сообщения и блокировать других пользователей
					</SELECT>
				</td>
			</tr>
			<tr>
				<td>Новый пароль:</td>
				<td><input type="text" name="new_cfg_password" value=""><br><span class="comment">Оставите пустым если изменение пароля не требуется</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="" value="Сохранить"></td>
			</tr>
			</table>
			</form>
			</div>';
			
		}else{
			echo'<div class="msg">Пользователь не найден</div>';
		}
	}
	
	
	if($act=='addedit')
	{
		if(($CUser = User::getConfig($_POST['login'])) != false){
			$listEmailsUsers = System::listEmailsUsers();
			if(($key = array_search($CUser->email, $listEmailsUsers)) !== false){
				unset($listEmailsUsers[$key]); // Удалили найденый элемент массива
			}
			
			$CUser->login = $_POST['new_cfg_login'];
			$CUser->email = htmlspecialchars(strtolower(specfilter($_POST['new_cfg_email'])));
			$CUser->numPost = is_numeric($_POST['new_cfg_numPost'])?(int)$_POST['new_cfg_numPost']:0;
			$CUser->preferences = is_numeric($_POST['new_cfg_preferences'])?(int)$_POST['new_cfg_preferences']:0;
			if($_POST['new_cfg_password'] != ''){
				$CUser->salt = random(255);
				$CUser->password = cipherPass($_POST['new_cfg_password'], $CUser->salt);
			}
			
			if(User::setConfig($_POST['login'], $CUser)){
				$listEmailsUsers[] = $CUser->email;
				System::updateListEmailsUsers($listEmailsUsers);

				echo '<div class="msg">Настройки успешно сохранены</div>';
				if($_POST['login'] != $_POST['new_cfg_login']) System::notification('Изменен логин пользователя '.$_POST['login'].' на '.$_POST['new_cfg_login'], 'g');
				System::notification('Изменена конфигурация пользователя '.$CUser->login, 'g');
			}else{
				echo'<div class="msg">Ошибка при сохранении настроек</div>';
				System::notification('Ошибка при сохранении конфигурации пользователя '.$CUser->login, 'r');
			}
			
			
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?act=user&login=<?php echo $CUser->login;?>&nom_page=<?php echo isset($_POST['nom_page'])?htmlspecialchars($_POST['nom_page']):1;?>\';', 3000);
</script>
<?php			
		}else{
			echo'<div class="msg">Пользователь не найден</div>';
		}

	}


	if($act=='adduser')
	{
			
			echo'<div class="header">
				<h1>Регистрация нового пользователя</h1>
			</div>
			
			<div class="menu_page"><a href="users.php?">&#8592; Вернуться назад</a></div>
			
			<div class="content">
			
			<form name="form_name" action="users.php?" method="post">
			<INPUT TYPE="hidden" NAME="act" VALUE="adduser2">
			<INPUT TYPE="hidden" NAME="login" VALUE="">
			<div class="row r">Все поля обязательны для заполнения</div>
			<table class="tblform">
			<tr>
				<td>Логин:</td>
				<td><input type="text" name="login" value=""><br><span class="comment">Допускаются только латинские символы, цифры, тире и подчеркивания</span></td>
			</tr>
			<tr>
				<td>Email:</td>
				<td><input type="text" name="email" value=""></td>
			</tr>
			<tr>
				<td>Подтверждение Email:</td>
				<td>
					<SELECT NAME="emailChecked">
						<OPTION VALUE="1" selected>Считать email подтвержденным
						<OPTION VALUE="0">Пользователь должен подтвердить email
					</SELECT>
				</td>
			</tr>
			<tr>
				<td>Количество сообщений:</td>
				<td><input type="text" name="numPost" value="0"></td>
			</tr>
			<tr>
				<td>Преференции:</td>
				<td>
					<SELECT NAME="preferences">
						<OPTION VALUE="0">Нет
						<OPTION VALUE="1">Удалять сообщения других пользователей
						<OPTION VALUE="2">Удалять сообщения и блокировать других пользователей
					</SELECT>
				</td>
			</tr>
			<tr>
				<td>Пароль для входа:</td>
				<td><input type="text" name="password" value=""></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="" value="Зарегистрировать"></td>
			</tr>
			</table>
			</form>
			</div>';
			
		
	}


	if($act=='adduser2')
	{
		$salt = random(255);
		if(($ers = User::registration($_POST['login'], $_POST['password'], $salt, $_POST['email'])) == 0){
			$CUser = User::getConfig($_POST['login']);

			$listEmailsUsers = System::listEmailsUsers();
			if(($key = array_search($CUser->email, $listEmailsUsers)) !== false){
				unset($listEmailsUsers[$key]); // Удалили найденый элемент массива
			}

			$CUser->email = htmlspecialchars(strtolower(specfilter($_POST['email'])));
			$CUser->numPost = is_numeric($_POST['numPost'])?(int)$_POST['numPost']:0;
			$CUser->preferences = is_numeric($_POST['preferences'])?(int)$_POST['preferences']:0;
			$CUser->emailChecked = is_numeric($_POST['emailChecked'])?(int)$_POST['emailChecked']:0;
			

			User::setConfig($_POST['login'], $CUser);
			
			$listEmailsUsers[] = $CUser->email;
			System::updateListEmailsUsers($listEmailsUsers);

			System::notification('Зарегистрирован пользователь '.$_POST['login'].'', 'g');
			echo '<div class="msg">Пользователь успешно зарегистрирован</div>';
		}else{
			$errmsg = 'Неизвестная ошибка';
			if ($ers == 1){$errmsg = 'Пользователь с похожим логином уже существует';}
			if ($ers == 2){$errmsg = 'Ошибка при сохранении параметров';}
			if ($ers == 3){$errmsg = 'Не все поля формы заполнены';}
			if ($ers == 4){$errmsg = 'Логин содержит недопустимые символы';}
			if ($ers == 5){$errmsg = 'Слишком длинный логин, пароль или емайл';}

			System::notification('Ошибка при регистрации админом пользователя '.$_POST['login'].', '.$errmsg, 'r');
			echo '<div class="msg">'.$errmsg.'</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?act=adduser\';', 3000);
</script>
<?php	
	}
	
	
	if($act=='ban')
	{
		if(($CUser = User::getConfig($_GET['login'])) != false){
			
			echo'<div class="header">
				<h1>Управление пользователями</h1>
			</div>
			
			<div class="menu_page"><a href="users.php?act=user&amp;login='.$CUser->login.'&amp;nom_page='.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">&#8592; Вернуться назад</a></div>
			
			<div class="content">
			<h2>'.$CUser->login.'</h2>
			<form name="form_name" action="users.php?" method="post">
			<INPUT TYPE="hidden" NAME="act" VALUE="addban">
			<INPUT TYPE="hidden" NAME="nom_page" VALUE="'.(isset($_GET['nom_page'])?htmlspecialchars($_GET['nom_page']):1).'">
			<INPUT TYPE="hidden" NAME="login" VALUE="'.$CUser->login.'">
			
			<table class="tblform">
			<tr>
				<td class="top">Причина блокировки:</td>
				<td><TEXTAREA NAME="cause" ROWS="20" COLS="100" style="height:150px;">'.$CUser->causeBan.'</TEXTAREA></td>
			</tr>
			<tr>
				<td>На какое время:</td>
				<td>
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
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="" value="Отправить"></td>
			</tr>
			</table>
			</form>
			</div>';
			
		}else{
			echo'<div class="msg">Пользователь не найден</div>';
		}
	}
	
	
	if($act=='addban')
	{
		if(($CUser = User::getConfig($_POST['login'])) != false){
			
			$CUser->causeBan = htmlspecialchars($_POST['cause']);// Причина 
			
			if($_POST['time'] != 'none'){
				$CUser->timeBan =  time() + (is_numeric($_POST['time'])?(int)$_POST['time']:0);// Время
			}

			if(User::setConfig($CUser->login, $CUser)){
				echo '<div class="msg">Настройки пользователя сохранены</div>';
				System::notification('Настройки блокировки пользователя '.$CUser->login.' сохранены', 'g');
			}else{
				echo'<div class="msg">Ошибка при сохранении настроек</div>';
				System::notification('Ошибка при сохранении конфигурации пользователя '.$CUser->login, 'r');
			}
			
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?act=user&login=<?php echo $CUser->login;?>&nom_page=<?php echo isset($_POST['nom_page'])?htmlspecialchars($_POST['nom_page']):1;?>\';', 3000);
</script>
<?php			
		}else{
			echo'<div class="msg">Пользователь не найден</div>';
		}

	}
	
    
	if($act=='dell'){

		$login = htmlspecialchars(specfilter($_GET['login']));

		if (User::exists($login)){

			if(User::delete($login)){
				System::notification('Удален пользователь '.$login.'', 'g');
				echo'<div class="msg">Пользователь успешно удален</div>';
			}else{
				System::notification('Ошибка при удалении пользователя '.$login.', возможно недостаточно прав доступа', 'r');
				echo'<div class="msg">Ошибка при удалении пользователя</div>';
			}
			
		}else{
			System::notification('Ошибка при удалении пользователя '.$login.', пользователь не найден или запрос некорректен', 'r');
			echo'<div class="msg">Ошибка при удалении пользователя</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?\';', 3000);
</script>
<?php
	}
	




	if($act=='ip'){
		echo'<div class="header">
			<h1>Заблокированные IP пользователей</h1>
		</div>
		<div class="menu_page"><a href="users.php?">&#8592; Вернуться назад</a></div> 
		
		<div class="content">
		
		<div class="row">
				<form action="users.php?act=ipban" method="post">
				<input style="width: 250px;" type="text" name="ip" value="'.htmlspecialchars(specfilter($_GET['ip'])).'" placeholder="IP адрес пользователя" autofocus>
				<input type="submit" name="" value="Заблокировать"> 
				</form>
		</div>
		
		';
		
		
		
		if(count($Config->ipBan) == 0 || !is_array($Config->ipBan)){
			echo'<div class="msg">Нет заблокированных IP адресов</div>';
		}else{
			
			
			echo'
			
			<table class="tables">
			<tr>
				<td class="tables_head" colspan="2">IP адреса</td>
				<td class="tables_head" style="text-align: right;">&nbsp;</td>
			</tr>';
			
			
			
			foreach($Config->ipBan as $value){
				echo'<tr>
					<td class="img"><img src="include/user.svg" alt=""></td>
					<td>'.$value.($value == IP?' <span style="color:red;">Текущий ip адрес</span>':'').'</td>
					<td><a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', ipdell(\''.$value.'\'));">Удалить</a></td>
				</tr>';
			}
			echo'</table>';
			
			
		}
			
		echo'</div>';
	}




	if($act=='ipdell')
	{
		$arrIP = $Config->ipBan;
		foreach($arrIP as $key => $value){
			if($value == $_GET['ip']){
				unset($arrIP[$key]);
			}
		}
		
		$Config->ipBan = array_values($arrIP); // Переиндексировали числовые индексы 
		
		if(System::saveConfig($Config)){
			echo '<div class="msg">Удаление успешно завершено</div>';
			System::notification('Изменена конфигурация системы, удалены заблокированные IP адреса', 'g');
		}else{
			echo'<div class="msg">Ошибка при сохранении настроек</div>';
			System::notification('Ошибка при сохранении конфигурации системы', 'r');
		}
			
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?act=ip\';', 3000);
</script>
<?php		

	}





	if($act=='ipban')
	{
		if($_POST['ip'] != '' && $_POST['ip'] != IP){
			$Config->ipBan[] = htmlspecialchars(specfilter(substr($_POST['ip'], 0, 255))); 
			
			if(System::saveConfig($Config)){
				echo '<div class="msg">IP адрес успешно заблокирован</div>';
				System::notification('Заблокирован IP '.$_POST['ip'].'', 'g');
			}else{
				echo'<div class="msg">Ошибка при сохранении настроек</div>';
				System::notification('Ошибка при сохранении конфигурации системы', 'r');
			}
		}else{
			if ($_POST['ip'] == IP){
				System::notification('Ошибка при блокировки IP адреса. Введенный IP адрес совпадал с адресом администратора. IP '.IP.'', 'r');
			}
			echo'<div class="msg">Ошибка при сохранении настроек</div>';
		}
			
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?act=ip\';', 3000);
</script>
<?php		

	}





	if($act=='search'){

		$q = isset($_POST['q'])?htmlspecialchars(trim($_POST['q'])):'';
		
		echo'<div class="header">
			<h1>Поиск пользователей</h1>
		</div>
		<div class="menu_page"><a href="users.php?">&#8592; Вернуться назад</a></div>
			
		
		<div class="content">
			
			<div class="row">
				<form action="users.php?act=search" method="post">
				<input style="width: 250px;" type="text" name="q" value="'.$q.'" placeholder="Поиск пользователей" autofocus>
				<input type="submit" name="" value="Поиск"> 
				</form>
			</div>';
			
			if($q != ''){
				
				$users = System::listUsers();
				$users = array_reverse($users);//перевернули масив
				
				$uSearch = array(); // Пустой массив результатов
				
				foreach($users as $value){
					if(($pos = stripos($value, $q)) !== false){
						$uSearch[$value] = $pos;
						//echo $value.' - '.$uSearch[$value].'<br>';
						
					}
				}
				asort($uSearch);
				
				echo'<table class="tables">
				<tr>
					<td class="tables_head" colspan="2">Всего результатов: '.count($uSearch).'</td>
					<td class="tables_head"></td>
				</tr>';
				$i = 0; // Счетчик показов
				foreach($uSearch as $key => $value){
					echo'<tr>
						<td class="img"><img src="include/user.svg" alt=""></td>
						<td><a href="users.php?act=user&amp;login='.$key.'">'.str_ireplace($q, '<span class="r">'.$q.'</span>', $key).'</a></td>
						<td><a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\''.$key.'\'));">Удалить</a></td>
					</tr>';
					++$i; if($i == 1000) break; // Ограничение показов
				}
				echo'</table>';
				
			}
		
		echo'</div>';
	}
	
	if($act=='dellold')
	{
?>
<script type="text/javascript">
var info = '<div class="a">' +
	'Email адреса удаляемых пользователей можно сохранить в хранилище. Помните, что новые пользователи, по существующим в этом хранилище email адресам, зарегистрироваться не смогут.<br>'+
	'</div>'+
	'<div class="b">'+
	'<button type="button" onclick="closewindow(\'window\');">Закрыть</button>'+
	'</div>';
</script>
<?php
		
		echo'<div class="header">
			<h1>Удаление пользователей</h1>
		</div>
		
		<div class="menu_page"><a href="users.php?">&#8592; Вернуться назад</a></div>
		
		<div class="content">
			
		<form name="form_name" action="users.php?act=adddellold" method="post">
		
		<table class="tblform">
		
		<tr>
			<td>Выберите период неактивности:</td>
			<td>
				<SELECT NAME="time">
					<OPTION VALUE="31536000">1 год
					<OPTION VALUE="15552000">6 месяцев
					<OPTION VALUE="7776000">3 месяца
					<OPTION VALUE="2592000">1 месяц
					<OPTION VALUE="604800">1 неделя
				</SELECT>
			</td>
		</tr>

		<tr>
			<td>Удалить почтовые адреса пользователей:</td>
			<td class="middle">
				<input type="checkbox" name="dellEmails" value="n" onclick="if(this.checked) openwindow(\'window\', 650, \'auto\', info);">
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="" value="Начать удаление"></td>
		</tr>
		</table>
		</form>
		</div>';
			
		
	}
	
	if($act=='adddellold')
	{
	
		$dellEmails = isset($_POST['dellEmails']);
		if($dellEmails){
			// $listEmailsUsers = System::listEmailsUsers();
			$listEmailsUsers = array();
		}
		$time = htmlspecialchars(specfilter($_POST['time']));
		if($time >= 604800){
			$users = System::listUsers();
				
			$countDell = 0;
			$logDell = '';
			foreach($users as $value){
				if(($CUser = User::getConfig($value)) != false){
					if((time() - $CUser->timeActive) >= $time){
						
						// if($dellEmails){
						// 	if(($key = array_search($CUser->email, $listEmailsUsers)) !== false){
						// 		unset($listEmailsUsers[$key]); // Удалили найденый элемент массива
						// 	}
						// }

						User::delete($value, false);
						++$countDell;
						$logDell.= $value.', ';
					}else{
						if($dellEmails){
							$listEmailsUsers[] = $CUser->email;
						}
					}
				}
			}

			if($dellEmails){
				System::updateListEmailsUsers(array_unique($listEmailsUsers));
			}

			$logDell.= 'Всего удалено '.$countDell.'.';
			System::notification('Удаление пользователей: '.$logDell.'', 'g');

			echo'<div class="msg">Удалено '.$countDell.' пользователей</div>';
		}else{
			echo'<div class="msg">Ошибка при удалении пользователя</div>';
		}
		
?>
<script type="text/javascript">
setTimeout('window.location.href = \'users.php?\';', 5000);
</script>
<?php
		
	}


}else{
echo'<div class="msg">Необходимо выполнить авторизацию</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'index.php?\';', 3000);
</script>
<?php
}
require('include/end.dat');
?>