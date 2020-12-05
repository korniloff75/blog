<?php

if(
	class_exists('BlogKff')
	&& $artDB= BlogKff::getArtDB()
){
	// $log->add('',null,['$artData'=>$artData]);

	// *Подкладываем данные из Блога
	foreach(['title','description','keywords'] as $prop){
		if(!empty($val= trim($artDB->{$prop}))){
			$Page->{$prop}= $val;
			// $log->add('',null,["$prop"=>$Page->{$prop}]);
		}
	}
}

$Page->endhtml.= '<link rel="stylesheet" href="/'.$kff::$dir.'/css/core.style.css" />';


// *UIKit
if(!empty($kff::$cfgDB->uk['use_styles_input']))
{
	ob_start();
?>
	<script data-file="<?=basename(__DIR__)?>">
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

if(!empty($kff::$cfgDB->uk['use_styles_ul']))
{
	$Page->endhtml.= '<script>
	kff.checkLib(\'jQuery\')
	.then($=>{
		$(\'ul\').addClass(\'uk-list uk-list-striped uk-list-large\');
	})
	</script>';
}

return null;
