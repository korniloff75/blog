<h6 id="comments_name"><?=$this->Title?></h6>

<form name="comments_form" id="comments_form" hidden action="" method="POST">
	
	<div class="flex">
		<!-- <div class="min700 name">Имя : </div> -->
		<label for="name">Имя</label>
		<input type="text" class="item-block" required="required" size="30" name="name" id="name" value="<?=$user? $user : $_POST['name']??''?>" placeholder="Имя">
	</div>

	<div class="flex">
		<div class="min700 name">Почта * (для обратной связи) : </div>
		<input type="text" class="item-block" size="30" name="email" id="email" value="<?=$user? $_SESSION['auth']['data'][1] : $_POST['email']??''?>" placeholder="email">
	</div>

	<div class="flex-wrap">
		<div class="min700 name">
			Сообщение *:
			<div id="bb_bar" class="flex-inline-block"></div>
		</div>
		<div class="item-block">
			<p>Вы можете ввести <span class=strong id="maxLen"><?=Comments::MAX_LEN?></span> символов</p>
			<textarea name="entry" id="entry" required="required" rows="7" onkeyup="commFns.countChars.call(this, $('#maxLen')[0], event)"><?=$_POST['entry']??''?></textarea>
		</div>
	</div>
</form>

<div id="sm_bar" class="right"></div>

<div class="container right" style=" margin-top: 20px;">
<input id="c_subm" type="button" class="button" value="Добавить">
</div>
