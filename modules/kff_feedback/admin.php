<style>
	.tblform td:first-child {
	width: 50%;
	overflow-x: auto;
	white-space: pre-line;
	padding: 16px 30px 16px 0;
}
</style>

<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

// *Path to internal module folder
$internalDir = DR.'/' . $kff::$internalModulesPath.'/'.basename(__DIR__);

$mailToAdminStorage = new DbJSON($internalDir.'/cfg.db.dat', __DIR__);
// $mailToAdminStorage = new EngineStorage_kff('cfg.db', __DIR__);
// $mailToAdminStorage = new EngineStorage('module.mail.to.admin');

// $cfg = $mailToAdminStorage->get();
$cfg = $mailToAdminStorage->get();
// $cfg = json_decode($mailToAdminStorage->get('cfg'),1);

// var_dump($cfg->getPathName());

// *Target email(s)
$emails = &$cfg['emails'];

$fromallform = &$cfg['fromallform'];

$tokenPathname= $internalDir.'/token.json';

$tokenDB= new DbJSON($tokenPathname);
$tg_token = $tokenDB->get('tg');

$checked = $fromallform?' checked':'';
if($emails == false){ $emails = '';}

// *Render
if($act=='index'){
	?>

	<div class="header">
		<h1>Настройки обратной связи</h1>
	</div>
	<div class="menu_page">
		<a href="index.php">&#8592; Вернуться назад</a>
	</div>

	<div class="content"><form name="forma" action="module.php?module=<?=$MODULE?>" method="post">
	<INPUT TYPE="hidden" NAME="act" VALUE="add">

	<div>
		<div class="uk-display-inline-block uk-width-1-3@s">
			Email адрес получателя писем:<br><span class="comment">Можно указать несколько адресов через запятую.</span>
		</div>
		<input type="text" name="new_cfg_emal_admin" class="uk-width-expand" value="<?=$emails?>" size="50">
	</div>
	<div>
		<div class="uk-display-inline-block uk-width-1-3@s">
			Username:<br><span class="comment">Этот адрес будет использован как Username при SMTP авторизации, а также указан в строке отправителя письма.</span>
		</div>
		<input type="text" name="smtp_username" class="uk-width-expand" value="<?=@$cfg['smtp']['username']?>">
	</div>

	<h3>SMTP</h3>
	<p class="comment">Данные SMTP-сервера</p>

	<div>
		<div class="uk-display-inline-block uk-width-1-3@s">
			Host:
		</div>
		<input type="text" name="smtp_host" class="uk-width-expand" value="<?=@$cfg['smtp']['host']?>">
	</div>
	<div>
		<div class="uk-display-inline-block uk-width-1-3@s">
			Password:
		</div>
		<input type="text" name="smtp_password" class="uk-width-expand" value="<?=@$cfg['smtp']['password']?>">
	</div>

	<h3>Telegram</h3>
	<p class="comment">Настройки для получения копий писем в ТГ-бота</p>

	<div>
		<div class="uk-display-inline-block uk-width-1-3@s">
			Token:<br><span class="comment">Индивидуальный токен бота.<br>Как создать нового бота через <i>@botfather</i> и получить его токен есть куча  <a href="https://yandex.ru/search/?clid=2186621&text=%D0%BA%D0%B0%D0%BA%20%D1%81%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C%20%D0%B1%D0%BE%D1%82%20%D1%87%D0%B5%D1%80%D0%B5%D0%B7%20botfather&lr=146&redircnt=1596791584.1" target="_blank" rel="nofollow">информации в инете</a>. </span>
		</div>
		<input type="text" name="tg_token" class="uk-width-expand" value="<?=@$tg_token?>">
	</div>
	<div>
		<div class="uk-display-inline-block uk-width-1-3@s">
			ID пользователя, группы или канала:<br><span class="comment"> <a href="https://yandex.ru/search/?text=%D1%83%D0%B7%D0%BD%D0%B0%D1%82%D1%8C%20id%20%D0%BA%D0%B0%D0%BD%D0%B0%D0%BB%D0%B0%20telegram&lr=146&clid=2186621&src=suggest_B" target="_blank" rel="nofollow">ID канала</a>. </span>
		</div>
		<input type="text" name="tg_chat_id" class="uk-width-expand" value="<?=@$cfg['tg']['chat_id']?>">
	</div>

	<div>
		<input type="submit" name="" value="Сохранить">
	</div>

	</form></div><!-- .content -->

	<?php
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
	$cfg['tg']['token']= filter_var($_POST['tg_token']);

	// *Save new token
	if(isset($_POST['tg_token']) && $_POST['tg_token'] !== $tg_token)
	{
		if(file_exists($tokenPathname)){
			copy($tokenPathname, "$tokenPathname.bak");
		}
		$tokenDB->set(['tg'=> $cfg['tg']['token']]);
		// $tokenDB->set(['tg'=> ['token'=>$cfg['tg']['token']]]);
	}

	$mailToAdminStorage->set($cfg );
	// $mailToAdminStorage->set('cfg',
	// 	json_encode($cfg, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
	// );
	echo'<div class="msg">Настройки успешно сохранены</div>
	<p><a href="module.php?module='.$MODULE.'"><<Назад</a></p>';
?>

<script type="text/javascript">
// setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 5000);
</script>
<?php
}
