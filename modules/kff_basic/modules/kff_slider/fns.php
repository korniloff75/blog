<?php
function getStorage($filename = __DIR__.'/cfg.json')
{
	DbJSON::$convertPath = false;
	return new DbJSON($filename);
}


function showImgs($dir)
{
	if(
		is_null($dir)
		|| !file_exists($dir)
	) return;

	$iterator = scandir($dir);
	// var_dump($iterator);
	?>

	<div>

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
			<img src="<?=$path?>" style="height:55px;">

	<?php
	}
	?>

	</div>

	<?php
}