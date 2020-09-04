<style>
	* {
		/* max-width: 100%;
		box-sizing: border-box; */
	}
</style>

<!-- UIkit CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.5.5/css/uikit.min.css" />

<!-- UIkit JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.5.5/js/uikit.min.js"></script>


<?php
echo '<div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slider>
<div uk-slider-container>
<ul uk-slider-items uk-child-width-1-3@m uk-grid-match" uk-height-viewport="min-height: 300">';

// $iterator = new DirectoryIterator(__DIR__);
// $iterator->rewind();
$iterator = scandir(__DIR__);

foreach($iterator as $i)
{
	$fi = new SplFileInfo($i);
	if(
		$fi->isExecutable()
		|| strpos($fi->getExtension(), 'php') === 0
		|| $fi->isDir()
		// || $fi->isDot()
	) continue;

	// echo $_SERVER['DOCUMENT_ROOT'];

	$path = 'http://blog.js-master.ru' . str_replace(
		$_SERVER['DOCUMENT_ROOT'], '',
		str_replace('\\','/',$fi->getRealPath())
	);

	?>
	<li>
		<?#=$fi->getRealPath()?>
		<div class="uk-cover-container">
			<img src="<?=$path?>" alt="">
		</div>
	</li>
	<?php
}

echo '</ul>
</div>
</div>';