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
	static $dir, $cfg, $log, $mds_prefix;

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
		self::$cfg= array_merge(
			[
				'copyModules'=> 1,
				'mds_prefix'=> 'kff' // *Префикс для сканируемых модулей
			], json_decode(
				@file_get_contents(__DIR__.'/cfg.json'), 1
			)
			);



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
		$ini[$s_name] = $s_val;

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
			self::$cfg[$s_name] = $s_val;
			file_put_contents(
				__DIR__.'/cfg.json', json_encode(self::$cfg)
			);

			if(self::$cfg['copyModules'])
			{
				self::CopyModules();
				// self::createKFF();
			}
			else
			{
				// self::destructKFF();
			}

			die;
		}
	}


	// *Создаём ссылки на модули в /modules
	static function CopyModules()
	{
		require_once __DIR__.'/kff_custom/cpDir.class.php';
		linkDir::$excludes= '~token(\..+)?|cfg\..+~';

		$lnk_mds = new linkDir(__DIR__.'/modules', DR.'/modules');

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
		$mds = glob(realpath(__DIR__.'/..')."/".self::$cfg['mds_prefix']."_*");
		/* $mds= array_filter(
			$mds,
			function(&$i){
				return stripos($i, 'basic') === false;
			}
		); */
		// self::$log->add('kff modules',null,[$mds]);

		return $mds;
	}


	static function RenderPU()
	{
		$mds = self::scanModules();
		echo '<h3>Модули '.self::$cfg['mds_prefix'].'</h3>';
		echo '<p class=comment>Все изменённые настройки сохраняются автоматически -- после потери фокуса редактируемым полем.</p>
		<ul id=mds_sts uk-accordion>';

		foreach($mds as &$m)
		{
			$ini_path = "$m/info.ini";
			$adm_path = "$m/admin.php";
			$ini = parse_ini_file($ini_path);

			$is_feedback = mb_stripos($ini['name'],'связь через TG') && count(explode('.', $ini['version'])) > 2;

			/* self::$log->add(__METHOD__,null,[
				$ini['name'],mb_stripos($ini['name'],'связь через TG'),
				count(explode('.', $ini['version'])),
				mb_stripos($ini['name'],'связь через TG') && count(explode('.', $ini['version'])) > 2
			]); */


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

		// *Подключаем морду
		require_once __DIR__.'/admin.htm';
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