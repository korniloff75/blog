<?php
if(__DIR__ === realpath('.')) die;

require_once __DIR__.'/kff_custom/index_my_addon.php';

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
			'kff'=> 0,
			'mds_prefix'=> 'kff'
		]; */
		self::$cfg= array_merge(
			[
				'kff'=> 0,
				'mds_prefix'=> 'kff'
			], json_decode(
				@file_get_contents(__DIR__.'/cfg.json'), 1
			)
			);

		// *Префикс для сканируемых модулей
		self::$mds_prefix = self::$cfg['mds_prefix'] ?? 'kff';

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
			empty($ini_path = $_REQUEST['ini_path'])
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

			if(self::$cfg['kff'])
			{
				self::createKFF();
			}
			else
			{
				self::destructKFF();
			}

			die;
		}
	}


	static function createKFF()
	{
		if(!file_exists(__DIR__.'/kff_custom/cpDir.class.php'))
			link(__DIR__.'/cpDir.class.php', __DIR__.'/kff_custom/cpDir.class.php');

		require_once __DIR__.'/kff_custom/cpDir.class.php';

		// *Создаём в корне каталог /kff_custom со ссылками
		$dir = new linkDir(__DIR__.'/kff_custom', DR.'/kff_custom');

		// *Бэкапим оригинальный индекс
		if(
			!file_exists(DR.'/index.php.bak')
			&& copy(DR.'/index.php', DR.'/index.php.bak')
		)
		{
			$ind= file_get_contents(DR.'/index.php');

			// *Подключаем /kff_custom/index_my_addon.php
			$ind = preg_replace("~^.+\/system\/global\.dat.+$~um", 'require_once \'./kff_custom/index_my_addon.php\';', $ind, 1);

			if(file_put_contents(DR.'/index.php', $ind))
			{
				echo "<pre><h3 style='color:#fff;'>Изменения в индексный файл успешно внесены.</h3>".htmlspecialchars($ind)."</pre>";
			}

		}


		echo "<pre><h3 style='color:#fff;'>Каталог /kff_custom успешно создан или обновлён.</h3>";
		ob_start();
		print_r($dir->get_log());
		ob_end_flush();
		echo "</pre>";
	}


	static function destructKFF()
	{
		require_once __DIR__.'/kff_custom/cpDir.class.php';

		// *Создаём в корне каталог /kff_custom со ссылками
		$dir = new linkDir(__DIR__.'/kff_custom', DR.'/kff_custom');

		// *Бэкапим оригинальный индекс
		if(
			file_exists(DR.'/index.php.bak')
			&& copy(DR.'/index.php.bak', DR.'/index.php')
			&& unlink(DR.'/index.php.bak')
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
		$mds = glob(realpath(__DIR__.'/..')."/".self::$mds_prefix."_*");
		/* $mds= array_filter(
			$mds,
			function(&$i){
				return stripos($i, 'basic') === false;
			}
		); */
		// self::$log->add('kff modules',null,[$mds]);

		echo '<h3>Модули kff</h3>';
		echo '<ul id=mds_sts uk-accordion>';

		foreach($mds as &$m)
		{
			$ini_path = "$m/info.ini";
			$ini = parse_ini_file($ini_path);

			$is_feedback = mb_stripos($ini['name'],'связь через TG') && count(explode('.', $ini['version'])) > 2;

			/* self::$log->add(__METHOD__,null,[
				$ini['name'],mb_stripos($ini['name'],'связь через TG'),
				count(explode('.', $ini['version'])),
				mb_stripos($ini['name'],'связь через TG') && count(explode('.', $ini['version'])) > 2
			]); */

			// print_r($ini);
			// self::$log->add(basename($m),null,[$ini]);


			echo "<li ".($is_feedback?'class=uk-open':'').">
			<h4 class=uk-accordion-title data-ini-path='$ini_path'>{$ini['name']} v.{$ini['version']}</h4>";
			echo '<ul class="uk-accordion-content uk-margin-bottom">';
			foreach($ini as $key=>&$val)
			{
				$val = htmlspecialchars($val);

				$tag = $key !== 'description' ? "<input class=uk-width-2-3 type=text value='$val'>" : "<textarea class=uk-width-2-3>$val</textarea>";

				echo "<li class='uk-flex uk-flex-wrap uk-padding-small'><span class=uk-width-1-3>$key </span> $tag</li>";
			}
			echo '</ul></li>';
		}
		echo '<!-- /uk-accordion --></ul>';
	}
}


Basic::init();

Basic::scanModules();

// *Подключаем морду
require_once __DIR__.'/admin.htm';



// *Тесты
$log->add('$Page',null,[$Page]);