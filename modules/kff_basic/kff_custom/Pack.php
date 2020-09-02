<?php
// Убрать рудименты !!!

class Pack

{
	private static $log;
	static $excludes = ['\.log$', '__$', 'cfg\.', 'token'];

	public
	$dest = DR . '/files/zip/',
	$single = 1;

	function __construct (?string $filename=null)

	{
		global $log;
		self::$log = &$log;
		if(!class_exists('ZipArchive'))
			throw new Exception("Нет класса ZipArchive", 1);

		// нужен для Internet Explorer, иначе Content-Disposition игнорируется
		if(ini_get('zlib.output_compression'))  ini_set('zlib.output_compression', 'Off');

		$this->noDate= isset($_REQUEST['noDate']);
		// *Папка назначения
		$this->dest= $_REQUEST['dest'] ?? $this->dest;

		if(
			!is_dir($this->dest)
			&& !mkdir($this->dest, 0755, true)
		)
			die("Не удаётся создать $this->dest !");


		# класс для работы с архивами
		$this->zip = new ZipArchive;

		if(!$filename) return;

		if(isset($_REQUEST['recurse']))
		{
			$this->nameZIP = $this->RecursiveFolder($filename, $dest, $noDate);
		}
		else
		{
			$this->nameZIP = $this->folder($filename);
		}


	} // __construct


	/*
	Упаковка содержимого директории без рекурсии
	Игнорируются: *.zip
	*/

	function folder (string $pathdir='../js/Diz_alt_LITE/') :string

	{
		$nameZIP = ($_REQUEST['nameZIP'] ?? $this->dest . basename($pathdir)) . '.zip';
		$pathdir = HOME . $pathdir;

		if(!\ADMIN && file_exists($nameZIP))
			return $nameZIP;

		$iter = new FilesystemIterator ($pathdir, FilesystemIterator::SKIP_DOTS);

		// var_dump($iter);

		if(!$this->zip->open($nameZIP, ZIPARCHIVE::OVERWRITE | ZipArchive:: CREATE )) die ('Произошла ошибка при создании архива' . __FILE__ . ' : ' . __LINE__ );


		# создаем архив, если все прошло удачно продолжаем

		# открываем папку с файлами

		foreach ($iter as $fn)
		{
			if(!$fn->isFile() || $fn->getExtension() === 'zip') continue;

			// echo '<pre>'; var_dump($fn->getPathname(), $fn->getExtension()); echo '</pre>';

			$this->zip->addFile($fn->getPathname(), $fn->getFilename());
		}

		$this->zip->close();
		// exit;

		return $nameZIP;

	} // folder


	/*
	Упаковка содержимого директории с рекурсией поддиректорий
	*/
	function RecursiveFolder ($pathdir)
	{
		$excludes = '~'.implode('|',static::$excludes).'~u';

		// $dirname =

		$nameZIP = Index_my_addon::translit($this->dest . basename($pathdir) . (!$this->single ? ('_' . date("Ymd_His")):'')) .'.zip';

		self::$log->add(__METHOD__,null,[basename($this->dest) .'___'. end(explode(DIRECTORY_SEPARATOR,realpath($pathdir)))  .'___'. $nameZIP . '<hr>']);


		# создаем архив, если все прошло удачно продолжаем
		if ( !$this->zip->open($nameZIP, ZipArchive::OVERWRITE | ZipArchive::CREATE )) eself::$log->add('Не работает $this->zip->open');


		# Создаем новый объект RecursiveDirectoryIterator
	//	echo basename($pathdir);
		$iter = new RecursiveDirectoryIterator($pathdir, FilesystemIterator::SKIP_DOTS);
		// Цикл по списку директории
		// Нужно создать новый экземпляр RecursiveIteratorIterator


		foreach (new RecursiveIteratorIterator($iter) as $fileInfo) {
			$name = $fileInfo->getPathname();
			$zipName = str_replace($pathdir,'',$name);
			if(preg_match($excludes, $name))
				continue;

			self::$log->add("{$name}___$zipName");

			$this->zip->addFile($fileInfo, $zipName) or die ("<br>ERROR: Could not add file: $fileInfo in " . __FILE__ . ' : ' . __LINE__);
		}

		$this->zip->close(); # закрываем архив

		return $nameZIP;
	}


	/*
Удаляем лишние резервные копии
*/
// todo
function actualQuantity ($pathdir, $nameCopy='articles') {
	global $cf, $rpsn;
	$pathdir= $rpsn . $pathdir;
	$dir= scandir($pathdir);
//	echo '$dir1= ' . print_r($dir) . '<br>';
	foreach($dir as $key=>$fn) {
		if(strpos($fn, $nameCopy)===false) unset($dir[$key]);
	}
	$dir= array_values($dir);

//	echo '$dir2= ' . print_r($dir) . '<br>';

	$unwanted= count($dir) - $cf['backup']['numberoffiles'];
	'<br>count($dir)= ' . count($dir) . '<br> $unwanted= ' . $unwanted . '<br>$cf[\'backup\'][\'numberoffiles\']= ' . $cf['backup']['numberoffiles'];

	if($unwanted<=0) return false;

	$o= '';
	for ($i = 0; $i < $unwanted; $i++) {
		if(unlink($pathdir . $dir[$i])) $o.= $pathdir . $dir[$i].'<br>';
	}
	return $o;
}

} // Pack
