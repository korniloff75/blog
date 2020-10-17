<?php
class DbJSON implements Iterator, Countable
{
	public $test;

	static
		$log,
		$convertPath = false,
		$defaultDB;

	private
		$position = 0,
		$changed = 0,
		$reversed = false,
		$values,
		$path;

	private
		$sequence = [],
		$db = [];# DataBase # Array


	public function __construct(?string $path=null)
	{
		global $log;

		self::$log= &$log;

		// *Deprecated
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

		if(!empty($path)){
			$json = @file_get_contents($this->path);
			// trigger_error(__METHOD__.' ./'.$path." \$this->path= " . $this->path);
			// $this->json = str_replace(["'", '"'], ["\'", '\"'], $this->json);
			$this->db = json_decode($json, true) ?? [];

			if(empty($this->db)){
				if(is_object($log)){
					$log->add(__METHOD__.": DB is EMPTY!", $log::BACKTRACE);
				}
				else{
					trigger_error(__METHOD__.": DB is empty from {$this->path}", E_USER_WARNING);
				}
			}
			else{
				$this->rewind();
			}

		} //if(!empty($path))
	} // __construct


	/**
	 * *Проверка в дефолтном массиве
	 */
	public function __get($key) {
		if(self::$defaultDB && is_null($v= $this->db[$key])){
			return self::$defaultDB[$key];
		}
		return $v;
	}

	public function rewind() {
		$this->position = 0;
		// return $this;
	}

	public function values() {
		$this->values= $this->values ?? array_values($this->db);
		return $this->values;
	}

	public function current() {
		// *Если строковые ключи
		if(empty($cur= &$this->db[$this->position])){
			$cur= &$this->values()[$this->position];
		}
		// self::$log->add(__METHOD__,null,['position'=>$this->position, 'cur'=>$cur,]);
		return $cur;
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		++$this->position;
		// self::$log->add(__METHOD__,null,[ '$this->position'=>$this->position]);
		// return $this->db[$this->position] ?? $this->values()[$this->position] ?? null;
	}

	public function valid() {
		/* self::$log->add(__METHOD__,null,['bool'=>(
			isset($this->db[$this->position]) || isset($this->values()[$this->position])
		), '$this->db[$this->position]'=>$this->db[$this->position], '$this->position'=>$this->position]); */
		return
			isset($this->db[$this->position])
			|| isset($this->values()[$this->position]);
	}

	public function count($mode=null)
	{
		return count($this->db, $mode);
	}

	/**
	 * *Clear base
	 * @key optional <string|int>
	 */
	public function clear($key=null)
	{
		if(!empty($key)){
			unset($this->db[$key]);
			// *Удаляем null
			$this->db= array_filter($this->db);
		}
		else $this->db = [];
		$this->changed= 1;
		return $this;
	}

	public function remove($key=null)
	{
		return $this->clear($key);
	}


	/**
	 * @key optional <string|int>
	 */
	public function get($key=null)
	{
		// $db = array_diff_key($this->db, ['change'=>1]);
		$db = &$this->db;
		return is_null($key)?
			$db : (
				$db[$key] ?? null
			);
	}

	/**
	 * Получаем ключи базы
	 */
	public function getKeys($id=null)
	{
		return array_keys($this->get());
	}

	/**
	 * @param sequence - массив с последовательностью ключей
	 * ???
	 * todo ...
	 */
	public function getSequence(array $sequence)
	{
		$db=[];
		if(count($sequence) !== count($this->get())){
			// return false;
		}

		foreach($sequence as $i){
			if(empty($this->db[$i])) continue;
			$db[$i]= &$this->db[$i];
		}
		return $db;
	}


	/**
	 * @param data {array}
	 */
	public function set(array $data, $append = false)
	{
		$handler = $append ? 'array_merge_recursive' : 'array_replace_recursive';

		$this->db = $handler($this->db, $data);

		$this->changed= 1;

		return $this;
	}

