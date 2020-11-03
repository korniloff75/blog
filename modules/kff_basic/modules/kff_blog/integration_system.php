<?php
class BlogKff extends Index_my_addon
{
	protected static
		$inputData,
		$AJAX= false,
		$modDir,
		// *default
		$def_cfg = [
			'name'=> 'Блог',
			'ext'=>'.dat',
			'newsTapeLength'=>10,
		],
		$blogDB,
		// *Локальный конфиг
		$l_cfg,
		$storagePath = \DR.'/kff_blog_data',
		$catPath,
		$catsDB,
		$artDB,
		$catDataKeys = ['name','id','items'],
		// *Данные по текущей статье
		$art= [],
		$map;


	public function __construct()
	{
		global $Page;

		// *Директория модуля от DR
		self::$modDir = self::getPathFromRoot(__DIR__);

		// self::$log->add(__METHOD__,null,['$artDB'=>$artDB,]);

		if(
			!file_exists(self::$storagePath)
			&& !mkdir(self::$storagePath, 0755, 1)
		) die(__METHOD__.': Невозможно создать директорию хранилища');

		// * self::$catsDB с категориями
		self::_defineCatsDB();

		if(
			($artDB= self::getArtDB())
			&& $artDB->count()
		){
			$Page->catId= $artDB->catId;
			$Page->artId= $artDB->id;
		}

		self::getBlogMap();

		if(!$this->_InputController())
			self::addUIkit();

	} // __construct


	/**
	 * *Обработка внешних запросов
	 * Методы контроллера с префиксом c_
	 */
	protected function _InputController()
	{
		// if(!self::is_adm()) return false;
		;

		$r = &$_REQUEST;

		// *get AJAX
		if(
			empty(self::$inputData)
			&& is_string(self::$inputData= file_get_contents('php://input'))
			&& !empty(self::$inputData= json_decode(self::$inputData, 1))
		){
			$r= array_merge($r, self::$inputData);
			self::$AJAX = true;
		}

		// self::$log->add(__METHOD__,null,['$r'=>$r, 'method_exists($this, ($m_name = "c_{$r[\'name\']}"))'=>method_exists($this, ($m_name = "c_{$r['name']}")), '$this'=>$this]);

		if(!empty($r['name']) && method_exists($this, ($m_name = "c_{$r['name']}")))
		{
			if(is_string($r['opts']))
				$r['opts'] = json_decode($r['opts'],1);
			$this->opts = @$r['opts'];

			self::$log->add(__METHOD__,null,['$this->opts'=>$this->opts]);

			$val= is_array($r['value'])? filter_var_array($r['value']): filter_var($r['value']);
			return $this->{$m_name}($val) || true;
		}
		return false;
	}


	public static function is_edit()
	{
		return self::is_adm() && isset($_GET['edit']);
	}


	protected static function _defineCatsDB()
	{
		if(self::$catsDB) return;

		self::$blogDB = new DbJSON(DR.'/data/cfg/kff_blog.json');
		self::$l_cfg= self::$blogDB->get();
		if(!self::$blogDB->count())
			self::$blogDB->replace(self::$def_cfg);

		// *Define self::$art
		// ?
		/* self::$art= [
			'pathname'=> self::getArtPathname()
		];
		$catPathname= dirname(self::$art['pathname']);
		self::$art['catId']= basename($catPathname);
		self::$art['id']= basename(self::$art['pathname'], self::$blogDB->ext); */

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
		// self::$log->add(__METHOD__,null,['$catsDB'=>$catsDB]);
	}


	/**
	 * @param artPathname - путь к файлу статьи
	 * если не передан - вычисляем текущую из URI
	 */
	public static function getArtDB($artPathname=null)
	:?DbJSON
	{
		global $Page;

		$curArtPathname= self::getArtPathname();

		if(
			empty($artPathname)
			|| trim($artPathname, '/') === trim($curArtPathname, '/')
		){
			if(!empty($artDB= &self::$artDB))
				return $artDB;

			$artPathname= $artPathname ?? $curArtPathname;
		}

		self::_defineCatsDB();
		// self::$log->add(__METHOD__." \$artPathname= $artPathname");

		$catPathname= dirname($artPathname);
		$catId= basename($catPathname);
		$artId= basename($artPathname, self::$blogDB->ext);

		if(empty(trim($catId))){
			self::$log->add(__METHOD__ . "\$catId is EMPTY!" ,E_USER_WARNING,['$artPathname='=>$artPathname, '$artId'=>$artId]);
			return null;
		}

		if( $catPathname === \DR ){
			// self::$log->add(__METHOD__.': $catPathname is not VALID!',Logger::BACKTRACE,['$Page->id'=>$Page->id,'$artPathname'=>$artPathname,'$catId'=>$catId]);
			// note Устранение конфликтов
			return new DbJSON;
		}

		$dbPath= $catPathname ."/$artId.json";

		// self::$log->add(__METHOD__,\Logger::BACKTRACE,[/* '$Page->id'=>$Page->id,  */'$catPathname'=>$catPathname, '$artPathname'=>$artPathname]);

		$artDB= new DbJSON($dbPath);

		return $artDB;
	}


