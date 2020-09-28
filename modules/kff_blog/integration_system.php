<?php
class BlogKff extends Index_my_addon
{
	protected static
		$modDir,
		// *default
		$def_cfg = [
			'name'=> 'Блог',
			'ext'=>'.dat'
		],
		// *Локальный конфиг
		$l_cfg,
		$storagePath = \DR.'/kff_blog_data',
		$catPath,
		$catsDB,
		$catDataKeys = ['name','id','items'],
		$artBase= [],
		$mapCreated= false;


	public function __construct()
	{
		global $kff;

		// *Директория модуля от DR
		self::$modDir = $kff::getPathFromRoot(__DIR__);

		$this->blogDB = new DbJSON(__DIR__.'/cfg.json');

		self::$l_cfg= $this->blogDB->get();

		if(
			!file_exists(self::$storagePath)
			&& !mkdir(self::$storagePath, 0755, 1)
		) die(__METHOD__.': Невозможно создать директорию хранилища');

		// self::prepare();

		if(empty(self::$l_cfg))
			$this->blogDB->replace(self::$def_cfg);

		// * self::$catsDB с категориями
		self::_defineCatsDB();

		if(!$this->_InputController())
			self::addUIkit();

		// self::$log->add('self::$cfg=',null, [self::$cfg]);

	} // __construct


	/**
	 * *Обработка внешних запросов
	 * Методы контроллера с префиксом c_
	 */
	protected function _InputController()
	{
		if(!self::is_adm()) return false;

		$r = &$_REQUEST;
		if(!empty($r['name']) && method_exists($this, ($m_name = "c_{$r['name']}")))
		{
			if(is_string($r['opts']))
				$r['opts'] = json_decode($r['opts'],1);
			$this->opts = @$r['opts'];
			return $this->{$m_name}(filter_var($r['value']));
		}
		return false;
	}


	protected static function _defineCatsDB()
	{
		self::$catPath = self::$storagePath.'/categories.json';
		$catsDB= &self::$catsDB;
		$catsDB= $catsDB ?? new DbJSON(self::$catPath);

		// *Перезаписываем список категорий
		// note Последовательность теряется
		if(!$catsDB->count()) foreach(new FilesystemIterator(self::$storagePath, FilesystemIterator::SKIP_DOTS|FilesystemIterator::KEY_AS_FILENAME| FilesystemIterator::UNIX_PATHS) as $filename=>$catFI){
			if(!$catFI->isDir()) continue;
			$catsDB->push ($filename);
		}
		self::$log->add(__METHOD__,null,['$filename'=>$filename, '$catsDB'=>$catsDB]);
	}


	/**
	 * @param artPathname - путь к файлу статьи
	 * если не передан - вычисляем текущую из URI
	 */
	public static function getArtDB($artPathname=null)
	{
		global $Page;
		// self::$log->add(__METHOD__." \$artPathname= $artPathname");
		$artPathname= $artPathname ?? str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];
		$catPathname= dirname($artPathname);
		$catId= basename($catPathname);
		$artId= basename($artPathname, self::$l_cfg['ext']);

		if(empty(trim($catId))){
			self::$log->add(__METHOD__ . "\$catId is EMPTY! \$artPathname= $artPathname; \$artId= $artId" ,E_USER_WARNING,[$artPathname, $artId]);
			return;
		}

		if(
			/* !file_exists($artPathname)
			|| $artPathname === self::$storagePath */
			$catPathname === \DR
		){
			self::$log->add(__METHOD__.': $catPathname is not VALID!',Logger::BACKTRACE,['$Page->id'=>$Page->id,'$artPathname'=>$artPathname,'$catId'=>$catId]);
			// note Устранение конфликтов
			return new DbJSON;
		}

		$dbPath= $catPathname ."/$artId.json";

		// self::$artBase[$catId][$artId]= self::$artBase[$catId][$artId] ?? new DbJSON($dbPath);

		$db= &self::$artBase[$catId][$artId];

		if(empty($db)){
			$db= new DbJSON($dbPath);
		}

		// if(empty($db->get('title'))) $db->set(['title'=>$db->get('name'), 'test'=>1]);
		if(empty($db->get('title'))) $db->set(['title'=>$db->get('name')]);

		return $db;
	}


	/**
	 * *Получаем категорию по id
	 * @return Array
	 */
	public static function getCategoryDB($catId)
	:DbJSON
	{
		$db= &self::$artBase[$catId]['self'];

		if(empty($db)){
			$db= new DbJSON(self::$storagePath . "/$catId/data.json");
		}

		if(!$db->count())

		return $db;
	}


	/**
	 * *Создаём карту блога
	 * @return Array with objects DbJSON
	 *
	 */
	private static function _createBlogMap()
	:DbJSON
	{
		$map= new DbJSON(self::$storagePath.'/map.json');
		if(self::$mapCreated && $map->count())
			return $map;

		$map->clear();

		self::_defineCatsDB();

		// *Перебираем категории
		foreach(self::$catsDB->get() as $catNum=>$catId){

			// *Собираем элемент и добавляем в нумерованный массив
			$catDB= self::getCategoryDB($catId);
			// $catDB= new DbJSON("$catPathname/data.json");

			// *Проверяем ключи - очистка карты от рудиментов
			/* foreach($catDB->getKeys() as $key){
				if(!in_array($key, self::$catDataKeys)){
					$catDB->remove($key);
				}
				// elseif(empty($catDB->get($key)))
			} */

			// ???
			// self::$log->add(__METHOD__.' excess '. $catDB->get('name'),null,[self::$catDataKeys, $catDB->getKeys(), array_diff(self::$catDataKeys, $catDB->getKeys())]);

			// *fix to olders
			if(!$catDB->get('id')) $catDB->push($catId, 'id');
			// var_dump($cat);

			// *Массив с базой категории добавляем в карту
			$map->push($catDB->get()) ;

		} // foreach

		// self::$log->add(__METHOD__.' BlogMap',null,[$map]);
		new Sitemap($map);
		// *Защита от повторных вызовов
		self::$mapCreated = 1;
		// die;
		return $map;
	}


	public static function getBlogMap()
	:DbJSON
	{
		$mapPath= self::$storagePath.'/map.json';

		// !test
		// return self::_createBlogMap();

		$map= new DbJSON($mapPath);

		if(!$map->count()){
			$map= self::_createBlogMap();
		}

		// self::$log->add(__METHOD__.' BlogMap',null,[$map->get(), /* $map->get('Novaya') */]);

		return $map;
	}


	/**
	 * *Получаем UIkit
	 */
	public static function addUIkit()
	{
		// *UIkit подключён
		if(
			!empty(self::$cfg['uk']['include_uikit'])
		) return;

		$UIKpath = '/'. self::$internalModulesPath . '/kff_uikit-3.5.5';
		?>

		<!-- UIkit CSS -->
		<link rel="stylesheet" href="<?=$UIKpath?>/css/uikit.min.css" />

		<!-- UIkit JS -->
		<script src="<?=$UIKpath?>/js/uikit.min.js"></script>
		<!-- /UIkit -->

		<?php
		self::$cfg['uk']['include_uikit'] = 0;
	}

	public function __destruct()
	{
		// unset(self::$artBase);
		return false;
	}

}