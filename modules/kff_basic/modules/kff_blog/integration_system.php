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
		$catPath = __DIR__.'/categories.json',
		$artBase= [];


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

		$this->catsDB = new DbJSON(self::$catPath);

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


	/**
	 * @param artPathname - путь к файлу статьи
	 * если не передан - вычисляем текущую из URI
	 */
	public static function getArtDB($artPathname=null)
	{
		global $Page;
		$artPathname= $artPathname ?? str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];
		$catId= basename(dirname($artPathname));
		$artId= basename($artPathname, self::$l_cfg['ext']);

		if(empty(trim($catId))){
			self::$log->add(__METHOD__ . "\$catId is EMPTY! \$artPathname= $artPathname; \$artId= $artId" ,E_USER_WARNING,[$artPathname, $artId]);
			return;
		}

		$dbPath= dirname($artPathname) ."/$artId.json";

		return self::$artBase[$catId][$artId]= self::$artBase[$catId][$artId] ?? new DbJSON($dbPath);
	}


	/**
	 * *Получаем категории из базы
	 */
	public function getCategories()

	{
		return $this->catsDB->get();
	}


	/**
	 * *Получаем категорию по id
	 */
	public function getCategory($id)
	:array
	{
		return (new DbJSON(self::$storagePath . "/$id/data.json"))->get();
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
		return false;
	}

}