	/**
	 * *db - линейный массив с ассоциативными
	 * [{},{},...]
	 * @param key - ключ для поиска
	 * @param val - искомое значение key
	 * @return индекс ассоциативного массива
	 */
	public function getInd($key, $val, $strict=1)
	{
		foreach($this->db as $ind=>&$i){
			if(
				$strict && $i[$key] === $val
				|| !$strict && $i[$key] == $val
			) {
				return $i['ind']= $ind;
			}
		}

		return null;
	}

	/**
	 * *db - [{},{},...]
	 * *Добавление / замена элемента $this->db с индексом $this->getInd(@key, @val)
	 * @param item {array} - ассоциативный массив
	 */
	public function setInd(array $item, $key, $val, $strict=1)
	{
		$ind= $this->getInd($key,$val,$strict);

		$this->db[$ind]= $item;

		return $this;
	}

	/**
	 * *db - [{},{},...]
	 * *Поиск в базе по значению ключа
	 */
	public function find($key, $val, $strict=1)
	{
		$ind= $this->getInd($key,$val,$strict);

		if(empty($this->db[$ind])){
			self::$log->add(__METHOD__,\Logger::BACKTRACE,['$this->db'=>$this->db, '$key'=>$key, '$val'=>$val]);
			return null;
		}
		else return $this->db[$ind];
	}


	/**
	 * alias $this->set() без перезаписи
	 */
	public function append(array $data)
	{
		return $this->set($data, true);
	}

	public function push($item, $key=null)
	{
		if($key){
			$this->db[$key]= $item;
		}
		else{
			$this->db[]= $item;
		}
		$this->changed= 1;
		return $this;
	}

	// ?
	/* public function filter($key)
	{
		return array_filter($this->db, function(&$i) use($key){
			if(!is_array($i)) return;
			return isset($i[$key]);
		});
	} */


	/**
	 * Меняем местами элементы
	 */
	public function swap($firstId, $secondId)
	{
		list($this->db[$secondId], $this->db[$firstId]) = [$this->db[$firstId], $this->db[$secondId]];
		$this->changed= 1;
		return $this;
	}

	/**
	 * @param data {array}
	 */
	public function replace(array $data)
	{
		$this->db = $data;
		$this->changed= 1;

		return $this;
	}


	# Плоский массив из многомерного
	// ?
	public function getFlat()
	{
		return array_values(iterator_to_array(
			new \RecursiveIteratorIterator(
				new \RecursiveArrayIterator($this->db)
			)
		));
	}


	public function reverse($force=0)
	{
		if(!$force) $this->reversed= !$this->reversed;
		$this->db= array_reverse($this->db);
	}


	# Массив в JSON
	public static function toJSON(array $arr)
	{
		return json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR |JSON_HEX_QUOT | JSON_HEX_APOS );
	}


	public function save()
	{
		global $log;

		$this->saved= 1;

		if($this->reversed){
			$this->reverse(1);
		}

		if(empty($this->path))
			is_object($log) && $log->add(__METHOD__.': Не указан путь записи базы',$log::BACKTRACE,['$this->path'=>$this->path]);
		else {
			file_put_contents(
				$this->path,
				self::toJSON($this->db), LOCK_EX
			);
			$this->changed= 0;
			return true;
		}
	}


	public function __destruct()
	{
		global $log;
		// note test
		// $this->changed= 1;
		if(!empty($this->db['test']) || $this->test){
			$log->add(__METHOD__.': База перед записью',E_USER_WARNING,[$this->db]);
			// *Deprecated
			unset($this->db['test']);
		}

		// *check changes
		if(
			!empty($this->saved)
			|| !$this->changed
		) return;

		$this->save();

		/* if(!file_put_contents(
			$this->path,
			self::toJSON($this->db), LOCK_EX
		)) trigger_error(__METHOD__."❗️❗️❗️\nСервер в данный момент перегружен и Ваши данные не были сохранены. Попробуйте повторить.", E_USER_WARNING); */
	}
} //* DbJSON