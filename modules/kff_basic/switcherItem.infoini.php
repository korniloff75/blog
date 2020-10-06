<h2>Настройки <b>info.ini</b></h2>

<div class="kff_sts">
<!-- <p class="box"><label>Префикс для модулей <input name="mds_prefix" type="text" value="<?= Basic::$cfg['mds_prefix']?>"></label></p> -->
<p class="box">
	<label>Префикс для модулей
	<select name="mds_prefix" type="text" value="<?= Basic::$cfg['mds_prefix']?>">
		<option value="kff_">kff_</option>
		<option value="" <?=empty(Basic::$cfg['mds_prefix'])?'selected':''?>>Все модули</option>
	</select>
	</label>
</p>
<p class="comment">Скрипт выбирает все модули с указанным префиксом. Например, папки искомых модулей должны называться как <b>kff_modulename</b>. Для выбора всех модулей из системы -- удалите префикс. После любого изменения префикса -- перезагрузите страницу.</p>
</div><!-- #kff_sts -->

<h2>
	Модули <?=empty(self::$cfg['mds_prefix'])? 'из <i>/modules</i>': self::$cfg['mds_prefix']?>
</h2>

<p class=comment>Все изменённые настройки сохраняются автоматически -- после потери фокуса редактируемым полем.</p>

<ul id=mds_sts uk-accordion>

<?php
// echo '';

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

	$ini = array_merge([
		'disable' => 0,
	],parse_ini_file($ini_path) ?? []);

	$info[$name] = $ini;

	self::disableModules(new SplFileInfo($m), $ini);

	$is_feedback = mb_stripos($ini['name'],'связь через TG') && count(explode('.', $ini['version'])) > 2;

	/* if($name === 'kff_ajaxMenu')
		self::$log->add(__METHOD__.' '.$ini['name'],null,[
			$ini, $ini['name'],$ini['version']
		]); */


	echo "<li ".($is_feedback?'class=uk-open':'').">
	<h4 class='uk-accordion-title".(
		!empty($ini['disable'])?" uk-background-primary uk-background-muted uk-light":''
		)."' data-ini-path='$ini_path'>{$ini['name']} v.{$ini['version']}".(
			!empty($ini['disable'])?" -- disabled":''
		)." <a href='/admin/module.php?module=$name' class='uk-button uk-button-primary uk-button-small' onclick='event.stopPropagation();'>Настройки</a>
		</h4>";
	echo '<ul class="uk-accordion-content uk-margin-bottom">';

	foreach($ini as $key=>&$val)
	{
		// $val = htmlspecialchars($val);
		// todo history
		if(is_array($val)) continue;

		$tag = $key !== 'description' ? "<input class=uk-width-2-3 type=text value='$val'>" : "<div contentEditable=true class=uk-width-2-3@s>$val</div>";

		echo "<li class='uk-flex uk-flex-wrap uk-padding-small'><span class=uk-width-1-3@s>$key </span> $tag</li>";
	}
	// todo
	// require_once $adm_path;
	echo '</ul>';

	echo '</li>';
}
echo '<!-- /uk-accordion --></ul>';