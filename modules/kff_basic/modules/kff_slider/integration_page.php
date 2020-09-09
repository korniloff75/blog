<?php
require_once __DIR__.'/fns.php';

$Storage = getStorage();
// *pathname to imgs folder
$Folder = DR."/files/slider/{$Page->id}";

// $log->add('Folder=',null,[$Storage, $Page->id, $Folder]);

ob_start();

// *UIkit не подключён
if(empty($kff::$cfg['uk']['include_uikit']))
{
	$UIKpath = '/modules/kff_basic/modules/kff_uikit-3.5.5';
	// $UIKpath = 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.5.5'
	?>

	<!-- UIkit CSS -->
	<link rel="stylesheet" href="<?=$UIKpath?>/css/uikit.min.css" />

	<!-- UIkit JS -->
	<script src="<?=$UIKpath?>/js/uikit.min.js"></script>

	<?php
}


// *Выводим изображения из Uploads::$pathname
if($Folder !== __DIR__)
{
	scanImgs($Folder);
}
// *Выводим изображения из каждой подпапки модуля в отдельный слайдер
/* else
{
	$iterator = new DirectoryIterator(__DIR__);
	foreach($iterator as $fi)
	{
		if(
			!$fi->isDir()
			|| $fi->isDot()
		) continue;

		echo "<h2>".$fi->getFilename()."</h2>";

		// echo $fi->getPathname();

		scanImgs($fi->getPathname());

	}
} */



function scanImgs(string $dir)
{
	$iterator = scandir($dir);
	// var_dump($iterator);
	?>

	<div uk-slider class="kff-slider uk-position-relative uk-visible-toggle uk-dark">
	<!-- <div class="uk-slider-container"> -->
	<div class="uk-slider-container">
		<ul class="uk-slider-items uk-child-width-2" >
		<!-- uk-grid-match uk-height-viewport="min-height: 200" -->

	<?php

	foreach($iterator as $i)
	{
		$fi = new SplFileInfo("$dir/$i");
		if(
			$fi->isExecutable()
			|| strpos($fi->getExtension(), 'php') === 0
			|| $fi->isDir()
			// || $fi->isDot()
		) continue;

		// echo $fi->getRealPath();

		// *fix 4 Win
		$path = str_replace(
			str_replace('\\','/',DR), '',
			str_replace('\\','/',$fi->getRealPath())
		);

		?>
		<li>
			<img src="<?=$path?>">
		</li>

	<?php
	}
	?>
		</ul>
		<a class="uk-position-center-left uk-position-medium uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
		<a class="uk-position-center-right uk-position-medium uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>

		<ul class="uk-slider-nav uk-dotnav uk-position-large uk-position-bottom-center"></ul>

		</div>
	</div>

	<?php
}


// *fix 4 Module
if(basename(__FILE__) === 'index.php')
	ob_end_flush();
else
	return ob_get_clean();