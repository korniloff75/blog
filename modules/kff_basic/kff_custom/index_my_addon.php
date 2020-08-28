<?php
/**
 *note подключить к /index.php 1-й строкой
 ** require_once './kff_custom/index_my_addon.php';
 *note и удалить строку (не обязательно)
 ** require_once './system/global.dat' ;
 */

// ini_set('short_open_tag', 'On');


class Index_my_addon
{
	public static
		// $log = false,
		// *Путь к kff_custom
		$dir;

	private static $log = false;


	public function __construct()
	{
		// ini_set('display_errors', 1);
		// trigger_error('test');

		// *Отсекаем проверки комментов
		if(
			$is_comment_ajax = stripos($_SERVER['REQUEST_URI'], 'ajax/newcommentcheck')
			|| stripos($_SERVER['REQUEST_URI'], 'ajax/loadcomments')
		)
			return;


		// *Logger
		require_once __DIR__.'/Logger.php' ;
		self::$log = new Logger('kff.log', __DIR__.'/..');

		self::$dir = self::getPathFromRoot(__DIR__);

		self::$log->add('REQUEST_URI=',null, [$_SERVER['REQUEST_URI']]);

	}


	/**
	 * *Запускать из integration_pages.php
	 */
	public static function headHtml()
	{
		global $Page;
		$modulesPath = '/' . self::getPathFromRoot(DR.'/modules');
		$UIKitPath = $modulesPath . '/kff_uikit-3.5.5';
		$kffJsPath = '/' . self::$dir . '/js';

		$addons= '

		<!-- Start from '.__METHOD__.' -->
		<!-- Load UIKit from '.$UIKitPath.' -->
		<link rel="stylesheet" href="'.$UIKitPath.'/css/uikit.min.css" />
		<script src="'.$UIKitPath.'/js/uikit.min.js"></script>
		<!-- / UIKit -->
		<script src="'.$kffJsPath.'/kff.js"></script>
		<script src="'.$kffJsPath.'/jquery-3.3.1.min.js"></script>
		<!-- / Start from '.__METHOD__.' -->

		';

		// *Подключаем скрипты в страницы
		if(is_object($Page))
		{
			$Page->headhtml.= $addons;
		}

		// *Подключаем скрипты в админпанель
		if(!self::is_adm()) return;

		System::addAdminHeadHtml($addons);

		return $addons;
	}


	public static function get_log()
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

	// *Получаем путь относительно DR
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


	public static function is_adm ()

	{
		return $GLOBALS['status'] === 'admin';
	}


	/**
	 * *Преобразование массива в формат INI
	 */
	public static function arr2ini(array $a, array $parent = [])
	:string
	{
		$out = '';
		foreach ($a as $k => &$v)
		{
			if (is_array($v))
			{
				//*subsection case
				//merge all the sections into one array...
				$sec = array_merge((array) $parent, (array) $k);
				//add section information to the output
				$out .= '[' . join('.', $sec) . ']' . PHP_EOL;
				//recursively traverse deeper
				$out .= self::arr2ini($v, $sec);
			}
			else
			{
				if(is_string($v))
					$v= '"' . htmlspecialchars_decode($v) . '"';

				//*plain key->value case
				$out .= "$k=$v" . PHP_EOL;
			}
		}
		return $out;
	}


	public function __destruct()
	{
		// var_dump($GLOBALS['log']);
		if(self::is_adm())
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

$log = $kff::get_log();

$kff::headHtml();



// require_once $_SERVER['DOCUMENT_ROOT'].'/system/global.dat' ;

if(!defined('DR'))
	define('DR', $_SERVER['DOCUMENT_ROOT']);

// $log->add('$URL=',null,[$URL]);


// todo Разработать сохранение материалов по категориям
class EngineStorage_kff extends EngineStorage
{
	public
		$log,
		$prefix = 'cat_';
	private $cats;
	/**
	 * *Определение параметров
	 * @param storageDir - путь к родительской папке хранилища
	 */
	public function __construct($storage, $storageDir=null)
	{
		$this->log= Index_my_addon::get_log();

		if(file_exists(__DIR__.'/DbJSON.php'))
		{
			require_once __DIR__.'/DbJSON.php' ;
			parent::__construct($storage);
			if(!is_null($storageDir))
				$this->storageDir = $storageDir;
		}
		else
		{
			$this->log->add('DbJSON is not exist!', E_USER_WARNING);
		}
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
			mkdir($this->getPathName(), 0775, true);
		}

		if(!is_string($value))
		{
			$value= DbJSON::toJSON($value);
		}

		return filefputs($this->getPathName(). "/{$key}.dat", $value, $q);
	}
}