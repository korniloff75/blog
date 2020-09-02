<?php
class cpDir
{
	protected static
		$log = [];
	public static
		$excludes; //* Исключения для копирования regEx

	private $target;

	public function __construct($d1, $d2, $upd = true, $force = false)
	{
		$this->target = $this->target ?? $d2;

		if ( is_dir( $d1 ) )
		{
			$d2 = $this->mkdir_safe( $d2, $force );
			if (!$d2)
			{
				$this->fs_log("!!fail $d2"); return;
			}
			$d = dir( $d1 );

			while ( false !== ( $entry = $d->read() ) )
			{
				if ( $entry != '.' && $entry != '..' )
					$this->__construct( "$d1/$entry", "$d2/$entry", $upd, $force );
			}
			$d->close();
		}
		else
		{
			$ok = $this->copy_safe( $d1, $d2, $upd );
			$ok = ($ok) ? "ok-- " : " -- ";
			$this->fs_log("{$ok}$d1");
		}
	}


	function mkdir_safe( $dir, $force ) {
		if (file_exists($dir))
		{
			if (is_dir($dir)) return $dir;
			elseif (!$force) return false;
			unlink($dir);
		}
		return (mkdir($dir, 0755, true)) ? $dir : false;
	} // mkdir_safe


	function copy_safe ($f1, $f2, $upd) {
		$time1 = filemtime($f1);
		if (file_exists($f2))
		{
			$time2 = filemtime($f2);
			if ($time2 >= $time1 && $upd) return false;
		}

		$ok = copy($f1, $f2);

		if ($ok) touch($f2, $time1);
		return $ok;
	} // copy_safe


	static function RemoveDir($pathdir)
	{
		exec("rm $pathdir -rf *");
	}

	function fs_log($str)
	{
		$time = date("Y-m-d H:i:s");

		self::$log[]= "$str ($time)\n";
	}


	public function get_log()
	{
		return self::$log;
	}

	public function __destruct()
	{
		if(!empty($log=&$GLOBALS['log']))
		{
			$log->add(__METHOD__, null, [self::$log]);
		}
		// FILE_APPEND|LOCK_EX
		file_put_contents("{$this->target}/".basename(__FILE__).'.log',self::$log);
	}
}


/**
 * *Создаем жесткие ссылки
 */
class linkDir extends cpDir
{
	function copy_safe ($f1, $f2, $upd) {
		if (
			file_exists($f2)
			|| self::$excludes
			&& preg_match(self::$excludes, basename($f1))
		)
		{
			return false;
		}
		return link($f1, $f2);
	} // copy_safe
}
