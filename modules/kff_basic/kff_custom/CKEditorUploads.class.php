<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/system/global.dat';
require_once __DIR__.'/Uploads.class.php';

class CKEditorUploads extends Uploads
{
	public static
		// Разрешенные расширения файлов.
		$allow = ['jpg','jpeg','png','gif'],
		$input_name = 'files';

	private static $explorer= '<input /><button class="uk-button uk-button-small" onclick="kff.request(\'\',{name: \'addImgFolder\', value: this.previousElementSibling.value},[\'#explorer\'])">New folder</button>
	<span class="uk-button" onclick="kff.request(\'\',{name: \'createCKEditorBrowser\'}, [\'#existsImg\']);">/</span>';


	public static function scanImgs($pathname=null)
	{
		$pathname= $pathname ?? static::$pathname;

		if(!file_exists($pathname))
			return;

		$Imgs= '';

		foreach(
			new FilesystemIterator($pathname, FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS) as $imgFI
		){
			if($imgFI->isDir()){
				self::$explorer.= '<span class="uk-button" onclick="kff.request(\'\',{name: \'createCKEditorBrowser\', opts:{folder:'. $imgFI->getFilename() .'}}, [\'#existsImg\']);">'. $imgFI->getFilename() .'</span>';
				continue;
			}

			if(!in_array($imgFI->getExtension(),self::$allow))
				continue;

			$src= '/'. Index_my_addon::getPathFromRoot($imgFI->getPathname());
			$Imgs.= "<img uk-img data-src='$src' uk-tooltip title='$src' />".PHP_EOL;
		}
		return $Imgs;
	}


	public static function RenderBrowser()
	{
		global $Page;

		Index_my_addon::headHtml();

		$UIKpath = '/'. Index_my_addon::$internalModulesPath . '/kff_uikit-3.5.5';
		?>

		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<?php $Page->get_headhtml()?>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title><?=__CLASS__?></title>
			<!-- <script async src="/<?=Index_my_addon::$dir?>/js/kff.js"></script> -->
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
		<div class="uk-flex uk-flex-wrap">
			<div id="existsImg" class="uk-width-expand@s">
				<?=self::scanImgs();?>
			</div>

			<div id="explorer" class="uk-width-1-4@s uk-flex uk-flex-column">
				<h4>Explorer</h4>
				<?=self::$explorer?>
			</div>
		</div>

		<script>
		'use strict';
		var bar = document.getElementById('js-progressbar');

		UIkit.upload('.js-upload', {

			url: '?name=CKEditorUpload',
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

					bar.hidden= 0;
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
					bar.hidden= 1;

				}, 3000);

				setTimeout(location.reload.bind(location), UIkit.notification('Загрузка завершена', 'success').timeout);
				// .then(()=>location.reload());

			}

		});


		function closeBrowser( fileUrl ){
			window.top.close() ;
			window.top.opener.focus() ;
		}

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
			closeBrowser();
		};

		UIkit.util.on('#existsImg','click',function(e) {
			// console.log(e);
			var t= e.target,
				relPath= t.getAttribute('data-src');

			UIkit.modal.confirm("Выбрать файл для страницы?")
			.then(()=>{
				console.log('Выбрано',arguments);
				OpenFile(relPath);
			}, ()=>{
				console.log('Отменено',arguments);
				return false;
			});
		});

		UIkit.util.on(document, 'keydown', e=>e.keyCode === 27 && closeBrowser());

		</script>

		</body>
		</html>
		<?php
	}
}



// *Upload

/* $Upload = new CKEditorUploads(DR."/files/CKeditor");

echo $Upload->checkSuccess()? ('Файлы успешно загружены в ' . $Upload::$pathname): implode('<br>',$Upload->getResult());

$Upload::RenderBrowser(); */