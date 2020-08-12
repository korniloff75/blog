<?php
require_once "../kff_custom/index_my_addon.php";
$dirFromRoot = $kff::getPathFromRoot(__DIR__);

$params= json_decode(
	file_get_contents(__DIR__."/sts.json"), 1
) ?? [
	'include_uikit'=>0,
	'include_picts'=>0,
	'use_styles'=>0,
];

// *REQUEST
if(!empty($name_req = @$_REQUEST['name']))
{
	$params[$name_req] = $_REQUEST['sts'];
	file_put_contents( __DIR__."/sts.json",
		json_encode($params, JSON_UNESCAPED_UNICODE)
	);
	die ('is request!');
}

$params_js= json_encode($params, JSON_UNESCAPED_UNICODE);

?>

<link rel="stylesheet" href="/<?=$dirFromRoot?>/css/uikit.min.css" />
<script src="/<?=$dirFromRoot?>/js/uikit.min.js"></script>
<script src="/<?=$dirFromRoot?>/js/uikit-icons.min.js"></script>

<div class="warning">
	<p>Для работы модуля требуется директория kff_custom !!!</p>
</div>

<div class="uk-form">
	<?#=$MODULE;?>

	<ul>
		<li><label>Подключить UIKIT <input name="include_uikit" type="checkbox"></label></li>
		<li><label>Подключить изображения <input name="include_picts" type="checkbox"></label></li>
		<li><label>Применить стили ко всем элементам <input name="use_styles" type="checkbox"></label></li>
	</ul>

	<!-- <button>Save</button> -->
</div>

<script>
'use strict';
$(()=>{
	var $form= $('.uk-form');
	$form.find('input[type=checkbox]')
	.each((ind,i)=>{
		i.classList.add('uk-checkbox');
		i.checked= <?=$params_js?>[i.name] == 1;
		console.log(i, i.checked);
	});
	// *Save sts
	$form.on('change','input', (e)=>{
		var p_name= e.target.name;
		console.log(e, {
			name: p_name,
			sts: e.target.checked,
		});
		if(p_name) $.post('',{
			name: p_name,
			sts: e.target.checked?1:0,
		});
	});
})
</script>