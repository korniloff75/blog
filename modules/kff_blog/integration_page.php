<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

// *Очищаем основную систему от лишнего кода
class BlogKff_page extends BlogKff
{
	/**
	 * *Лента новостей
	 */
	public function newsTape()
	{
		echo "<h2>Тут будет лента новостей</h2>";
	}


	// *Стартовая страница блога
	public static function is_indexPage()
	{
		global $URI, $Page;
		return is_object($Page) && $URI[1] === $Page->id && empty($URI[2]);
	}


	/**
	 * *Вывод контента по /$Page->id/catName/artName
	 * todo добавить страницу по умолчанию
	 */
	private function _addArticle()
	{
		global $URI, $Page;
		// *вырубаем в админке
		if(	!is_object($Page)	) {
			return;
		}

		// *На стартовой - новостная лента
		if(self::is_indexPage()) {
			$this->newsTape();
			return;
		}

		$path = str_replace($Page->id, basename(self::$storagePath), DR.explode('?',REQUEST_URI)[0]) . self::$l_cfg['ext'];

		self::$log->add('$path=',null,[$path, file_exists($path), $URI]);

		if( !file_exists($path) ) return;

		require_once $path;
	}


	/**
	 * *Вывод в страницу
	 */
	public function Render()
	{
		global $Page;
		$edit= isset($_GET['edit']);
	?>

		<script src="/<?=self::$modDir?>/js/blogHelper.js"></script>

		<ul id="categories" class="uk-subnav uk-subnav-divider">

		<?php
		foreach($this->getCategories() as &$cat) {
			// $catData = (new DbJSON(self::$storagePath."/$cat/cfg.json"))->get();
			$catData = $this->getCategory($cat);
			if(!count($catData['items']))
				continue;
			// print_r ($catData);
		?>

			<li>

				<a href="#"><h4><?=$catData['name']?></h4></a>
				<div uk-dropdown="mode: hover; delay-hide: 100;">

					<ul data-cat=<?=$cat?>  class="uk-nav uk-dropdown-nav">

					<?php

					foreach($catData['items'] as &$art) {

						echo "<li data-id={$art['id']} data-cat=$cat>
						<a href=\"/{$Page->id}/$cat/{$art['id']} \">{$art['name']}</a>

						</li>";
					}

					?>
					</ul>
				</div>

		</li>
		<?php
		}
		?>

	</ul><!-- #categories -->

	<?php
	if($edit)
	{
	?>
	<div>
		<button id="saveEdit">SAVE</button>
	</div>
	<script>
		document.querySelector('#saveEdit')
		.addEventListener('click', BH.editRequest.bind(null, '.blog_content'));

	</script>
	<?php
	}
	?>

	<div class="blog_content" <?=$edit?'contenteditable=true':''?>>
		<?php $this->_addArticle()?>
	</div><!-- .blog_content -->
	<?php
	}
}

ob_start();

$Blog = new BlogKff_page;

$Blog->Render();

?>



<?php
return ob_get_clean();