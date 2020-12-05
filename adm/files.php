<?php
require('../system/global.dat');
require('./include/start.dat');

function is_img($fname){
	$f = explode(".",$fname);
	$f = strtolower($f[count($f) - 1]);
	return 
	$f == 'gif'||
	$f == 'jpg'||
	$f == 'jpeg'||
	$f == 'png'||
	$f == 'ico'?true:false;
}

if($status=='admin'){
	
	if(isset($_GET['dir'])){$dir = htmlspecialchars($_GET['dir']);}
	if(isset($_POST['dir'])){$dir = htmlspecialchars($_POST['dir']);}
	if(isset($_GET['file'])){$file = htmlspecialchars($_GET['file']);}
	if(isset($_POST['file'])){$file = htmlspecialchars($_POST['file']);}
	
	
	if($act=='index'){
		if(empty($dir)){ $dir='../files'; }
?>
<script type="text/javascript">
var newfileform = '<form name="renamedirform" action="files.php?" method="post"><div class="a">'+
	'<INPUT TYPE="hidden" NAME="act" VALUE="new_file">'+
	'<INPUT TYPE="hidden" NAME="dir" VALUE="<?php echo $dir;?>">'+
	'Имя для нового файла &nbsp; <input style="max-width: 100%;" type="text" name="new_file_name" value="" autofocus>'+
	'</div>' +
	'<div class="b">'+
	'<input type="submit" name="" value="Создать"> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div></form>';	
var newdirform = '<form name="renamedirform" action="files.php?" method="post"><div class="a">'+
	'<INPUT TYPE="hidden" NAME="act" VALUE="new_dir">'+
	'<INPUT TYPE="hidden" NAME="dir" VALUE="<?php echo $dir;?>">'+
	'Имя для новой папки &nbsp; <input style="max-width: 100%;" type="text" name="new_dir_name" value="" autofocus><br>'+
	'</div>' +
	'<div class="b">'+
	'<input type="submit" name="" value="Создать"> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div></form>';	
	
var renamedirform = '<form name="renamedirform" action="files.php?" method="post"><div class="a">'+
	'<INPUT TYPE="hidden" NAME="act" VALUE="renamedir">'+
	'<INPUT TYPE="hidden" NAME="dir" VALUE="<?php echo $dir;?>">'+
	'Новое имя для текущей папки &nbsp; <input style="max-width: 100%;" type="text" name="new_name_dir" value="<?php echo basename($dir);?>" autofocus>'+
	'</div>' +
	'<div class="b">'+
	'<input type="submit" name="" value="Переименовать"> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div></form>';	
var uploadform = '<form name="uploadform" enctype="multipart/form-data" action="files.php?" method="post"><div class="a">'+
	'<INPUT TYPE="hidden" NAME="act" VALUE="upload">'+
	'<INPUT TYPE="hidden" NAME="dir" VALUE="<?php echo $dir;?>">'+
	'<INPUT TYPE="file" NAME="userfile"></div>'+
	'<div class="b"><input type="submit" name="" value="Загрузить"> <button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div></form>';
var chmodform = '<form name="form_chmod" action="" method="post"><div class="a">'+
	'Права доступа для выделенных папок или файлов &nbsp; <input type="text" name="chmod" value="0777" size="4" style="width: 100px; text-align: center;" autofocus> '+
	'</div>'+
	'<div class="b"><button type="button" onClick="SubmitFormChmod();">Изменить</button> <button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div></form>';
	
	

	
function infofile(file){
var imge = is_img(file)?'<div style="height: 150px; background-color:#000; padding: 20px; text-align: center;"><img style="max-width: 100%; max-height: 150px;"  src="'+file+'" alt=""></div>': ''; 
var style = is_img(file)?' style=" background-color:#000; color:#aaa; text-align: center;"': ''; 
return '<div class="a"'+ style +'>'+ imge +
	'Путь к файлу: ' +
	'<a href="'+file+'">'+ file.substr(2, file.length)+'</a><br>' +
	'</div>' +
	'<div class="b">'+
	
	'<button type="button" onClick="window.open(\'/files/'+file+'\', \'Скачивание\');">Скачать</button> '+
	'<button type="button" onClick="window.location.href = \'files.php?act=editor&amp;dir=<?php echo $dir;?>&amp;file='+file+'\';">Править</button> '+
	
	'<button type="button" onClick=" openwindow(\'window1\', 650, \'auto\', renamefileform(\''+file+'\'));">Переим.</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Закрыть</button>'+
	'</div>';
}

function basename( path ){parts = path.split( '/' ); return parts[parts.length-1];}
function renamefileform(file){
closewindow('window');
return '<form name="renamedirform" action="files.php?" method="post"><div class="a">'+
	'<INPUT TYPE="hidden" NAME="act" VALUE="renamefile">'+
	'<INPUT TYPE="hidden" NAME="dir" VALUE="<?php echo $dir;?>">'+
	'<INPUT TYPE="hidden" NAME="file" VALUE="'+file+'">'+
	'Новое имя для файла &nbsp; <input type="text" name="new_name_file" value="'+basename(file)+'" autofocus>'+
	'</div>' +
	'<div class="b">'+
	'<input type="submit" name="" value="Переименовать"> '+
	'<button type="button" onclick="closewindow(\'window1\');">Отмена</button>'+
	'</div></form>';
}

function is_img(file){
var a = file.split('.');  
var ext = a[a.length-1]; 
var r = (ext == 'gif' ||
	ext == 'GIF' ||
	ext == 'jpg' ||
	ext == 'JPG' ||
	ext == 'jpeg'||
	ext == 'JPEG'||
	ext == 'ico'||
	ext == 'png' ||
	ext == 'PNG')? true: false;
return r;
}
</script>
<?php
		echo'
		
		<div class="header">
			<h1>Управление файлами</h1>
			
		</div>
		
		<div class="menu_page">
		<span>Директория:</span> <a href="files.php?dir=..">'.SERVER.'</a> / ';
		$new_dir_url='..';
		$new_dir_url_arr = explode('/', $dir);
		foreach($new_dir_url_arr as $value){
			if($value == '..') continue;
			$new_dir_url = $new_dir_url.'/'.$value;
			echo'<a href="files.php?dir='.$new_dir_url.'">'.$value.'</a> / ';
		}
		echo'</div>
		<div class="content">
		
		
		
		
		<table style="width: 100%;">
		<tr>
			<td style="text-align: left;">
				<button type="button" onClick="openwindow(\'window\', 650, \'auto\', uploadform);" title="Загрузить файл в текущую директорию">Загрузить файл</button> 
				<button type="button" onClick="openwindow(\'window\', 650, \'auto\', newfileform);" title="Создать новый файл в текущей папке">Создать файл</button> 
				<button type="button" onClick="openwindow(\'window\', 650, \'auto\', newdirform);" title="Создать новую папку">Создать папку</button> 
				<button type="button" onClick="openwindow(\'window\', 650, \'auto\', renamedirform);" title="Переименовать текущую папку">Переим. папку</button> 
				
				
			</td>
			<td style="text-align: right;">
				<a href="javascript:void(0);" onClick="openwindow(\'window\', 650, \'auto\', chmodform);">Права доступа</a> &nbsp; 
				<a href="javascript:void(0);" onClick="SubmitFormDell();">Удалить выделенное</a>
			</td>
		</tr>
		</table>
		
		';
		
		
		if(file_exists($dir))
		{
			if(is_dir($dir))
			{
				$dir_array = scandir($dir);
				$param = array();
				foreach($dir_array as $file_name){
					if(( $file_name != '.')&&($file_name != '..')&& ($file_name != 'Thumbs.db')){
						$param[$file_name] = filectime($dir.'/'.$file_name);
					}
				}
				unset($dir_array);
				arsort($param);
				
				
				$nom = count($param);
				if($nom <= 1000 || isset($_GET['showdir'])){
					echo'<form name="form_checkbox" action="files.php?" method="post">
					<INPUT TYPE="hidden" NAME="act" VALUE="index">
					<INPUT TYPE="hidden" NAME="new_obj_chmod" VALUE="">
					<INPUT TYPE="hidden" NAME="dir" VALUE="'.$dir.'">
					
					<table class="tables">
					<tr>
						
						<td class="tables_head" colspan="2">Имя</td>
						<td class="tables_head">Размер</td>
						<td class="tables_head">Дата создания</td>
						<td class="tables_head">Доступ</td>
						<td class="tables_head"><INPUT TYPE="checkbox" NAME="control" VALUE="" onchange="checkAll(\'checkbox\');"></td>
					</tr>';
					
					if(!$nom)
					{
						echo'<tr>
						<td class="img">&nbsp;</td>
						<td>Папка пуста</td>
						<td>---</td>
						<td>---</td>
						<td>---</td>
						<td>---</td>
						</tr>';
					}
					$countDir = 0;
					$countFile = 0;
					$countSize = 0;
					foreach($param as $key => $value)
					{
							if(is_dir($dir.'/'.$key))
							{
								++$countDir;
								echo'<tr>
								<td class="img"><img src="include/dir.svg" alt=""></td>
								<td><a href="files.php?dir='.$dir.'/'.$key.'">'.$key.'</a></td>
								<td>---</td>
								<td>'.date("d.m.y H:i",$value).'</td>
								<td>'.substr(sprintf('%o', fileperms($dir.'/'.$key)), -4).'</td>
								<td><INPUT TYPE="checkbox" NAME="obj[]" VALUE="'.$dir.'/'.$key.'"></td>
								</tr>';
							}
							if(is_file($dir.'/'.$key))
							{
								++$countFile;
								$filesize = filesize($dir.'/'.$key);
								$countSize += $filesize;
								echo'<tr>
								<td class="img"><img src="include/'.(is_img($key)?'img':'file').'.svg" alt=""></td>
								<td><a href="javascript:void(0);" onclick="openwindow(\'window\', 700, \'auto\', infofile(\''.$dir.'/'.$key.'\'));">'.$key.'</a></td>
								<td>'.convert_size($filesize).'</td>
								<td>'.date("d.m.y H:i",$value).'</td>
								<td>'.substr(sprintf('%o', fileperms($dir.'/'.$key)), -4).'</td>
								<td><INPUT TYPE="checkbox" NAME="obj[]" VALUE="'.$dir.'/'.$key.'"></td>
								</tr>';
							}
					}
					echo'<tr>
						<td colspan="6" style="background-color: #eee; text-align:left;">Папок: '.$countDir.';&nbsp; Файлов: '.$countFile.';&nbsp; Размер файлов: '.convert_size($countSize).';</td>
					</tr>';
					echo'</table>
					
					
					</form>';
	?>
	<script type="text/javascript">
	function SubmitFormDell() {
		var c = '<div class="a">Подтвердите удаление выделенных элементов</div>' +
		'<div class="b">' +
		'<button type="button" onClick="document.form_checkbox.act.value = \'dell\'; form_checkbox.submit();">Удалить</button> '+
		'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
		'</div>';
		openwindow('window', 650, 'auto', c);
	}

	function SubmitFormChmod() {
		document.form_checkbox.act.value = "chmod";
		document.form_checkbox.new_obj_chmod.value = document.form_chmod.chmod.value;
		form_checkbox.submit();
	}

	function checkAll(chk_name) {
		var elements = document.form_checkbox.elements.length;
		if(document.form_checkbox.control.checked)
		{
			for(i=0; i<elements; i++)
			{
				if(document.form_checkbox.elements[i].type == chk_name)
				{
					document.form_checkbox.elements[i].checked = true;
				}
			}
		}else
		{
			for(i=0; i<elements; i++)
			{
				if(document.form_checkbox.elements[i].type == chk_name)
				{
					document.form_checkbox.elements[i].checked = false;
				}
			}
		}
	}
	</script>
	<?php
					
					
					
				}else{
					echo'<div class="msg">Эта папка содержит слишком много элементов, может потребоваться много времени для её отображения, это может привести к перегрузке и блокировки вашего хостинга.</div> 
					<div style="text-align: center;"><a href="files.php?dir='.$new_dir_url.'&amp;showdir=on">Все равно открыть эту папку</a></div>';
				}
			}else{
				echo'<div class="msg">Выбранная директория не является директорией</div>';
			}
		}else{
			echo'<div class="msg">Выбранной директории не существует</div>';
		}
		echo'</div>';
	}
	
	
	
	
	
	
	
	if($act=='editor'){
		if(is_file($file) && file_exists($file)){
				
				echo'
				<div class="header">
					<h1>Редактирование файла</h1>
				</div>
				<div class="menu_page">
				<span>Директория:</span> <a href="files.php?dir=..">'.SERVER.'</a> / ';
				$new_dir_url='..';
				$new_dir_url_arr = explode('/', $dir);
				foreach($new_dir_url_arr as $value){
					if($value == '..') continue;
					$new_dir_url = $new_dir_url.'/'.$value;
					echo'<a href="files.php?dir='.$new_dir_url.'">'.$value.'</a> / ';
				}
				echo basename($file);
				echo'</div>';
				
				if(!is_writable($file)){
					
					echo'<div class="error">Файл предназначен только для чтения, сохранение невозможно.</div>';
					
				}
				
				echo'<div class="content">';
				
				if(is_img($file)){
					echo'<div class="msg">Выбранный файл не предназначен для редактирования</div>';
					?><script type="text/javascript">
					setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
					</script><?php
				}else{
					echo'
					<form name="forma" action="files.php?" method="post" style="margin:0px; padding:0px;">
					<INPUT TYPE="hidden" NAME="act" VALUE="save_file">
					<INPUT TYPE="hidden" NAME="dir" VALUE="'.$dir.'">
					<INPUT TYPE="hidden" NAME="file" VALUE="'.$file.'">
					<div>
					<TEXTAREA NAME="editor" ROWS="20" COLS="90" class="editor" autofocus>'.htmlspecialchars(file_get_contents($file)).'</TEXTAREA>
					</div>
					<br>
					<input type="submit" name="" value="Сохранить"> &nbsp; <a href="files.php?dir='.$dir.'">Вернуться назад без сохранения</a>
					</form>';
				}
				echo'</div>';
		}else {
			echo'<div class="msg">Файл не найден</div>';
		}
	}
	if($act == 'save_file'){
		if(file_exists($file)){
			if(is_writable($file)){
				filefputs($file, $_POST['editor'], 'w+');
				System::notification('Отредактирован файл '.str_replace('..','',$file).'', 'g');
				echo'<div class="msg">Файл успешно сохранен</div>';
?>
<script type="text/javascript">
var nfa = '<div class="a" style="text-align: center;">'+
	'<a href="files.php?act=editor&amp;dir=<?php echo $dir;?>&amp;file=<?php echo $file;?>&amp;">Вернуться к редактированию файла</a><br><br>или<br><br><a href="files.php?dir=<?php echo $dir;?>&amp;">Вернуться к папке файла</a>'+
	'</div>' +
	'</form>';
setTimeout(function(){
	openwindow('window', 320, 'auto', nfa);
}, 1000);
</script>
<?php
				
				
			}else{
				System::notification('Ошибка при редактировании файла '.str_replace('..','',$file).', недостаточно прав доступа', 'r');
				echo'<div class="msg">Ошибка при сохранении файла, недостаточно прав доступа</div>';
			}
			
			
		}else{echo'<div class="msg">Файл не найден</div>';}
		
		?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 5000);
</script>
<?php
	}
	
	
	
	
	
	
	
	if($act=='new_file')
	{
		$new_file_name = $_POST['new_file_name'];
		if(!file_exists($dir.'/'.$new_file_name))
		{
			filefputs($dir.'/'.$new_file_name, '', 'w+');
			System::notification('Создан новый файл '.str_replace('..','',$dir.'/'.$new_file_name).'', 'g');
			echo'<div class="msg">Файл успешно создан</div>';
?>
<script type="text/javascript">
var nfa = '<div class="a" style="text-align: center;">'+
	'<a href="files.php?act=editor&amp;dir=<?php echo $dir;?>&amp;file=<?php echo $dir.'/'.$new_file_name;?>&amp;">Перейти к редактированию файла</a><br><br>или<br><br><a href="files.php?dir=<?php echo $dir;?>&amp;">Вернуться назад к папке</a>'+
	'</div>' +
	'</form>';
setTimeout(function(){
	openwindow('window', 320, 'auto', nfa);
}, 1000);
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 5000);
</script>
<?php			
		}else{
			echo'<div class="msg">Файл с таким именем уже существует</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
</script>
<?php
		}
	}
	
	
	if($act=='new_dir'){
		$new_dir_name = $_POST['new_dir_name'];
		
		if(mkdir($dir.'/'.$new_dir_name)){
			System::notification('Создана новая папка '.str_replace('..','',$dir.'/'.$new_dir_name).'', 'g');
			echo'<div class="msg">Папка успешно создана</div>';
		}else{
			System::notification('Ошибка при создании папки '.str_replace('..','',$dir.'/'.$new_dir_name).', нет прав доступа или недопустимое имя', 'r');
			echo'<div class="msg">Ошибка при создании</div>';
		}
		
		?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
</script>
<?php
	}
	
	
	
	if($act=='upload'){
		if(isset($_FILES['userfile'])){
			if($_FILES['userfile']['error'] == '0'){
				if(move_uploaded_file($_FILES['userfile']['tmp_name'], $dir.'/'.$_FILES['userfile']['name'])){
					System::notification('Загружен Файл '.str_replace('..','',$dir).'/'.$_FILES['userfile']['name'].'', 'g');
					echo'<div class="msg">Файл успешно загружен</div>';
				}else{
					System::notification('Ошибка при загрузке файла, не удалось переместить файл из временной папки', 'r');
					echo'<div class="msg">Ошибка при загрузке файла</div>';
				}
			}else{
				if($_FILES['userfile']['error'] == '1') System::notification('Ошибка при загрузке файла, размер принятого файла превысил максимально допустимый размер, который задан настройками сервера', 'r');
				elseif($_FILES['userfile']['error'] == '3') System::notification('Ошибка при загрузке файла, загружаемый файл был получен только частично', 'r');
				elseif($_FILES['userfile']['error'] == '4') System::notification('Ошибка при загрузке файла, файл не был загружен', 'r');
				elseif($_FILES['userfile']['error'] == '6') System::notification('Ошибка при загрузке файла, отсутствует временная папка', 'r');
				elseif($_FILES['userfile']['error'] == '7') System::notification('Ошибка при загрузке файла, не удалось записать файл на диск', 'r');
				else System::notification('Ошибка при загрузке файла, неизвестная ошибка', 'r');
				
				echo'<div class="msg">Ошибка при загрузке файла</div>';
			}
		}else{
			System::notification('Ошибка при загрузке файла, файл не был загружен', 'r');
			echo'<div class="msg">Ошибка при загрузке файла</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
</script>
<?php
	}
	
	if($act == 'renamefile'){
		$new_name_file = $_POST['new_name_file'];
		
		if(file_exists($file) && file_exists($dir.'/'.$new_name_file) == false)
		{
			if(rename($file, $dir.'/'.$new_name_file))
			{
				System::notification('Переименован файл '.str_replace('..','',$file).' в файл '.str_replace('..','',$dir.'/'.$new_name_file).'', 'g');
				echo'<div class="msg">Файл успешно переименован</div>';
			}else{
				System::notification('Ошибка переименовывания файла '.str_replace('..','',$file).' в файл '.str_replace('..','',$dir.'/'.$new_name_file).'', 'r');
				echo'<div class="msg">Файл не переименован</div>';
			}
		}else{
			System::notification('Ошибка переименовывания файла '.str_replace('..','',$file).' в файл '.str_replace('..','',$dir.'/'.$new_name_file).', возможно файл с новым именем уже существует или не найден исходный файл', 'r');
			echo'<div class="msg">Файл не переименован</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
</script>
<?php
	}
	
	
	
	if($act=='renamedir'){
		$new_name_dir = $_POST['new_name_dir'];
		
		$new_dir_url="..";
		$new_dir_url_arr = explode('/', $dir);
		
		$var = count($new_dir_url_arr)-1;
		for($i = 1; $i < $var; ++$i)
		{
				$new_dir_url.= '/'.$new_dir_url_arr[$i];
		}
		$new_dir_url = $new_dir_url.'/'.$new_name_dir;
		
		
		if(rename($dir, $new_dir_url)){
			System::notification('Переименована папка '.str_replace('..','',$dir).' в папку '.str_replace('..','',$new_dir_url).'', 'g');
			echo'<div class="msg">Папка успешно переименована</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $new_dir_url;?>\';', 3000);
</script>
<?php			
		}else{
			System::notification('Ошибка при переименовывании папки '.str_replace('..','',$dir).'/ в папку '.str_replace('..','',$new_dir_url).'/', 'r');
			echo'<div class="msg">Произошла ошибка при переименовывании папки</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
</script>
<?php			
		}
	}
	
	
	if($act=='dell'){
		$dlog = '';
		//удаление
		$nom = count($_POST['obj']);
		for($i = 0; $i < $nom; ++$i){
			if(is_dir($_POST['obj'][$i])){
				delldir($_POST['obj'][$i]);
				$dlog.= 'Удалена папка: '.$_POST['obj'][$i].'; ';
      	    }
			if(is_file($_POST['obj'][$i])){
				unlink($_POST['obj'][$i]);
				$dlog.= 'Удален файл: '.$_POST['obj'][$i].'; ';
			}
		}
		if($nom == 0){
			echo'<div class="msg">Нет выбранных элементов</div>';
		}else{
			System::notification(str_replace('..','',$dlog), 'g');
			echo'<div class="msg">Удаление успешно завершено</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
</script>
<?php
	}
	
	if($act=='chmod')
	{
		$new_obj_chmod = $_POST['new_obj_chmod'];
		$clog = '';
		$nom = count($_POST['obj']);
		
		for($i = 0; $i < $nom; ++$i){
			chmod($_POST['obj'][$i], octdec(intval($new_obj_chmod)));
			$clog.= $_POST['obj'][$i].'; ';
		}
		
		if($nom == 0){
			echo'<div class="msg">Нет выбранных элементов</div>';
		}else{
			echo'<div class="msg">Права доступа выставлены</div>';
			System::notification('Попытка выставить права доступа '.$new_obj_chmod.' следующим элементам: '.str_replace('..','',$clog), 'g');
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'files.php?dir=<?php echo $dir;?>\';', 3000);
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