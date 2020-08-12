<?php
$params= json_decode(
	file_get_contents(__DIR__."/sts.json"), 1
) ?? [
	'include_uikit'=>0,
	'include_picts'=>0,
];

// *REQUEST
if(!empty(@$name_req = $_REQUEST['name']??null))
{
	$params[$name_req] = $_REQUEST['sts'];
	file_put_contents( __DIR__."/sts.json",
		json_encode($params, JSON_UNESCAPED_UNICODE)
	);
	die ('is ajax_request!');
}

require_once "../kff_custom/index_my_addon.php";
$dirFromRoot = $kff::getPathFromRoot(__DIR__);

$params_js= json_encode($params, JSON_UNESCAPED_UNICODE);

?>

<link rel="stylesheet" href="/<?=$dirFromRoot?>/css/uikit.min.css" />
<script src="/<?=$dirFromRoot?>/js/uikit.min.js"></script>
<script src="/<?=$dirFromRoot?>/js/uikit-icons.min.js"></script>

<div class="header"><h1>Настройки</h1></div>

<div class="menu_page">
	<a href="index.php">&#8592; Вернуться назад</a>
</div>

<div class="content">

	<!-- <h2>uikit-3.5.5</h2> -->
	<h2><?=$MODULE?></h2>

	<div class="box">
		<p>Для работы модуля требуются:</p>
		<ul>
			<li>jQuery</li>
			<li>директория kff_custom</li>
		</ul>
	</div>


	<div class="uk-form">

		<ul class="uk-list uk-list-striped uk-list-large">
			<li><label>Подключить UIKIT <input name="include_uikit" type="checkbox"></label></li>
			<li><label>Подключить изображения <input name="include_picts" type="checkbox"></label></li>
			<li><label>Применить стили ко всем элементам <i>input</i> <input name="use_styles_input" type="checkbox"></label></li>
			<li><label>Применить стили ко всем элементам <i>ul</i> <input name="use_styles_ul" type="checkbox"></label></li>
		</ul>

	</div>

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
	$form.on('change','input[type=checkbox]', (e)=>{
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
