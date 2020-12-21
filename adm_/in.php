<?php
require('../system/global.dat');
if(in_array(IP, $Config->ipBan)){
	System::notification('Попытка авторизации в панели управления пользователем, ip которого было заблокировано. IP '.IP.' UA '.UA.'', 'r');
	header('Content-type: text/html; charset=utf-8'); 
	require DR.'/pages/ipban.html'; ob_end_flush(); exit();
}
require('./include/start.dat');
if(cipherPass(cipherPass($_POST['password_form'], $Config->salt), $Config->salt) != $Config->adminPassword){
	System::notification('При входе в панель администратора введен ошибочный пароль. IP '.IP.' UA '.UA.'', 'r');
	echo'<div class="msg">Введенный пароль неверен</div>';
}else{
	setcookie('password',cipherPass($_POST['password_form'], $Config->salt),time() + $Config->timeAuth,'/');
	System::notification('Выполнен вход в панель управления. IP '.IP.' UA '.UA.'', 'g');
	echo'<div class="msg">Пожалуйста подождите</div>';
}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'index.php?\';', 3000);
</script>
<?php
require('include/end.dat');
?>