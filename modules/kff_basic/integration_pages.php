<?php

// *Basic::cfg['kff']==0
if(empty($kff))
{
	include_once $_SERVER['DOCUMENT_ROOT'].'/system/global.dat';
	include_once __DIR__.'/kff_custom/index_my_addon.php';
}

// Index_my_addon
$kff::headHtml();

return null;
?>