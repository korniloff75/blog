<?php

require_once __DIR__ . "/kff_custom/traits/Helpers.trait.php";


class Index_my_addon implements BasicClassInterface
{
	use Helpers;

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

		spl_autoload_register([__CLASS__,'_autoloader']);

		// require_once __DIR__.'/kff_custom/Logger.php' ;
		// *Logger
		self::$log = new Logger('kff.log', DR);

		// *Системные настройки
		self::$Config= self::getSystemConfig();

		// require_once DR .'/'. self::$dir.'/DbJSON.php';
		self::$cfgDB = new DbJSON(__DIR__.'/cfg.json');

		// *Настройки Basic
		self::$cfg = self::$cfgDB->get();

		self::$modulesPath = '/modules';
		// *Path to internal modules
		self::$internalModulesPath = self::getPathFromRoot(__DIR__).'/modules';

		self::headHtml();

		// *Подключаем класс для админки
		if(self::is_admPanel())
		{
			// require_once __DIR__.'/kff_custom/AdmPanel.class.php';
			// AdmPanel::$cfg = &self::$cfg;
			// AdmPanel::$cfgDB = &self::$cfgDB;

			// *Корректировка системы
			AdmPanel::fixSystem();

			AdmPanel::addResponsive();
		}

		self::$log->add('REQUEST_URI=',null, [$_SERVER['REQUEST_URI']]);
		self::$log->add(__METHOD__,null, [__CLASS__.'::$cfg'=>self::$cfg]);

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
		$Config= $Config ?? [];
		$def= [
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
		// $cfg= new stdClass;
		$Config = json_decode(file_get_contents(DR.'/data/cfg/config.dat'));
		$Config->__get= function($name){
			return $this->{$name} ?? $def[$name] ?? $Config->{$name} ?? null;
		};

		return $Config;
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


	public function __destruct()
	{
		if(!self::$log) return;

		self::$log->add(__METHOD__.' self::is_admPanel()',null,[self::is_admPanel(), realpath('.')]);
		// var_dump($GLOBALS['log']);
	}
}

$kff = new Index_my_addon();

$log = $kff::get_log();

// $log->add('$URI=',null,[$URI]);
