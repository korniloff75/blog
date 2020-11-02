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
			$artData['ts']= $ts;

			// *Черновики видны только админу
			if(!empty(filter_var($artData['not-public'],FILTER_VALIDATE_BOOLEAN))){
				if(self::is_adm()){
					$o.= "<p class='not-public'>Черновик</p>";
				}
				else{
					continue;
				}
			}

			ob_start();
				require $artPathname;
			$art= ob_get_clean();

			$doc = new DOMDocument('1.0','utf-8');
			// @$doc->loadHTMLFile($artPathname);
			@$doc->loadHTML($art);
			$doc->normalizeDocument();

			$catId= $artData['catId'] ?? basename(dirname($artPathname));
			$catName= $artData['catName'] ?? $catId;
			$artId= basename($artPathname, self::$blogDB->ext);

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
					($src= $img->getAttribute("src"))
					|| ($src= $img->getAttribute("data-src"))
				)
				&& (
					$src= self::fixImgs($artId, $src)
				)
			){
				$o.= "<img src=".$src.">" ;
				// self::$log->add('$img->getAttribute("src")',null,[$img->getAttribute("src")]);
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
			<p class='uk-margin-small-bottom'>Категория: <a href='?name=getCategoryList&value=$catId' onclick='BH.getCategoryList(\"$catId\",\"$catName\", event)' style='cursor:pointer;'>$catName</a></p>";

			ob_start();
			self::renderDateBlock($artData);
			$o.= ob_get_clean();

			$o.= "</div>
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
			'~\\' . self::$blogDB->ext . '$~'
		); */

		// foreach(new RecursiveIteratorIterator($storageIterator) as $c=>$FI){
		foreach(new RecursiveIteratorIterator($storageIterator) as $c=>$FI){
			if(
				$FI->isDir()
				|| $FI->getExtension() !== substr(self::$blogDB->ext, 1)
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
	 * *Список статей из категории
	 * @param catId -
	 */
	protected function c_getCategoryList($catId)
	{
		global $Page;
		ob_clean();
		/* echo '<pre>';
		var_dump(self::getCategoryData($catId)['items'] );
		echo '</pre>'; */
		echo '<ul>';
		foreach(self::getCategoryData($catId)['items'] as $artData){
			echo "<li><a href='/{$Page->id}/$catId/".$artData['id']."'>{$artData['name']}</a></li>";
		}
		echo '</ul>';
		ob_end_flush();
		die;
	}

	/**
	 * *Список статей по #тэгу
	 * @param hashtag -
	 */
	protected function c_getHashList($hashtag)
	{
		global $Page;
		ob_clean();

		echo '<ul>';

		foreach(self::getBlogMap() as $catData){
			foreach($catData['items'] as &$artData){
				if(stripos($artData['tag'], $hashtag) === false) continue;

				echo "<li><a href=\"/{$Page->id}/{$catData['id']}/{$artData['id']}\">{$artData['title']}</a></li>";
			}
		}

		echo '</ul>';
		ob_end_flush();
		die;
	}


	/**
	 * *Сохраняем редактирование
	 */
	protected function c_saveEdit($html)

	{
		if(!self::is_adm()) return false;

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

		array_walk($this->opts['artOpts'], function(&$v, $k) use($html){
			switch ($k) {
				case 'tag':
					// *Вытаскиваем #тэги
					preg_match_all('~[\s\W]#([^"]+?\b)~u', $html, $matchTags);
					self::$log->add(__METHOD__,null,['$matchTags[1]'=>$matchTags[1], '$html'=>$html]);
					if(count($matchTags[1])){
						$v= implode(',', array_unique(array_merge($matchTags[1], array_filter(explode(',', $v)))));
					}
				case 'keywords':
					$v= preg_replace(['~\s*(,)\s*~u','~\s+~u'], ['$1', '_'], $v);
					break;
				case 'title':
					if(empty(trim($v))) $v= $this->opts['artOpts']['name'];
					break;
				case 'not-public':
				case 'enable-comments':
					$v= filter_var($v,FILTER_VALIDATE_BOOLEAN);
					break;
			}
		});

		$this->opts['artOpts']['date'] = date (self::DATE_FORMAT, filemtime($artPathname));

		$artDB->set($this->opts['artOpts']);
		// self::$map->set()

		$html= htmlspecialchars_decode(str_replace(['#+#','#-#'], ['<?','?>'], trim($html)));
		// $html= preg_replace(['~^[\s\n'.PHP_EOL.']+?~','~\n{2,}~'], ['',"\n\n"], $html);

		file_put_contents(self::$storagePath . "/{$this->opts['cat']}/{$this->opts['art']}" . self::$blogDB->ext, $html);

		// *Обновляем карту

		$map= self::getBlogMap();
		$ind= $artDB->get('ind');

		$newData= [$ind[0]=>[
			'items'=>[$ind[1]=>$artDB->get()]
		]];
		$map->set($newData);

		self::$log->add(__METHOD__,null,['$ind'=>$ind,'$artDB'=>$artDB, "\$map->get({$ind[0]})"=>$map->get()]);
	}



	/**
	 * *Вывод контента по /$Page->id/catName/artName
	 */
	private function _printArticle()
	{
		global $URI, $Page;

		if(
			!is_object($Page)
			|| !($artDB= self::getArtDB())->count()
		) {
			self::$log->add(__METHOD__.': Отключаем в админке', null,['$Page'=>$Page, '$artDB'=>$artDB]);
			return;
		}

		$path = self::getArtPathname();
		if( !file_exists($path) ) return;

		self::$log->add(__METHOD__,null,['$path'=>$path, '$URI'=>$URI]);

		if(self::is_edit()){
			$article= file_get_contents($path);
			// echo htmlentities($article);
			echo str_replace(['<?','?>'], ['#+#','#-#'],$article);
		}
		else{
			include_once $path;
		}


		if(!empty($tags= array_filter(explode(',', $artDB->tag)))){
			echo '<div class="tags uk-margin" itemprop="about" itemscope itemtype="https://schema.org/Thing">';

			foreach($tags as $tag){
				if(substr($tag,0,1)!=='#') $tag= "#$tag";
			?>
				<a href="?name=getHashList&value=<?=trim($tag, '#')?>" itemprop="name" class="uk-button uk-button-small" onclick="BH.getHashList('<?=trim($tag, '#')?>', event);"><?=$tag?></a>

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

		$folder= $this->opts['folder'] ?? self::$State->get('CKEfolder');

		CKEditorUploads::$pathname .= "/CKeditor/$folder";

		self::$State->upd($folder, 'CKEfolder');

		self::$log->add(__METHOD__, null, ['CKEditorUploads::$pathname'=>CKEditorUploads::$pathname, 'self::$State->get(\'CKEfolder\')'=>Index_my_addon::$State->get('CKEfolder')]);

		if(self::is_adm() && !empty($upload))
			new CKEditorUploads;

		CKEditorUploads::RenderBrowser();

		die;
	}

	/**
	 * Загружаем файлы
	 */
	protected function c_CKEditorUpload()
	{
		$this->opts['folder']= self::$State->get('CKEfolder');
		// *Upload
		$this->c_createCKEditorBrowser('upload');
	}

	/**
	 * ?Предыдущая / Следующая
	 * todo...
	 */
	function c_getSiblingArticle($data)
	{
		$map= self::getBlogMap();
		$ind= self::getArtDB()->ind;
		self::$log->add(__METHOD__, null,['data'=>$data]);
	}

	/**
	 * Создаем папку
	 */
	protected function c_addImgFolder(string $name)
	{
		ob_clean();
		if(empty($name)) self::$log->add(__METHOD__. 'folder name is EMPTY!', E_USER_ERROR);
		elseif(self::is_adm()) mkdir(CKEditorUploads::$pathname . "/CKeditor/$name", 0775, 1);
		$this->c_createCKEditorBrowser();
		die;
	}


	/**
	 * *Вывод в страницу
	 */
	public function Render($artPathname=null)
	{
		$artDB= self::getArtDB($artPathname);

		self::$log->add(__METHOD__,null,['$artDB'=>$artDB]);

		echo '<script src="/' .self::$modDir. '/js/blogHelper.js"></script>';

		// *На стартовой - новостная лента
		if(!$artDB->count()){
			echo $this->newsTape(self::$l_cfg['newsTapeLength']);
			return;
		}

		echo '<h1 id="title" itemprop="headline" class="'. (filter_var($artDB->{'not-public'},FILTER_VALIDATE_BOOLEAN)? 'not-public':'') .'">' . ($artDB->title ?? $artDB->name) . '</h1>';
		?>

		<meta itemprop="identifier" content="<?=self::getPathFromRoot(self::getArtPathname())?>">

		<?php
		// *Редактирование
		if(self::is_edit())
		{
		?>
		<div id="artOpts" class="uk-flex uk-flex-wrap uk-flex-middle">
			<span class="uk-width-1-3@s"><b>name</b></span> <input name="name" class="uk-width-expand" type="text" placeholder="name" value="<?=$artDB->name?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>title</b></span> <input name="title" class="uk-width-expand" type="text" placeholder="title" value="<?=$artDB->title ?? $artDB->name?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>description</b></span><textarea name="description" class="uk-width-expand uk-resize-vertical" type="text" placeholder="description"><?=$artDB->description?></textarea><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>keywords</b> (через запятую)</span><input name="keywords" class="uk-width-expand" type="text" placeholder="keywords" value="<?=$artDB->keywords?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>Метки</b> (через запятую)</span><input name="tag" class="uk-width-expand" type="text" placeholder="метки" value="<?=$artDB->tag?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>Автор</b></span><input name="author" class="uk-width-expand" type="text" value="<?=$artDB->author?>"><p class="uk-width-1 uk-margin-remove"></p>

			<span class="uk-width-1-3@s"><b>Комментарии</b></span>
			<select name="enable-comments" value="<?=!empty($artDB->{'enable-comments'})? 1: 0 ?>">
				<option value="0">Отключены</option>
				<option value="1" <?=!empty($artDB->{'enable-comments'})? 'selected': '' ?>>Подключены</option>
			</select>

			<span class="uk-width-1-3@s"><b>Черновик</b></span>
			<select name="not-public" value="<?=!empty($artDB->{'not-public'})? 1: 0 ?>">
				<option value="0">Опубликовано</option>
				<option value="1" <?=!empty($artDB->{'not-public'})? 'selected': '' ?>>Черновик</option>
			</select>

		</div>

		<?php
		}
		?>


		<div id='editor1' class="blog_content" <?=self::is_edit()?'contenteditable=true':''?> itemprop="articleBody">
			<?php $this->_printArticle() ?>
		</div><!-- .blog_content -->


		<?php
		if(self::is_edit())
		{
		?>
		<div class="uk-margin-vertical">
			<button id="saveEdit" class="uk-button-primary">SAVE</button>
			<button id="resetEdit" class="uk-button-default uk-float-right" onclick="location.replace(location.pathname)">Reset</button>
		</div>

		<script type="text/javascript" src="/modules/ckeditor_4.5.8_standard/ckeditor/ckeditor.js"></script>

		<script>
			'use strict';

			// *saveEdit
			U.on('#saveEdit', 'click', BH.editRequest.bind(null, <?=DbJSON::toJSON($artDB->get())?>));

			// *Удаляем теги
			// $('#editor1').find('[itemprop="about"]').remove();
			U.$$('[itemprop="about"]', U.$('#editor1')).forEach(i=>i.remove());

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

		if(!self::is_indexPage()){
			self::renderDateBlock($artDB->get());

			echo '<div class="uk-margin-vertical">
			<a href="#" onclick="BH.getSiblingArticle(-1,event)"><span uk-pagination-previous></span>Предыдущая</a>
			<a href="#" class="uk-float-right" onclick="BH.getSiblingArticle(+1,event)">Следующая<span uk-pagination-next></span></a>
			</div>';
		}

		// *Comments
		if(self::is_adm() || filter_var($artDB->{'enable-comments'},FILTER_VALIDATE_BOOLEAN)){
			require_once DR.'/'. self::$internalModulesPath . '/kff_comments/Comments.class.php';

			// self::$log->add(__METHOD__,null,['$artDB'=>$artDB]);

			// todo @param $artDB
			$comments= new Comments($artDB->get());
			$comments->Render();
		}
	}


	public static function renderDateBlock(array $artData)
	{
		$ts= $artData['ts'] ?? (file_exists(self::getArtPathname())?
			filemtime(self::getArtPathname())
			: null);

		echo '<div class="dateBlock uk-margin">';

		if(!empty($artData['author'])){
		?>
		<p>Автор: <em itemprop="author"><?=$artData['author']?></em></p>
		<?php } ?>

		<p class="uk-text-meta">Дата публикации / редактирования: <time itemprop="dateModified" data-ts="<?=$ts?>"
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