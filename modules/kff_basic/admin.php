<?php
if(__DIR__ === realpath('.')) die;

// $curDir =

// *Первый старт
if(empty($kff))
{
	System::initModules();
	die ('<h2>Модуль активирован. Перезагрузите страницу.</h2>');
}
echo $kff::headHtml();

// *Только админам
if(!$kff::is_adm()) die('Access denied!');


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

		?>
		<div class="header"><h1>Настройки <?=$MODULE?></h1></div>

		<div class="content">
			<?php
			self::RenderPU();
			?>
		</div><!-- .content -->

		<?php

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
		// self::$log->add('s_val_0', null,[$_REQUEST['name'],$_REQUEST['val']]);
		$s_name = filter_var($_REQUEST['name']);
		if( !is_array($_REQUEST['val']) )
			$s_val = filter_var($_REQUEST['val']);
		else
			$s_val = $_REQUEST['val'];
		$group = filter_var($_REQUEST['group']);
		// *Отключение всех элементов группы
		$disable = filter_var($_REQUEST['disable']);

		self::$log->add('s_val', null,[$s_name,$s_val]);

		// *4 checkboxes
		if(!is_array($s_val) && in_array($s_val, ['true','false']))
		{
			// $s_val = $s_val === 'true';
			$s_val = filter_var($s_val, FILTER_VALIDATE_BOOLEAN);
		}

		if($s_name=='init_mods' && $s_val)
		{
			System::initModules();
		}
		elseif(!empty($s_name))
		{
			if(!empty($group))
			{
				if(is_array($s_val))
				{
					self::$cfg[$group] = array_merge(self::$cfg[$group], $s_val);

					// self::$log->add('array', null,[ self::$cfg]);
				}
				else
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

			// die;
		}
	}


	/**
	 * *Наличие в /modules и подключение модуля
	 */
	static function checkModule(string $name)
	{
		$path = DR."/modules/$name";
		return (
			file_exists($path)
			&& !empty(self::$cfg['mds']["installed_$name"])
		);
	}


	/**
	 * *Создаём ссылки на модули в /modules
	 */
	static function CopyModules()
	{
		require_once __DIR__.'/kff_custom/cpDir.class.php';
		// linkDir::$excludes= '~token(\..+)?|cfg\..+~';

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
				// || !empty(self::$cfg['mds']["installed_$name"])
			) continue;

			if(empty(self::$cfg['mds']["installed_$name"]))
			{
				self::RemoveModule($name);
				self::$log->add("Module $name",null,[self::checkModule($name)]);
			}
			else
			{
				new linkDir($fileInfo->getPathname(), DR."/modules/$name");
			}

		}

		System::initModules();
	}


	/**
	 * *Удаление модулей
	 */
	static function RemoveModule($name)
	{
		self::$log->add("Вызываем ".__METHOD__,null,[$name,!is_dir($name),!is_dir(DR."/modules/$name")]);

		if(
			!is_dir($name)
			&& !is_dir($name = DR."/modules/$name")
		)
			return;

		self::$log->add("Удаляем Module $name");

		require_once __DIR__.'/kff_custom/cpDir.class.php';
		cpDir::RemoveDir($name);
	}

	/* static function createKFF()
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
	} */


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


	/**
	 * Отключение модулей
	 */
	static function disableModules($fileInfo, array &$ini)
	{
		if(empty($ini))
			return;

		$ini['disable'] = filter_var($ini['disable'], FILTER_VALIDATE_BOOLEAN);

		// self::$log->add(__METHOD__.$fileInfo->getFilename().' $ini[\'disable\']',null,[$ini['disable']]);

		$integrations = glob($fileInfo->getPathname().'/*integration_*');

		foreach($integrations as &$i)
		{
			if(empty($ini['disable']))
				$name = str_replace('dis_integration_', 'integration_', $i, $rcount);
			elseif(strpos($i,'dis_')===false)
				$name = str_replace('integration_', 'dis_integration_', $i, $rcount);
			if(!empty($rcount)) rename($i, $name);
		}

	}

	static function installModules(array &$info)
	{
		// self::$log->add('$info',null,[$info]);
		?>
		<h2>Установить модули:</h2>
		<div class="comment">
			<p>Данная настройка позволяет подключить в систему дополнительный функционал. Каждый выбранный модуль будет установлен в общую папку модулей.</p>
			<p>При снятии выбора модуль будет тут же удалён. Настройки сохранены не будут при последующей установке.</p>
		</div>
		<ul id="installModules" class="uk-list uk-list-striped uk-list-medium" data-group="mds">
			<li>
				<h4><label>Подключить все внутренние модули <input name="installAll" type="checkbox" <?=!empty(Basic::$cfg['mds']['installAll'])?'checked':''?>
			></label></h4>
			<p class="comment">Поставленный флажок внедряет и инициализирует все внутренние модули <b>kff</b>.</p>
			</li>

		<?php
		foreach (new FilesystemIterator(__DIR__.'/modules', FilesystemIterator::SKIP_DOTS) as $fileInfo) {
			$name= $fileInfo->getFilename();
			// self::$log->add('$name=',null,[$name]);

			if(
				!file_exists($fileInfo->getPathname().'/admin.php')
			) continue;

			if(empty($info[$name]) && file_exists($ini_path = $fileInfo->getPathname()."/info.ini"))
				$info[$name] = parse_ini_file($ini_path);

			$icon = file_exists($fileInfo->getPathname().'/icon.png')? '<img src="/'.Index_my_addon::getPathFromRoot($fileInfo->getPathname().'/icon.png').'" style="height:70px">': '';

			echo "<li><h5><label><input name='installed_$name' type=checkbox "
			. (self::checkModule($name) ? 'checked' : 'data-unchecked')
			."> $icon</label> $name v.{$info[$name]['version']}</h5>
			<p><a href='/admin/module.php?module=$name' class='uk-button uk-button-primary uk-button-small'>Настройки</a></p>"
			. "<div class=comment>".($info[$name]['description'] ?? '')."</div>

			</li>\n";
		}
		echo '</ul>';

	}



	private static function RenderPU()
	{
		$mds = self::scanModules();
		?>
		<!-- <link rel="stylesheet" href="/<?=self::$dir?>/admin.style.css"> -->

		<!-- uk-switcher -->
		<!-- class="menu_page" -->
		<!-- <div class="uk-flex"> -->
			<!-- <div style="width: 4em;"></div> -->
			<ul uk-tab uk-sticky="top: 100; show-on-up:true;">
				<li><button>Модули</button></li>
				<li><button>Подмодули</button></li>
				<li><button>Настройки</button></li>
			</ul>
		<!-- </div> -->

		<div class="uk-switcher">
		<!--  -->
		<?php
		echo '<div class="switcher-item">';
			require_once __DIR__.'/switcherItem.infoini.php';
		echo '</div><!-- .switcher-item -->';

		echo '<div class="switcher-item">';
			self::installModules($info);
		echo '</div><!-- .switcher-item -->';

		echo '<div class="switcher-item">';
			// require_once __DIR__.'/admin.htm';
			require_once __DIR__.'/switcherItem.globSts.php';
		echo '</div><!-- .switcher-item -->';

		echo '</div><!-- .uk-switcher -->';

		// self::$log->add('self::$cfg=',null,[self::$cfg]);
	}

}


Basic::init();


// *Тесты
// $log->add('$Page',null,[$Page]);