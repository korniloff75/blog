<?php
if(__DIR__ === realpath('.')) die;

// $curDir =

// *Basic::cfg['copyModules']==0
/* if(empty($kff))
{
	include_once __DIR__.'/kff_custom/index_my_addon.php';


} */
echo $kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die;


class Basic
{
	static $dir, $cfgDB, $cfg, $log, $mds_prefix;

	static function init()
	{
		global $kff, $log;

		self::$log = &$log;
		// *Директория модуля от DR
		self::$dir = $kff::getPathFromRoot(__DIR__);

		/* self::$cfg= json_decode(
			@file_get_contents(__DIR__.'/cfg.json'), 1
		) ?? [
			'copyModules'=> 0,
			'mds_prefix'=> 'kff'
		]; */
		self::$cfgDB= &$kff::$cfgDB;
		self::$cfg= &$kff::$cfg;
		self::$cfg= array_merge(
			[
				'mds_prefix'=> 'kff_' // *Префикс для сканируемых модулей
			], self::$cfg
		);

		// self::$log->add('$kff::$cfg=',null, [$kff::$cfg]);



		// *Save cfg
		self::saveBasicCfg();

		// *save INI
		self::saveINI();

	}


	// todo
	static function saveINI()
	{
		global $kff;
		if(
			empty(@$ini_path = &$_REQUEST['ini_path'])
			|| !strpos($ini_path, '.ini')
		)
			return;

		$s_name = filter_var($_REQUEST['name']);
		$s_val = filter_var($_REQUEST['val']);

		$ini = parse_ini_file($ini_path);
		// self::$log->add('parsed INI=',null,[$ini]);
		$ini[$s_name] = addslashes($s_val);

		file_put_contents($ini_path, $kff::arr2ini($ini));
		?>
		<div id="response">
			<?php# var_dump($_REQUEST)?>
		</div>
		<?php
	}


	static function saveBasicCfg()
	{
		if(@$_REQUEST['type'] !== 'global')
			return;
		$s_name = filter_var($_REQUEST['name']);
		$s_val = filter_var($_REQUEST['val']);
		$group = filter_var($_REQUEST['group']);
		// *Отключение всех элементов группы
		$disable = filter_var($_REQUEST['disable']);

		// self::$log->add('disable', null,[$disable]);

		// *4 checkboxes
		if(in_array($s_val, ['true','false']))
		{
			$s_val = $s_val === 'true';
		}

		if($s_name=='init_mods' && $s_val)
		{
			System::initModules();
		}
		elseif(!empty($s_name))
		{
			if(!empty($group))
			{
				self::$cfg[$group][$s_name] = $s_val;
				if(!empty($disable))
				{
					unset(self::$cfg[$group]);
					self::$log->add('disable', null,[$disable, self::$cfg]);
				}
			}
			else
			{
				self::$cfg[$s_name] = $s_val;
			}

			self::$cfgDB->replace(self::$cfg);

			self::CopyModules();

			die;
		}
	}


	/**
	 * *Наличие в /modules и подключение модуля
	 */
	static function checkModule(string $name)
	{
		$path = DR."/modules/$name";
		if(
			file_exists($path)
			&& !empty(self::$cfg['mds']["installed_$name"])
		)
			return 'checked';
		else return 'data-unchecked';
	}


	/**
	 * *Создаём ссылки на модули в /modules
	 */
	static function CopyModules()
	{
		require_once __DIR__.'/kff_custom/cpDir.class.php';
		linkDir::$excludes= '~token(\..+)?|cfg\..+~';

		// *Копируем все модули
		if(!empty(self::$cfg['mds']['installAll']))
		{
			$lnk_mds = new linkDir(__DIR__.'/modules', DR.'/modules');
			return;
		}

		// *Копируем модули из self::$cfg['mds']['installed']
		foreach (new DirectoryIterator(__DIR__.'/modules') as $fileInfo)
		{
			$name = $fileInfo->getFilename();
			if(
				$fileInfo->isDot()
				|| !empty(self::$cfg['mds']["installed_$name"])
			) continue;

			new linkDir($fileInfo->getPathname(), DR."/modules/$name");

		}

		System::initModules();
	}

	static function createKFF()
	{
		if(!file_exists(__DIR__.'/kff_custom/cpDir.class.php'))
			link(__DIR__.'/cpDir.class.php', __DIR__.'/kff_custom/cpDir.class.php');

		// *Бэкапим оригинальный global.dat
		$global_path = DR . '/system/global.dat';

		if(
			!file_exists($global_path.'.bak')
			&& copy($global_path, $global_path.'.bak')
		)
		{
			$ind= file_get_contents($global_path);

			// *Подключаем /kff_custom/index_my_addon.php
			$ind = preg_replace("~(\?>)\s*$~", 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/kff_custom/index_my_addon.php\';'.PHP_EOL."$1", $ind, 1);
			// $ind = preg_replace("~^.+\/system\/global\.dat.+$~um", 'require_once \'./kff_custom/index_my_addon.php\';', $ind, 1);

			if(file_put_contents($global_path, $ind))
			{
				echo "<pre><h3 style='color:#fff;'>Изменения в файл global.dat успешно внесены.</h3>".htmlspecialchars($ind)."</pre>";
			}

		}

		require_once __DIR__.'/kff_custom/cpDir.class.php';

		linkDir::$excludes= '~token(\..+)?|cfg\..+~';
		// *Создаём в корне каталог /kff_custom со ссылками
		$dir = new linkDir(__DIR__.'/kff_custom', DR.'/kff_custom');

		// *Создаём ссылки на модули в /modules
		$lnk_mds = new linkDir(__DIR__.'/modules', DR.'/modules');
		System::initModules();


		echo "<pre><h3 style='color:#fff;'>Каталог /kff_custom успешно создан или обновлён.</h3>";
		ob_start();
		print_r($dir->get_log());
		ob_end_flush();
		echo "</pre>";
	}


