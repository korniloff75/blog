<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

// *Очищаем основную систему от лишнего кода
class BlogKff_page extends BlogKff
{
	private
		$artPathname,
		$artData;

	/* public function __construct()
	{
		global $Page;
		parent::__construct();

		$this->artPathname=
	} */


	/**
	 * *Лента новостей
	 * @param quantity - кол-во элементов
	 */
	public function newsTape($quantity=5)
	{
		global $Page;
		$o= "";


		foreach($this->getArticleList($quantity) as $ts=>&$artPathname){
			$artData= self::getArtData($artPathname);

			// *Черновики видны только админу
			if(!empty($artData['not-public'])){
				if(self::is_adm()){
					$o.= "<p class='not-public'>Черновик</p>";
				}
				else{
					continue;
				}
			}

			$doc = new DOMDocument('1.0','utf-8');
			@$doc->loadHTMLFile($artPathname);
			$doc->normalizeDocument();

			$catId= $artData['catId'] ?? basename(dirname($artPathname));
			$catName= $artData['catName'] ?? $catId;
			$artId= basename($artPathname, self::$l_cfg['ext']);

			$xpath = new DOMXpath($doc);
			$imgs = $xpath->query("//img[1]");
			$fragm = $xpath->query("//p");

			// self::$log->add(__METHOD__,null,[$img, $fragm]);

			// echo "$artId<br>";
			// echo addcslashes($artId, "'")."<br>";
			$artHref= "/{$Page->id}";
			if($catId) $artHref.= "/$catId";
			$artHref.= "/$artId";

			$o.="<a href=\"$artHref\"><h3 class='uk-h3'>" . ($artData['title'] ?? $artData['name']) . "</h3></a>";

			// *Первое изображение
			if(
				!empty($img= $imgs->item(0))
				&& (
					$src= $img->getAttribute("src")
					// || ($src= $img->getAttribute("data-src"))
				)
			){
				$o.= "<img src=".$src.">" ;
				// self::$log->add('$imgs->item(0)->getAttribute("src")',null,[$img->getAttribute("src")]);
			}

			// *Ищем сепаратор
			// todo ...
			if(
				false &&
				($separators= $xpath->query("//p[@class=separator]"))
				&& !empty($separators->item(0))
			){
				$c=0;
				while (
					!empty($p= $fragm->item($c++))
					// && !$p->attributes
				) {
					// todo определить: есть ли у элемента нужный класс
				}
			}

			// *Первые параграфы
			if(!empty($fragm->item(0)))
			{
				$c=0;

				while ($c < 5) {
					if(!empty($p= $fragm->item($c++))){
						$o.= "<p>".utf8_decode($p->nodeValue)."</p>" ;
						// $o.= "<p>" . $p->nodeValue . "</p>" ;
					}
				}
			}

			$o.="<div class='info uk-float-left'>
			<p class='uk-margin-small-bottom'>Категория: <b>$catName</b></p>
			<p class='uk-margin-remove'>Дата: <time itemprop=\"dateModified\"
			datetime=\"" . date(DATE_ISO8601, $ts) . "\">" . date(self::DATE_FORMAT, $ts) . "</time></p>
			</div>
			<p style='text-align:right;' class='uk-float-right'>";
			if(self::is_adm()){
				$o.= "<a href=\"{$artHref}?edit\" class='uk-margin-right' uk-icon='icon: file-edit' title='Редактировать'></a>";
			}
			$o.= "<a href=\"$artHref\"><button>Читать</button></a></p>
			<p class='uk-clearfix'></p>

			<hr class=\"uk-divider-icon \">";
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
		self::$log->add(__METHOD__ . 'self::$storagePath= ' . self::$storagePath);
		$storageIterator= new RecursiveDirectoryIterator(self::$storagePath, FilesystemIterator::SKIP_DOTS| FilesystemIterator::UNIX_PATHS);

		/* $iterator = new RegexIterator(
			$storageIterator->getChildren(),
			'~\\' . self::$l_cfg['ext'] . '$~'
		); */

		// foreach(new RecursiveIteratorIterator($storageIterator) as $c=>$FI){
		foreach(new RecursiveIteratorIterator($storageIterator) as $c=>$FI){
			if(
				$FI->isDir()
				|| $FI->getExtension() !== substr(self::$l_cfg['ext'], 1)
				|| !$ts= $FI->getMTime()
			) continue;

			$arr[$ts]= $FI->getPathname();
			// echo $c . $FI . '<br>';
		}

		krsort($arr);

		if(is_numeric($quantity) && $quantity != 0)
			$arr= array_slice($arr, 0, $quantity, 1);

		// self::$log->add(__METHOD__,null,[$arr]);

		return $arr;
	}


	/**
	 * *Сохраняем редактирование
	 */
	protected function c_saveEdit($html)

	{
		$artPathname= self::getArtPathname();
		self::$log->add(__METHOD__,null,['$artPathname'=>$artPathname]);

		if(
			!self::is_adm()
			|| !file_exists($artPathname)
		)
			return false;

		$artDB= self::getArtDB($artPathname);
		// $artData= self::getArtData($artPathname);

		self::$log->add(__METHOD__,null,['$this->opts[\'artOpts\']'=>$this->opts['artOpts']]);

		array_walk($this->opts['artOpts'], function(&$v, $k){
			switch ($k) {
				case 'tag':
				case 'keywords':
					$v= preg_replace('~\s*(,)\s*~u', '$1', $v);
					break;
				case 'title':
					if(empty(trim($v))) $v= $this->opts['artOpts']['name'];
					break;
				case 'not-public':
					$v= (bool)$v;
					break;
			}
		});

		$this->opts['artOpts']['date'] = date ('Y-m-d', filemtime($artPathname));

		$artDB->set($this->opts['artOpts']);
		// self::$map->set()

		$html= htmlspecialchars_decode(str_replace(['#+#','#-#'], ['<?','?>'], trim($html)));
		$html= preg_replace(['~^[\s\n'.PHP_EOL.']+?~','~\n{2,}~'], ['',"\n\n"], $html);

		file_put_contents(self::$storagePath . "/{$this->opts['cat']}/{$this->opts['art']}" . self::$l_cfg['ext'], $html);

		// *Обновляем карту
		// self::_createBlogMap(1);
		// !ind === null
		$map= self::getBlogMap();
		$ind= $artDB->get('ind');

		$newData= [$ind[0]=>[
			'items'=>[$ind[1]=>$artDB->get()]
		]];
		$map->set($newData);

		self::$log->add(__METHOD__,null,['$ind'=>$ind,'$artDB'=>$artDB, '$html'=>$html, "\$map->get({$ind[0]})"=>$map->get($ind[0])]);
	}



	/**
	 * *Вывод контента по /$Page->id/catName/artName
	 */
	private function _printArticle($edit=null)
	{
		global $URI, $Page;

		if(	!is_object($Page)	) {
			self::$log->add(__METHOD__.': Отключаем в админке', null,['$Page'=>$Page]);
			return;
		}

		$path = self::getArtPathname();

		self::$log->add(__METHOD__,null,['$path'=>$path,'file_exists($path)'=> file_exists($path), '$URI'=>$URI]);

		if( !file_exists($path) ) return;

		$article= file_get_contents($path);

		// *Вытаскиваем #тэги
		preg_match_all('~[\s\W](#\D.+?\b)~u', $article, $matchTags);

		// self::$log->add(__METHOD__,null,['$tag'=>$matchTags]);

		$this->artData['tag']= array_unique(array_merge($matchTags[1], explode(',', $this->artData['tag'])));

		if($edit){
			// echo htmlentities($article);
			echo str_replace(['<?','?>'], ['#+#','#-#'],$article);
		}
		else{
			include_once $path;
		}


		if(!empty($this->artData['tag'])){
			echo '<div class="tags uk-margin" itemprop="about" itemscope itemtype="https://schema.org/Thing">';

			foreach($this->artData['tag'] as $tag){
				if(!trim($tag)) continue;
				if(substr($tag,0,1)!=='#') $tag= "#$tag";
			?>
				<span itemprop="name" class="uk-button uk-button-small"><?=$tag?></span>

			<?php
			}
			echo '<!--about--></div>';
		}
	}



	/**
	 * Выводим файловый менеджер для загрузки изображений в CKEditor
	 *
	 */
	protected function c_createCKEditorBrowser($upload=null)
	{
		global $Page, $URI;
		$pathToFiles = "/CKeditor";
		CKEditorUploads::$pathname .= $pathToFiles;
		self::$log->add('CKEditorUploads::$pathname= ' . CKEditorUploads::$pathname);

		CKEditorUploads::RenderBrowser();
		if(self::is_adm() && !empty($upload))
			new CKEditorUploads;

		die;
	}

	/**
	 * Загружаем файлы
	 */
	protected function c_CKEditorUpload()
	{
		// *Upload
		$this->c_createCKEditorBrowser('upload');
	}


	/**
	 * *Вывод в страницу
	 */
	public function Render()
	{
		$edit= isset($_GET['edit']);

		$artData= &$this->artData;
		$artData= self::getArtData(self::getArtPathname());

		self::$log->add(__METHOD__,null,['$artData'=>$artData]);

		// *На стартовой - новостная лента
		if(!$artData){
			echo $this->newsTape(self::$l_cfg['newsTapeLength']);
			return;
		}

		echo '<h1 id="title" itemprop="headline">' . ($artData['title'] ?? $artData['name']) . '</h1>';
		?>

		<meta itemprop="identifier" content="<?=self::getPathFromRoot(self::getArtPathname())?>">

		<script src="/<?=self::$modDir?>/js/blogHelper.js"></script>

		<?php
		// *Редактирование
		if($edit)
		{
		?>
		<div id="artOpts" class="uk-flex uk-flex-wrap uk-flex-middle">
			<span class="uk-width-1-3@s"><b>name</b></span> <input name="name" class="uk-width-expand" type="text" placeholder="name" value="<?=$artData['name']?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>title</b></span> <input name="title" class="uk-width-expand" type="text" placeholder="title" value="<?=$artData['title'] ?? $artData['name']?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>description</b></span><textarea name="description" class="uk-width-expand" type="text" placeholder="description"><?=$artData['description']?></textarea><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>keywords</b> (через запятую)</span><input name="keywords" class="uk-width-expand" type="text" placeholder="keywords" value="<?=$artData['keywords']?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>Метки</b> (через запятую)</span><input name="tag" class="uk-width-expand" type="text" placeholder="метки" value="<?=$artData['tag']?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>Автор</b></span><input name="author" class="uk-width-expand" type="text" value="<?=$artData['author']?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>Черновик</b></span>
			<select name="not-public" value="<?=!empty($artData['not-public'])? 1: 0 ?>">
				<option value="0">Опубликовано</option>
				<option value="1" <?=!empty($artData['not-public'])? 'selected': '' ?>>Черновик</option>
			</select>

		</div>

		<?php
		}
		?>


		<div id='editor1' class="blog_content" <?=$edit?'contenteditable=true':''?> itemprop="articleBody">
			<?php $this->_printArticle($edit) ?>
		</div><!-- .blog_content -->


		<?php
		if($edit)
		{
		?>
		<div class="uk-margin-vertical">
			<button id="saveEdit" class="uk-button-primary">SAVE</button>
			<button id="resetEdit" class="uk-button-default uk-float-right" onclick="location.replace(location.pathname)">Reset</button>
		</div>

		<script type="text/javascript" src="/modules/ckeditor_4.5.8_standard/ckeditor/ckeditor.js"></script>

		<script>
			document.querySelector('#saveEdit')
			.addEventListener('click', BH.editRequest.bind(null));

			// *Удаляем теги
			$('#editor1').find('[itemprop="about"]').remove();

			// *Запускаем редактор с файловым браузером
			CKEDITOR.replace( 'editor1', {
				filebrowserBrowseUrl: '?name=createCKEditorBrowser',
				disallowedContent : 'img{width,height}',
				image_removeLinkByEmptyURL: true,
			});

			CKEDITOR.disableAutoInline = true;
			/* CKEDITOR.inline( 'editor1', {
				customConfig: ''
			}); */

		</script>

		<?php
		}
		elseif(self::is_adm() && !self::is_indexPage())
		{
			echo '<p><a href="?edit"><button>EDIT</button></a></p>';
		}

		// var_dump(self::is_indexPage());

		if(!self::is_indexPage()){
			$this->renderDateBlock($artData);
		}
	}


	function renderDateBlock(array $artData)
	{
		$ts= file_exists(self::getArtPathname())?
			filemtime(self::getArtPathname())
			: null;

		echo '<div class="dateBlock uk-margin">';

		if(!empty($artData['author'])){
		?>

		<p>Автор: <em itemprop="author"><?=$artData['author']?></em></p>
		<?php } ?>

		<p>Дата публикации / редактирования: <time itemprop="dateModified" data-ts="<?=$ts?>"
		datetime="<?=date(DATE_ISO8601, $ts)?>"><?=date(self::DATE_FORMAT, $ts)?></time></p>

		</div>
		<?php
	}

	function __destruct()
	{
		// $this->Render();
	}

}

ob_start();

$Blog = new BlogKff_page;

$Blog->Render();

return ob_get_clean();
// ob_end_clean();