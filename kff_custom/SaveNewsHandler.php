<?php
require_once __DIR__.'/Logger.php' ;

$log = new Logger('kff.log', __DIR__.'/..');

require_once "../system/global.dat";

if(
	$status !== 'admin'
	|| $act !=='addedit'
)
{
	die("<h1>Пшёл вон, холоп!</h1>");
	// todo 403
}

// $log->add('get_defined_constants() =',null,[(get_defined_constants(1))['user']]);

$log->add('$module_news=',null,[$module_news]);
// $log->add(__DIR__,null,[$RunModules->system]);
// news_categories


class SaveNewsHandler
{
	// private $path = trim(\SELF, '/');
	private $log;

	public function __construct()
	{
		$this->log= $GLOBALS['log'];
		// $this->pathName= trim(\SELF, '/');
		$pathArr= explode('/', trim($_REQUEST['basePath'], '/'));

		$this->pathName= "../data/storage/{$_REQUEST['module_news']}/news_{$pathArr[1]}.dat";

		$this->log->add('$pathArr =',null,[$pathArr]);

		$this->log->add('$this->pathName =',null,[$this->pathName]);

		$success = $this->addEdit();

		if(!$success)
		{
			$this->log->add('Редактируемый файл не сохранён!!! Не перегружай страницу!',null,[$this->getBase()]);
		}
	}

	private function getBase()
	{
		$base = file_get_contents($this->pathName);
		return json_decode($base, 1);
	}

	private function addEdit()
	{
		$base= $this->getBase();
		$base['content'] = $_REQUEST['content'];

		$baseStr = json_encode(
			$base,
			JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
		);

		return file_put_contents($this->pathName, $baseStr);
	}
}

new SaveNewsHandler;