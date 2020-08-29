<?php

class EngineStorage_kff extends EngineStorage
{
	public
		$log,
		$prefix = 'cat_';
	private $cats;
	/**
	 * *Определение параметров
	 * @param storageDir - путь к родительской папке хранилища
	 */
	public function __construct($storage, $storageDir=null)
	{
		$this->log= Index_my_addon::get_log();

		if(file_exists(__DIR__.'/DbJSON.php'))
		{
			require_once __DIR__.'/DbJSON.php' ;
			parent::__construct($storage);
			if(!is_null($storageDir))
				$this->storageDir = $storageDir;
		}
		else
		{
			$this->log->add('DbJSON is not exist!', E_USER_WARNING);
		}
	}

	public function getPathName()
	:string
	{
		return $this->storageDir.'/'.$this->storage;
	}

	public function getCatsArr()
	:array
	{
		if(is_null($this->cats))
		{
			glob($this->getPathName() . "/{$this->prefix}*", GLOB_ONLYDIR );
		}
		return $this->cats;
	}

	// JSON_UNESCAPED_UNICODE

	//* Получение значения ключа
	public function get($key=null)
	{
		$path = $this->getPathName() . ($key? "/$key":'') . ".dat";
		// var_dump($path);
		return
			json_decode(
				file_get_contents(
					$path
				), 1
			);
	}

	//* Создание ключа
	public function set($key, $value, $q = 'w+')
	{
		if(!$this->exists())
		{
			mkdir($this->getPathName(), 0775, true);
		}

		if(!is_string($value))
		{
			$value= DbJSON::toJSON($value);
		}

		return filefputs($this->getPathName(). "/{$key}.dat", $value, $q);
	}
}