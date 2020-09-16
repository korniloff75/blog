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
		$catPath = __DIR__.'/categories.json';


	public function __construct()
	{
		global $kff;

		// *Директория модуля от DR
		self::$modDir = $kff::getPathFromRoot(__DIR__);

		$this->DB = new DbJSON(__DIR__.'/cfg.json');

		self::$l_cfg= $this->DB->get();
		if(empty(self::$l_cfg))
			$this->DB->replace(self::$def_cfg);

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
	 * *Сохраняем редактирование
	 */
	public function c_saveEdit($html)

	{
		self::$log->add('$this->opts=',null,[$this->opts]);
		return file_put_contents(self::$storagePath . "/{$this->opts['cat']}/{$this->opts['art']}" . self::$l_cfg['ext'], $html);
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
	 * *Лента новостей
	 */
	public function newsTape()
	{
		echo "<h2>Тут будет лента новостей</h2>";
	}


	// *Стартовая страница
	public function is_indexPage()
	{
		global $URI, $Page;
		return is_object($Page) && $URI[1] === $Page->id && empty($URI[2]);
	}


	/**
	 * *Вывод контента по /$Page->id/catName/artName
	 * todo добавить страницу по умолчанию
	 */
	public function addArticle()
	{
		global $URI, $Page;
		// *вырубаем в админке
		if(	!is_object($Page)	) {
			return;
		}

		// *На стартовой - новостная лента
		if($this->is_indexPage()) {
			$this->newsTape();
			return;
		}

		$path = str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];

		self::$log->add('$path=',null,[$path, file_exists($path), $URI]);

		if( !file_exists($path) ) return;

		require_once $path;
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


	/**
	 * *Вывод в страницу
	 */
	public function Render()
	{
		global $Page;
		$edit= isset($_GET['edit']);
	?>

		<script src="/<?=self::$modDir?>/js/blogHelper.js"></script>

		<ul id="categories" class="uk-subnav uk-subnav-divider">

		<?php
		foreach($this->getCategories() as &$cat) {
			// $catData = (new DbJSON(self::$storagePath."/$cat/cfg.json"))->get();
			$catData = $this->getCategory($cat);
			if(!count($catData['items']))
				continue;
			// print_r ($catData);
		?>

			<li>

				<a href="#"><h4><?=$catData['name']?></h4></a>
				<div uk-dropdown="mode: click;">

					<ul data-cat=<?=$cat?>  class="uk-nav uk-dropdown-nav">

					<?php

					foreach($catData['items'] as &$art) {

						echo "<li data-id={$art['id']} data-cat=$cat>
						<a href=\"/{$Page->id}/$cat/{$art['id']} \">{$art['name']}</a>

						</li>";
					}

					?>
					</ul>
				</div>

		</li>
		<?php
		}
		?>

	</ul><!-- #categories -->

	<?php
	if($edit)
	{
	?>
	<div>
		<button id="saveEdit">SAVE</button>
	</div>
	<script>
		document.querySelector('#saveEdit')
		.addEventListener('click', BH.editRequest.bind(null, '.blog_content'));

	</script>
	<?php
	}
	?>

	<div class="blog_content" <?=$edit?'contenteditable=true':''?>>
		<?php $this->addArticle()?>
	</div><!-- .blog_content -->
	<?php
	}
}