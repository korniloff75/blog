<?php

// $log->add('$Page',null,[$Page]);

ob_start();
?>

<script src="<?=$kff::$dir?>/js/kff.js"></script>

<link rel="stylesheet" type="text/css" href="/<?=$kff::getPathFromRoot(__DIR__) ?>/fb_form.css" />

<div id="form-outbox">

	<div class="form-container">

		<form id="feedback-form" name="feedback-form" method="post" action="/?module=php/modules/PHPMailer/handler.php">
			<input type="hidden" name="captcha" id="captcha"  value="<?=$kff::realIP()?>" />
			<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
			<div>
				<label for="name">Имя</label>
				<input type="text" name="name" id="name"  required />
			</div>

			<div>
				<label for="email">Email</label>
				<input type="text" name="email" id="email" required />
			</div>

			<div>
				<label for="tg">Telegram</label>
				<input type="text" name="tg" id="tg" placeholder="@NickName или https://t.me/yourLogin" />
			</div>

			<div>
				<label for="subject">Тема</label>
				<select name="subject" id="subject" required>
					<option value="" selected="selected"> - Выбрать -</option>
					<option value="Заказ">Заказ</option>
					<option value="Вопрос">Вопрос</option>
					<option value="Предложение">Предложение</option>
					<option value="Реклама">Реклама</option>
				</select>
			</div>

			<div>
				<label for="message">Сообщение</label>
				<textarea name="message" id="message" cols="35" rows="5" required></textarea>
			</div>

			<div>
				<label for="file">Вложение</label>
				<input type="file" name="file" />
			</div>

			<div>
				<input type="submit" name="submit" value="Отправить" />
				<input type="reset" name="reset" value="Очистить" />
			</div>

			<p style="text-align:center;"><img id="loading" src="<?=$kff::getPathFromRoot(__DIR__) ?>/img/ajax-load.gif" width="16" height="16" alt="loading" style="margin: auto;"/></p>
		</form>

	</div>

	<div id="response">

	</div>

</div>


<script>
'use strict';
// window.addEventListener('load', function() {
kff.checkLib('jQuery')
.then(function($) {
	var form = document.forms['feedback-form'],
		$loader = $('#loading');

	$loader.hide();

	form.onsubmit = function(e) {
		e.preventDefault();
		e.stopPropagation();

		$loader.show();

		var $form = $(this),
			$resp_node = $('#response'),
			formData = new FormData(this);

		$resp_node.removeClass();
		$resp_node.html('');

		if($resp_node.text()) {
			$resp_node.removeClass();
			$resp_node.addClass('error');
			$resp_node.append('<br>Введите корректные значения и повторите отправку.');
			return;
		}


		$.ajax({
		  // url: '/modules/feedback/PHPMailer/handler.php',
		  url: '/modules/<?=$Page->module?>/PHPMailer/handler.php',
		  data: formData,
		  processData: false,
		  contentType: false,
		  type: 'POST',
		})
		.done(function(response) {
			$resp_node.append(response);
			$form[0].elements.submit.disabled = 1;
		})
		.fail(function(response) {
			$resp_node.append('<div class="error">Сообщение не было отправлено. Попробуйте ещё раз.</div>');
		})
		.always(response=>{
			$loader.hide();
		});
	}
	// console.log(formData);
})
</script>
<?php
return ob_get_clean();