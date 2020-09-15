<?php
class BlogKff extends Index_my_addon
{
	protected static
		$modDir,
		// *default
		$def_cfg = [
			'name'=> 'Блог',
			'ext'=>'.dat'
		],
		// *Локальный конфиг
		$l_cfg,
		$storagePath = \DR.'/kff_blog_data',
		$catPath = __DIR__.'/categories.json';


	public function __construct()
	{
		global $kff;

		// *Директория модуля от DR
		self::$modDir = $kff::getPathFromRoot(__DIR__);

		$this->DB = new DbJSON(__DIR__.'/cfg.json');

		self::$l_cfg= $this->DB->get();
		if(empty(self::$l_cfg))
			$this->DB->replace(self::$def_cfg);

		$this->catsDB = new DbJSON(self::$catPath);

		self::addUIkit();

		// self::$log->add('self::$cfg=',null, [self::$cfg]);

	} // __construct


	/**
	 * *Получаем категории из базы
	 */
	public function getCategories()
	{
		return $this->catsDB->get();
	}


	/**
	 * *Вывод контента по /Blog/catName/artName
	 */
	public function addContent()
	{
		global $URI;
		$path = str_replace('Blog', basename(self::$storagePath), DR.REQUEST_URI) . self::$l_cfg['ext'];

		self::$log->add('$path=',null,[$path, file_exists($path)]);

		if(
			count($URI) < 3
			|| !file_exists($path)
		) return;

		require_once $path;
	}


	/**
	 * *Получаем UIkit
	 */
	public static function addUIkit()
	{
		// *UIkit подключён
		if(
			!empty(self::$cfg['uk']['include_uikit'])
		) return;

		$UIKpath = '/modules/kff_basic/modules/kff_uikit-3.5.5';
		?>

		<!-- UIkit CSS -->
		<link rel="stylesheet" href="<?=$UIKpath?>/css/uikit.min.css" />

		<!-- UIkit JS -->
		<script src="<?=$UIKpath?>/js/uikit.min.js"></script>
		<!-- /UIkit -->

		<?php
		self::$cfg['uk']['include_uikit'] = 0;
	}


	/**
	 * *Вывод в страницу
	 */
	public function Render()
	{
	?>

		<script src="/<?=self::$modDir?>/js/blogHelper.js"></script>

		<ul id="categories" class="uk-subnav uk-subnav-divider">

		<?php
		foreach($this->getCategories() as $cat=>&$arts) {
			if(!count($arts)) continue;

			$catInfo = (new DbJSON(self::$storagePath."/$cat/cfg.json"))->get();
			// print_r ($catInfo);
		?>

			<li>

				<a href="#"><h4><?=$catInfo['name']?></h4></a>
				<div uk-dropdown="mode: click;">

					<ul data-cat=<?=$cat?>  class="uk-nav uk-dropdown-nav">

					<?php

					foreach($arts as &$art) {
						$pageInfo = (new DbJSON(self::$storagePath."/$cat/{$art}.json"))->get();

						echo "<li data-id=$art data-cat=$cat>
						<a href=\"/".self::getPathFromRoot(self::$storagePath)."/$cat/$art" . self::$l_cfg['ext'] . "\" target='_blank'>{$pageInfo['name']}</a>

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

	<div class="blog_content">
		<?php $this->addContent()?>
	</div><!-- .blog_content -->
	<?php
	}
}