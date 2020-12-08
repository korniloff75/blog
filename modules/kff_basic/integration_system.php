<?php
date_default_timezone_set('Europe/Moscow');

require_once __DIR__ . "/kff_custom/traits/Helpers.trait.php";


class Index_my_addon implements BasicClassInterface
{
	use Helpers;

	const ADM_FOLDER_NAME= 'admin';

	public static
		$tmp,
		$dir, // *Путь к kff_custom
		$internalModulesPath,
		$modulesPath,
		$Storage= DR.'/data/cfg',
		$Config,
		$cfgDB,
		$State, // *DbJSON с состояниями
		$cfg;

	protected static
		$admFolder,
		$log = false;


	public function __construct()
	{
		// ini_set('display_errors', 1);
		// trigger_error('test');

		self::$dir = self::getPathFromRoot(__DIR__) . '/kff_custom';

		define('BASE_URL', (self::is('https')?'https':'http') . '://' . \HOST);

		spl_autoload_register([__CLASS__,'_autoloader']);

		// *Logger
		self::$log = new Logger('kff.log', DR);

		// *Отсекаем
		if(
			// *проверки комментов
			$is_comment_ajax = stripos($_SERVER['REQUEST_URI'], 'ajax/newcommentcheck')
			|| stripos($_SERVER['REQUEST_URI'], 'ajax/loadcomments')
		){
			self::$log::$notWrite= 1;
			return;
		}

		self::$log->add(__METHOD__,null, ['REQUEST_URI'=>\REQUEST_URI]);

		// *Отсекаем на 404
		if(
			// *обращения к несуществующим файлам
			$has_ext = !file_exists(\DR.$_SERVER['SCRIPT_NAME']) && !self::is_admPanel() && strpos($_SERVER['REQUEST_URI'], '.')
		){
			self::$log->add(__METHOD__,null,['$has_ext'=>$has_ext, '$_SERVER'=>$_SERVER]);
			self::$log::$notWrite= 1;
			header(PROTOCOL.' 404 Not Found'); require(DR.'/pages/404.html');
			echo __FILE__;
			die('404');
		}

		// *Расширяем системные настройки
		self::$Config= self::getSystemConfig();

		// require_once DR .'/'. self::$dir.'/DbJSON.php';
		self::$cfgDB = new DbJSON(self::$Storage . '/kff.json');
		self::$State = new DbJSON(self::$Storage . '/kff_State.json');

		// note tmp
		if(!self::$cfgDB->count()){
			self::$cfgDB->set((new DbJSON(__DIR__.'/cfg.json'))->get());
		}

		// *Настройки Basic
		// ?deprecated
		self::$cfg = self::$cfgDB->get();

		self::$modulesPath = '/modules';
		// *Path to internal modules
		self::$internalModulesPath = self::getPathFromRoot(__DIR__).'/modules';

		// *Подключаем класс для админки
		if(self::is_admPanel())
		{
			self::headHtml();
			// *Корректировка системы
			AdmPanel::fixSystem();

			AdmPanel::addResponsive();
		}

		self::$log->add(__METHOD__ . 'kff_basic is constructed',null, [
			__CLASS__.'::$cfg'=>self::$cfgDB->get(),
			/* '$Config'=>self::$Config */
		]);

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
		// $Config= $Config ?? json_decode(file_get_contents(self::$Storage .'/config.dat'));

		$Config= self::$Config ?? new DbJSON(self::$Storage .'/config.dat');
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
	 * *Для обработки $Page - zапускать из integration_pages.php
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

		self::$log->add(__METHOD__,null, [
			// '$Page->headhtml'=>$Page->headhtml,
			// '$Page'=>$Page,
		]);

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

		<!-- From '.__METHOD__.' -->
			<!-- Load UIKit from '.$UIKitPath.' -->
			<link rel="stylesheet" href="'.$UIKitPath.'/css/uikit.min.css" />
			<script src="'.$UIKitPath.'/js/uikit.min.js"></script>
			<!-- UIkit picts-->
			<script src="'.$UIKitPath.'/js/uikit-icons.min.js"></script>
			<!-- / UIKit -->
			<script src="'.$kffJsPath.'/kff.js"></script>
			<script src="'.$kffJsPath.'/jquery-3.3.1.min.js"></script>
		<!-- / From '.__METHOD__.' -->

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

	/**
	 * *Возвращает имя папки админпанели
	 * папка должна содержать файл admin.trigger
	 */
	static function getAdmFolder()
	{
		$f= &self::$admFolder;
		if(!empty($f)) return $f;

		if(file_exists(DR.'/'. self::ADM_FOLDER_NAME)) $f= self::ADM_FOLDER_NAME;
		else foreach(new FilesystemIterator(\DR, FilesystemIterator::SKIP_DOTS| FilesystemIterator::KEY_AS_FILENAME| FilesystemIterator::UNIX_PATHS) as $fn=>$fFI){
			if($fFI->isDir() && file_exists(DR. "/$fn/admin.trigger")){
				$f= $fn;
				break;
			}
		}

		return $f;
	}


	public static function is_admPanel ()
	{
		$folder= explode('/', \REQUEST_URI)[1];

		return $folder === self::getAdmFolder();
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
