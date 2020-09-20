<?php

if(!defined('DR'))
{
	define('DR', $_SERVER['DOCUMENT_ROOT']);
}


class Index_my_addon
{
	public static
		// $log = false,
		$tmp,
		// *Путь к kff_custom
		$dir,
		$internalModulesPath,
		$modulesPath,
		$cfgDB,
		$cfg;

	protected static $log = false;


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

		self::$dir = self::getPathFromRoot(__DIR__) . '/kff_custom';

		// *Logger
		require_once __DIR__.'/kff_custom/Logger.php' ;
		self::$log = new Logger('kff.log', DR);

		require_once DR .'/'. self::$dir.'/DbJSON.php';

		self::$cfgDB = new DbJSON(__DIR__.'/cfg.json');

		self::$cfg = self::$cfgDB->get();

		self::$modulesPath = '/modules';
		// *Path to internal modules
		self::$internalModulesPath = self::getPathFromRoot(__DIR__).'/modules';

		self::headHtml();

		// *Подключаем класс для админки
		if(self::is_admPanel())
		{
			require_once __DIR__.'/kff_custom/AdmPanel.class.php';
			// AdmPanel::$cfg = &self::$cfg;
			// AdmPanel::$cfgDB = &self::$cfgDB;

			// *Корректировка системы
			AdmPanel::fixSystem();

			AdmPanel::addResponsive();
		}

