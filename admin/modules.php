<?php
require('../system/global.dat');
require('./include/start.dat');

if($status=='admin'){
	if($act=='index'){
?>
<script type="text/javascript">
var uploadform = '<form name="uploadform" enctype="multipart/form-data" action="modules.php?" method="post"><div class="a">'+
	'<INPUT TYPE="hidden" NAME="act" VALUE="up">'+
	'<INPUT TYPE="file" NAME="userfile"></div>'+
	'<div class="b"><input type="submit" name="" value="Загрузить и установить"> <button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div></form>';
var init = '<div class="a">'+
	'Если какие-нибудь расширения были установлены не через панель администратора, то для их нормальной работы необходимо выполнить инициализацию'+
	'</div>'+
	'<div class="b"><button type="button" onClick="window.location.href = \'modules.php?act=init\';">Инициализировать</button> <button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';	
function dell(m, n){
return '<div class="a">Подтвердите удаление расширения: <i>' + n + '</i></div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'modules.php?act=dell&amp;module='+m+'&amp;titleshow=off\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}

</script>
<?php
		echo'
		<div class="header">
			<h1>Управление расширениями</h1>
		</div>
		<div class="menu_page">
			<a class="link" href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', uploadform);">Загрузить и установить расширение</a>
			<a class="link" href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', init);">Инициализация расширений</a>
			<a class="link" href="//my-engine.ru/extensions" target="_blank">Посмотреть каталог расширений на веб сайте разработчиков &#8594;</a>
			
		</div>
		<div class="content">
		
		<table class="tables">
		<tr>
			<td class="tables_head" style="" colspan="2">Название</td>
			<td class="tables_head" style="">Версия</td>
			<td class="tables_head" style="">Разработчик</td>
			<td class="tables_head" style="">Веб сайт</td>
			<td class="tables_head" style="">Расположение</td>
			<td class="tables_head" style="">&nbsp;</td>
		</tr>';
		
		$modules = System::listModules();
		foreach($modules as $value){
			$info = Module::info($value);
			echo'<tr>
				<td class="img"><img src="include/module.svg" alt=""></td>
				<td><a href="modules.php?act=info&amp;module='.$value.'">'.$info['name'].'</a></td>
				<td>'.$info['version'].'</td>
				<td>'.$info['developer'].'</td>
				<td>'.($info['site']!=''?'<a href="//'.$info['site'].'" target="_blank">'.$info['site'].'</a>':'').'</td>
				<td>modules/'.$value.'</td>
				<td style="text-align: right;">'.($info['delete']?'<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\''.$value.'\', \''.$info['name'].'\'));">Удалить</a>':'').'</td>
			</tr>';
		}
		echo'</table></div>';
	}

	if($act=='info'){
		$module = htmlspecialchars($_GET['module']);
		if(Module::exists($module)){
			$info = Module::info($module);
			echo'<div class="header">
				<h1>Управление расширениями</h1>
			</div>
			<div class="menu_page"><a href="modules.php">&#8592; Вернуться назад</a></div>
			<div class="content">';

			echo'<h2>'.$info['name'].'</h2>
			'.($info['version']!=''?'<p>Версия: '.$info['version'].'</p>':'').'
			'.($info['developer']!=''?'<p>Разработчик: '.$info['developer'].'</p>':'').'
			'.($info['site']!=''?'<p>Веб сайт: <a href="//'.$info['site'].'" target="_blank">'.$info['site'].'</a></p>':'').'
			<p>Расположение: <a href="files.php?dir=../modules/'.$module.'">modules/'.$module.'</a></p>
			<h3>Описание</h3>
			'.($info['description']!=''?$info['description']:'<p>Описание отсутствует</p>').'';

			if($info['version_history']){
				echo'<h3>История версий</h3>';
				foreach($info['version_history'] as $key => $value){
					echo'<h4>Версия '.$key.'</h4>';
						echo'<div style="padding-left: 40px; ">'.$value.'</div>';
					
				}

			}


			echo'</div>';
			
		}else{
			echo'<div class="msg">Ошибка, расширение не найдено.</div>';
		}
	}
	
	if($act=='dell'){
		$module = htmlspecialchars($_GET['module']);
		if(Module::exists($module)){
			$info = Module::info($module);
			if($module == $Config->template){
				System::notification('Попытка удаления работающего шаблона '.$info['name'].'', 'g');
				echo'<div class="msg">Работающий шаблон удалять нельзя, выберите в настройках другой шаблон для<br>вывода и повторите попытку удаления.</div>';
			}elseif(Module::delete($module) && $info['delete']){
				System::notification('Удалено расширение '.$info['name'].'', 'g');
				echo'<div class="msg">Расширение успешно удалено</div>';
			}else{
				System::notification('Ошибка при удалении расширения '.$info['name'].', нету доступа к некоторым файлам или папкам', 'r');
				echo'<div class="msg">Ошибка, расширение не удаленно или удалено не полностью</div>';
			}
			System::initModules();
		}else{
			System::notification('Ошибка при удалении расширения, расширение не найдено', 'r');
			echo'<div class="msg">Ошибка, расширение не удаленно</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'modules.php?\';', 3000);
</script>
<?php
	}


	
	if($act=='init'){
		System::initModules();
		System::notification('Выполнена инициализация расширений', 'g');
?>
<div class="msg">Инициализация выполнена успешно</div>
<script type="text/javascript">
setTimeout('window.location.href = \'modules.php?\';', 3000);
</script>
<?php
	}
	
	
	
	if($act=='up'){
		if(isset($_FILES['userfile'])){
			if($_FILES['userfile']['error'] == '0'){
				$file_name = $_FILES['userfile']['name'];
				if(move_uploaded_file($_FILES['userfile']['tmp_name'], '../files/'.$file_name)){
					chmod('../files/'.$file_name, 0777);
					
					if(class_exists('ZipArchive')){
						$zip = new ZipArchive;
						if($zip->open('../files/'.$file_name) === TRUE){
							$zip->extractTo('../modules/');
							$dirindex = explode('/',$zip->getNameIndex(0));
							$zip->close();
							$extracted = true;
							System::notification('Распакован архив '.$file_name.', использован ZipArchive', 'g');
						}else{
							$extracted = false;
							System::notification('Ошибка при распаковке архива '.$file_name.', использован ZipArchive', 'r');
						}
					}else{
						require('../system/pclzip.lib.php');
						$zip = new PclZip('../files/'.$file_name);
						$list = $zip->extract(PCLZIP_OPT_PATH, '../modules/');
						if($list == 0){
							$extracted = false;
							System::notification('Ошибка при распаковке архива '.$file_name.', использован PclZip', 'r');
						}else{
							$dirindex = explode('/',$list[0]['stored_filename']);
							$extracted = true;
							System::notification('Распакован архив '.$file_name.', использован PclZip', 'g');
						}
					}
					
					
					if($extracted){
						$info = Module::info($dirindex[0]);
						System::notification('Установлено расширение '.$info['name'].'', 'g');
						echo'<div class="msg">Расширение успешно загружено и установлено</div>';
						System::initModules();
					}else{
						System::notification('Ошибка при установке расширения, не удалось распаковать архив', 'r');
						echo'<div class="msg">Ошибка извлечения архива</div>';
					}
					
					unlink('../files/'.$file_name);
				}else{
					System::notification('Ошибка при загрузке расширения, не удалось переместить файл из временной папки', 'r');
					echo'<div class="msg">Ошибка при загрузке расширения</div>';
				}
			}else{
				if($_FILES['userfile']['error'] == '1') System::notification('Ошибка при загрузке расширения, размер принятого файла превысил максимально допустимый размер, который задан настройками сервера', 'r');
				elseif($_FILES['userfile']['error'] == '3') System::notification('Ошибка при загрузке расширения, загружаемый файл был получен только частично', 'r');
				elseif($_FILES['userfile']['error'] == '4') System::notification('Ошибка при загрузке расширения, файл не был загружен', 'r');
				elseif($_FILES['userfile']['error'] == '6') System::notification('Ошибка при загрузке расширения, отсутствует временная папка', 'r');
				elseif($_FILES['userfile']['error'] == '7') System::notification('Ошибка при загрузке расширения, не удалось записать файл на диск', 'r');
				else System::notification('Ошибка при загрузке расширения, неизвестная ошибка', 'r');
				echo'<div class="msg">Ошибка при загрузке расширения</div>';
			}
		}else{
			System::notification('Ошибка при загрузке расширения, файл не получен', 'r');
			echo'<div class="msg">Ошибка при загрузке расширения</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'modules.php?\';', 3000);
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