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

		$this->RenderPU();

	} // __construct


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
			$catPathname = $catFI->getPathname();
			$catFilename = $catFI->getFilename();

			// self::$log->add($catFI->getPathname() . "/*" . self::$l_cfg['ext']);

			$catDB = new DbJSON($catPathname . "/data.json");
			$catDB->clear('items');
			// $catDB->append(['name'=>$catFilename]);

			foreach(glob($catPathname . "/*" . self::$l_cfg['ext']) as &$i) {
				$artName = pathinfo($i, PATHINFO_FILENAME);

				$catDB->append(['items'=>[[
					'id'=>$artName,
					'name'=>(new DbJSON("$catPathname/$artName.json"))->get('name'),
				]]]);
			}

			$cats []= $catFilename;

			/* $cats [$catFilename]= array_map(
				function(&$i) use($catPathname, $catDB) {
					$artName = pathinfo($i, PATHINFO_FILENAME);

					$catDB->append(['items'=>[[
						'id'=>$artName,
						'name'=>(new DbJSON("$catPathname/$artName.json"))->get('name'),
					]]]);
					return $artName;
				},
				glob($catPathname . "/*" . self::$l_cfg['ext'])
			); */

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
		global $MODULE;
		?>

		<div class="header"><h1><a href="#" onclick="location.reload(); return false;">Настройки</a> <?=$MODULE?></h1></div>

		<div class="content">

			<script src="/<?=self::$modDir?>/js/blogHelper.js"></script>

			<h3>Категории</h3>

			<input type="text" name="addCategory" placeholder="Название категории"><button>Новая</button>

			<ul id="categories" class="uk-nav uk-nav-default">

			<?php
			foreach($this->getCategories() as &$cat) {
				$catData = $this->getCategory($cat);
			?>
				<li>
				<h4><?=$catData['name']?></h4>
				<div style="display: inline-block;">
					<input type="hidden" name="cat" value="<?=$catData['id']?>">
					<input type="text" name="addArticle" placeholder="Название статьи">
				</div><button>ADD</button>

				<ul data-id=<?=$catData['id']?> class="listArticles uk-nav uk-nav-default uk-width-medium" uk-sortable="group: cat-items; handle: .uk-sortable-handle; cls-custom: uk-box-shadow-small uk-flex uk-flex-middle uk-background">

				<?php
				foreach($catData['items'] as &$art) {
					echo "<li data-id={$art['id']} data-cat=\"{$catData['id']}\" class=\"uk-flex uk-flex-middle\">
					<div class=\"uk-sortable-handle\" uk-icon=\"icon: table\"></div>
					<a href=\"/Blog/$cat/{$art['id']}?edit \" target='_blank'>{$art['name']}</a>

					<!-- <div style=\"display: inline-block;\">
					<input type=\"hidden\" name=\"cat\" value=\"$cat\">
					</div> -->

					<input type='button' class='uk-button-secondary delArticle' value=DEL>
					</li>";
					// print_r($art);
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