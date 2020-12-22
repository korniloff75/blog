<?php
if(realpath('.') === __DIR__) die('Access denied to file '.__FILE__);

/**
 * Вспомогательный класс для внесения изменений в админпанель.
 * Подключается в Index_my_addon
 */

class AdmPanel extends Index_my_addon
{
	const FIXED= '// FIXED'. PHP_EOL;

	public static $fixes=[];

	public static function addResponsive ()
	{
		// System::addAdminHeadHtml('<link rel="stylesheet" href="/'.self::$dir.'/../admin.style.css">');
		ob_start();
		?>
		<link rel="stylesheet" href="/<?=self::getPathFromRoot(__DIR__)?>/css/admin.style.css">
		<script data-method="<?=__METHOD__?>">
		$(()=>{
			'use strict';
			var $sidebar= $("#bar");

			// *Добавляем пункт Basic
			$sidebar.find('#menu').append('<a href="/<?=self::getAdmFolder()?>/module.php?module=kff_basic" class="mdls activ"><button>Basic</button></a>');

			// *Скрываем меню на маленьких мониторах
			if($(window).width() < 1100){
				$sidebar.addClass("uk-offcanvas-bar")
			.wrap("<div id='navbar' uk-offcanvas='overlay: true'/>");

				$("#main>.header").prepend("<a class='uk-offcanvas-close' uk-toggle='target: #navbar' style='position:static;' uk-icon='icon: menu; ratio:2;' title='Меню' uk-tooltip></a>", $("a.exit"));
			}
			else{
				$sidebar.css({
					// position: 'sticky',
					// float: 'left',
				}).addClass('uk-width-1-4');

				$('div#main').addClass('uk-width-3-4 uk-float-right');
			}
		})
		</script>

		<?php
		System::addAdminEndHtml(ob_get_clean());
	}


	/**
	 * *Хирургия
	 * *Отмена - self::restoreSystem()
	 */
	static function fixSystem()
	{
		// if(!Index_my_addon::is_adm()) die('Access denied!');

		// self::$cfg['fixSystem'] = self::$cfgDB->get('fixSystem');
		if(self::$cfg['fixSystem'] === 'disable')
			return;

		// *Адаптивный дизайн
		// c версии 5.1.27 удаляет содержимое файла /include/start.dat
		$sourcePath = '/'.self::getAdmFolder().'/include/start.dat';
		if(isset($_GET['test']) || !file_exists(DR."$sourcePath.bak"))
		{
			$start = file_get_contents(isset($_GET['test']) ? DR."$sourcePath.bak": DR.$sourcePath);
			self::$fixes[$sourcePath] = str_replace('<meta name="viewport" content="width=1300">','<meta name="viewport" content="width=device-width, initial-scale=1.0">',$start);

			if(!in_array($sourcePath, self::$cfg['fixSystem']))
				self::$cfgDB->append(['fixSystem' => [$sourcePath]]);
		}

		// *Обработка ресурсов
		$sourcePath = '/index.php';

		if(isset($_GET['test']) || !file_exists(DR."$sourcePath.bak"))
		{
			$start = file_get_contents(isset($_GET['test']) ? DR."$sourcePath.bak": DR.$sourcePath);

			// self::$log->add(__METHOD__,null,['matched'=>preg_match('~<?php\s*require\(\'./system/global.dat\'\);~i', $start), $start]);

			self::$fixes[$sourcePath] = preg_replace([
				'~<\?php(\s*require\(\'./system/global.dat\'\);)~i',
			],[
				'<?php '. self::FIXED
				.'$START_PROFILE = microtime(true);$1',
			], $start, 1);

			if(!in_array($sourcePath, self::$cfg['fixSystem']))
				self::$cfgDB->append(['fixSystem' => [$sourcePath]]);
		}

		// *Успокаиваем autoloader
		$sourcePath = '/system/global.dat';

		if(isset($_GET['test']) || !file_exists(DR."$sourcePath.bak"))
		{
			$start = file_get_contents(isset($_GET['test']) ? DR."$sourcePath.bak": DR.$sourcePath);

			self::$fixes[$sourcePath] = str_replace([
				'require DR.\'/system/classes/\' . $class . \'.dat\'',
			],[
				'if(file_exists($inc= DR."/system/classes/{$class}.dat")) @include_once $inc',
			], $start);

			if(!in_array($sourcePath, self::$cfg['fixSystem']))
				self::$cfgDB->append(['fixSystem' => [$sourcePath]]);
		}

		// *Изменение файлов
		$fixSystem = self::$cfgDB->fixSystem;

		if(!empty($fixSystem)) foreach($fixSystem as $fp) {
			self::$log->add(DR.$fp,null,self::$fixes[$fp]);

			if(empty(self::$fixes[$fp])){
				trigger_error("$fp may be EMPTY after fixing!", E_USER_WARNING);
				continue;
			}

			if(isset($_GET['test'])){
				@file_put_contents(DR."$fp.test", self::$fixes[$fp]);
				continue;
			}
			elseif(file_exists(DR."$fp.bak")){
				trigger_error("Файл $fp уже был обработан ранее. Для его повторной обработки переименуйте файл {$fp}.bak -> $fp");
				continue;
			}

			copy(DR.$fp,DR."$fp.bak");
			file_put_contents(DR.$fp,self::$fixes[$fp]);

		}

	}

	/**
	 * *Восстановление после self::fixSystem()
	 * todo...
	 */
	static function restoreSystem()
	{
		$fixSystem = self::$cfgDB->fixSystem;

		if(!empty($fixSystem)) foreach($fixSystem as $fp) {
			rename(DR."$fp.bak", DR.$fp);
		}
		self::$cfgDB->replace(['fixSystem' => 'disable']);
	}
}