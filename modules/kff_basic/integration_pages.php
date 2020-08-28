<?php

// *Basic::cfg['kff']==0
if(empty($kff))
{
	include_once __DIR__.'/kff_custom/index_my_addon.php';

	$kff::headHtml();

}
// Index_my_addon

return null;
?>