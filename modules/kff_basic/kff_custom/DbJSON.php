<?php
class DbJSON {
	static
		$convertPath = false;

	private
		$path,
		$json; # String

	public
		$db = [];# DataBase # Array


	public function __construct(string $path)
	{
		if(self::$convertPath)
		{
			//* fix 4 __destruct
			$this->path= substr($path, 0, 1) !== DIRECTORY_SEPARATOR
			? realpath($path)
			: $path;

			$dir= realpath(dirname($this->path));

			// trigger_error(__METHOD__.": \$this->path1= {$this->path}");

			if(!$this->path) $this->path= $_SERVER['DOCUMENT_ROOT']. '/' . $path;

			// trigger_error(__METHOD__.": \$this->path2= {$this->path}; \$path= $path; \$dir= $dir");

			// var_dump($this->path);
		}
		else
		{
			$this->path= $path;
		}


		$this->json = @file_get_contents($this->path);
		// trigger_error(__METHOD__.' ./'.$path." \$this->path= " . $this->path);
		$this->db = json_decode($this->json, true) ?? [];

	}

	/**
	 * @id optional <string|int>
	 */
	public function get($id=null)
	{
		$db = array_diff_key($this->db, ['change'=>1]);
		return empty($id)?
			$db : (
				$db[$id] ?? null
			);
	}

	/**
	 * @param data {array}
	 */
	public function set(array &$data, $append = false)
	{
		$handler = $append ? 'array_merge_recursive' : 'array_replace_recursive';

		$this->db = $handler($this->db, $data);

		$this->db['change']= 1;

		return $this;
	}

	/**
	 * @param data {array}
	 */
	public function replace(array &$data)
	{
		$this->db = $data;
		$this->db['change']= 1;

		return $this;
	}


	# Плоский массив из многомерного
	public function getFlat()
	{
		return array_values(iterator_to_array(
			new \RecursiveIteratorIterator(
				new \RecursiveArrayIterator($this->db)
			)
		));
	}


	# Массив в JSON
	public static function toJSON(array $arr)
	{
		return json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
	}


	public function __destruct()
	{
		// note test
		// $this->db['change']= 1;

		// *check changes
		if(empty($this->db['change'])) return;

		unset($this->db['change']);

		file_put_contents(
			$this->path,
			self::toJSON($this->db), LOCK_EX
		);

		/* if(!file_put_contents(
			$this->path,
			self::toJSON($this->db), LOCK_EX
		)) trigger_error(__METHOD__."❗️❗️❗️\nСервер в данный момент перегружен и Ваши данные не были сохранены. Попробуйте повторить.", E_USER_WARNING); */
	}
} //* DbJSON