		self::$log->add('REQUEST_URI=',null, [$_SERVER['REQUEST_URI']]);
		self::$log->add(__CLASS__.'::$cfg=',null, [self::$cfg]);

	}


	/**
	 * @param pathname - имя модуля или полный путь к директории
	 */
	public static function getZipModule(string $pathname)
	{
		if(
			!file_exists($pathname)
			&& !file_exists($pathname = DR."/modules/$pathname")
		)
		{
			return false;
		}

		require_once DR.'/' . self::$dir.'/Pack.php';
		Pack::$dest = DR . '/files/zip';
		Pack::$excludes[] = '\.zip$';
		// *Пакуем с добавлением корневой папки
		Pack::$my_engine_format = 1;

		$pack = new Pack;

		return $pack->RecursiveDirectory($pathname);
	}


	public static function profile($rem='')
	:string
	{
		global $START_PROFILE;

		if(empty($START_PROFILE))
		{
			return '';
		}
		else
		{
			$info = '<p>Page generation - ' . round((microtime(true) - $START_PROFILE)*1e4)/10 . 'ms | Memory usage - now ( '. round (memory_get_usage()/1024) . ') max (' . round (memory_get_peak_usage()/1024) . ') kB</p>';

			return  "<div class='core info'><b>Used PHP-" . phpversion() . " Technical Info $rem </b>: $info</div>";
		}

	}


	public static function translit(string $s, $direct = 0)
	:string
	{
		$translit = [
		'а' => 'a', 'б' => 'b', 'в' => 'v','г' => 'g', 'д' => 'd', 'е' => 'e','ё' => 'yo', 'ж' => 'zh', 'з' => 'z','и' => 'i', 'й' => 'j', 'к' => 'k','л' => 'l', 'м' => 'm', 'н' => 'n','о' => 'o', 'п' => 'p', 'р' => 'r','с' => 's', 'т' => 't', 'у' => 'u','ф' => 'f', 'х' => 'x', 'ц' => 'c','ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh','ь' => '\'', 'ы' => 'y', 'ъ' => '\'\'','э' => 'e\'', 'ю' => 'yu', 'я' => 'ya', ' ' => '_',

		 'А' => 'A', 'Б' => 'B', 'В' => 'V','Г' => 'G', 'Д' => 'D', 'Е' => 'E','Ё' => 'YO', 'Ж' => 'Zh', 'З' => 'Z','И' => 'I', 'Й' => 'J', 'К' => 'K','Л' => 'L', 'М' => 'M', 'Н' => 'N','О' => 'O', 'П' => 'P', 'Р' => 'R','С' => 'S', 'Т' => 'T', 'У' => 'U','Ф' => 'F', 'Х' => 'X', 'Ц' => 'C','Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SHH','Ь' => '\'', 'Ы' => 'Y\'', 'Ъ' => '\'\'','Э' => 'E\'', 'Ю' => 'YU', 'Я' => 'YA',

		];

		if($direct) {
			$translit = array_flip(
				array_diff_key($translit, [
				'Ь' => 1, 'Ъ' => 1
			]));
		}

		return strtr($s, $translit);
	}


	/**
	 * *Для обработки $Page - апускать из integration_pages.php
	 */
	public static function headHtml()
	{
		global $Page;
		$UIKitPath = '/'.self::getPathFromRoot(__DIR__) . '/modules/kff_uikit-3.5.5';
		$kffJsPath = '/' . self::$dir . '/js';

		$addonsPages= '
		<!-- Start from '.__METHOD__.' -->
		<script src="'.$kffJsPath.'/kff.js"></script>
		';

		if(!empty(self::$cfg['uk']['include_uikit']))
		{
			$addonsPages .= '
			<!-- UIKit from '.$UIKitPath.' -->
			<link rel="stylesheet" href="'.$UIKitPath.'/css/uikit.min.css" />
			<script src="'.$UIKitPath.'/js/uikit.min.js"></script>
			';

			if(!empty(self::$cfg['uk']['include_picts']))
			{
				$addonsPages .= '
				<!-- UIkit picts-->
				<script src="'.$UIKitPath.'/js/uikit-icons.min.js"></script>';
			}

			$addonsPages .= '<!-- / UIKit -->';
		}

		$addonsPages .= '
		<script src="'.$kffJsPath.'/jquery-3.3.1.min.js"></script>
		<!-- / Start from '.__METHOD__.' -->

		';

		// *Подключаем скрипты в страницы
		if(is_object($Page))
		{
			$Page->headhtml.= $addonsPages;
		}

		elseif(!self::is_admPanel())
		{
			// echo $addonsPages;
			return;
		}

		// *Подключаем скрипты в админпанель
		$addonsAdm= '

		<!-- Start from '.__METHOD__.' -->
		<!-- Load UIKit from '.$UIKitPath.' -->
		<link rel="stylesheet" href="'.$UIKitPath.'/css/uikit.min.css" />
		<script src="'.$UIKitPath.'/js/uikit.min.js"></script>
		<!-- UIkit picts-->
		<script src="'.$UIKitPath.'/js/uikit-icons.min.js"></script>
		<!-- / UIKit -->
		<script src="'.$kffJsPath.'/kff.js"></script>
		<script src="'.$kffJsPath.'/jquery-3.3.1.min.js"></script>
		<!-- / Start from '.__METHOD__.' -->

		';

		System::addAdminHeadHtml($addonsAdm);

		return $addonsAdm;
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

	// *Путь относительно DR
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


	public static function is_admPanel ()
	{
		// return file_exists('./newpassword.php');
		return explode('/', \REQUEST_URI)[1] === 'admin';
	}


	/**
	 * *PageInfo
	 * данные по странице с id
	 * System::listPages()
	 */
	public static function getPageInfo($id, $storagePath=null)
	:array
	{
		$array = explode('<||>', file_get_contents(
			($storagePath ?? DR.'/data/pages/cfg_').$id.'.dat')
		);

		$array[7] = $array[7] ?? 'def/template';

		$a = array_combine(
			['name','title','keywords','description','show','module','time','template',], $array
		);

		$a['id'] = $id;
		return $a;
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
					$v= '"' . htmlspecialchars_decode($v, ENT_NOQUOTES) . '"';

				//*plain key->value case
				$out .= "$k=$v" . PHP_EOL;
			}
		}
		return $out;
	}


	public function __destruct()
	{
		if(!self::$log) return;

		self::$log->add('self::is_admPanel()',null,[self::is_admPanel(), realpath('.')]);
		// var_dump($GLOBALS['log']);
	}
}

$kff = new Index_my_addon();

$log = $kff::get_log();

// $log->add('$URI=',null,[$URI]);
