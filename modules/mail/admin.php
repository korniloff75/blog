<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

$mailToAdminStorage = new EngineStorage('module.mail.to.admin');
$emails = $mailToAdminStorage->get('emails');
$fromallform = $mailToAdminStorage->get('fromallform');
$checked = $fromallform?' checked':'';
if($emails == false){ $emails = '';}

if($act=='index'){
	echo'<div class="header"><h1>Настройки обратной связи</h1></div>
	<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a></div>
	<div class="content">
	<form name="forma" action="module.php?module='.$MODULE.'" method="post">
	<INPUT TYPE="hidden" NAME="act" VALUE="add">
	<table class="tblform">
	<tr>
		<td>Email адрес получателя писем:</td>
		<td><input type="text" name="new_cfg_emal_admin" value="'.$emails.'" size="50"><br><span class="comment">Можно указать несколько адресов через запятую без пробелов.</span></td>
	</tr>
	<tr>
		<td>Отправлять содержимое любых форм:<br><span class="comment">Капча не проверяется, может быть спам</span></td>
		<td class="middle"><input type="checkbox" name="fromallform" value="y" id="checkbox"'.$checked.'></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="" value="Сохранить"></td>
	</tr>
	</table>
	</form>
	</div>';
}

if($act=='add'){
	$mailToAdminStorage->set('emails', htmlspecialchars(specfilter($_POST['new_cfg_emal_admin'])));
	$mailToAdminStorage->set('fromallform', ($_POST['fromallform'] == 'y'?'1':'0'));
	echo'<div class="msg">Настройки успешно сохранены</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}

?>