	static function destructKFF()
	{
		require_once __DIR__.'/kff_custom/cpDir.class.php';

		$global_path = DR . '/system/global.dat';

		// *Восстанавливаем оригинальный global.dat
		if(
			file_exists($global_path.'.bak')
			&& copy($global_path.'.bak', $global_path)
			&& unlink($global_path.'.bak')
		)
		{
			echo "<pre><h3 style='color:#5f5;'>Оригинальный индексный файл успешно восстановлен.</h3></pre>";
		}
		else
		{
			echo "<pre><h3 style='color:#f55;'>Индексный файл восстановить не удалось.</h3>$ind</pre>";
		}
	}


	// *Перебираем свои модули
	static function scanModules()
	{
		$mds = glob(realpath(__DIR__.'/..')."/".self::$cfg['mds_prefix']."*");
		/* $mds= array_filter(
			$mds,
			function(&$i){
				return stripos($i, 'basic') === false;
			}
		); */
		// self::$log->add('kff modules',null,[$mds]);

		return $mds;
	}


	static function installModules(array &$info)
	{
		// self::$log->add('$info',null,[$info]);
		echo '<hr>;
			<h2>Установить модули:</h2>
			<ul id="installModules" class="uk-list uk-list-striped uk-list-medium" data-group="mds">
			<li><label>Подключить все внутренние модули <input name="installAll" type="checkbox" ' .
			(!empty(Basic::$cfg['mds']['installAll'])?'checked':'')
			.'></label>
			<p class="comment">Поставленный флажок внедряет и инициализирует все внутренние модули <b>kff</b>.</p>
			</li>';

		foreach (new DirectoryIterator(__DIR__.'/modules') as $fileInfo) {
			$name= $fileInfo->getFilename();
			// self::$log->add('$name=',null,[$name]);

			if(
				$fileInfo->isDot()
				|| !file_exists($fileInfo->getPathname().'/admin.php')
			) continue;

			if(empty($info[$name]) && file_exists($ini_path = $fileInfo->getPathname()."/info.ini"))
				$info[$name] = parse_ini_file($ini_path);

			echo "<li><label><h5><input name='installed_$name' type=checkbox "
			. self::checkModule($name)
			."> $name v.{$info[$name]['version']}</h5>"
			. "<div class=comment>".($info[$name]['description'] ?? '')."</div>
			</label></li>\n";
		}
		echo '</ul>';

	}


	static function RenderPU()
	{
		$mds = self::scanModules();
		echo '<h2>Модули '.(empty(self::$cfg['mds_prefix'])? 'из <i>/modules</i>': self::$cfg['mds_prefix']).'</h2>';
		echo '<p class=comment>Все изменённые настройки сохраняются автоматически -- после потери фокуса редактируемым полем.</p>
		<ul id=mds_sts uk-accordion>';

		$info = [];

		foreach($mds as &$m)
		{
			$ini_path = "$m/info.ini";
			$name = basename($m);
			// $adm_path = "$m/admin.php";
			if(
				!file_exists($ini_path)
			)
			{
				continue;
			}

			$ini = parse_ini_file($ini_path);

			$info[$name] = $ini;

			$is_feedback = mb_stripos($ini['name'],'связь через TG') && count(explode('.', $ini['version'])) > 2;

			if($name === 'kff_ajaxMenu')
				self::$log->add(__METHOD__.' '.$ini['name'],null,[
					$ini, $ini['name'],$ini['version']
				]);


			echo "<li ".($is_feedback?'class=uk-open':'').">
			<h4 class=uk-accordion-title data-ini-path='$ini_path'>{$ini['name']} v.{$ini['version']}</h4>";
			echo '<ul class="uk-accordion-content uk-margin-bottom">';
			foreach($ini as $key=>&$val)
			{
				// $val = htmlspecialchars($val);

				$tag = $key !== 'description' ? "<input class=uk-width-2-3 type=text value='$val'>" : "<div contentEditable=true class=uk-width-2-3>$val</div>";

				echo "<li class='uk-flex uk-flex-wrap uk-padding-small'><span class=uk-width-1-3>$key </span> $tag</li>";
			}
			// todo
			// require_once $adm_path;
			echo '</ul>';

			echo '</li>';
		}
		echo '<!-- /uk-accordion --></ul>';

		self::installModules($info);

		// *Подключаем морду
		require_once __DIR__.'/admin.htm';

		// self::$log->add('self::$cfg=',null,[self::$cfg]);
	}

}


Basic::init();

?>
<div class="header"><h1>Настройки <?=$MODULE?></h1></div>

<div class="menu_page">
	<a href="index.php">&#8592; Вернуться назад</a>
</div>

<div class="content">
<?php

Basic::RenderPU();

?>

</div>

<?php
// *Тесты
// $log->add('$Page',null,[$Page]);