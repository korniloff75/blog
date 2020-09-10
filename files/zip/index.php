<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/system/global.dat';


// *input handler
if(!empty($Module = filter_var($_GET['module'])))
{
	header('Location: /' . $kff::getPathFromRoot(
		$kff::getZipModule($Module)
	));
	die;
}
?>

<h1>Archives</h1>

<p><a href="screens/" target="_blank">Скрины</a></p>

<?php
require_once DR.'/'.$kff::$dir.'/cpDir.class.php';

if($kff::is_adm())
{
	echo '<h2>Modules kff_</h2>
	<p>Отключённые модули не выводятся</p>';
	// *Вывод всех модулей
	echo '<ul>';
	foreach(new GlobIterator(DR.'/modules/kff_*') as $kff_fi)
	{
		$ini = parse_ini_file($kff_fi->getPathname().'/info.ini');
		if(!empty($ini['disable']))
			continue;

		$name = explode('.',$kff_fi->getFilename())[0];
		echo "<li><a href=\"?module=$name\">" . $kff_fi->getFilename() . "</a> UPD - " . date("d.m.Y H:i:s", $kff_fi->getMTime()) . ", Size - " . round(cpDir::getSize($kff_fi->getPathname())/10.24)/100 . " kB</li>";
	}
	echo '</ul>';
}

echo '<h2>Downloads</h2>';

// *Вывод готовых архивов
$Zips = new GlobIterator(__DIR__.'/*.zip');

echo '<ul>';
foreach($Zips as $fi)
{
	$name = explode('.',$fi->getFilename())[0];
	echo "<li><a href=\"?module=$name\">" . $fi->getFilename() . "</a> UPD - " . date("d.m.Y H:i:s", $fi->getMTime()) . ", Size - " . round(cpDir::getSize($fi->getPathname())/102.4)/10 . " kB</li>";
}
echo '</ul>';

echo '<h2>Others</h2>';

$Txts = new GlobIterator(__DIR__.'/*.txt');


foreach($Txts as $n=>$fi)
{
	$nameArr = explode('.',$fi->getFilename());
	$name = array_diff($nameArr, ['txt']);
	$name = implode('.', $name);

	echo "<hr><h3>$name</h3>
	<pre><code>"
	. htmlspecialchars(
		file_get_contents($fi->getPathname())
	)
	. "</code></pre>";

}

?>

<script src="/modules/kff_highlight/hl.js"></script>