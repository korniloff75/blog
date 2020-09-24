<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/system/global.dat';
$UIKpath = '/'. $kff::$internalModulesPath . '/kff_uikit-3.5.5';
$Imgpath = DR."/files/CKeditor";

require_once DR.'/'.$kff::$dir.'/Uploads.class.php';
Uploads::$allow = ['jpg','jpeg','png','gif'];
Uploads::$pathname = $Imgpath;

$log->add('$act= '."$act, count(\$_FILES)= ".count($_FILES));

if($act === 'upload' && count($_FILES))
{
	if(!$kff::is_adm())
		die('Access denied to ' . __FILE__);

	Uploads::$input_name = 'files';

	$Upload = new Uploads;

	echo $Upload->checkSuccess()? ('Файлы успешно загружены в ' . Uploads::$pathname): 'Что-то пошло не так';
	// die;
}


if(file_exists(Uploads::$pathname))
{
	$Imgs= '';

	foreach(
		new FilesystemIterator(Uploads::$pathname, FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS) as $imgFI
	){
		if(!$imgFI->isFile() || !in_array($imgFI->getExtension(),Uploads::$allow))
			continue;

		$src= '/'. $kff::getPathFromRoot($imgFI->getPathname());
		$Imgs.= "<img src='$src' data-src='$src' uk-tooltip title='$src' />".PHP_EOL;
	}

	// $log->add('Imgs= '.$Imgs);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>kff.Explorer</title>
	<!-- UIkit CSS -->
	<link rel="stylesheet" href="<?=$UIKpath?>/css/uikit.min.css" />

	<!-- UIkit JS -->
	<script src="<?=$UIKpath?>/js/uikit.min.js"></script>
	<script src="<?=$UIKpath?>/js/uikit-icons.min.js"></script>
	<!-- /UIkit -->
	<style>
		#existsImg img{height:100px; cursor:pointer;}
	</style>
</head>


<body class="uk-flex uk-flex-column">
<div class="js-upload uk-placeholder uk-text-center uk-width-1 uk-height-medium@s uk-height-large@m ">
	<span uk-icon="icon: cloud-upload"></span>
	<span class="uk-text-middle">Перетащите изображения в это поле или воспользуйтесь</span>
	<div uk-form-custom>
			<input type="file" multiple>
			<span class="uk-link">менеджером</span>
	</div>
</div>

<progress id="js-progressbar" class="uk-progress uk-width-1" value="0" max="100" hidden></progress>

<!-- Существующие изображения -->
<div id="existsImg">
	<?=$Imgs?>
</div>

<script>
'use strict';
var bar = document.getElementById('js-progressbar');

UIkit.upload('.js-upload', {

	url: '',
	multiple: true,
	params: {
		act: 'upload'
	},

	beforeSend: function () {
			// console.log('beforeSend', arguments);
	},
	beforeAll: function () {
			// console.log('beforeAll', arguments);
	},
	load: function () {
			// console.log('load', arguments);
	},
	error: function () {
			// console.log('error', arguments);
	},
	complete: function () {
			// console.log('complete', arguments);
	},

	loadStart: function (e) {
			// console.log('loadStart', arguments);

			bar.removeAttribute('hidden');
			bar.max = e.total;
			bar.value = e.loaded;
	},

	progress: function (e) {
			// console.log('progress', arguments);

			bar.max = e.total;
			bar.value = e.loaded;
	},

	loadEnd: function (e) {
			// console.log('loadEnd', arguments);

			bar.max = e.total;
			bar.value = e.loaded;
	},

	completeAll: function (xhr) {
		// console.log('completeAll', arguments);

		setTimeout(function () {
			bar.setAttribute('hidden', 'hidden');

		}, 3000);

		UIkit.modal.alert('Загрузка завершена')
		.then(()=>location.reload());

		// *Обновляем страницу
		document.documentElement.innerHTML= xhr.response;

	}

});


/**
 * *Вставляем путь к выбранному изображению в CKEditor
 */
function OpenFile( fileUrl )
{
	var funcNum = window.top.location.search.replace(/.*\?/, "").split(/\&/)
	.reduce((acc,cur)=>{
		var arr= cur.split('=');
		acc[arr[0]]= decodeURIComponent(arr[1]);
		return acc;
	}, {})['CKEditorFuncNum'] ;

	window.top.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl);

	window.top.close() ;
	window.top.opener.focus() ;
};

UIkit.util.on('#existsImg','click',function(e) {
	// console.log(e);
	var t= e.target,
		relPath= t.getAttribute('data-src');

	UIkit.modal.prompt("Путь к файлу",relPath)
	.then(()=>{
		OpenFile(relPath);
	});
});

</script>
</body>
</html>