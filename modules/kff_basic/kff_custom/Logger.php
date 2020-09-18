<?php

/**
 * $log = new Logger('sample.log', 'path/to');
 * write to sample.log
 * $log->add("message", errorLevel, dump);
 * output log to the screen
 * $log->print();
 * Если в запросе имеется параметр dev != false лог отображается автоматически внизу страницы
 */

//* Включение протоколирования ошибок
error_reporting(-1);

class Logger
{
	const
		FATALS = [E_ERROR, E_PARSE, E_COMPILE_ERROR],
		// * STR_LEN = 0 - infinity
		STR_LEN = 250;

	protected
		# realpath to the log file
		$file;
	private
		$rewriteLog,
		# array with a current log
		$log = [];

	static $printed = false;

	/**
	 * @name - name of the log file
	 * optional @dir - realpath to the directory
	 * optional bool @rewriteLog - If == true (default) then log file should rewriting
	 */
	public function __construct($name, $dir='.', $rewriteLog=true)
	{
		set_error_handler([&$this, 'userErrorHandler']);
		# Обрабатываем фаталы
		register_shutdown_function([&$this, 'handleFatals']);

		$this->file = $dir . "/$name";
		$this->rewriteLog = (bool) $rewriteLog;
	}


	/**
	 * @param message - string to the log
	 * optional @level - error constant || code
	 * optional mixed @dump - will be output in the log by the function var_dump
	 */
	public function add(string $message, $level=null, $dump=[])
	{
		$bt = debug_backtrace();
		$caller = array_shift($bt);
		// $fileName = basename($caller['file']);
		$fileName = $this->_getFileName($caller['file']);

		$log = $this->_formatLog($fileName, $caller['line'], $message, $level);

		if(!is_array($dump)) $dump = [$dump];
		if(count($dump))
		{
			foreach ($dump as $d) {
				$d = $this->_CutLength($d, $message);
				ob_start();
				echo PHP_EOL;
				var_dump($d);
				$log .= ob_get_clean();
			}
		}

		$this->log[]= $log . PHP_EOL . PHP_EOL;
	}

	public function get()
	{
		return $this->log;
	}

	private function _getFileName($fileName)
	{
		return basename(dirname($fileName)) . DIRECTORY_SEPARATOR . basename($fileName);
	}

	private function _CutLength($item, $message)
	{
		if(
			self::STR_LEN && is_string($item)
			&& (strpos($message, 'response') === false)
			&& strlen($item) > self::STR_LEN * 1.1
		)
			return mb_substr($item, 0, self::STR_LEN) . " ...[Обрезано]";

		if(is_array($item))
		{
			foreach($item as &$i)
			{
				$i = $this->_CutLength($i, $message);
			}
		}
		return $item;
	}


	protected function _addToLog($fileName, $line, $message, $level=null)
	:string
	{
		return $this->log[]= $this->_formatLog($fileName, $line, $message, $level) . PHP_EOL . PHP_EOL;
	}


	protected function _formatLog($fileName, $line, $message, $level=null)
	:string
	{
		$errorLevel = (bool) $level ? "code:$level" : '';

		switch ($level) {
			case E_USER_WARNING:
				$errorLevel = " WARNING";
			break;
			case E_WARNING:
				$errorLevel = "$errorLevel WARNING";
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$errorLevel = "$errorLevel ERROR";
				break;
			default:
			case E_USER_NOTICE:
				$errorLevel = " INFO:";
				break;
		}
		return "[{$fileName}:{$line} " . date('Y/M/d H:i:s',time()) . " $errorLevel] $message";
	}

	/**
	 ** Печать в браузер
	 */
	public function print()
	{
		ob_start();
		?>
		<meta charset="UTF-8">
		<style>
		pre.log {
			box-sizing: border-box;
			white-space: pre-wrap;
			border: inset 1px #eee;
		}
		</style>
		<?php
		print_r("<h3 class='logCaption' style='text-align:center;'>Log</h3><pre class='log'>\n");
		foreach ($this->log as &$string) {
			print_r($string . "\n");
		}
		echo "</pre>";
		self::$printed = 1;
		return ob_get_clean();
	}

	public function printTG()
	{
		ob_start();
			$this->print();
		return strip_tags(ob_get_clean());
	}


	public function userErrorHandler($errno, $errstr, $errfile, $errline) :bool
	{
		if (!(error_reporting() & $errno)) {
			// Этот код ошибки не включен в error_reporting,
			// так что пусть обрабатываются стандартным обработчиком ошибок PHP
			// return false;
		}

		# Убираем ошибки парсера
		if(
			class_exists('CommonBot', false) &&
			CommonBot::stripos_array($errstr, ["loadHTML"]) !== false
		)
			return false;

		$fileName = $this->_getFileName($errfile);

		switch ($errno) {
			case E_ERROR:
			case E_USER_ERROR:
			case E_COMPILE_ERROR:
			case E_PARSE:
				$this->_addToLog($fileName, $errline, $errstr, $errno);
				$this->__destruct();
				die("Завершение работы.<br />\n");
				break;

			default:
				$this->_addToLog($fileName, $errline, $errstr, $errno);
				break;
		}

		# На серьёзных ошибках запускаем системный обработчик
		if ($errno && in_array($errno, self::FATALS))
			return false;
		else
			return true;
	} // userErrorHandler


	/**
	 * Логируем фаталы
	 */
	public function handleFatals()
	{
		$error = error_get_last();

		if (!$error || !in_array($error['type'], self::FATALS))
			return;

		# если кончилась память
		if (strpos($error['message'], 'Allowed memory size') === 0)
		{
			# выделяем немножко что бы доработать корректно
			ini_set('memory_limit', (intval(ini_get('memory_limit'))+64)."M");
		}

		$this->_addToLog($error['file'], $error['line'],$error['message'], $error['type']);
		// $this->add("PHP Fatal: ".$error['message']." in ".$error['file'].":".$error['line']);

		$_GET['dev']= 1;
		$this->__destruct();

		// die();

	} // handleFatals


	public function __destruct()
	{
		$txt = __METHOD__;
		$dump = null;
		// $this->add();
		if(!empty($GLOBALS['_bot']))
		{
			$txt .= ": check bot->is_owner =";
			$dump = [$GLOBALS['_bot']->is_owner ?? '_bot NOT exist!!!'];
		}
		else
		{
			$txt .= ": was started without bot";
		}

		$this->add("INFO: $txt",null,$dump);

		$this->log = array_map(function($i) {
			return strip_tags($i);
		}, $this->log );
		// echo __METHOD__ . " {$this->file} " . realpath($this->file);

		if(!empty($_GET['dev']) && !self::$printed)
		{
			$this->print();
		}
		file_put_contents($this->file, $this->log, !$this->rewriteLog ? FILE_APPEND : null);
	}
} // Logger


// todo

// Нужно передать аргументы.
/* require_once $_SERVER['DOCUMENT_ROOT'] . "/php/traits/Singleton.trait.php";

class Log extends Logger
{
	use Singleton;
	public function __construct($name, $dir='.', $rewriteLog=true)
	{
		parent::__construct($name, $dir, $rewriteLog);
	}
}
 */