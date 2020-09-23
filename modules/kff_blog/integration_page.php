<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

// *Очищаем основную систему от лишнего кода
class BlogKff_page extends BlogKff
{
	// public ;
	/**
	 * *Лента новостей
	 * @param quantity - кол-во элементов
	 */
	public function newsTape($quantity=5)
	{
		global $Page;
		$o= "";


		foreach($this->getArticleList($quantity) as $ts=>&$artPathname){
			$db= self::getArtDB($artPathname)->get();

			// *Черновики видны только админу
			if(!empty($db['not-public'])){
				if(self::is_adm()){
					$o.= "<p class='not-public'>Черновик</p>";
				}
				else{
					continue;
				}
			}

			$doc = new DOMDocument('1.0','utf-8');
			@$doc->loadHTMLFile($artPathname);

			$catId= $db['catId'] ?? basename(dirname($artPathname));
			$catName= $db['catName'];
			$artId= basename($artPathname, self::$l_cfg['ext']);

			$xpath = new DOMXpath($doc);
			$imgs = $xpath->query("//img[1]");
			$fragm = $xpath->query("//p");
			// self::$log->add(__METHOD__,null,[$img, $fragm]);

			// echo "$artId<br>";
			// echo addcslashes($artId, "'")."<br>";
			$artHref= "/{$Page->id}/$catId/$artId";
			$o.="<a href=\"$artHref\"><h3>{$db['name']}</h3></a>";

			// *Первое изображение
			if(!empty($img= $imgs->item(0)))
			{
				$o.= "<img src=".$img->getAttribute("src").">" ;
				self::$log->add('$imgs->item(0)->getAttribute("src")',null,[$img->getAttribute("src")]);
			}

			// *Первые параграфы
			if(!empty($fragm))
			{
				$c=0;

				while ($c < 5) {
					if(!empty($p= $fragm->item($c++))){
						$o.= "<p>".utf8_decode($p->nodeValue)."</p>" ;
						// $o.= "<p>" . $p->nodeValue . "</p>" ;
					}
				}
			}

			$o.="<div class='info'>
			<p class='uk-margin-small-bottom'>Категория: <b>$catName</b></p>
			<p class='uk-margin-remove'>Дата: " . date(self::DATE_FORMAT, $ts) . "</p>
			</div>
			<p style='text-align:right;'><a href=\"$artHref\"><button>Читать</button></a></p>
			<hr class=\"uk-divider-icon\">";
		} // foreach

		return $o;
	}

	/**
	 * *Получаем список статей
	 * note Перебираем  все файлы со статьями из всех категорий.
	 * @param quantity - длина возвращаемого массива
	 * @return {Array} - quantity последних статей
	 */
	public function getArticleList($quantity=null)
	{
		$arr= [];
		$storageIterator= new RecursiveDirectoryIterator(self::$storagePath, FilesystemIterator::SKIP_DOTS| FilesystemIterator::UNIX_PATHS);

		/* $iterator = new RegexIterator(
			$storageIterator->getChildren(),
			'~\\' . self::$l_cfg['ext'] . '$~'
		); */

		foreach(new RecursiveIteratorIterator($storageIterator) as $c=>$FI){
			if($FI->isDir() || $FI->getExtension() !== substr(self::$l_cfg['ext'], 1)) continue;

			$arr[$FI->getMTime()]= $FI->getPathname();
			// echo $c . $FI . '<br>';
		}

		krsort($arr);

		if(is_numeric($quantity) && $quantity != 0)
			$arr= array_slice($arr, 0, $quantity, 1);

		// self::$log->add(__METHOD__,null,[$arr]);

		return $arr;
	}


	// *Стартовая страница блога
	public static function is_indexPage()
	{
		global $URI, $Page;
		return is_object($Page) && $URI[1] === $Page->id && empty($URI[2]);
	}


	/**
	 * *Сохраняем редактирование
	 */
	protected function c_saveEdit($html)

	{
		$db= self::getArtDB();
		self::$log->add('$this->opts=',null,[$this->opts]);

		array_walk($this->opts['artOpts'], function(&$v, $k){
			switch ($k) {
				case 'keywords':
					$v= preg_replace('~\s*(,)\s*~u', '$1', $v);
					break;
				case 'not-public':
					$v= (bool)$v;
					break;
			}
		});

		/* $data= [
			'title'=> $this->opts['title'],
			'description'=> $this->opts['description'],
			'keywords'=> preg_replace('~\s*(,)\s*~u', '$1', $this->opts['keywords']),
			'not-public'=> (bool)$this->opts['not-public'],
		]; */
		$db->set($this->opts['artOpts']);

		return file_put_contents(self::$storagePath . "/{$this->opts['cat']}/{$this->opts['art']}" . self::$l_cfg['ext'], $html);
	}



