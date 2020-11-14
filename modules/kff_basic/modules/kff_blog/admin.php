<?php
if(__DIR__ === realpath('.')) die;
if(empty($kff)) global $kff;

$kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die('Access denied!');



class BlogKff_adm extends BlogKff
{
	// protected static
	// *Extended
		// $modDir;

	/* public function __construct()
	{
		parent::__construct();

	} // __construct */


	// *Методы контроллера

	/**
	 * *Сохрааняем сортировку статей
	 */
	protected function c_sortCategories($catsAll)
	{
		if(is_string($catsAll))
			$catsAll = json_decode($catsAll, 1);

		$cats = array_keys($catsAll);

		// todo
		$map= self::getBlogMap();

		// self::$log->add(__METHOD__,null,['$cats'=>$cats, '$catsAll'=>$catsAll]);

		self::$catsDB->replace($cats);

		// *Перебираем категории
		foreach($cats as $catInd=>$catId) {
			$catPathname = self::$storagePath . "/$catId";
			$items = &$catsAll[$catId];

			$catDB = new DbJSON($catPathname . "/data.json");
			// $catDB->set(['ind'=>$catInd]);
			$catDB->push($catInd, 'ind');

			$catDB->clear('items');

			self::$log->add(__METHOD__,null,['$catId'=>$catId,/* '$items'=>$items, */]);

			// *Перебираем элементы
			foreach($items as $ind=>&$item) {
				$artPathname= "$catPathname/{$item['id']}" . self::$blogDB->ext;

				$artDB = self::getArtDB($artPathname);

				// *Элемент перемещён в другую категорию
				if($item['oldCatId'] !== $catId){
					$oldCatPath = self::$storagePath . "/{$item['oldCatId']}";

					foreach(glob("{$oldCatPath}/{$item['id']}.*") as $removed){
						rename($removed, str_replace("{$oldCatPath}/{$item['id']}", "{$catPathname}/{$item['id']}", $removed));
					}

					// *Перезаписываем данные в базе статьи
					// $artDB = new DbJSON("$catPathname/{$item['id']}.json");

					$item = array_replace($item, ['ind'=>[$catInd,$ind],'catId'=>$catId, 'oldCatId'=>$catId, 'catName'=>$catDB->get('name')]);
					$artDB->set($item);
					$artDB->save();

				} //remove
				// *Элемент перемещён внутри категории
				elseif($item['ind'][1] != $artDB->ind[1]){
					self::$log->add(__METHOD__,null,['$artDB->ind'=>$artDB->ind,'$ind'=>$ind, '$item[\'ind\']'=>$item['ind']]);
					$artDB->set(['ind'=>$item['ind']]);
				}

				// *Обновляем базу элементов категории
				$catDB->append(['items'=>[$item]]);

			} //foreach

			/* if(!empty($oldCatPath))
				self::_updateCatDB(new SplFileInfo($oldCatPath));
			else $catDB->save(); */
			// $catDB->save();

			// *Обновляем категорию в карте
			$map->setInd($catDB->get(), 'id', $catId);

		} //foreach


		// *Обновляем карту
		// self::_createBlogMap(1);

		return $cats;
	}


	/**
	 * *Сохраняем настройки блога
	 */
	protected static function c_saveSts($sts)
	{
		// var_dump($sts);
		self::$blogDB->replace($sts);
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
		unlink("$removePath" . self::$blogDB->ext);

		self::_updateCatDB(new SplFileInfo($catPath));

		// *Обновляем карту
		self::_createBlogMap(1);
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

		self::$catsDB->save();

		// *Обновляем карту
		self::_createBlogMap(1);
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

		self::$map->append([$catDB->get()]);

		return $success;
	}

	/**
	 * *Добавляем статью
	 */
	public function c_addArticle($new_article)
	{
		if(!self::is_adm()) die('Access denied');

		$cfg = ['name'=>$new_article];
		$new_article = Index_my_addon::translit($new_article);
		$cfg['id'] = $new_article;
		$cfg['ind']= explode(',', $this->opts['ind']);

		$catId = $cfg['catId']= $this->opts['catId'] ?? 'default';
		$catName = $cfg['catName']= $this->opts['catName'];
		$catPath = self::$storagePath."/$catId";
		$cfg['path'] = Index_my_addon::getPathFromRoot($catPath);
		$artPathname = "{$catPath}/{$new_article}" . self::$blogDB->ext;

		self::$log->add(__METHOD__,null,['$cfg'=>$cfg]);

		$artDB = self::getArtDB($artPathname);

		if(!is_dir($catPath))
		{
			$this->c_addCategory($catPath);
		}

		// *Already exists
		// self::$log->add(__METHOD__." Файл $artPathname уже существует???",E_USER_WARNING,[file_exists($artPathname)]);

		if(file_exists($artPathname)) {
			self::$log->add(__METHOD__." ERROR: Файл $artPathname уже существует!",E_USER_WARNING);
			return false;
		}

		if (
			file_put_contents($artPathname,"<p>New Article - <b>$new_article</b>!</p>")
		) {
			$artDB->set($cfg);

			// *Записываем в карту
			self::$map->set([$cfg['ind'][0]=>['items'=>[
				$cfg['ind'][1]=> $artDB->get()
			]]]);

			$artDB->save();

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
		<script src="/<?=self::$modDir?>/js/blogAdmin.js" defer></script>

		<div class="content">

			<ul uk-tab uk-sticky="top: 100; show-on-up:true;">
				<li><button>Материалы</button></li>
				<li><button>Настройки</button></li>
			</ul>

			<div class="uk-switcher">
				<div class="switcher-item order">

					<h4>Добавить категорию</h4>
					<input type="text" name="addCategory" placeholder="Название категории" class="uk-width-expand"><button class="addCategory">ADD</button>

					<h3>Категории блога</h3>
					<?php
					// *ul#categories
					require_once __DIR__.'/admin_order.php'
					?>

					<h4>Сохранить изменения</h4>
					<button id="save_sts">Save</button>
				</div>

				<div class="switcher-item sts">
				<?php
					// *
					require_once __DIR__.'/admin_sts.php'
				?>
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
