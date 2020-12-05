<?php
require('../system/global.dat');
require('./include/start.dat');

if (file_exists('newpassword.dat')){
?>
<script type="text/javascript">
var newpassword = '<div class="a">' +
	'Нажмите "Продолжить" для сохранения нового пароля из файла<br>'+
	'</div>'+
	'<div class="b">'+
	'<button type="button" onclick="window.location.href = \'newpassword.php\';">Продолжить</button>'+
	'</div>';
openwindow('window', 650, 'auto', newpassword);
</script>
<?php

}else{

if($status == 'admin'){
?>
<script type="text/javascript">
var info = '<div class="a" style="font: 12px Courier New, monospace;">' +
	'Версия my-engine: <?php echo $version;?><br>'+
	'Версия php: <?php echo phpversion();?><br>'+
	'Host name: <?php echo HOST;?><br>'+
	'Server name: <?php echo SERVER;?><br>'+
	'Server protocol: <?php echo PROTOCOL;?><br>'+
	'Document root: <?php echo quotemeta(DR);?><br>'+
	'Server Document root: <?php echo $_SERVER['DOCUMENT_ROOT'];?><br>'+
	'User agent: <?php echo UA;?><br>'+
	'</div>'+
	'<div class="b">'+
	'<a class="button" href="notifications.php">Посмотреть уведомления системы</a> <button type="button" onclick="closewindow(\'window\');">Закрыть</button>'+
	'</div>';
</script>
<div class="header">
	<h1>Панель управления</h1>
</div>
<div class="menu_page">
		<a class="link" href="//my-engine.ru/download" target="_blank">Проверить обновления</a>
		<a class="link" href="javascript:void(0);" onclick="openwindow('window', 650, 'auto', info);">Информация о системе</a>
		<a class="link" href="license.php">Соглашение с пользователем</a>
		<a class="link" href="notifications.php">Уведомления системы</a>

</div>
<?php
	if (!is_writable('../data/cfg/config.dat')||
		!is_writable('../data/')||
		!is_writable('../modules/')){
		echo'<div class="error">Необходимо выставить нужные права доступа файлам и папкам. Какие права выставлять вы можете узнать на <a href="//my-engine.ru/" target="_blank" style="text-decoration: underline;">сайте</a> разработчиков.</div>';
	}
	if($Config->adminPassword == cipherPass(cipherPass('123', $Config->salt), $Config->salt)){
		echo'<div class="error">Необходимо изменить пароль от панели управления, перейдите в <a href="setting.php?act=pass">настройки</a> и введите новый пароль.</div>';
	}
	if($Config->ticketSalt == '123'){
		echo'<div class="error">Необходимо изменить соль шифрования, перейдите в <a href="setting.php">настройки</a> и введите любые другие символы.</div>';
	}
	if (file_exists('../admin/index.php')){
		echo'<div class="notification">Для повышения безопасности сайта, вы можете переименовать папку панели администратора</div>';
	}
	?>
	
	<div class="content">
		<div class="modules">
		<?php
			$integration = array();
			
			$listModules = System::listModules();
			
			foreach($listModules as $value){
				
				if(Module::isAdminPage($value)){
					
					if(($icon = Module::icon($value)) === false){
						$icon = 'include/indexmodule.svg';
					}
					
					$info = Module::info($value);
					
					echo'<div class="module">
						<a href="module.php?module='.$value.'"><img src="'.$icon.'" alt=""><br>'.$info['indexname'].'</a>
					</div>';
				}
				
				if(Module::isIntegrationAdminIndex($value)){
					$integration[] = Module::pathRun($value, 'integration_admin');
				}
				
			}
		?>
		</div>
		
		
		
		<div class="integration">
			<?php
				foreach($integration as $value){
					include($value);
				}
			?>
		</div>
		
	</div>
<?php	
}else{
?>
<script type="text/javascript">
var enterform = '<form action="in.php?" method="post">'+
	'<div class="a">' +
	'Введите пароль<br><input type="password" name="password_form" value="" autofocus>' +
	'</div><div class="b">' +
	'<a style="float: left;" href="/">На главную страницу сайта</a> <input type="submit" name="" value="Вход">' +
	'</div></form>';
setTimeout(function(){
	openwindow('window', 400, 'auto', enterform);
}, 1000);
</script>
<?php
}
}
require('./include/end.dat');
?>