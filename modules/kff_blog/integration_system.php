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
		$blogDB,
		// *Локальный конфиг
		$l_cfg,
		$storagePath = \DR.'/kff_blog_data',
		$catPath,
		$catsDB,
		$catDataKeys = ['name','id','items'],
		// ?
		$artBase= [],
		$map;


	public function __construct()
	{
		// *Директория модуля от DR
		self::$modDir = self::getPathFromRoot(__DIR__);

		if(
			!file_exists(self::$storagePath)
			&& !mkdir(self::$storagePath, 0755, 1)
		) die(__METHOD__.': Невозможно создать директорию хранилища');

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
		if(self::$catsDB) return;

		self::getBlogMap();

		self::$blogDB = new DbJSON(__DIR__.'/cfg.json');
		self::$l_cfg= self::$blogDB->get();
		if(!self::$blogDB->count())
			self::$blogDB->replace(self::$def_cfg);

		self::$catPath = self::$storagePath.'/categories.json';
		$catsDB= &self::$catsDB;
		$catsDB= $catsDB ?? new DbJSON(self::$catPath);

		// *Перезаписываем список категорий
		// note Последовательность теряется
		if(!$catsDB->count()) foreach(new FilesystemIterator(self::$storagePath, FilesystemIterator::SKIP_DOTS|FilesystemIterator::KEY_AS_FILENAME| FilesystemIterator::UNIX_PATHS) as $filename=>$catFI){
			if(!$catFI->isDir()) continue;
			$catsDB->push ($filename);
			self::$log->add(__METHOD__,null,['$filename'=>$filename]);
		}
		self::$log->add(__METHOD__,null,['$catsDB'=>$catsDB]);
	}


	/**
	 * @param artPathname - путь к файлу статьи
	 * если не передан - вычисляем текущую из URI
	 */
	public static function getArtDB($artPathname=null)
	{
		global $Page;

		self::_defineCatsDB();
		// self::$log->add(__METHOD__." \$artPathname= $artPathname");
		$artPathname= $artPathname ?? str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];
		$catPathname= dirname($artPathname);
		$catId= basename($catPathname);
		$artId= basename($artPathname, self::$l_cfg['ext']);

		if(empty(trim($catId))){
			self::$log->add(__METHOD__ . "\$catId is EMPTY!" ,E_USER_WARNING,['$artPathname='=>$artPathname, '$artId'=>$artId]);
			return;
		}

		if( $catPathname === \DR ){
			self::$log->add(__METHOD__.': $catPathname is not VALID!',Logger::BACKTRACE,['$Page->id'=>$Page->id,'$artPathname'=>$artPathname,'$catId'=>$catId]);
			// note Устранение конфликтов
			return new DbJSON;
		}

		$dbPath= $catPathname ."/$artId.json";

		// $artDB= &self::$artBase[$catId][$artId];
		// $artDB= $artDB ?? new DbJSON($dbPath);

		return new DbJSON($dbPath);
	}

	public static function getArtData($artPathname=null)
	{
		global $Page;

		self::_defineCatsDB();
		// self::$log->add(__METHOD__." \$artPathname= $artPathname");
		$artPathname= $artPathname ?? str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];
		$catPathname= dirname($artPathname);
		$catId= basename($catPathname);
		$artId= basename($artPathname, self::$l_cfg['ext']);

		if(empty(trim($catId))){
			self::$log->add(__METHOD__ . "\$catId is EMPTY!" ,E_USER_WARNING,['$artPathname='=>$artPathname, '$artId'=>$artId]);
			return;
		}

		if( $catPathname === \DR ){
			self::$log->add(__METHOD__.': $catPathname is not VALID!',Logger::BACKTRACE,['$Page->id'=>$Page->id,'$artPathname'=>$artPathname,'$catId'=>$catId]);
			// note Устранение конфликтов
			return;
		}

		$catData= self::$map->find('id',$catId);

		/* $artData= end(array_filter($catData['items'], function(&$i) use($artId){

			return $i['id'] === $artId;
		})); */

		foreach($catData['items'] as $i){
			if(is_numeric($i['ind']))
				$i['ind']= [$catData['ind'], $i['ind']];
			if($i['id'] === $artId){
				self::$log->add(__METHOD__,null,['$artData'=>$i]);
				return $i;
			}
		}

		
		return $artData;
	}


	/**
	 * *Получаем категорию по id
	 * @return DbJSON
	 * note ресурсозатратная. Предпочтение getCategoryData
	 */
	private static function _getCategoryDB($catId)
	:DbJSON
	{
		return new DbJSON(self::$storagePath . "/$catId/data.json");

		$db= &self::$artBase[$catId]['self'];

		if(empty($db)){
			$db= new DbJSON(self::$storagePath . "/$catId/data.json");
		}

		return $db;
	}


	/**
	 * *Получаем категорию по id
	 * @return Array
	 */
	public static function getCategoryData($catId)
	:array
	{
		$map= self::getBlogMap();

		if(empty($catData= $map->find('id', $catId))){
			$catData= self::_getCategoryDB($catId)->get();
		}

		self::$log->add(__METHOD__,null,['$catData'=>$catData]);

		return $catData;
	}


	function getArtPathname()
	{
		global $Page;
		return str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];
	}


	/**
	 * *Создаём карту блога
	 * @return Array with objects DbJSON
	 *
	 */
	protected static function _createBlogMap($force=0)
	:DbJSON
	{
		$map= new DbJSON(self::$storagePath.'/map.json');
		if(!$force && $map->count())
			return $map;

		$map->clear();

		self::_defineCatsDB();

		// *Перебираем категории
		foreach(self::$catsDB as $catInd=>$catId){

			// *Собираем элемент и добавляем в нумерованный массив
			$catDB= self::_getCategoryDB($catId);
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
			if(!$catDB->get('ind')) $catDB->push($catInd, 'ind');
			// var_dump($cat);

			// *Массив с базой категории добавляем в карту
			$map->push($catDB->get()) ;

		} // foreach

		// self::$log->add(__METHOD__.' BlogMap',null,[$map]);
		new Sitemap($map);

		// die;
		return $map;
	}


	public static function getBlogMap()
	:DbJSON
	{
		$mapPath= self::$storagePath.'/map.json';

		// !test
		// if(self::is_adm())
		// 	return Sitemap::test();

		// *Держим в памяти карту
		// todo избавиться от self::$artBase
		$map= &self::$map;
		$map= $map ?? new DbJSON($mapPath);

		/* $catData= array_filter($map->get(), function($i){return $i['id'] === $catId;})['items'];

		$artData array_filter($catData, function($i){return $i['id'] === $artId;}); */

		if(!$map->count()){
			$map= self::_createBlogMap();
		}

		// self::$log->add(__METHOD__.': BlogMap',null,[$map->get(), /* $map->get('Novaya') */]);

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