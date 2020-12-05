<?php
require('../system/global.dat');
ob_start($Config->gzip?'ob_gzhandler':null); 
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header('X-XSS-Protection: 0');

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

function absPath($t){
	return str_replace('..','',$t);
}

if($status=='admin'){
	if(isset($_GET['dir'])){$dir = htmlspecialchars($_GET['dir']);}
	if(isset($_POST['dir'])){$dir = htmlspecialchars($_POST['dir']);}
	if(isset($_GET['file'])){$file = htmlspecialchars($_GET['file']);}
	if(isset($_POST['file'])){$file = htmlspecialchars($_POST['file']);}
	if(isset($_GET['id'])){$id = htmlspecialchars($_GET['id']);}
	if(isset($_POST['id'])){$id = htmlspecialchars($_POST['id']);}

	if(empty($dir)){ $dir='../files'; }

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body{
	font-family: sans-serif;
	font-size: 14px;
	margin:0;
	padding:0;
}
.up{
	background-color:#ddd;
	border: 1px solid #dadada;
	padding: 15px;
}
.directory{
	padding: 3px 1px;
}
.directory a{
	color: #1976D2;
}
.content{overflow: hidden;}
.content a{
	position: relative;
	display: block;
	float: left;
	height: 90px;
	overflow: hidden;
	background-color:#222;
	text-align: center;
	margin: 0 5px 5px 0;
}

.content a img{
	height: 100%;
}
.content a span{
	display: block;
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	width: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	color: #fff;
	font-size: 12px;
	padding: 2px;
}
.msg{
	text-align: center;
	padding: 50px 0;
}
</style>
</head>
<body>
<?php
	if($act == 'upload'){
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
setTimeout('window.location.href = \'iframefiles.php?id=<?php echo $id;?>&dir=<?php echo $dir;?>\';', 3000);
</script>
<?php
	}


	if($act == 'index'){
		
		echo'<div class="up">
			<form name="uploadform" enctype="multipart/form-data" action="iframefiles.php?id='.$id.'" method="post">
				<INPUT TYPE="hidden" NAME="act" VALUE="upload">
				<INPUT TYPE="hidden" NAME="dir" VALUE="'.$dir.'">
				<INPUT TYPE="file" NAME="userfile"> <input type="submit" name="" value="Загрузить">
			</form>
		</div>
		<div class="directory">
		<span>Директория:</span> <a href="iframefiles.php?id='.$id.'&amp;dir=..">'.SERVER.'</a> / ';
		$new_dir_url='..';
		$new_dir_url_arr = explode('/', $dir);
		foreach($new_dir_url_arr as $value){
			if($value == '..') continue;
			if($value == '.') continue;
			$new_dir_url = $new_dir_url.'/'.$value;
			echo'<a href="iframefiles.php?id='.$id.'&amp;dir='.$new_dir_url.'">'.$value.'</a> / ';
		}
		echo'</div>';

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
				$count = 0;
				echo'<div class="content">';
				foreach($param as $key => $value)
				{
					if(is_dir($dir.'/'.$key))
					{
						++$count;
						echo'<a href="iframefiles.php?id='.$id.'&amp;dir='.$dir.'/'.$key.'"><img src="include/dir.png" alt=""><span>'.$key.'</span></a>';
					}
					if(is_file($dir.'/'.$key) && is_img($key))
					{
						++$count;
						echo'<a href="javascript:void(0);" title="Вставить" onclick="window.parent.document.getElementById(\''.$id.'\').value=\''.absPath($dir.'/'.$key).'\'; window.parent.closewindow(\'window\');">
						<img src="'.$dir.'/'.$key.'" alt=""><span>'.$key.'</span></a>';
						
					}
				}
				if($count == 0) echo'<div class="msg">В этой папке нет элементов для отображения</div>';
				echo'</div>';
			}
		}else{
			echo'<div class="msg">Папки или файла не найдено</div>';
		}
		
	}
	echo'</body></html>';
}
?>