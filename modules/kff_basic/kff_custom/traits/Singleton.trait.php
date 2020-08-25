<?php
/**
 *
 */
trait Singleton
{
	protected static $_instance;
	//note private function __construct(){...}

	public static function getInstance()
	{
		$args = func_get_args();

		var_dump($args);

		if(!self::$_instance)
		{
			self::$_instance = new self;
			// call_user_func_array('$i->__construct', $args);
			// self::$_instance->__named('__construct', $args);
		}

		// self::$_instance = self::$_instance ?? new self;
		return self::$_instance;
	}

	/**
	 * Pass method arguments by name
	 *
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 * $object->__named('methodNameHere', ['arg3' => 'three', 'arg1' => 'one']);
	 */
	public function __named(string $method, array $args=[])
	{
	$reflection = new ReflectionMethod($this, $method);

	$pass = array();
	foreach($reflection->getParameters() as $param)
	{
		/* @var $param ReflectionParameter */
		if(isset($args[$param->getName()]))
		{
		$pass[] = $args[$param->getName()];
		}
		else
		{
		$pass[] = $param->getDefaultValue();
		}
	}

	return $reflection->invokeArgs($this, $pass);
	}

	private function __clone() {}

	private function __wakeup() {}
}
