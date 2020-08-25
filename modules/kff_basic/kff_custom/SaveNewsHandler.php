<?php
require_once __DIR__.'/Logger.php' ;

require_once "../system/global.dat";

if(
	$status !== 'admin'
	|| $act !=='addedit'
)
{
	die("<h1>Пшёл вон, холоп!</h1>");
	// todo 403
}


class SaveNewsHandler
{
	// private $path = trim(\SELF, '/');
	private $log;

	public function __construct()
	{
		$this->log = $GLOBALS['log'] ?? new Logger('kff.log', __DIR__.'/..');

		$this->log->add('$module_news=',null,[$module_news]);

		// $this->pathName= trim(\SELF, '/');
		$pathArr= explode('/', trim($_REQUEST['basePath'], '/'));

		$this->pathName= $_SERVER['DOCUMENT_ROOT']."/data/storage/{$_REQUEST['module_news']}/news_{$pathArr[1]}.dat";

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

	private function setBase($base)
	{
		$baseStr = json_encode(
			$base,
			JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
		);

		return file_put_contents($this->pathName, $baseStr);
	}

	private function addEdit()
	{
		$base= $this->getBase();
		$base['content'] = $_REQUEST['content'];

		return $this->setBase($base);
	}
}

new SaveNewsHandler;