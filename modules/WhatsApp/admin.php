<?php
if (!class_exists('System')) exit; // Запрет прямого доступа
require('../modules/WhatsApp/info.dat');
if($status=='admin'){
	if($act=='index'){


		echo'<div class="header"><h1>Настройки</h1></div>
		<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a></div>
		<div class="content">
		  
     <h2>WhatsApp чат для сайта</h2><p class="box">С помощью данного модуля вам смогут отпровлять сообщеня с вашего сайта, 
		то тогда заполните форму.
     
		
		
		<form name="settingform" action="module.php?module=WhatsApp" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addsetting">
		
		<table class="tblform">



				     
			         
		<tr>
			<td> Заголовок 1:</td>
			<td><input type="text" name="App_1" value="'.$WhatsApp['1'].'"></td>
		</tr>
		<tr>
			<td>Заголовок 2:</td>
			<td><input type="text" name="App_2" value="'.$WhatsApp['2'].'"></td>
		</tr>
		<tr>
		<tr>
			<td>Текст сообщения:</td>
			<td><input type="text" name="App_3" value="'.$WhatsApp['3'].'"></td>
		</tr>
		<tr>
			<td>Ваш телефон:</td>
			<td><input type="text" name="App_4" value="'.$WhatsApp['4'].'"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="" value="Сохранить"></td>
		</tr>
		</form>
		</div>
		';
	}
	
	if($act=='addsetting')
	{
			$WhatsApp['1'] = htmlspecialchars($_POST['App_1']);
			$WhatsApp['2'] = htmlspecialchars($_POST['App_2']);
			$WhatsApp['3'] = htmlspecialchars($_POST['App_3']);
			$WhatsApp['4'] = htmlspecialchars($_POST['App_4']);

$inset = '<?php
$WhatsApp[\'1\'] = \''.addslashes($WhatsApp['1']).'\';
$WhatsApp[\'2\'] = \''.addslashes($WhatsApp['2']).'\';
$WhatsApp[\'3\'] = \''.addslashes($WhatsApp['3']).'\';
$WhatsApp[\'4\'] = \''.addslashes($WhatsApp['4']).'\';
?>';

			if(filefputs('../modules/WhatsApp/info.dat', $inset, 'w+')){
				echo'<div class="msg">Настройки успешно сохранены</div>';
			}else{
				echo'<div class="msg">Ошибка при сохранении настроек</div>';
			}
	
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=WhatsApp\';', 3000);
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

?>