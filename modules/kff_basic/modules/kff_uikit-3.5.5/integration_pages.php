<?php
if(empty($kff))
	return "Для работы модуля требуется наличие директории <b>kff_custom</b> в корне движка."
	;


// require_once $_SERVER['DOCUMENT_ROOT']."/kff_custom/index_my_addon.php";
$modDir = $kff::getPathFromRoot(__DIR__);

$log->add(basename(__FILE__)." started");

$params= json_decode(
	file_get_contents(__DIR__."/sts.json"), 1
) ?? [
	'include_uikit'=>0,
];

if($params['include_uikit'])
{
	$Page->headhtml.= '
	<!-- UIkit-->
	<link rel="stylesheet" href="/'.$modDir.'/css/uikit.min.css" />
	<script src="/'.$modDir.'/js/uikit.min.js"></script>';

	if(@$params['include_picts'])
	{
		$Page->headhtml.= '
		<!-- UIkit picts-->
		<script src="/'.$modDir.'/js/uikit-icons.min.js"></script>';
	}

	$Page->headhtml.= '<!-- /UIkit-->';

	// $log->add('',null,[$Page]);

	if(@$params['use_styles_input'])
	{
		ob_start();
	?>
		<script>
		'use strict';
		kff.checkLib('jQuery')
		.then($=>{
			$('input:not([type=checkbox], [type=file])').addClass('uk-input');

			// $('input[type=button]').addClass('uk-input');

			$('select').addClass('uk-select');

			$('textarea').addClass('uk-textarea');

			$('input[type=checkbox]')
			.addClass('uk-checkbox');

			$('input[type=radio]').addClass('uk-radio');

			$('input[type=range]').addClass('uk-range');

			$('input[type=file]')
			.wrap('<div uk-form-custom />')
			.after('<button class="uk-link" style="color:#fff">Загрузить</button>')
			// .text(this.value)
			// .text(this.value||"Загрузить")
		})
		</script>

	<?php
		$Page->endhtml.= ob_get_clean();
	}

	if(@$params['use_styles_ul'])
	{
		$Page->endhtml.= '<script>
		kff.checkLib(\'jQuery\')
		.then($=>{
			$(\'ul\').addClass(\'uk-list uk-list-striped uk-list-large\');
		})
		</script>';
	}

}

return null;