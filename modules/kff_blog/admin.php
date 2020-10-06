<?php
if(__DIR__ === realpath('.')) die;
if(empty($kff)) global $kff;

$kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die('Access denied!');



class BlogKff_adm extends BlogKff
{
	/* public function __construct()
	{
		parent::__construct();

	} // __construct */


	// *Методы контроллера

	/**
	 * *Сохрааняем сортировку статей
	 */
	function c_sortCategories($catsAllJson)
	{
		$catsAll = json_decode($catsAllJson, 1);
		$cats = array_keys($catsAll);

		// self::$log->add(__METHOD__,null,['$cats'=>$cats, '$catsAll'=>$catsAll]);

		self::$catsDB->replace($cats);

		// *Перебираем категории
		foreach($cats as $catInd=>$catId) {
			$catPathname = self::$storagePath . "/$catId";
			$items = &$catsAll[$catId];

			$catDB = new DbJSON($catPathname . "/data.json");
			$catDB->clear('items');

			self::$log->add(__METHOD__,null,['$catId'=>$catId,'$items'=>$items,]);

			// *Перебираем элементы
			foreach($items as $ind=>&$item) {
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
					$artDB->set(['ind'=>[$catInd,$ind],'catId'=>$catId, 'catName'=>$catDB->get('name')]);

					unset($item['oldCatId']);
				}

				// *Обновляем базу элементов категории
				$catDB->append(['items'=>[$item]]);

			} //foreach

			if(!empty($oldCatPath))
				self::_updateCatDB(new SplFileInfo($oldCatPath));

			$catDB->__destruct();
			$catDB->__destruct= null;

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

		self::_updateCatDB(new SplFileInfo($catPath));

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
			$artDB->__destruct();
			$artDB->__destruct = null;
			self::_updateCatDB(new SplFileInfo($catPath), $cfg['name']);
		}
		else {
			self::$log->add(__METHOD__.' Не получается добавить статью '.$artPathname,E_USER_WARNING);
		}
	}

	/**
	 * *Вывод в админку
	 */
	public function RenderPU()
	{
		global $MODULE;
		?>

		<div class="header">
			<h1><a href="#" onclick="location.reload(); return false;">Настройки</a> <?=$MODULE?></h1>
		</div>

		<script src="/<?=self::$modDir?>/js/blogHelper.js" defer></script>

		<div class="content">

			<ul uk-tab uk-sticky="top: 100; show-on-up:true;">
				<li><button>Сортировка</button></li>
				<li><button>Настройки</button></li>
			</ul>

			<div class="uk-switcher">
				<div class="switcher-item">

					<h4>Добавить категорию</h4>
					<input type="text" name="addCategory" placeholder="Название категории"><button class="addCategory">ADD</button>

					<h3>Категории блога</h3>
					<?php
					// *ul#categories
					require_once __DIR__.'/admin_order.php'
					?>

					<h4>Сохранить изменения</h4>
					<button id="save_sts">Save</button>
				</div>
			</div><!-- .uk-switcher -->

		</div><!-- .content -->
		<?php
	}

	function __destruct()
	{
		// $this->RenderPU();
	}
}

$Blog = new BlogKff_adm;
$Blog->RenderPU();

// *Tests
