<?php

if(!defined('DR'))
{
	define('DR', $_SERVER['DOCUMENT_ROOT']);
}

class Index_my_addon
{
	public static
		// $log = false,
		// *Путь к kff_custom
		$dir,
		$internalModulesPath,
		$modulesPath,
		$cfgDB,
		$cfg;

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

		self::$dir = self::getPathFromRoot(__DIR__) . '/kff_custom';

		// *Logger
		require_once __DIR__.'/kff_custom/Logger.php' ;
		self::$log = new Logger('kff.log', DR);

		require_once DR .'/'. self::$dir.'/DbJSON.php';

		self::$cfgDB = new DbJSON(__DIR__.'/cfg.json');

		self::$cfg = self::$cfgDB->get();

		// self::$modulesPath = '/' . self::getPathFromRoot($_SERVER['DOCUMENT_ROOT'].'/modules');
		self::$modulesPath = '/modules';
		// *Path to internal modules
		self::$internalModulesPath = __DIR__.'/modules';

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
		Pack::$excludes[] = '\.zip$';
		// *Пакуем с добавлением корневой папки
		Pack::$my_engine_format = 1;

		$pack = new Pack;

		return $pack->RecursiveDirectory($pathname);
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
	 * ?Запускать из integration_pages.php
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

		// *Подключаем скрипты в админпанель
		elseif(!self::is_admPanel()) return $addonsPages;

		$addonsAdm= '

		<!-- Start from '.__METHOD__.' -->
		<!-- Load UIKit from '.$UIKitPath.' -->
		<link rel="stylesheet" href="'.$UIKitPath.'/css/uikit.min.css" />
		<script src="'.$UIKitPath.'/js/uikit.min.js"></script>
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

	public static function is_admPanel ()

	{
		// return file_exists('./newpassword.php');
		return explode('/', REQUEST_URI)[1] === 'admin';
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
		if(!self::$log) return;

		self::$log->add('self::is_admPanel()',null,[self::is_admPanel(), realpath('')]);
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

				if(self::is_admPanel())
				{
				?>
					<script>
					$('#main').append($('#logWrapper'));
					</script>
				<?php
				}

				self::$log::$printed = 1;
			}
		}
	}
}

$kff = new Index_my_addon();

$log = $kff::get_log();

$kff::headHtml();

/* System::addAdminEndHtml("
	<script>
	$('#main').append($('#logWrapper'));
	console.log(111);
	</script>
"); */

// $log->add('$URI=',null,[$URI]);


// todo Разработать сохранение материалов по категориям
// ? Changed at DbJSON
/* if(class_exists('EngineStorage'))
{
	require_once __DIR__.'/kff_custom/EngineStorage_kff.class.php';
} */
