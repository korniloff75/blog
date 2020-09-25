<?php

$kff::headHtml();

// *Подкладываем данные из Блога
if(class_exists('BlogKff'))
{
	foreach(['title','description','keywords'] as $prop){
		$Page->{$prop}= BlogKff::getArtDB()->get($prop);
	}
}

$Page->endhtml.= '<link rel="stylesheet" href="/'.$kff::$dir.'/css/core.style.css" />';


// *UIKit
if(!empty($kff::$cfg['uk']['use_styles_input']))
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

if(!empty($kff::$cfg['uk']['use_styles_ul']))
{
	$Page->endhtml.= '<script>
	kff.checkLib(\'jQuery\')
	.then($=>{
		$(\'ul\').addClass(\'uk-list uk-list-striped uk-list-large\');
	})
	</script>';
}

// *Examples 4 Surfyk
/* $pages_start = System::listPages();
print_r($pages_start);
// *Нужно поднять вверх $pages_start[4]
list($pages_start[4], $pages_start[3]) = [$pages_start[3], $pages_start[4]];
print_r($pages_start);

// *Если нужно записать в файл
if(
	file_put_contents(DR.'/data/pages/list.dat', json_encode($pages_start))
)
	echo "Файл весело записан."; */

return null;