	public static function getArtData($artPathname=null)
	{
		global $Page;

		self::_defineCatsDB();
		// self::$log->add(__METHOD__." \$artPathname= $artPathname");
		$artPathname= $artPathname ?? self::getArtPathname();
		$catPathname= dirname($artPathname);
		$catId= basename($catPathname);
		$artId= basename($artPathname, self::$blogDB->ext);

		if(empty(trim($catId))){
			self::$log->add(__METHOD__ . "\$catId is EMPTY!" ,E_USER_WARNING,['$artPathname='=>$artPathname, '$artId'=>$artId]);
			return;
		}

		if( $catPathname === \DR ){
			// self::$log->add(__METHOD__.': $catPathname is not VALID!',Logger::BACKTRACE,['$Page->id'=>$Page->id,'$artPathname'=>$artPathname,'$catId'=>$catId]);
			// note Устранение конфликтов
			return;
		}

		$catData= self::getBlogMap()->find('id',$catId);

		foreach($catData['items'] as $ind=>&$artData){
			// if(is_numeric($artData['ind']))
			// 	$artData['ind']= [$catData['ind'], $ind];
			if($artData['id'] === $artId){
				// self::$log->add(__METHOD__,null,['$artData'=>$artData]);
				$artData['catName']= $catData['name'];
				return $artData;
			}
		}

		return $catData;
	}


		/**
	 * *Перезаписываем catName/data.json
	 *
	 */
	protected static function _updateCatDB(SplFileInfo $catFI, $humName=null)
	{
		if(!self::is_adm()) die('Access denied in '. __METHOD__);

		$catPathname = $catFI->getPathname();

		// self::$log->add($catFI->getPathname() . "/*" . self::$blogDB->ext);

		$catId= $catFI->getFilename();
		$catDB = new DbJSON($catPathname . "/data.json");
		// $catDB->clear('items');
		$items= [];
		// $catDB->append(['name'=>$catFilename]);
		$catDbFixed= is_string($catDB->get('name'));

		// self::$log->add(__METHOD__,null,['glob($catPathname . "/*" . self::$blogDB->ext)'=>glob($catPathname . "/*" . self::$blogDB->ext) ]);

		foreach(glob($catPathname . "/*" . self::$blogDB->ext) as $ind=>&$artPathname) {
			// *без расширения
			$artId = pathinfo($artPathname, PATHINFO_FILENAME);
			// todo Перевести на artData
			$artDB = self::getArtDB($artPathname);

			// if(empty($artDB->get('title')))
			// 	$artDB->set(['title'=>$artDB->get('name')]);

			$item= $artDB->get();
			$item['id']= $item['id']?? $artId;

			// *Сохраняем порядок
			if(
				is_numeric($artInd= (int)$artDB->ind[1])
				&& is_null($items[$artInd])
			){
				$items[$artInd]= $item;
			}
			else
				$items[]= $item;

			// *Берем данные из статьи категории
			if($artDB->catName && !$catDbFixed){
				$catDB->push($artDB->catId, 'id');
				$catDB->push($artDB->catName, 'name');
				$catDbFixed=1;
			}

		} //foreach

		// self::$log->add(__METHOD__,null,['$items'=>$items ]);

		ksort($items);

		$catDB->push($items, 'items');
		$catDB->save();

		// *Заносим в $map
		self::$map->setInd($catDB->get(), 'id', $catId);

		return $catDB;
	}


	/**
	 * *Получаем категорию по id
	 * @return DbJSON
	 * note ресурсозатратная. Предпочтение getCategoryData
	 */
	protected static function _getCategoryDB($catId)
	:DbJSON
	{
		$catDB= new DbJSON(self::$storagePath . "/$catId/data.json");

		if(!$catDB->count()){
			$catDB= self::_updateCatDB(new SplFileInfo(self::$storagePath . "/$catId"));
		}

		return $catDB;
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

		// self::$log->add(__METHOD__,null,['$catId'=>$catId, '$catData'=>$catData]);

		return $catData;
	}


	public static function getArtPathname()
	{
		global $Page;

		if(!self::$blogDB) self::$log->add(__METHOD__,Logger::BACKTRACE,['self::$blogDB'=>self::$blogDB]);

		return is_object($Page)?
		str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$blogDB->ext
		: null;
	}


	// *Стартовая страница kff_blog
	public static function is_indexPage()
	{
		global $URI, $Page;
		return is_object($Page) && $Page->module === 'kff_blog' && $URI[1] === $Page->id && empty($URI[2]);
	}

	// *FIX img[src]
	public static function fixImgs($artId, $txt)
	{
		global $Page;

		// $log->add('FIX img[src]',null,['$Page->artId'=>$Page->artId]);
		return str_replace('{DIR}assets', "/files/CKeditor/{$artId}", $txt);
	}


	/**
	 * *Создаём карту блога
	 * @return Array with objects DbJSON
	 *
	 */
	protected static function _createBlogMap($force=0)
	:DbJSON
	{
		$map= &self::$map;
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
			$map->push($catDB->get());

		} // foreach

		// self::$log->add(__METHOD__.' BlogMap',null,[$map]);
		new Sitemap($map);

		/* $map->__destruct();
		$map->__destruct= null; */

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
			filter_var(self::$cfgDB->uk['include_uikit'], FILTER_VALIDATE_BOOLEAN)
		) return;

		$UIKpath = '/'. self::$internalModulesPath . '/kff_uikit-3.5.5';
		?>

		<!-- UIkit CSS -->
		<link rel="stylesheet" href="<?=$UIKpath?>/css/uikit.min.css" />

		<!-- UIkit JS -->
		<script src="<?=$UIKpath?>/js/uikit.min.js"></script>

		<?php
		if(self::is_adm()){
			echo '<script src="'.$UIKpath.'/js/uikit-icons.min.js"></script>';
		}
		?>
		<!-- /UIkit -->

		<?php
	}

	public function __destruct()
	{
		return false;
	}

}