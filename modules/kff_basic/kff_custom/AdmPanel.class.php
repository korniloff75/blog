<?php
if(realpath('.') === __DIR__) die('Access denied to file '.__FILE__);

/**
 * Вспомогательный класс для внесения изменений в админпанель.
 * Подключается в Index_my_addon
 */

class AdmPanel extends Index_my_addon
{
	/* static
		$cfg,
		$cfgDB; */
	/**
	 *
	 */
	public static function addResponsive ()
	{
		// System::addAdminHeadHtml('<link rel="stylesheet" href="/'.self::$dir.'/../admin.style.css">');
		ob_start();
		?>
		<link rel="stylesheet" href="/<?=Index_my_addon::getPathFromRoot(__DIR__)?>/css/admin.style.css">
		<script>
			$("#bar").addClass("uk-offcanvas-bar")
			.wrap("<div id='navbar' uk-offcanvas='overlay: true'/>");

			$("#main>.header").prepend("<button class='uk-offcanvas-close' uk-toggle='target: #navbar' style='position:static;'>Menu</button>", $("a.exit"));
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

		$fixes=[];

		self::$cfg['fixSystem'] = self::$cfgDB->get('fixSystem');
		if(self::$cfg['fixSystem'] === 'disable')
			return;

		// *Адаптивный дизайн
		$sourcePath = '/admin/include/start.dat';
		if(!file_exists(DR."$sourcePath.bak"))
		{
			$start = file_get_contents(DR.$sourcePath);
			$fixes[$sourcePath] = str_replace('<meta name="viewport" content="width=1300">','<meta name="viewport" content="width=device-width, initial-scale=1.0">',$start);

			if(!in_array($sourcePath, self::$cfg['fixSystem']))
				self::$cfgDB->set(['fixSystem' => [$sourcePath]], 'append');
		}

		// *Обработка ресурсов
		$sourcePath = '/index.php';

		if(!file_exists(DR."$sourcePath.bak"))
		{
			$start = file_get_contents(DR.$sourcePath);
			$fixes[$sourcePath] = str_replace([
				'<?php'.PHP_EOL
				.'require(\'./system/global.dat\');',
				'ob_end_flush();'.PHP_EOL
				.'?>'
			],[
				'<?php'.PHP_EOL
				.'$START_PROFILE = microtime(true);'.PHP_EOL
				.'require(\'./system/global.dat\');',
				'echo Index_my_addon::profile(\'base\');'.PHP_EOL
				.'ob_end_flush();'.PHP_EOL.'?>'
			], $start);

			if(!in_array($sourcePath, self::$cfg['fixSystem']))
				self::$cfgDB->set(['fixSystem' => [$sourcePath]], 'append');
		}

		// *Изменение файлов
		foreach(self::$cfgDB->get('fixSystem') as $fp) {
			if(file_exists(DR."$fp.bak"))
			{
				self::$log->add("Файл $fp уже был обработан ранее. Для его повторной обработки переименуйте файл {$fp}.bak -> $fp");
				continue;
			}
			copy(DR.$fp,DR."$fp.bak");
			file_put_contents(DR.$fp,$fixes[$fp]);
		}

		// self::$log->add
	}

	/**
	 * *Восстановление после self::fixSystem()
	 */
	static function restoreSystem()
	{
		foreach(self::$cfg['fixSystem'] as $fp) {
			rename(DR."$fp.bak", DR.$fp);
		}
		self::$cfgDB->replace(['fixSystem' => 'disable']);
	}
}