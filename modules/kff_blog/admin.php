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
	 * *Перезаписываем catName/data.json
	 *
	 */
	private function _updateCatData(SplFileInfo &$catFI, $humName=null)
	{
		$catPathname = $catFI->getPathname();

		// self::$log->add($catFI->getPathname() . "/*" . self::$l_cfg['ext']);

		$catDB = new DbJSON($catPathname . "/data.json");
		$catDB->clear('items');
		// $catDB->append(['name'=>$catFilename]);

		foreach(glob($catPathname . "/*" . self::$l_cfg['ext']) as &$i) {
			// *без расширения
			$artName = pathinfo($i, PATHINFO_FILENAME);

			$catDB->append(['items'=>[[
				'id'=> $artName,
				'name'=> (new DbJSON("$catPathname/$artName.json"))->get('name') ?? $humName,
			]]]);
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

			$catFilename = $catFI->getFilename();

			$this->_updateCatData($catFI);

			$cats []= $catFilename;

		} // foreach

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
	function c_sortCategories($cats)
	{
		$catsAll = json_decode($cats, 1);
		$cats = array_keys($catsAll);

		self::$log->add(__METHOD__.' $catsAll',null,[$catsAll]);

		$this->catsDB->replace($cats);

		// *Перебираем категории
		foreach($cats as $cat) {
			$catPathname = self::$storagePath . "/$cat";
			$items = &$catsAll[$cat];

			$catDB = new DbJSON($catPathname . "/data.json");
			$catDB->clear('items');

			self::$log->add(__METHOD__,null,[$cat,$items]);

			// *Перебираем элементы
			foreach($items as &$item) {
				// *Элемент перемещён в другую категорию
				if($item['oldCatId'] !== $cat)
				{
					$oldCatPath = self::$storagePath . "/{$item['oldCatId']}";
					rename("{$oldCatPath}/{$item['id']}" . self::$l_cfg['ext'], "$catPathname/{$item['id']}" . self::$l_cfg['ext']);
					rename("{$oldCatPath}/{$item['id']}.json", "$catPathname/{$item['id']}.json");
				}
				$catDB->append(['items'=>[[
					'id'=>$item['id'],
					'name'=>$item['name'],
				]]]);
			}

			if(!empty($oldCatPath))
				$this->_updateCatData(new SplFileInfo($oldCatPath));


		}

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
		$data = ['name'=>$new_cat];
		$new_cat = Index_my_addon::translit($new_cat);
		$data ['id']= $new_cat;
		$catPath = self::$storagePath."/$new_cat";
		$catDB = new DbJSON("$catPath/data.json");
		$catDB->set($data);

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

		$this->catsDB->append([$new_cat]);

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

		$cat = $this->opts['cat'] ?? 'default';
		$catPath = self::$storagePath."/$cat";
		$cfg['path'] = Index_my_addon::getPathFromRoot($catPath);
		$artPath = "{$cfg['path']}/{$new_article}" . self::$l_cfg['ext'];

		$artDB = new DbJSON("$catPath/{$new_article}.json");

		if(!is_dir($catPath))
		{
			$this->c_addCategory($catPath);
		}

		// *Already exists
		if(file_exists($artPath)) {
			self::$log->add(__METHOD__." Файл $artPath уже существует!",E_USER_WARNING);
			return false;
		}

		if (
			file_put_contents(DR."/$artPath",'<p>New Article!</p>')
		) {
			$artDB->set($cfg);
			$this->_updateCatData(new SplFileInfo($catPath), $cfg['name']);
		}
		else
		{
			self::$log->add(__METHOD__,E_USER_WARNING, [$artPath, file_exists($artPath)]);
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
				$catData['id'] = $catData['id'] ?? $cat;
				self::$log->add(__METHOD__,null,[$cat, $catData]);
			?>
				<li>
				<h4><?=$catData['name']?></h4>
				<div style="display: inline-block;">
					<input type="hidden" name="cat" value="<?=$catData['id']?>">
					<input type="text" name="addArticle" placeholder="Название статьи">
				</div><button>ADD</button>

				<ul data-id=<?=$catData['id']?> class="listArticles uk-nav uk-nav-default uk-width-medium" uk-sortable="group: cat-items; handle: .uk-sortable-handle; cls-custom: uk-box-shadow-small uk-flex uk-flex-middle uk-background">

				<?php
				if(is_array($catData['items'])) foreach($catData['items'] as &$art) {
					echo "<li data-id={$art['id']} data-name=\"{$art['name']}\" data-oldCatId= {$catData['id']} class=\"uk-flex uk-flex-middle\">
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