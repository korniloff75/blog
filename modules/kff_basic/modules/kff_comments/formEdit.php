<style>
#edit_comm {
	z-index: 10000;
	min-width: 350px;
	/* width: 95%; */
	max-width: 600px;
	min-height: 200px;
	background: #7e7;
}
#edit_comm .admin {
	border-width: 1px 1px 1px 5px;
	padding: 0;
	margin: .3em 0;
	font-size: .7em;
}
#edit_comm h2 {
	font-size: 20px;
}
</style>

<form name="edit_comm" id="edit_comm" action="" method="POST">

	<h2>Редактирование комментария</h2>

	<input name="ind" type="hidden" value="<?=$ind?>">
	<!-- <input name="cp" type="hidden" value="<?#=$_POST['cp']?>"> -->

	<p><span class="uk-width-1-2@s uk-display-inline-block">Дата: <input type="text" required="required" name="dt" value="<?=$u_date?>"></span><span class="uk-width-1-2@s uk-display-inline-block">IP: <input type="text" name="ip" value="<?=$u_ip?>"></span></p>

	<hr>

	<div>
		<div id="bb_bar" class="center" style="margin-top:15px;"></div>
		<div id="sm_bar"></div>
	</div>

	<p><input type="text" required="required" size="30" name="name" value="<?=$u_name?>"> <input type="text" size="30" name="email" value="<?=$u_email?>" placeholder="email"></p>
	<p><input type="text" size="30" name="homepage"  value="<?=$u_home?>" placeholder="http://"> <input type="text" size="30" name="CMS"  value="<?=$u_CMS?>" placeholder="CMS"></p>
	<p><textarea id="entry" required="required" name="entry" rows="7" style="width:100%;"><?=$u_mess?></textarea></p>
	<p><textarea name="otvet" rows="7" style="width:100%; height:100px;" placeholder="Ответ админа"><?=$u_otvet?></textarea></p>

	<div data-uk-margin class="uk-flex uk-flex-wrap uk-flex-middle uk-flex-center">
		<input type="button" onclick='commFns.Edit.save()' class="uk-button-small uk-button-primary uk-margin-right" value="Сохранить">

		ESC - <input type="button" onclick='commFns.Edit.$formEdit.hide()' title="Закрыть окно" class="uk-button-small" value="Отмена">

		<label><span class="uk-button-small"><input type="checkbox" class="uk-checkbox" name="sendToMail" onchange="this.value = +this.checked" title="Отправить уведомление" value=0> На почту</span></label>
	</div>

</form>