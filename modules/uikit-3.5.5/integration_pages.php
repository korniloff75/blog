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

	$log->add('',null,[$Page]);

	if($params['use_styles'])
	{
		$Page->endhtml.= '<script>
		$(()=>{$(\'input\')
		.each((ind,i)=>{
			i.classList.add(\'uk-input\');
		});

		$(\'input[type=checkbox]\')
		.each((ind,i)=>{
			i.classList.add(\'uk-checkbox\');
		});})
		</script>';
	}

	$Page->headhtml.= '<!-- /UIkit-->';

}


// var_dump($Page->headhtml);

return null;