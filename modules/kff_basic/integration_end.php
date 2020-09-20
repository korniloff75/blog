<?php
$buf = ob_get_contents();
ob_clean();

$buf = preg_replace('~(<\/body>)~',
	Index_my_addon::profile('base')
	. "\n$1", $buf, 1
);

$log->add("Уровень буфера= ". ob_get_level());


// *Admin
if($kff::is_adm())
{
	ob_start();
?>

	<style>
		pre.log{
			background: #111;
			color: #3f3;
		}
	</style>

	<script>
		window.kff && kff.highlight('.log');
	</script>

	<?php
	$logHahdles = ob_get_clean();


	// *Add to End HTML

	$buf= preg_replace('~(<\/body>)~',
		"<div id='logWrapper'>"
		. $log->print()
		. "\n</div>"
		. $logHahdles
		. "\n$1", $buf, 1
	);

}


echo $buf;
// -> to flush in index