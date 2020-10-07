<h5 id="comments_name"><?=$this->Title?></h5>

<div id="form-outbox">

	<div id="form-container">

		<!-- Attr hidden will remove from comments.js -->
		<form name="comments_form" id="comments_form" hidden action="" method="POST">

			<div class="flex-column">
				<label for="entry">Сообщение *: </label>

				<div class="item-block">
					<p>Вы можете ввести <span class=strong id="maxLen"><?=Comments::MAX_LEN?></span> символов</p>
					<textarea name="entry" id="entry" required="required" rows="7" onkeyup="commFns.countChars.call(this, $('#maxLen')[0], event)"><?=$_POST['entry']??''?></textarea>
				</div>

				<div class="flex-justify-around">
					<div id="bb_bar"></div>
					<div id="sm_bar"></div>
				</div>
			</div>

			<div>
				<!-- <div class="min700 name">Имя : </div> -->
				<label for="name">Имя</label>
				<input type="text" class="item-block" name="name" id="name" value="<?=$user? $user : $_POST['name']??''?>" placeholder="Имя">
			</div>

			<div>
				<label for="email">Почта * (для обратной связи) : </label>
				<input type="text" class="item-block" required="required" name="email" id="email" value="<?=$user? $_SESSION['auth']['data'][1] : $_POST['email']??''?>" placeholder="email">
			</div>

			<div class="container right" style=" margin-top: 20px;">
				<input id="c_subm" type="button" class="button" style="width: 100%;" value="Добавить">
			</div>

			<h6 style="margin: 0;text-align: center; font-size:1.1em;">По желанию:</h6>
			<!-- <p style="margin: 0;text-align: center;" class="bold">По желанию:</p> -->

			<div class="flex-justify-between">
				<div>
					<label for="tg">Телеграм</label>
					<input type="text" class="item-block" name="tg" id="tg" value="<?=$user? $_SESSION['auth']['data'][1] : $_POST['tg']??''?>" placeholder="@NicName">
				</div>
				<div>
					<label for="Site">Сайт</label>
					<input type="text" class="item-block" name="Site" id="Site" value="<?= $_POST['Site']??''?>" placeholder="http://site.net">
				</div>
				<div>
					<label for="CMS">CMS</label>
					<input type="text" class="item-block" name="CMS" id="CMS" value="<?= $_POST['CMS']??''?>">
				</div>

			</div>
		</form>




	</div>

	<div id="response">
		<!-- Response from server -->
	</div>

</div>
