<?php
require('../system/global.dat');
require('./include/start.dat');
if($status=='admin'){
	if($act=='index'){?>
		<div class="header">
			<h1>Уведомления системы</h1>
		</div>
		<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a></div>
		<div class="content">
		<script type="text/javascript">
		var clearlog = '<div class="a">' +
			'Уведомления автоматически чистятся как только размер их хранилища превысит 100кб.' +
			'</div><div class="b">' +
			'<a class="button" href="notifications.php?act=clear">Очистить сейчас</a> <button type="button" onclick="closewindow(\'window\');">Закрыть</button>' +
			'</div>';

		</script>
		
		<div class="slog" id="scroll" style="">
		<?php
			$file = file('../data/cfg/notifications.dat');
			foreach($file as $key => $value){
				$arr = explode('|',trim($value));
				echo'<div style="color: '.$arr[0].';">'.$arr[1].' - '.$arr[2].'</div>';
			}
		?>
		</div>
		<script type="text/javascript">
			window.onload = function(){
				document.getElementById('scroll').scrollTop = document.getElementById('scroll').scrollHeight;
			}
		</script>
		<button type="button" onClick="openwindow('window', 680, 'auto', clearlog);" title="Создать новый файл в текущей папке">Очистить уведомления</button> 
		</div>
		
		<?php
		
	}
	if($act=='clear'){
		filefputs('../data/cfg/notifications.dat', '', 'w+');
		System::notification('Выполнена полная очистка уведомлений', 'y');
		echo'<div class="msg">Уведомления успешно очищены</div>';
		?>
		<script type="text/javascript">
			setTimeout('window.location.href = \'notifications.php?\';', 3000);
		</script>
		<?php
	}
	
}else{
echo'<div class="msg">Необходимо выполнить авторизацию</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'index.php?\';', 3000);
</script>
<?php
}
require('include/end.dat');
?>