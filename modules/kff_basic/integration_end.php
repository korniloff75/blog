<?php
// *Собираем весь буфер в $buf и очищаем его (не закрываем)
$buf = ob_get_contents();
ob_clean();

if(!$Page) {
	$log::$notWrite= 1;
	header(PROTOCOL.' 404 Not Found'); require('./pages/404.html');
	die('404');
}


// *Templater
// *Собираем сюда все финишные замены
$Templater = [];

// *{{coreHead}}
ob_start();
?>

	<meta charset="utf-8">
	<?php $Page->get_headhtml()?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>{{Title}}</title>
	<meta name="description" content="<?php $Page->get_description()?>">
	<meta name="keywords" content="<?php $Page->get_keywords()?>">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">

<?php
$Templater['coreHead']= ob_get_clean();

// *если нет {{coreHead}} - вставляем в head
if(!stripos($buf,'{{coreHead}}')){
	$buf= str_replace('</head>', "<!-- coreHead -->\n{{coreHead}}\n</head>", $buf, $cb);
	$log->add("{{coreHead}} is EMPTY! ",null,[$cb]);
}

// *{{Title}}
ob_start();
	$Page->get_title();
$Templater['Title']= ob_get_clean();

// *{{coreFooter}}
ob_start();
	$Page->get_endhtml();
	echo Index_my_addon::profile('base');

$Templater['coreFooter']= ob_get_clean();

// *если нет {{coreFooter}} - вставляем в конце
if(!strpos($buf,'{{coreFooter}}')){
	$buf= str_replace('</body>', "<!-- coreFooter -->\n{{coreFooter}}\n</body>", $buf, $cb);
	$log->add("{{coreFooter}} is EMPTY! ",null,[$cb]);
}



// $log->add("\$Templater = ",null,[$Templater]);


// *Admin
if($kff::is_adm())
{
	// *{{coreFooter}}
	ob_start();
	?>
		<div id='logWrapper'>
			<?=$log->printCode()?>
			<style>
				pre.log{
					background: #111;
					color: #3f3;
				}
			</style>

			<script>
				window.kff && kff.highlight('.log');
			</script>
		</div>
	<?php

	$Templater['coreFooter'].= ob_get_clean();

} // *$kff::is_adm()


// *Финишная замена кодов шаблона на HTML
$buf = preg_replace(
	array_map(function(&$i){
		return "~\{\{\s*?$i\s*?\}\}~i";
	},array_keys($Templater)),
	array_values($Templater),
	$buf, 1, $count
);

// $log->add("Количество замен= ". $count,null,[$Page]);
$log->add("Уровень буфера= ". ob_get_level() . ' -> flush to index');


echo $buf;
// *-> to flush in index.php