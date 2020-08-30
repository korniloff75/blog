<?php

/* // *Basic::cfg['kff']==0
if(empty($kff))
{
	include_once $_SERVER['DOCUMENT_ROOT'].'/system/global.dat';
	include_once __DIR__.'/kff_custom/index_my_addon.php';
} */

// Index_my_addon

$kff::headHtml();

// *UIKit
if(!empty($kff::$cfg['uk']['use_styles_input']))
{
	ob_start();
?>
	<script data-file="<?=basename(__FILE__)?>">
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

if(!empty($kff::$cfg['uk']['use_styles_ul']))
{
	$Page->endhtml.= '<script>
	kff.checkLib(\'jQuery\')
	.then($=>{
		$(\'ul\').addClass(\'uk-list uk-list-striped uk-list-large\');
	})
	</script>';
}

return null;
?>