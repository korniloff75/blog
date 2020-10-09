<!-- <link rel="stylesheet" type="text/css" href="/<?=$kff::getPathFromRoot(__DIR__) ?>/fb_form.css" /> -->

<style>
.content main {
	padding: 1em !important;
}
.content main .contact{
	padding: 0;
}

.form-container div {
	display: flex;
	flex-wrap: wrap;
	flex-basis: 100%;
	align-items: center;
	align-content: center;
	margin: 15px 0;
}

.form-container div > * {
	flex: 4 1;
	padding: 2px 5px
}

.form-container div > *:first-child {
	flex: 1 1 15%;
}

.form-container div > input[type=submit] {
	flex-grow: 7;
}

.form-container label{
	text-transform:uppercase;
	font-size:10px;
	font-family:Tahoma,Arial,Sans-serif;
}
</style>

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

			<img id="loading" src="assets/img/ajax-load.gif" width="16" height="16" alt="loading" />

		</form>

	</div>

	<div id="response">

	</div>

</div>

<p>Благодаря внедрению <a href="https://core.telegram.org/api" rel="nofollow" target="_blank">Telegram API</a> с декабря 2019г. в исходный код сайта, ваши письма стали доходить ко мне значительно быстрее.</p>
<p>Если у вас установлен Telegram, можете <a href="https://t.me/js_master_bot">написать мне</a> через него.</p>



<script>
(function() {
	var form = document.forms['feedback-form'];

	form.onsubmit = function(e) {
		e.preventDefault();
		e.stopPropagation();

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
		  url: '/<?=$kff::getPathFromRoot(__DIR__) ?>/PHPMailer/handler.php',
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
		});
	}
	// console.log(formData);
})()
</script>
