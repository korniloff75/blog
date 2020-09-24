<?php

/**
 * ! required Index_my_addon
 * @param pathname todo ...
 * *Usage
 * $newZip = new Pack;
 * ?optional
 * ? Pack::dest = '/path/to/destFolder';
 * ? Pack::excludes[] = '\.zip';
 * ? Pack::$my_engine_format = 1;
 * ???
 * $newZip->Directory('/path/to/source');
 * OR
 * $newZip->RecursiveDirectory('/path/to/source');
 * @return archivePathname
 */

class Pack

{
	private static $log;

	public static
		$dest = DR . '/files/zip',
		$excludes = ['\.log$', '__$', 'cfg\.', 'token', 'categories\.json'],
		$my_engine_format = false;

	public
		$single = 1;

	function __construct (?string $pathname=null)

	{
		if(
			!class_exists('ZipArchive')
			|| !class_exists('Index_my_addon')
		)
			throw new Exception("Чего-то не хватает!", 1);

		global $log;
		self::$log = &$log;

		// нужен для Internet Explorer, иначе Content-Disposition игнорируется
		if(ini_get('zlib.output_compression'))  ini_set('zlib.output_compression', 'Off');

		if(
			!is_dir(static::$dest)
			&& !mkdir(static::$dest, 0755, true)
		)
			die("Не удаётся создать static::$dest !");


		# класс для работы с архивами
		$this->zip = new ZipArchive;

		/* if(!$pathname) return;

		$this->single= isset($_REQUEST['single']);

		// *Папка назначения
		static::$dest= $_REQUEST['dest'] ?? static::$dest;

		if(isset($_REQUEST['recurse']))
		{
			$this->nameZIP = $this->RecursiveFolder($filename);
		}
		else
		{
			$this->nameZIP = $this->folder($filename);
		}
 */

	} // __construct


	/*
	Упаковка содержимого директории без рекурсии
	*/

	function Directory (string $pathdir) :string
	{

		return $this->_Pack(
			new FilesystemIterator ($pathdir, FilesystemIterator::SKIP_DOTS), $pathdir
		);

	}

	function RecursiveDirectory (string $pathdir) :string
	{

		return $this->_Pack(
			new RecursiveIteratorIterator (
				new RecursiveDirectoryIterator($pathdir, FilesystemIterator::SKIP_DOTS)
			), $pathdir
		);

	}


	private function _Pack ($iter, string $pathdir)
	{
		$excludes = '~'.implode('|',static::$excludes).'~u';

		$pathdir = Index_my_addon::fixSlashes($pathdir);

		$nameZIP = Index_my_addon::translit(static::$dest . '/' . basename($pathdir) . (!$this->single ? ('_' . date("Ymd_His")):'')) .'.zip';

		// *Под Админом - перепаковываем
		if(!Index_my_addon::is_adm() && file_exists($nameZIP))
			return $nameZIP;

		self::$log->add(__METHOD__,null,[basename(static::$dest) .'___'. end(explode(DIRECTORY_SEPARATOR,realpath($pathdir)))  .'___'. $nameZIP . '<hr>']);


		# создаем архив, если все прошло удачно продолжаем
		if ( !$this->zip->open($nameZIP, ZipArchive::OVERWRITE | ZipArchive::CREATE )) eself::$log->add('Не работает $this->zip->open');


		foreach ($iter as $fileInfo)
		{
			$name = Index_my_addon::fixSlashes($fileInfo->getPathname());

			if(preg_match($excludes, $name))
				continue;

			// *Пакуем с добавлением корневой папки
			if(static::$my_engine_format)
			{
				$zipName = str_replace(
					dirname($pathdir).'/',
					'',$name
				);
			}
			else
			// *Напрямую в архив
			{
				$zipName = str_replace(
					$pathdir.'/',
					'',$name
				);
			}


			self::$log->add("dirname(\$pathdir)=".dirname($pathdir)."; \$name={$name}; \$pathdir={$pathdir}; <hr> \$zipName = $zipName");

			$this->zip->addFile($fileInfo, $zipName) or die ("<br>ERROR: Could not add file: $fileInfo in " . __FILE__ . ' : ' . __LINE__);
		}

		$this->zip->close();

		return $nameZIP;
	}


	/*
Удаляем лишние резервные копии
*/
// todo ...
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
