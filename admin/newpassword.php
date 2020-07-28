<?php
require('../system/global.dat');
require('./include/start.dat');

if(file_exists('newpassword.dat')){
	$Config->salt = random(255);
	$new_cfg_password = cipherPass(file_get_contents('newpassword.dat'), $Config->salt);
	$Config->adminPassword = cipherPass($new_cfg_password, $Config->salt);
	System::notification('Из файла "newpassword.dat" сгенерирован новый пароль от панели управления IP '.IP.' UA '.UA.'', 'g');
	if(System::saveConfig($Config)){
		echo '<div class="msg">Пароль успешно изменен<br>Удалите файл "newpassword.dat" из папки администратора</div>';
	}else{
		echo'<div class="msg">Ошибка при сохранении настроек</div>';
	}
}else{
?>
<div class="msg">Пожалуйста подождите</div>
<script type="text/javascript">
setTimeout('window.location.href = \'index.php\';', 3000);
</script>
<?php
}



require('./include/end.dat');
?>