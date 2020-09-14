<?php
if(__DIR__ === realpath('.')) die;
if(empty($kff)) global $kff;

$kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die('Access denied!');



class BlogKff extends Index_my_addon
{
	protected static
		$modDir,
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

		self::$l_cfg= array_merge(
			[
				'name'=> 'Блог',
			], $this->DB->get()
		);

		$this->catsDB = new DbJSON(self::$catPath);

		// self::$log->add('self::$cfg=',null, [self::$cfg]);

		$this->_InputController();

		$this->RenderPU();

	} // __construct


	/**
	 * *Обработка внешних запросов
	 * Методы контроллера с префиксом c_
	 */
	private function _InputController()
	{
		$r = &$_REQUEST;
		if(!empty($m_name = "c_{$r['name']}") && method_exists($this, $m_name))
		{
			$this->{$m_name}(filter_var($r['value']));
		}
	}


	/**
	 * *Перезаписываем категории
	 */
	function updateCategories()
	{
		$cats = [];
		foreach(
			new FilesystemIterator(self::$storagePath, FilesystemIterator::SKIP_DOTS) as $catFI
		) {
			if(!$catFI->isDir()) continue;
			// echo $catFI->getFilename() . '<br>';
			$cats []= $catFI->getFilename();
		}

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
	 * *Добавляем категорию
	 */
	public function c_addCategory($new_cat)
	{
		$catPath = self::$storagePath."/$new_cat";

		if(is_dir($catPath))
		{
			die("<div class=content>Категория <b>$new_cat</b> уже существует!</div>");
		}
		elseif(!$success = mkdir($catPath,0755,1))
		{
			die("<div class=content>Категория <b>$new_cat</b> не создана!</div>");
		}
		$this->updateCategories();
		return $success;
	}

	/**
	 * *Вывод в админку
	 */
	private function RenderPU()
	{
		?>

		<div class="header"><h1>Настройки <?=$MODULE?></h1></div>

		<div class="content">

			<script src="/<?=self::getPathFromRoot(__DIR__)?>/js/blogHelper.js"></script>
			<h2>Тут будет ПУ Блога</h2>

			<h3>Категории</h3>

			<input type="text" name="addCategory" placeholder="Название категории"><button>Новая</button>

			<ul id="categories">

				<?php
				foreach($this->getCategories() as $cat) {
				?>
					<li>
					<h4><?=$cat?></h4>
					<ul class="listArticles">

					</ul>
					</li>
				<?php
				}
				?>

			</ul><!-- #categories -->
		</div><!-- .content -->
		<?php
	}
}

$Blog = new BlogKff;


// *Tests
// print_r($Blog->getCategories());