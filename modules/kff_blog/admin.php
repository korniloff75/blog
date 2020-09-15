<?php
if(__DIR__ === realpath('.')) die;
if(empty($kff)) global $kff;

$kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die('Access denied!');



class BlogKff_adm extends BlogKff
{
	// protected static
	// 	$modDir;


	public function __construct()
	{
		parent::__construct();

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
	 * *Перезаписываем категории из ФС
	 * note сортировка статей сбрасывается
	 */
	function updateCategories()
	{
		$cats = [];
		foreach(
			new FilesystemIterator(self::$storagePath, FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS) as $catFI
		) {
			if(!$catFI->isDir()) continue;
			// echo $catFI->getFilename() . '<br>';
			$glob = glob($catFI->getPathname() . "/*" . self::$l_cfg['ext']);
			// self::$log->add($catFI->getPathname() . "/*" . self::$l_cfg['ext']);
			$cats [$catFI->getFilename()]= array_map(
				function(&$i){return pathinfo($i, PATHINFO_FILENAME);}, $glob
			);
			$catDB = new DbJSON($catFI->getPathname() . "/cfg.json");
			$map = ['map'=>$cats [$catFI->getFilename()]];
			$catDB->set($map);
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
	 * *Сохрааняем сортировку статей
	 */
	function c_setCategories($cats)
	{
		$cats = json_decode($cats, 1);

		$this->catsDB->replace($cats);

		return $cats;
	}

	/**
	 * *Добавляем категорию
	 */
	public function c_addCategory($new_cat)
	{
		if(is_numeric($new_cat))
		{
			die("<div class=content>Категория <b>$new_cat</b> не может быть создана с таким именем!</div>");
		}
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
		// $this->updateCategories();

		$addCat = ["$new_cat"=>[]];
		$this->catsDB->set($addCat,'append');

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
		$cfg['path'] = Index_my_addon::getPathFromRoot($catPath) . "/{$new_article}";
		$artPath = $cfg['path'] . self::$l_cfg['ext'];
		$artDB = new DbJSON("$catPath/{$new_article}.json");

		if(!is_dir($catPath))
		{
			$this->c_addCategory($catPath);
		}

		if(file_exists($artPath))
			return false;

		if (
			!file_put_contents(DR."/$artPath",'')
		) {
			$addToCat = [$cat=>[basename($cfg['path'])]];
			$this->catsDB->set($addToCat,'append');
			$artDB->set($cfg);
		}
		else
		{
			self::$log->add('=',E_USER_WARNING, [$artPath]);
		}
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

				<ul data-cat=<?=$cat?> class="listArticles uk-nav uk-nav-default uk-width-medium" uk-sortable="group: cat-items; handle: .uk-sortable-handle; cls-custom: uk-box-shadow-small uk-flex uk-flex-middle uk-background">

				<?php
				foreach($arts as &$art) {
					echo "<li data-id=$art data-cat=$cat class='uk-flex uk-flex-middle'>
					<div class=\"uk-sortable-handle\" uk-icon=\"icon: table\"></div>
					<a href=\"/".self::getPathFromRoot(self::$storagePath)."/$cat/$art" . self::$l_cfg['ext'] . "\"target='_blank'>$art</a>

					<!-- <div style=\"display: inline-block;\">
					<input type=\"hidden\" name=\"cat\" value=\"$cat\">
					</div> -->

					<input type='button' class='uk-button-secondary delArticle' value=DEL>
					</li>";
				}

				?>
				</ul>
			</li>
			<?php
			}
			?>

		</ul><!-- #categories -->

		<button id="save_sts">Save</button>
	</div><!-- .content -->
	<?php
	}
}

$Blog = new BlogKff_adm;


// *Tests
// print_r($Blog->getCategories());