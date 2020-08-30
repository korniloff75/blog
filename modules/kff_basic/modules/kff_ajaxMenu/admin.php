<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

require_once DR.'/'.$kff::$dir.'/DbJSON.php' ;

// $db = new EngineStorage_kff('cfg.db', __DIR__);
DbJSON::$convertPath = false;
$db = new DbJSON(__DIR__.'/cfg.json');

$cfg = $db->get();

$cfg = array_merge([
	'nav_selector'=>'#nav',
	'main_selector'=>'main'
], $cfg);

$log->add('$cfg',null,[$cfg]);

// return;


if($act=='index'){
	?>

	<div class="header"><h1>Настройки обратной связи</h1></div>
	<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a></div>

	<div class="content">

		<div id="sts" class="uk-section .uk-section-primary uk-padding">
			<p><label>Селектор блока с меню <input name="nav_selector" type="text" value="<?=$cfg['nav_selector']?>"></label></p>
			<p class="comment">CSS селектор блока с меню.</p>
			<p><label>Селектор блока с контентом <input name="main_selector" type="text" value="<?=$cfg['main_selector']?>"></label></p>
			<p class="comment">CSS селектор блока с контентом.</p>
		</div>

	</div>

	<script>
	'use strict';
	// kff.checkLib('jQuery')
	// .then($=>{
	$(()=>{
		// *Украшения
		// $('#kff_sts input[type=checkbox]').addClass('uk-checkbox');

		// *Сохр глоб. настроек
		$('#sts').on('change', 'input', $e=>{
			var $t= $($e.target);
			// console.log($t.prop('checked'));
			if(!$t.length) return;

			var val= $t.val() === 'on' ? $t.prop('checked') : $t.val();

			$.post('',{
				act: 'saveCfg',
				name: $t.prop('name'),
				val: val,
			}).then((response, status)=>{
				// console.log();
				$('<div/>').insertAfter($e.currentTarget)
				.html($(response).find('.log'));
			});
		});
	});

	</script>

	<?php
}


// *Save
if($act=='saveCfg')
{
	$s_name = filter_var($_REQUEST['name']);
	$s_val = filter_var($_REQUEST['val']);

	$cfg[$s_name] = $s_val;

	// $db->set('cfg', $cfg );
	$db->set($cfg );

	echo'<div class="msg">Настройки успешно сохранены</div>
	<p><a href="module.php?module='.$MODULE.'"><<Назад</a></p>';
?>

<?php
}
