<?php
/**
 * *Мультизагрузка файлов c защитой от исполняемых файлов и перезаписи дубликатов
 * доработано snipp.ru/php/uploads-files
 *
 */
class Uploads
{
	public static
		$log,
	// Название <input type="file">
		$input_name = 'file',
	// Разрешенные расширения файлов.
		$allow = [],
	// Запрещенные расширения файлов.
		$deny = [
			'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl', 'asp',

			'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh', 'js', 'html',

			'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi'

		],
	// Директория куда будут загружаться файлы.
		$pathname = \DR . '/files';

	protected
		$error = [],
		$success = [],
		$files = [];


	public function __construct()
	{
		self::$log = Index_my_addon::get_log();

		if (empty($_FILES[static::$input_name]))
			return;

		if(
			!is_dir(self::$pathname)
			&& !mkdir(self::$pathname, 0777, 1)
		)
			die("Невозможно создать " . self::$pathname);

		$this->_checkFiles();

		// Логгируем сообщение о результате загрузки.
		self::$log->add(
			__METHOD__,null,
			!empty($this->success) ?
				$this->success :
				$this->error
		);
	}


	/**
	 * *Проверяем мультизагрузку и формируем $this->files
	 */
	private function _checkFiles()
	{
		// Преобразуем массив $_FILES в удобный вид для перебора в foreach.

		$diff = count($_FILES[static::$input_name]) - count($_FILES[static::$input_name], COUNT_RECURSIVE);

		if ($diff == 0)
		{
			$this->files[] = $_FILES[static::$input_name];
		}
		else
		// *Мультизагрузка
		{
			foreach($_FILES[static::$input_name] as $k => $l)
			{

				foreach($l as $i => $v)
				{
					$this->files[$i][$k] = $v;
				}

			}
		}

		foreach ($this->files as $file)
		{
			$this->_iterFiles($file);
		}
	}


	private function _prefixDuplicated($file, $name, $parts)
	{
		$i = 0;
		$prefix = '';

		while (is_file(self::$pathname . '/' . $parts['filename'] . $prefix . '.' . $parts['extension']))
		{
			$prefix = '(' . ++$i . ')';
		}

		$name = $parts['filename'] . $prefix . '.' . $parts['extension'];

		// Перемещаем файл в директорию.
		if (move_uploaded_file($file['tmp_name'], self::$pathname . '/' . $name))
		{
			// Далее можно сохранить название файла в БД и т.п.
			$this->success []= 'Файл «' . $name . '» успешно загружен.';
		}
		else
		{
			$this->error []= 'Не удалось загрузить файл.';
		}

	}


	private function _iterFiles($file)
	{
		// Проверим на ошибки загрузки.

		if (!empty($file['error']) || empty($file['tmp_name']))
		{
			switch (@$file['error']) {
				case 1:
				case 2: $this->error []= 'Превышен размер загружаемого файла.'; break;
				case 3: $this->error []= 'Файл был получен только частично.'; break;
				case 4: $this->error []= 'Файл не был загружен.'; break;
				case 6: $this->error []= 'Файл не загружен - отсутствует временная директория.'; break;
				case 7: $this->error []= 'Не удалось записать файл на диск.'; break;
				case 8: $this->error []= 'PHP-расширение остановило загрузку файла.'; break;
				case 9: $this->error []= 'Файл не был загружен - директория не существует.'; break;
				case 10: $this->error []= 'Превышен максимально допустимый размер файла.'; break;
				case 11: $this->error []= 'Данный тип файла запрещен.'; break;
				case 12: $this->error []= 'Ошибка при копировании файла.'; break;
				default: $this->error []= 'Файл не был загружен - неизвестная ошибка.'; break;

			}

		}
		elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name']))
		{
			$this->error []= 'Не удалось загрузить файл.';
		}
		else
		{
			$name = Index_my_addon::translit($file['name']);

			// Оставляем в имени файла только буквы, цифры и некоторые символы.
			$pattern = "~[^a-zа-яё0-9,\~!@#%^-_\$\?\(\)\{\}\[\]\.]|[-]+~iu";

			$name = preg_replace($pattern, '-', $name);

			$parts = pathinfo($name);

			if (empty($name) || empty($parts['extension']))
			{
				$this->error []= 'Недопустимое тип файла';
			}
			elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow))
			{
				$this->error []= 'Недопустимый тип файла';
			}
			elseif (!empty($deny) && in_array(strtolower($parts['extension']), $deny))
			{
				$this->error []= 'Недопустимый тип файла';
			}
			else
			{
				$this->_prefixDuplicated($file, $name, $parts);
			}

		}
	}

}
