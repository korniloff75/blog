<?php
require('../system/global.dat');
require('./include/start.dat');

if($status=='admin'){
	if($act=='index'){
?>
<script type="text/javascript">
var template = '<div class="a"><div style="min-height: 111px; max-height: 221px; overflow: auto;"><?php
$listModules = System::listModules();
foreach($listModules as $value){
		
		
		if(Module::isTemlate($value)){
			if(($skr = Module::skrTemlate($value)) === false){
				$skr = 'include/noimgtemplate.png';
			}
			$info = Module::info($value);
			echo'<div style="clear: both; height: 40px; border: 1px solid #e5e5e5; background-color: #f9f9f9; margin: 1px 1px; padding: 6px;"><img style="float:left; margin: 0 6px 0 0;" src="'.$skr.'" width="60" height="40" alt="">'.$info['name'].'<a class="button0" style="float: right; margin: 0px;" href="javascript:void(0);" onclick="document.getElementById(\\\'template\\\').value = \\\''.$value.'\\\'; document.getElementById(\\\'name_template\\\').value = \\\''.$info['name'].'\\\'; closewindow(\\\'window\\\');">Выбрать</a></div>';
		}
}
?></div></div>'+
'<div class="b" style="clear:both;">'+
'<button type="button" onclick="closewindow(\'window\');">Закрыть</button>'+
'</div>';

var badchoice = '<div class="a"><span class="r">Внимание!</span> Даже через длительный промежуток времени, любой пользователь данного устройства будет считаться администратором</div>'+
'<div class="b">'+
'<button type="button" onclick="closewindow(\'window\');">Закрыть</button>'+
'</div>';

function random(n)
{
	var r = '';
	var arr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	var al = arr.length
	for( var i=0; i < n; i++ ){
		r += arr[Math.floor(Math.random() * al)];
	}
	return r;
}

</script>
<?php
		$info = Module::info($Config->template);
		echo'
		<div class="header">
			<h1>Настройки</h1>
		</div>
		<div class="menu_page">
			<a class="link" href="setting.php?">Основные настройки системы</a>
			<a class="link" href="files.php?act=editor&amp;dir=../modules/'.$Config->template.'&file=../modules/'.$Config->template.'/template.php">Правка текущего шаблона</a>
			<a class="link" href="setting.php?act=pass">Смена пароля администратора</a>
			<a class="link" href="users.php?act=cfg">Настройки пользователей</a>
		</div>
		
		<div class="content">
		
		<form name="settingform" action="setting.php?" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addsetting">
		<INPUT TYPE="hidden" name="template" id="template" VALUE="'.$Config->template.'">
		
		<table class="tblform">
		<tr>
			<td>Заголовок сайта:</td>
			<td><input type="text" name="header" value="'.$Config->header.'" size="50"></td>
		</tr>
		
		<tr>
			<td>Слоган сайта:</td>
			<td><input type="text" name="slogan" value="'.$Config->slogan.'" size="50"></td>
		</tr>
		
		<tr>
			<td>Шаблон сайта:</td>
			<td><input style="width: 220px; background-color: #ccc; text-align: center;" type="text" name="name_template" id="name_template" value="'.$info['name'].'" size="50" readonly> &nbsp; <a href="javascript:void(0);" onclick="openwindow(\'window\', 600, \'auto\', template);">Выбрать шаблон</a></td>
		</tr>
		
		<tr>
			<td>Временная зона работы сайта:</td>
			<td>
				<SELECT NAME="timeZone" style="">';
				$timezones = System::getTimeZones();
				echo'<OPTION VALUE="default" '.($Config->timeZone == 'default'?'selected':'').'>Использовать настройки сервера';
				foreach($timezones as $value){
					echo'<OPTION VALUE="'.$value.'" '.($Config->timeZone == $value?'selected':'').'>'.$value;
				}
				echo'</SELECT><br><span class="comment">Текущее дата и время сайта: '.date("d.m.Y H:i").'</span>
			</td>
		</tr>
		
		
		
		<tr>
			<td>Визуальный редактор:</td>
			<td>
				<SELECT NAME="wysiwyg">';
				echo'<OPTION VALUE="0" '.($Config->wysiwyg == '0'?'selected':'').'>Без визуального редактора';
				foreach($listModules as $value){
					if(Module::isWysiwyg($value)){
						$info = Module::info($value);
						echo'<OPTION VALUE="'.$value.'" '.($Config->wysiwyg == $value?'selected':'').'>'.$info['name'].' '.$info['version'];
					}
				}
				echo'</SELECT>
			</td>
		</tr>
		<tr>
			<td>Правила ссылок со слешем в конце:</td>
			<td>
				<SELECT NAME="slashRule">';
				if($Config->slashRule == '1'){
					echo'<OPTION VALUE="1" selected>Перенаправлять на ту же ссылку но без слеша';
					echo'<OPTION VALUE="2">Показывать ошибку 404 (Страница не найдена)';
				}else{
					echo'<OPTION VALUE="2" selected>Показывать ошибку 404 (Страница не найдена)';
					echo'<OPTION VALUE="1">Перенаправлять на ту же ссылку но без слеша';
				}
				echo'</SELECT>
			</td>
		</tr>
		<tr>
			<td>Правила GET параметров в адресах:</td>
			<td>
				<SELECT NAME="uriRule">';
				if($Config->uriRule == '1'){
					echo'<OPTION VALUE="1" selected>Разрешить работу произвольных GET параметров';
					echo'<OPTION VALUE="2">Запретить произвольные GET параметры';
				}else{
					echo'<OPTION VALUE="2" selected>Запретить произвольные GET параметры';
					echo'<OPTION VALUE="1">Разрешить работу произвольных GET параметров';
				}
				echo'</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>GZIP Сжатие страниц:</td>
			<td>
				<SELECT NAME="gzip">';
				if($Config->gzip == '1'){
					echo'<OPTION VALUE="1" selected>Включено';
					echo'<OPTION VALUE="0">Отключено';
				}else{
					echo'<OPTION VALUE="0" selected>Отключено';
					echo'<OPTION VALUE="1">Включено';
				}
				echo'</SELECT>
			</td>
		</tr>
		
		
		<tr>
			<td>Время сохранения авторизации:</td>
			<td>
				<SELECT NAME="timeAuth" id="sel" onchange="if(document.getElementById(\'sel\').value == 32000000) openwindow(\'window\', 600, \'auto\', badchoice);">
					<OPTION VALUE="1800" '.($Config->timeAuth == '1800'?'selected':'').'>30 минут (Рекомендуется)
					<OPTION VALUE="10800" '.($Config->timeAuth == '10800'?'selected':'').'>3 часа
					<OPTION VALUE="86400" '.($Config->timeAuth == '86400'?'selected':'').'>24 часа
					<OPTION VALUE="259200" '.($Config->timeAuth == '259200'?'selected':'').'>3 дня (Не рекомендуется)
					<OPTION VALUE="32000000" '.($Config->timeAuth == '32000000'?'selected':'').'>Всегда (Очень опасно)
				</SELECT><br><span class="comment">Чем меньше значение, тем безопасней</span>
			</td>
		</tr>
		
		<tr>
			<td>Протокол формирования ссылок:</td>
			<td>
				<SELECT NAME="protocol">
					<OPTION VALUE="http" '.($Config->protocol == 'http'?'selected':'').'>http
					<OPTION VALUE="https" '.($Config->protocol == 'https'?'selected':'').'>https
				</SELECT><br><span class="comment">Используется для статических данных, например sitemap.xml</span>
			</td>
		</tr>


		<tr>
			<td>Соль для шифрования (<a href="//my-engine.ru/ticketsalt" target="_blank">Инфо</a>):</td>
			<td>
				<input type="text" name="ticketSalt" id="salt" value="'.$Config->ticketSalt.'" size="50"><br>
				<a href="javascript:void(0);" onclick="document.getElementById(\'salt\').value = random(255)">Сгенерировать новую соль</a>
			</td>
		</tr>
		
		<tr>
			<td>Идентификатор главной страницы:</td>
			<td><input type="text" name="indexPage" value="'.$Config->indexPage.'" size="50"></td>
		</tr>

		
		
		<tr>
			<td>Email администратора:</td>
			<td><input type="text" name="adminEmail" value="'.$Config->adminEmail.'" size="50"></td>
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
	
	if($act=='pass'){
		echo'
		<div class="header">
			<h1>Настройки</h1>
		</div>
		<div class="menu_page">
			<a class="link" href="setting.php?">Основные настройки системы</a>
			<a class="link" href="files.php?act=editor&amp;dir=../modules/'.$Config->template.'&file=../modules/'.$Config->template.'/template.php">Правка текущего шаблона</a>
			<a class="link" href="setting.php?act=pass">Смена пароля администратора</a>
			<a class="link" href="users.php?act=cfg">Настройки пользователей</a>
		</div>
		<div class="content">
		
		<form name="settingform" action="setting.php?" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addpass">
		<table class="tblform">
		<tr>
			<td>Новый пароль администратора:</td>
			<td><input type="password" name="new_cfg_password" value="" size="50"></td>
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
	
	if($act=='addsetting'){
		if( ($_POST['header'] == '') ||
			($_POST['slogan'] == '')||
			($_POST['adminEmail'] == '')||
			($_POST['indexPage'] == '')){
			echo'<div class="msg">Не все поля заполнены</div>';
		}else{
			$Config->version = htmlspecialchars(specfilter($version));
			
			$Config->template = htmlspecialchars(specfilter($_POST['template']));
			$Config->header = htmlspecialchars(specfilter($_POST['header']));
			$Config->slogan = htmlspecialchars(specfilter($_POST['slogan']));
			
			$Config->wysiwyg = htmlspecialchars(specfilter($_POST['wysiwyg']));
			$Config->timeZone = htmlspecialchars(specfilter($_POST['timeZone']));
			$Config->slashRule = (int) htmlspecialchars(specfilter($_POST['slashRule']));
			$Config->gzip = (int) htmlspecialchars(specfilter($_POST['gzip']));
			$Config->adminEmail = htmlspecialchars(specfilter($_POST['adminEmail']));
			// $Config->adminStyleFile = htmlspecialchars(specfilter($_POST['adminStyleFile'])); // 5.2.0 удалено 
			$Config->timeAuth = (int) htmlspecialchars(specfilter($_POST['timeAuth']));
			$Config->ticketSalt = htmlspecialchars(specfilter($_POST['ticketSalt']));
			$Config->uriRule = (int) htmlspecialchars(specfilter($_POST['uriRule']));
			$Config->protocol = htmlspecialchars(specfilter($_POST['protocol']));

			$Config->indexPage = Page::exists($_POST['indexPage']) ? $_POST['indexPage'] : $Config->indexPage;
			
			if(System::saveConfig($Config)){
				echo '<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменена конфигурация системы', 'g');
			}else{
				echo'<div class="msg">Ошибка при сохранении настроек</div>';
				System::notification('Ошибка при сохранении конфигурации системы', 'r');
			}
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'setting.php?\';', 3000);
</script>
<?php
	}
	
	if($act=='addpass'){
		if($_POST['new_cfg_password'] == ''){
			echo'<div class="msg">Не все поля заполнены</div>';
		}else{
			$Config->salt = random(255); // Меняем соль для шифрования
			
			$new_cfg_password = cipherPass($_POST['new_cfg_password'], $Config->salt);
			setcookie('password',$new_cfg_password,time()+32000000,'/');
			
			$Config->adminPassword = cipherPass($new_cfg_password, $Config->salt); // Еще раз шифруем для записи в настройки
			System::notification('Изменен пароль от панели управления IP '.IP.' UA '.UA.'', 'g');
			
			if(System::saveConfig($Config)){
				echo '<div class="msg">Пароль успешно изменен</div>';
			}else{
				echo'<div class="msg">Ошибка при сохранении настроек</div>';
			}
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'setting.php?act=pass\';', 3000);
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

require('./include/end.dat');
?>