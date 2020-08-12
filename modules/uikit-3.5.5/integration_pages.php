<?php
require_once $_SERVER['DOCUMENT_ROOT']."/kff_custom/index_my_addon.php";
$dirFromRoot = $kff::getPathFromRoot(__DIR__);

$log->add(__FILE__." started!!!");

$params= json_decode(
	file_get_contents(__DIR__."/sts.json"), 1
) ?? [
	'include_uikit'=>0,
	'include_picts'=>0,
];

if($params['include_uikit'])
{
	$Page->headhtml.= '
	<!-- UIkit-->
	<link rel="stylesheet" href="/'.$dirFromRoot.'/css/uikit.min.css" />
	<script src="/'.$dirFromRoot.'/js/uikit.min.js"></script>';

	if($params['include_picts'])
	{
		$Page->headhtml.= '
		<!-- UIkit picts-->
		<script src="/'.$dirFromRoot.'/js/uikit-icons.min.js"></script>';
	}

	$Page->headhtml.= '<!-- /UIkit-->';

	// $log->add('',null,[$Page]);

	if(@$params['use_styles_input'])
	{
		$Page->endhtml.= '<script>
		$(()=>{
			$(\'input:not([type=checkbox], [type=file])\').addClass(\'uk-input\');

			// $(\'input[type=button]\').addClass(\'uk-input\');

			$(\'select\').addClass(\'uk-select\');

			$(\'textarea\').addClass(\'uk-textarea\');

			$(\'input[type=checkbox]\')
			.addClass(\'uk-checkbox\');

			$(\'input[type=radio]\').addClass(\'uk-radio\');

			$(\'input[type=range]\').addClass(\'uk-range\');

			$(\'input[type=file]\')
			.wrap(\'<div uk-form-custom />\')
			.after(\'<button class="uk-link" style="color:#fff">Загрузить</button>\')
			// .text(this.value)
			// .text(this.value||"Загрузить")
		})
		</script>';
	}

	if(@$params['use_styles_ul'])
	{
		$Page->endhtml.= '<script>
		$(()=>{
			$(\'ul\').addClass(\'uk-list uk-list-striped uk-list-large\');
		})
		</script>';
	}

}

return null;