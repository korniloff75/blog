<?php
/**
 *note после обновления -- подключить к /index.php 1-й строкой
 ** require_once './kff_custom/index_my_addon.php';
 */

ini_set('short_open_tag', 'On');


class Index_my_addon
{
	static $log = false;

	public function __construct()
	{
		// ini_set('display_errors', 1);
		// trigger_error('test');

		// *Отсекаем проверки комментов
		if(!$is_comment_ajax = stripos($_SERVER['REQUEST_URI'], 'ajax/newcommentcheck') || stripos($_SERVER['REQUEST_URI'], 'ajax/loadcomments') )
		{
			require_once __DIR__.'/Logger.php' ;
			self::$log = new Logger('kff.log', __DIR__.'/..');
			self::$log->add('REQUEST_URI=',null, [$_SERVER['REQUEST_URI']]);
		}
	}

	public function startLog()
	{
		return self::$log;
	}

	// *Переводим все слеши в Unix
	public static function fixSlashes(string $path)
	:string
	{
		$path = str_replace("\\", '/', $path);
		return preg_replace("#(?!https?|^)//+#", '/', $path);
	}

	// *Получаем путь относительно $_SERVER['DOCUMENT_ROOT']
	public static function getPathFromRoot(string $absPath)
	:string
	{
		return str_replace(self::fixSlashes($_SERVER['DOCUMENT_ROOT']) . '/', '', self::fixSlashes($absPath));
	}

	// *Реальный IP
	public static function realIP ()

	{
		return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
	}

	public function __destruct()
	{
		// var_dump($GLOBALS['log']);
		if($GLOBALS['status'] === 'admin')
		{
			// if(empty($_GET['dev']))
			if(self::$log && !self::$log::$printed)
			{
			?>
				<style>
				pre.log{
					background: #111;
					color: #3f3;
				}
				</style>
			<?php
				echo "<div id='logWrapper'>";
				// self::$log->__destruct();
				self::$log->print();
				echo "</div>";

				self::$log::$printed = 1;
			}
		}
	}
}

$kff = new Index_my_addon();
$log = $kff->startLog();