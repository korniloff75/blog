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
	if(!empty(@$disable = $_REQUEST['disable']??null))
	{
		$params= [];
	}
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
		<h5>Для работы модуля требуются:</h5>
		<ul>
			<li>jQuery</li>
			<li>директория kff_custom</li>
		</ul>
	</div>


	<ul class="uk-list uk-list-striped uk-list-large">
		<li><label>Подключить UIKIT <input id="include_uikit" name="include_uikit" type="checkbox"></label></li>
		<li><label>Подключить изображения <input name="include_picts" type="checkbox"></label></li>
		<ul class="uk-list uk-list-striped">
			<h5>Применить стили ко всем:</h5>
			<li><label>Тегам <i>input</i> <input name="use_styles_input" type="checkbox"></label></li>
			<li><label>Тегам <i>ul</i> <input name="use_styles_ul" type="checkbox"></label></li>
			<p class="comment">
				Подключённые теги будут динамически обрабатываться модулем.
			</p>
		</ul>

		</ul>

</div>


<script>
'use strict';
$(()=>{
	var $list= $('.uk-list');
	$list.find('input[type=checkbox]')
	.each((ind,i)=>{
		i.classList.add('uk-checkbox');
		i.checked= <?=$params_js?>[i.name] == 1;
		console.log(i, i.checked);
	});

	// *Save sts request
	$list.on('change','input[type=checkbox]', (e)=>{
		var p_name= e.target.name,
			send_data= {
				name: p_name,
				sts: e.target.checked?1:0,
			};

		if(!$('#include_uikit').prop('checked')) {
			send_data.disable = 1;
			$list.find('input[type=checkbox]').prop({checked:0});
		}
		console.log(e, send_data);
		if(p_name) $.post('', send_data);
	});
})
</script>