<style>
	.tblform td:first-child {
	width: 50%;
	overflow: visible;
	white-space: pre-line;
	padding: 16px 30px 16px 0;
}
</style>

<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

$mailToAdminStorage = new EngineStorage('module.mail.to.admin');
$cfg = json_decode($mailToAdminStorage->get('cfg'),1);

// *Target email(s)
$emails = &$cfg['emails'];

$fromallform = &$cfg['fromallform'];

$tg_token = file_get_contents(__DIR__.'/token');

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
		<td><input type="text" name="new_cfg_emal_admin" value="'.$emails.'" size="50"><br><span class="comment">Можно указать несколько адресов через запятую.</span></td>
	</tr>
	<!-- <tr>
		<td>Отправлять содержимое любых форм:<br><span class="comment">Капча не проверяется, может быть спам</span></td>
		<td class="middle"><input type="checkbox" name="fromallform" value="y" id="checkbox"'.$checked.'></td>
	</tr> -->
	<tr>
		<td>Username:<br><span class="comment">Этот адрес будет использован как Username при SMTP авторизации, а также указан в строке отправителя письма.</span></td>
		<td class="middle"><input type="text" name="smtp_username" value="'.@$cfg['smtp']['username'].'"></td>
	</tr>
	<tr>
		<td><h3>SMTP</h3>
		<span class="comment">Данные SMTP-сервера</span></td>
	</tr>
	<tr>
		<td>Host:</td>
		<td class="middle"><input type="text" name="smtp_host" value="'.@$cfg['smtp']['host'].'"></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td class="middle"><input type="text" name="smtp_password" value="'.@$cfg['smtp']['password'].'"></td>
	</tr>
	<tr>
		<td><h3>Telegram</h3>
		<span class="comment">Настройки для получения копий писем в ТГ-бота</span></td>
	</tr>
	<tr>
		<td>Token:<br><span class="comment">Индивидуальный токен бота.<br>Как создать нового бота через <i>@botfather</i> и получить его токен есть куча  <a href="https://yandex.ru/search/?clid=2186621&text=%D0%BA%D0%B0%D0%BA%20%D1%81%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C%20%D0%B1%D0%BE%D1%82%20%D1%87%D0%B5%D1%80%D0%B5%D0%B7%20botfather&lr=146&redircnt=1596791584.1" target="_blank" rel="nofollow">информации в инете</a>. </span></td>
		<td class="middle"><input type="text" name="tg_token" value="'.@$tg_token.'"></td>
	</tr>
	<tr>
		<td>ID пользователя, группы или канала:<br><span class="comment"> <a href="https://yandex.ru/search/?text=%D1%83%D0%B7%D0%BD%D0%B0%D1%82%D1%8C%20id%20%D0%BA%D0%B0%D0%BD%D0%B0%D0%BB%D0%B0%20telegram&lr=146&clid=2186621&src=suggest_B" target="_blank" rel="nofollow">ID канала</a>. </span></td>
		<td class="middle"><input type="text" name="tg_chat_id" value="'.@$cfg['tg']['chat_id'].'"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="" value="Сохранить"></td>
	</tr>
	</table>
	</form>
	</div>';
}


// *Save
if($act=='add')
{
	$cfg['emails']= htmlspecialchars(specfilter($_POST['new_cfg_emal_admin']));
	$cfg['fromallform']= $_POST['fromallform'] === 'y'?'1':'0';
	$cfg['smtp']['host']= $_POST['smtp_host'];
	$cfg['smtp']['username']= $_POST['smtp_username'];
	$cfg['smtp']['password']= $_POST['smtp_password'];

	$cfg['tg']['chat_id']= $_POST['tg_chat_id'];

	// *Save new token
	if(isset($_POST['tg_token']) && $_POST['tg_token'] !== $tg_token)
	{
		file_put_contents(__DIR__.'/token', $_POST['tg_token']);
	}

	$mailToAdminStorage->set('cfg',
		json_encode($cfg, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
	);
	echo'<div class="msg">Настройки успешно сохранены</div>
	<p><a href="module.php?module='.$MODULE.'"><<Назад</a></p>';
?>

<script type="text/javascript">
// setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 5000);
</script>
<?php
}
