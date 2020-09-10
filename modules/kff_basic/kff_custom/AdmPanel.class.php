<?php
if(realpath('.') === __DIR__) die('Access denied!');

/**
 * Вспомогательный класс для внесения изменений в админпанель.
 * Подключается в Index_my_addon
 */

class AdmPanel
{
	static
		$cfg,
		$cfgDB;
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
		if(!Index_my_addon::is_adm()) die('Access denied!');

		self::$cfg['fixSystem'] = self::$cfgDB->get('fixSystem');
		if(self::$cfg['fixSystem'] === 'disable')
			return;

		// *Адаптивный дизайн
		$startPath = '/admin/include/start.dat';
		if(!in_array($startPath, self::$cfg['fixSystem']))
		{
			copy(DR.$startPath,DR."$startPath.bak");
			$start = file_get_contents(DR.$startPath);
			$start = str_replace('<meta name="viewport" content="width=1300">','<meta name="viewport" content="width=device-width, initial-scale=1.0">',$start);
			file_put_contents(DR.$startPath,$start);
			self::$cfgDB->set(['fixSystem' => [$startPath]]);
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