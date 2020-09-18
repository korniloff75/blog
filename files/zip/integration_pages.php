<?php
require_once __DIR__.'/fns.php';

// $Storage = getStorage();
// *pathname to imgs folder
$Folder = DR."/files/slider/{$Page->id}";
// die('FUCK!');

// $log->add('Folder=',null,[$Storage, $Page->id, $Folder]);

ob_start();

$log->add("\$kff::\$cfg['uk']['include_uikit']",null,[$kff::$cfg['uk']['include_uikit']]);

// *UIkit не подключён
if(
	file_exists($Folder)
)
{
	BlogKff::addUIkit();
}


// *Выводим изображения из Uploads::$pathname
if($Folder !== __DIR__)
{
	scanImgs($Folder);
}



function scanImgs(string $dir)
{
	if(!file_exists($dir))
		return;

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