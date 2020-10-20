<?php
date_default_timezone_set('Europe/Moscow');

require_once __DIR__ . "/kff_custom/traits/Helpers.trait.php";


class Index_my_addon implements BasicClassInterface
{
	use Helpers;

	const ADM_FOLDER_NAME= 'admin';

	public static
		// $log = false,
		$tmp,
		// *Путь к kff_custom
		$dir,
		$internalModulesPath,
		$modulesPath,
		$Config,
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

		define('BASE_URL', (self::is('https')?'https':'http') . '://' . \HOST);

		spl_autoload_register([__CLASS__,'_autoloader']);

		// require_once __DIR__.'/kff_custom/Logger.php' ;
		// *Logger
		self::$log = new Logger('kff.log', DR);

		// *Расширяем системные настройки
		self::$Config= self::getSystemConfig();

		// require_once DR .'/'. self::$dir.'/DbJSON.php';
		self::$cfgDB = new DbJSON(DR.'/data/cfg/kff.json');
		if(!self::$cfgDB->count()){
			self::$cfgDB->set((new DbJSON(__DIR__.'/cfg.json'))->get());
		}

		// *Настройки Basic
		self::$cfg = self::$cfgDB->get();

		self::$modulesPath = '/modules';
		// *Path to internal modules
		self::$internalModulesPath = self::getPathFromRoot(__DIR__).'/modules';

		self::headHtml();

		// *Подключаем класс для админки
		if(self::is_admPanel())
		{
			// *Корректировка системы
			AdmPanel::fixSystem();

			AdmPanel::addResponsive();
		}

		self::$log->add(__METHOD__,null, ['REQUEST_URI'=>\REQUEST_URI, __CLASS__.'::$cfg'=>self::$cfgDB->get(), /* '$Config'=>self::$Config */]);

	}


	// *Автозагрузка классов
	private function _autoloader($class) {
		if(file_exists($path= DR.'/'. self::$dir . "/$class.class.php")){
			require_once $path;
		}
		// note подгрузка трейтов не работает
		elseif(file_exists($path= DR.'/'. self::$dir . "/traits/$class.trait.php")){
			require_once $path;
		}
	}


	/**
	 * Common config
	 */
	public static function getSystemConfig()
	{
		global $Config;
		$Config= $Config ?? json_decode(file_get_contents(DR.'/data/cfg/config.dat'));

		// $Config= self::$Config ?? new DbJSON(DR.'/data/cfg/config.dat');
		return $Config;

		$Config::$defaultDB= [
			'testKey'=>'testValue',
			'header'=>'Заголовок',
			'template'=>'deftpl',
			'slogan'=>'Добро пожаловать на наш сайт',
			'wysiwyg'=>'ckeditor_4.5.8_standard',
			'slashRule'=>1,
			'gzip'=>0,
			'adminEmail'=>'example@example.com',
			'adminStyleFile'=>'style_blue.css',
			'timeAuth'=>1800,
			'timeZone'=>'default',
			'adminPassword'=>'d36d27e3aca875fe3e0d929e3ec6e91b', // 123
			'ticketSalt'=>123,
			'uriRule'=>1,
			'ipBan'=>[],
			'registration'=>1,
			'userEmailChecked'=>1,
			'userEmailFilterList'=>1,
			'userNewPassword'=>1,
			'indexPage'=>'index',
			'userEmailChange'=>1,
			// ''=>,
		];

		/* self::$log->add(__METHOD__,null, ['$Config->kff'=>$Config->kff, 'empty($Config->kff)'=>empty($Config->kff)]);

		if(empty($Config->kff)){
			self::$cfgDB = new DbJSON(__DIR__.'/cfg.json');
			$Config->set(['kff'=>self::$cfgDB->get()]);
		} */


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
		<script async src="'.$kffJsPath.'/kff.js"></script>
		';

		if(!empty(self::$cfgDB->uk['include_uikit']))
		{
			$addonsPages .= '
			<!-- UIKit from '.$UIKitPath.' -->
			<link rel="stylesheet" href="'.$UIKitPath.'/css/uikit.min.css" />
			<script async src="'.$UIKitPath.'/js/uikit.min.js"></script>
			';

			if(
				!empty(self::$cfgDB->uk['include_picts'])
				|| self::is_adm()
			)
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


	public static function is_adm ()
	{
		return $GLOBALS['status'] === 'admin';
	}


	public static function is_admPanel ()
	{
		global $Page;
		// self::$log->add(__METHOD__,null,['is_admPanel'=>empty($Page)]);
		// return empty($Page);
		return explode('/', \REQUEST_URI)[1] === self::ADM_FOLDER_NAME;
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


	public function __destruct()
	{
		if(!self::$log) return;

		self::$log->add(__METHOD__,null,['self::is_admPanel()'=>self::is_admPanel(), realpath('.')]);
		// var_dump($GLOBALS['log']);
	}
}

$kff = new Index_my_addon();

$log = $kff::get_log();

// $log->add(basename(__FILE__),null,['$URI'=>$URI, '$Page'=>$Page]);
