<?php


if($status == 'admin'){
	

	if($act == 'customizeUpload'){
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
		if(isset($_GET['dir'])){$dir = htmlspecialchars($_GET['dir']);}
		if(isset($_POST['dir'])){$dir = htmlspecialchars($_POST['dir']);}
		if(isset($_GET['file'])){$file = htmlspecialchars($_GET['file']);}
		if(isset($_POST['file'])){$file = htmlspecialchars($_POST['file']);}

		if(empty($dir)){ $dir='files'; }

		$CKEditorFuncNum = htmlspecialchars($_GET['CKEditorFuncNum']);

		echo'<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
		<div class="menu_page">
		<span>Директория:</span> <a href="/customize?act=customizeUpload&amp;CKEditorFuncNum='.$CKEditorFuncNum.'&amp;dir=.">'.SERVER.'</a> / ';
		$new_dir_url='.';
		$new_dir_url_arr = explode('/', $dir);
		foreach($new_dir_url_arr as $value){
			if($value == '..') continue;
			if($value == '.') continue;
			$new_dir_url = $new_dir_url.'/'.$value;
			echo'<a href="/customize?act=customizeUpload&amp;CKEditorFuncNum='.$CKEditorFuncNum.'&amp;dir='.$new_dir_url.'">'.$value.'</a> / ';
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
				echo'<div class="content" style="overflow: hidden;">';
				foreach($param as $key => $value)
				{
					if(is_dir($dir.'/'.$key))
					{
						echo'<div style="float: left; width: 300;  margin: 0 5px 5px 0;">';
						echo'<a href="/customize?act=customizeUpload&amp;CKEditorFuncNum='.$CKEditorFuncNum.'&amp;dir='.$dir.'/'.$key.'"><img src="/modules/customize/dir1.png" alt="" style="width: 300;height: 240px;"><br>'.$key.'</a>';
						echo'</div>';
					}
					if(is_file($dir.'/'.$key) && is_img($key))
					{
						++$count;
						echo'<div style="float: left; width: 300;  margin: 0 5px 5px 0;">';
						echo'<a href="javascript:void(0);" onclick="window.opener.CKEDITOR.tools.callFunction( '.$CKEditorFuncNum.', \''.$dir.'/'.$key.'\'); window.close();">
						<img src="'.$dir.'/'.$key.'" alt="" style="width: 300;height: 240px;"><br>'.$key.'</a>';
						echo'</div>';
					}
				}
				if($count == 0) echo'В этой папке нет изображений';
				echo'</div>';
			}
		}else{
			echo'<div>Папки или файла не найдено</div>';
		}
		echo'</body></html>';
		exit;
	}
}

?>