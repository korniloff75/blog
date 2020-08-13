<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

if($act=='index'){
	echo'<div class="header"><h1>Настройки
		       <span style="float:left; padding-right:20px; margin-top:-7px;">
				<form action="https://money.yandex.ru/to/410012986152433" target="_blank">
				 <button type="submit"><img src="../modules/'.$MODULE.'/img_js_css/img.png" style="vertical-align: middle">&nbsp;&nbsp;Поддержать!</button>
				</form>
			   </span>
		</h1>
		</div>
	<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a> &nbsp;|&nbsp; <a href="../admin/modules.php?act=info&module=ckeditor_plus">О расширении</a></div>
	<div class="content">
	
	<img src="../modules/'.$MODULE.'/img_js_css/smile.jpg" style="height:160px;">
	
	</div>';
}

?>