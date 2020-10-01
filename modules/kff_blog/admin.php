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
	protected function _updateCatDB(SplFileInfo $catFI, $humName=null)
	{
		$catPathname = $catFI->getPathname();

		// self::$log->add($catFI->getPathname() . "/*" . self::$l_cfg['ext']);

		$catDB = new DbJSON($catPathname . "/data.json");
		$catDB->clear('items');
		// $catDB->append(['name'=>$catFilename]);

		foreach(glob($catPathname . "/*" . self::$l_cfg['ext']) as $n=>&$artPathname) {
			// *без расширения
			$artId = pathinfo($artPathname, PATHINFO_FILENAME);
			$artDB = self::getArtDB($artPathname);

			// if(empty($artDB->get('title')))
			// 	$artDB->set(['title'=>$artDB->get('name')]);

			$catDB->append([
				'items'=>[[
					'id'=> $artId,
					'name'=> $artDB->get('name') ?? $humName,
					'title'=> $artDB->get('title'),
					// 'title'=> $artDB->get('title') ? $artDB->get('title'): $artDB->get('name'),
					'not-public'=> $artDB->get('not-public') ?? 0,
				]]
			]);

			// *Берем данные из первой статьи категории
			if(!$n){
				if(!is_string($catDB->get('name')))
					$catDB->push($artDB->get('catName'), 'name');
			}

		}
	}


	// *Методы контроллера

	/**
	 * *Сохрааняем сортировку статей
	 */
	function c_sortCategories($catsAllJson)
	{
		$catsAll = json_decode($catsAllJson, 1);
		$cats = array_keys($catsAll);

		self::$log->add(__METHOD__,null,['$cats'=>$cats, '$catsAll'=>$catsAll]);

		self::$catsDB->replace($cats);

		// *Перебираем категории
		foreach($cats as $ind=>$catId) {
			$catPathname = self::$storagePath . "/$catId";
			$items = &$catsAll[$catId];

			$catDB = new DbJSON($catPathname . "/data.json");
			$catDB->clear('items');

			self::$log->add(__METHOD__,null,['$catId'=>$catId,'$items'=>$items,]);

			// *Перебираем элементы
			foreach($items as &$item) {
				$artPathname= "$catPathname/{$item['id']}" . self::$l_cfg['ext'];

				// *Элемент перемещён в другую категорию
				if($item['oldCatId'] !== $catId)
				{
					$oldCatPath = self::$storagePath . "/{$item['oldCatId']}";
					rename("{$oldCatPath}/{$item['id']}" . self::$l_cfg['ext'], $artPathname);
					rename("{$oldCatPath}/{$item['id']}.json", "$catPathname/{$item['id']}.json");

					// *Перезаписываем данные в базе статьи
					// $artDB = new DbJSON("$catPathname/{$item['id']}.json");
					$artDB = self::getArtDB($artPathname);
					$artDB->set(['catId'=>$catId, 'catName'=>$catDB->get('name')]);

					unset($item['oldCatId']);
				}

				// *Обновляем базу элементов категории
				$catDB->append(['items'=>[$item]]);

			} //foreach

			if(!empty($oldCatPath))
				$this->_updateCatDB(new SplFileInfo($oldCatPath));

			// *Обновляем карту
			self::_createBlogMap(1);

		} //foreach

		return $cats;
	}


	/**
	 * !Удаляем статью
	 */
	public function c_removeArticle($removePath)
	{
		$removePath= self::$storagePath. "/$removePath";
		$catPath= dirname($removePath);

		self::$log->add("Удаление статьи $removePath");

		unlink("$removePath.json");
		unlink("$removePath" . self::$l_cfg['ext']);

		$this->_updateCatDB(new SplFileInfo($catPath));

		// *Обновляем карту
		self::_createBlogMap();
	}

	/**
	 * !Удаляем категорию
	 */
	public function c_removeCategory($removeId)
	{
		if(empty($removeId))
			die(__METHOD__.": Попытка удаления неизвестной категории");

		self::$log->add("Удаление категории $removeId");
		require_once DR.'/'. self::$dir ."/cpDir.class.php";
		cpDir::RemoveDir(self::$storagePath. "/$removeId");
		$num= array_search($removeId, self::$catsDB->get());
		self::$catsDB->remove($num);

		// *Обновляем карту
		self::_createBlogMap();
	}


	/**
	 * *Добавляем категорию
	 */
	public function c_addCategory($new_cat)
	{
		if(empty($new_cat) || is_numeric($new_cat))
		{
			die("<div class=content>Категория <b>$new_cat</b> не может быть создана с таким именем!</div>");
		}
		$data = ['name'=>$new_cat];
		$catId = Index_my_addon::translit($new_cat);
		$data ['id']= $catId;
		$catPath = self::$storagePath."/$catId";

		// echo "<div class=content><b>$catId</b></div>";

		if(is_dir($catPath))
		{
			die("<div class=content>Категория <b>$catId</b> уже существует!</div>");
		}
		elseif(!$success = mkdir($catPath,0755,1))
		{
			die("<div class=content>Категория <b>$catId</b> не создана!</div>");
		}

		$catDB = new DbJSON("$catPath/data.json");
		$catDB->set($data);


		// *Переписываем список категорий
		if(!in_array($catId, self::$catsDB->get()))
			self::$catsDB->append([$catId]);

		foreach(self::$catsDB as $ind=>$catId){
			if(!is_dir(self::$storagePath."/$catId"))
			self::$catsDB->remove($ind);
		}

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

		$catId = $cfg['catId']= $this->opts['catId'] ?? 'default';
		$catName = $cfg['catName']= $this->opts['catName'];
		$catPath = self::$storagePath."/$catId";
		$cfg['path'] = Index_my_addon::getPathFromRoot($catPath);
		$artPathname = "{$catPath}/{$new_article}" . self::$l_cfg['ext'];

		$artDB = self::getArtDB($artPathname);

		if(!is_dir($catPath))
		{
			$this->c_addCategory($catPath);
		}

		// *Already exists
		// self::$log->add(__METHOD__." Файл $artPathname уже существует???",E_USER_WARNING,[file_exists($artPathname)]);

		if(file_exists($artPathname)) {
			self::$log->add(__METHOD__." Файл $artPathname уже существует!",E_USER_WARNING);
			return false;
		}

		if (
			file_put_contents($artPathname,"<p>New Article - <b>$new_article</b>!</p>")
		) {
			$artDB->set($cfg);
			$this->_updateCatDB(new SplFileInfo($catPath), $cfg['name']);
		}
		else {
			self::$log->add(__METHOD__.' Не получается добавить статью '.$artPathname,E_USER_WARNING);
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

		<script src="/<?=self::$modDir?>/js/blogHelper.js" defer></script>

		<div class="content">

			<h3>Категории</h3>

			<input type="text" name="addCategory" placeholder="Название категории"><button class="addCategory">ADD</button>

			<ul id="categories" class="uk-nav uk-nav-default" uk-sortable="group: cats; handle: .uk-sortable-handle;">

			<?php
			// self::$log->add(__METHOD__,null,['self::$catsDB'=>self::$catsDB, ]);

			foreach(self::$catsDB as $catId) {
				$catData = self::getCategoryData($catId);
				$catData['id'] = $catData['id'] ?? $catId;

				// self::$log->add(__METHOD__,null,['$catId'=>$catId, '$catData'=>$catData]);
				?>
				<li>
				<div class="uk-flex uk-flex-middle uk-margin-top">
					<div class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: table; ratio: 1.5"></div>
					<!-- Category name -->
					<h4 class="uk-margin-remove"><?=$catData['name']?> <div class="removeCategory" uk-icon="icon: trash; ratio: 1.5" data-del="<?=$catData['id']?>"></div></h4>
				</div>

				<div style="display: inline-block;">
					<input type="hidden" name="catId" value="<?=$catData['id']?>">
					<input type="hidden" name="catName" value="<?=$catData['name']?>">
					<input type="text" name="addArticle" placeholder="Название статьи">
				</div><button class="addArticle">ADD</button>

				<ul data-id=<?=$catData['id']?> class="listArticles uk-nav uk-nav-default uk-width-auto" uk-sortable="group: cat-items; handle: .uk-sortable-handle; cls-custom: uk-box-shadow-small uk-flex uk-flex-expand uk-background">

				<?php
				if(is_array($catData['items'])) foreach($catData['items'] as $ind=>&$artData) {
					$artData['title'] = $artData['title'] ?? $artData['name'];
					// $artData['date'] = $artData['date'];

					echo "<li data-id={$artData['id']} data-index={$ind} data-name=\"{$artData['name']}\" data-oldCatId= {$catData['id']} uk-tooltip title=\"{$artData['title']}\" data-title=\"{$artData['title']}\" class=\"uk-flex uk-flex-wrap uk-flex-middle\">
					<div class=\"uk-sortable-handle uk-margin-small-right\" uk-icon=\"icon: table\"></div>

					<!-- artName -->
					{$artData['name']}

					<!-- Remove article -->
					<span uk-icon=\"trash\" data-del=\"$catId/{$artData['id']}\" class='delArticle'></span>



					</li>";
					// print_r($artData);
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
