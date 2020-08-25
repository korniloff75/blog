<?php
/**
 *note подключить к /index.php 1-й строкой
 ** require_once './kff_custom/index_my_addon.php';
 *note и удалить строку (не обязательно)
 ** require_once './system/global.dat' ;
 */

ini_set('short_open_tag', 'On');


class Index_my_addon
{
	public static
		$log = false,
		$dir;


	public function __construct()
	{
		// ini_set('display_errors', 1);
		// trigger_error('test');

		// *Отсекаем проверки комментов
		if(
			strpos(__DIR__, 'kff_custom')
			&& (!$is_comment_ajax = stripos($_SERVER['REQUEST_URI'], 'ajax/newcommentcheck')
			|| stripos($_SERVER['REQUEST_URI'], 'ajax/loadcomments'))
		)
		{
			require_once __DIR__.'/Logger.php' ;
			self::$log = new Logger('kff.log', __DIR__.'/..');

			self::$dir = self::getPathFromRoot(__DIR__);

			self::$log->add('REQUEST_URI=',null, [$_SERVER['REQUEST_URI']]);
		}
	}

	public function startLog()
	{
		return self::$log;
	}

	// *Переводим все слеши в Unix
	public static function fixSlashes(string $path)
	:string
	{
		$path = str_replace("\\", '/', $path);
		return preg_replace("#(?!https?|^)//+#", '/', $path);
	}

	// *Получаем путь относительно $_SERVER['DOCUMENT_ROOT']
	public static function getPathFromRoot(string $absPath)
	:string
	{
		return str_replace(self::fixSlashes($_SERVER['DOCUMENT_ROOT']) . '/', '', self::fixSlashes($absPath));
	}

	// *Реальный IP
	public static function realIP ()

	{
		return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
	}

	public function __destruct()
	{
		// var_dump($GLOBALS['log']);
		if($GLOBALS['status'] === 'admin')
		{
			// if(empty($_GET['dev']))
			if(self::$log && !self::$log::$printed)
			{
			?>
				<style>
				pre.log{
					background: #111;
					color: #3f3;
				}
				</style>
			<?php
				echo "<div id='logWrapper'>";
				// self::$log->__destruct();
				self::$log->print();
				echo "</div>";

				self::$log::$printed = 1;
			}
		}
	}
}

$kff = new Index_my_addon();

$log = $kff->startLog();


require_once $_SERVER['DOCUMENT_ROOT'].'/system/global.dat' ;

if(!defined('DR'))
	define('DR', $_SERVER['DOCUMENT_ROOT']);

// $log->add('$URL=',null,[$URL]);


class EngineStorage_kff extends EngineStorage
{
	public
		$prefix = 'cat_';
	private $cats;
	/**
	 * *Определение параметров
	 * @param storageDir - путь к родительской папке хранилища
	 */
	public function __construct($storage, $storageDir=null)
	{
		require_once __DIR__.'/DbJSON.php' ;
		parent::__construct($storage);
		if(!is_null($storageDir))
			$this->storageDir = $storageDir;
	}

	public function getPathName()
	:string
	{
		return $this->storageDir.'/'.$this->storage;
	}

	public function getCatsArr()
	:array
	{
		if(is_null($this->cats))
		{
			glob($this->getPathName() . "/{$this->prefix}*", GLOB_ONLYDIR );
		}
		return $this->cats;
	}

	// JSON_UNESCAPED_UNICODE

	//* Получение значения ключа
	public function get($key=null)
	{
		$path = $this->getPathName() . ($key? "/$key":'') . ".dat";
		// var_dump($path);
		return
			json_decode(
				file_get_contents(
					$path
				), 1
			);
	}

	//* Создание ключа
	public function set($key, $value, $q = 'w+')
	{
		if(!$this->exists())
		{
			mkdir($this->getPathName());
		}

		if(!is_string($value))
		{
			$value= DbJSON::toJSON($value);
		}

		return filefputs($this->getPathName(). "/{$key}.dat", $value, $q);
	}
}