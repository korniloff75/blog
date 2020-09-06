<?php
if(!defined('DR'))
	define('DR', $_SERVER['DOCUMENT_ROOT']);

$modDir = str_replace(
	$_SERVER['DOCUMENT_ROOT'], '',
	str_replace('\\','/',__DIR__)
);

ob_start();
?>

<script src="<?=$modDir?>/hl.js" data-file="<?=basename(__DIR__)?>">
</script>

<?php
return ob_get_clean();