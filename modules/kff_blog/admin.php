<?php
if(__DIR__ === realpath('.')) die;
if(empty($kff)) global $kff;

$kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die('Access denied!');



class BlogKff extends Index_my_addon
{
	protected static
		$modDir,
		// *Локальный конфиг
		$def_cfg = [
			'name'=> 'Блог',
		],
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
			$this->DB->set(self::$def_cfg);

		$this->catsDB = new DbJSON(self::$catPath);

		// self::$log->add('self::$cfg=',null, [self::$cfg]);

		$this->_InputController();

		$this->RenderPU();

	} // __construct


	/**
	 * *Обработка внешних запросов
	 * Методы контроллера с префиксом c_
	 */
	private function _InputController()
	{
		$r = &$_REQUEST;
		if(!empty($m_name = "c_{$r['name']}") && method_exists($this, $m_name))
		{
			$this->{$m_name}(filter_var($r['value']));
		}
	}


	/**
	 * *Перезаписываем категории
	 */
	function updateCategories()
	{
		$cats = [];
		foreach(
			new FilesystemIterator(self::$storagePath, FilesystemIterator::SKIP_DOTS) as $catFI
		) {
			if(!$catFI->isDir()) continue;
			// echo $catFI->getFilename() . '<br>';
			$glob = glob($catFI->getPathname() . "/*.dat");
			$cats [$catFI->getFilename()]= array_map(
				function(&$i){return basename($i);}, $glob
			);
		}

		$this->catsDB->replace($cats);

		return $cats;
	}

	/**
	 * *Получаем категории из базы
	 */
	public function getCategories()
	{
		return empty($cats = $this->catsDB->get()) ?
			$this->updateCategories()
			: $cats;
	}


	// *Методы контроллера
	/**
	 * *Добавляем категорию
	 */
	public function c_addCategory($new_cat)
	{
		$cfg = ['name'=>$new_cat];
		$new_cat = Index_my_addon::translit($new_cat);
		$catPath = self::$storagePath."/$new_cat";
		$catDB = new DbJSON("$catPath/cfg.json");
		$catDB->set($cfg);
		// $new_cat = Transliterator::create('id')->transliterate($new_cat);

		// echo "<div class=content><b>$new_cat</b></div>";

		if(is_dir($catPath))
		{
			die("<div class=content>Категория <b>$new_cat</b> уже существует!</div>");
		}
		elseif(!$success = mkdir($catPath,0755,1))
		{
			die("<div class=content>Категория <b>$new_cat</b> не создана!</div>");
		}
		$this->updateCategories();
		return $success;
	}

	/**
	 * *Добавляем статью
	 */
	public function c_addArticle($new_article)
	{
		$cfg = ['name'=>$new_article];
		$new_article = Index_my_addon::translit($new_article);
		$cfg['id'] = $new_article;

		$cat = filter_var($_REQUEST['opts']['cat']) ?? 'default';
		$catPath = self::$storagePath."/$cat";
		$cfg['path'] = $artPath = "$catPath/{$new_article}.dat";
		$artDB = new DbJSON("$catPath/{$new_article}.json");
		$artDB->set($cfg);

		if(!is_dir($catPath))
		{
			$this->c_addCategory($catPath);
		}

		if(file_exists($artPath) || file_exists($cfgPath))
			return false;

		$addToCat = [$cat=>[basename($artPath)]];
		$this->catsDB->set($addToCat,'append');

		return (
			file_put_contents($artPath,'')
		);
	}

	/**
	 * *Вывод в админку
	 */
	private function RenderPU()
	{
		?>

		<div class="header"><h1><a href="#" onclick="location.reload(); return false;">Настройки</a> <?=$MODULE?></h1></div>

		<div class="content">

			<script src="/<?=self::$modDir?>/js/blogHelper.js"></script>
			<h2>Тут будет ПУ Блога</h2>

			<h3>Категории</h3>

			<input type="text" name="addCategory" placeholder="Название категории"><button>Новая</button>

			<ul id="categories" class="uk-nav uk-nav-default">

				<?php
				foreach($this->getCategories() as $cat=>&$arts) {
				?>
					<li>
					<h4><?=$cat?></h4>
					<div style="display: inline-block;">
						<input type="hidden" name="cat" value="<?=$cat?>">
						<input type="text" name="addArticle" placeholder="Название статьи">
					</div><button>ADD</button>

					<ul class="listArticles uk-nav uk-nav-default uk-width-medium" uk-sortable="group: cat-items; handle: .uk-sortable-handle; cls-custom: uk-box-shadow-small uk-flex uk-flex-middle uk-background">

					<?php
					foreach($arts as &$art) {
						echo "<li>
						<div class=\"uk-sortable-handle\" uk-icon=\"icon: table\"></div>
						$art
						<div style=\"display: inline-block;\">
						<input type=\"hidden\" name=\"cat\" value=\"$cat\">
						</div>
						<input type='button' class=uk-button-secondary value=DEL></li>";
					}

					?>
					</ul>
					</li>
				<?php
				}
				?>

			</ul><!-- #categories -->
		</div><!-- .content -->
		<?php
	}
}

$Blog = new BlogKff;


// *Tests
// print_r($Blog->getCategories());