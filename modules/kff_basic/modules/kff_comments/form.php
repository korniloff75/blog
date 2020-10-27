<h5 id="comments_name"><?=$this->Title?></h5>

<div id="form-outbox">

	<div id="form-container">

		<!-- Attr hidden will remove from comments.js -->
		<form name="comments_form" id="comments_form" hidden action="" method="POST" class="uk-flex-column">

			<div class="uk-flex-column uk-width-1">
				<label for="entry">Сообщение *: </label>

				<div class="item-block">
					<p>Вы можете ввести <span class=strong id="maxLen"><?=Comments::MAX_LEN?></span> символов</p>
					<textarea name="entry" id="entry" class="uk-resize-vertical" required="required" onkeyup="commFns.countChars.call(this, $('#maxLen')[0], event)"><?=$_POST['entry']??''?></textarea>
				</div>

				<div class="uk-flex-around">
					<div id="bb_bar"></div>
					<div id="sm_bar"></div>
				</div>
			</div>

			<div class="uk-flex-between uk-margin-small">
				<!-- <div class="min700 name">Имя : </div> -->
				<label for="name">Имя</label>
				<input type="text" class="item-block" name="name" id="name" value="<?=$user? $user : $_POST['name']??''?>" placeholder="Имя">
			</div>

			<div class="uk-flex-between">
				<label for="email">Почта * (для обратной связи) </label>
				<input type="text" class="item-block uk-width-expand" required="required" name="email" id="email" value="<?=$user? $_SESSION['auth']['data'][1] : $_POST['email']??''?>" placeholder="email">
			</div>

			<div class="right uk-margin">
				<input id="c_subm" type="button" class="button uk-width-expand" value="Добавить">
			</div>

			<h6 class="uk-margin-remove uk-text-center uk-h4">По желанию:</h6>
			<!-- <p style="margin: 0;text-align: center;" class="bold">По желанию:</p> -->

			<div class="uk-flex-wrap uk-flex-between uk-child-width-1-3@m">
				<div>
					<label for="tg" class="uk-width-1-3 uk-display-inline-block">Телеграм</label>
					<input type="text" class="item-block" name="tg" id="tg" value="<?=$user? $_SESSION['auth']['data'][1] : $_POST['tg']??''?>" placeholder="@NicName">
				</div>
				<div>
					<label for="Site" class="uk-width-1-3 uk-display-inline-block">Сайт</label>
					<input type="text" class="item-block" name="Site" id="Site" value="<?= $_POST['Site']??''?>" placeholder="http://site.net">
				</div>
				<div>
					<label for="CMS" class="uk-width-1-3 uk-display-inline-block">CMS</label>
					<input type="text" class="item-block" name="CMS" id="CMS" value="<?= $_POST['CMS']??''?>">
				</div>

			</div>
		</form>




	</div>

	<div id="response">
		<!-- Response from server -->
	</div>

</div>
