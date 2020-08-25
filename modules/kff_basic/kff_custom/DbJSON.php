<?php
class DbJSON {
	private
		$path,
		$json; # String

	public
		# DataBase
		$db = []; # Array


	public function __construct(string $path)
	{
		//* fix 4 __destruct
		$this->path= substr($path, 0, 1) !== DIRECTORY_SEPARATOR
		? realpath($path)
		: $path;

		$dir= realpath(dirname($this->path));

		if(!$this->path) $this->path= $_SERVER['DOCUMENT_ROOT']. '/' . $path;

		trigger_error(__METHOD__.": \$this->path= {$this->path}; \$path= $path; \$dir= $dir");

		// var_dump($this->path);

		$this->json = @file_get_contents($this->path);
		// trigger_error(__METHOD__.' ./'.$path." \$this->path= " . $this->path);
		$this->db = json_decode($this->json, true) ?? [];

	}

	/**
	 * @id optional <string|int>
	 */
	public function get($id=null)
	{
		return empty($id)
		? $this->db
		: (
			$this->db[$id] ?? null
		);
	}

	/**
	 * @param data <array>
	 */
	public function set(array $data, $append = false)
	{
		if($append)
		{
			$this->db = array_merge_recursive($this->db, $data);
		}
		else
		{

			$this->db = array_replace_recursive($this->db, $data);
		}

		$this->db['change']= 1;

		return $this;
	}

	/**
	 * @param data <array>
	 */
	public function replace(array $data)
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
		// todo test
		// $this->db['change']= 1;

		// trigger_error(__METHOD__." \$this->db['change']= " . ((bool) $this->db['change']) . "\nBase saved! \$this->path= {$this->path} current=" . realpath('.') . ' spl=' . $_SERVER['DOCUMENT_ROOT']. '/' .  $this->objPath->getPathname());

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