	/**
	 * *Вывод контента по /$Page->id/catName/artName
	 */
	private function _printArticle()
	{
		global $URI, $Page;
		// *вырубаем в админке
		if(	!is_object($Page)	) {
			return;
		}

		// *На стартовой - новостная лента
		if(self::is_indexPage()) {
			echo $this->newsTape();
			return;
		}

		$path = str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];

		self::$log->add('$path, file_exists($path), $URI=',null,[$path, file_exists($path), $URI]);

		if( !file_exists($path) ) return;

		include_once $path;
	}


	/**
	 * *Upload images
	 */
	// private function _Upload()
	public function Upload()
	{
		// *inputs handler
		global $Page, $URI;
		if(
			!count($_FILES)
		) return;

		if(!self::is_adm())
			die('Access denied!');

		require_once DR.'/'.self::$dir.'/Uploads.class.php';
		$pathInFiles = "/{$Page->id}/{$URI[2]}";
		Uploads::$input_name = "upload";
		Uploads::$pathname .= $pathInFiles;
		Uploads::$allow = ['jpg','jpeg','png','gif'];

		$Upload = new Uploads;

		if(!$Upload->checkSuccess()) foreach($Upload->getResult() as &$str){
			echo $str;
		}
		else {
			$out= '';
			foreach($Upload->fileNames as $name){
				$out.= "/files$pathInFiles/$name<br>";
			}
			echo $out;
		}

	}


	/**
	 * *Вывод в страницу
	 */
	public function Render()
	{
		global $Page;
		$edit= isset($_GET['edit']);

		$artDB= self::getArtDB();

		echo '<h1 id="title">' . ($artDB->get('title') ?? $artDB->get('name')) . '</h1>';
	?>

		<script src="/<?=self::$modDir?>/js/blogHelper.js"></script>

		<?php
		// *Редактирование
		if($edit)
		{
		?>
		<div id="artOpts" class="uk-flex uk-flex-wrap uk-flex-middle">
			<span class="uk-width-1-3"><b>name</b></span> <input name="name" class="uk-width-expand" type="text" placeholder="name" value="<?=$artDB->get('name')?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3"><b>title</b></span> <input name="title" class="uk-width-expand" type="text" placeholder="title" value="<?=$artDB->get('title')?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3"><b>description</b></span><textarea name="description" class="uk-width-expand" type="text" placeholder="description"><?=$artDB->get('description')?></textarea><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3"><b>keywords</b></span><input name="keywords" class="uk-width-expand" type="text" placeholder="keywords" value="<?=$artDB->get('keywords')?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3"><b>Черновик</b></span><!-- <input name="not-public" class="uk-width-expand" type="text" placeholder="bool" value="<?=$artDB->get('not-public')?>"> -->
			<select name="not-public" value="<?=!empty($artDB->get('not-public'))? 1: 0 ?>">
				<option value="0">Опубликовано</option>
				<option value="1" <?=!empty($artDB->get('not-public'))? 'selected': '' ?>>Черновик</option>
			</select>

		</div>

		<?php
		}
		?>

		<div id='editor1' class="blog_content" <?=$edit?'contenteditable=true':''?>>

		<?php
			$this->_printArticle();
		?>
		</div><!-- .blog_content -->

		<?php
		if($edit)
		{
		?>
		<div>
			<button id="saveEdit" class="uk-button-primary">SAVE</button>
		</div>

		<script type="text/javascript" src="/modules/ckeditor_4.5.8_standard/ckeditor/ckeditor.js"></script>

		<script>
			document.querySelector('#saveEdit')
			.addEventListener('click', BH.editRequest.bind(null));

			CKEDITOR.replace( 'editor1');

			CKEDITOR.disableAutoInline = true;
			/* CKEDITOR.inline( 'editor1', {
				customConfig: ''
			}); */

		</script>

		<?php
		}
		elseif(self::is_adm() && !self::is_indexPage())
		{
			echo '<a href="?edit"><button>EDIT</button></a>';
		}
	}

}

ob_start();

$Blog = new BlogKff_page;

if(!$Blog->Upload())
	$Blog->Render();

?>



<?php
return ob_get_clean();