<?php
if(__DIR__ === realpath('.')) die;
if(empty($kff)) global $kff;

echo $kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die('Access denied!');
?>


<?php
class BlogKff extends Index_my_addon
{
	protected static
		$modDir,
		// *Локальный конфиг
		$l_cfg,
		$storegePath = \DR.'/kff_blog_data',
		$catPath = __DIR__.'/categories.json';


	public function __construct()
	{
		global $kff;

		// *Директория модуля от DR
		self::$modDir = $kff::getPathFromRoot(__DIR__);

		$this->DB = new DbJSON(__DIR__.'/cfg.json');

		self::$l_cfg= array_merge(
			[
				'name'=> 'Блог',
			], $this->DB->get()
		);

		$this->catsDB = new DbJSON(self::$catPath);

		// self::$log->add('self::$cfg=',null, [self::$cfg]);

		?>
		<div class="header"><h1>Настройки <?=$MODULE?></h1></div>

		<div class="content">
			<?php
			$this->RenderPU();
			?>
		</div><!-- .content -->

		<?php

	}


	function updateCategories()
	{
		$cats = [];
		foreach(
			new FilesystemIterator(self::$storegePath, FilesystemIterator::SKIP_DOTS) as $catFI
		) {
			if(!$catFI->isDir()) continue;
			echo $catFI->getFilename() . '<br>';
			$cats []= $catFI->getFilename();

		}

		return $cats;
	}

	static function getCategories()
	{
		// $cats;
	}

	private function RenderPU()
	{
		?>
		<h2>Тут будет ПУ Блога</h2>


		<?php
	}
}

$Blog = new BlogKff;

// *Tests
$Blog->catsDB->replace(
	$Blog->updateCategories()
);