<?php
$buf = ob_get_contents();
ob_clean();

// *Собираем сюда все финишные замены
$Templater = [];

// *Подкладываем данные из Блога
if(class_exists('BlogKff'))
{
	// $artDB= $Blog->getArtDB()->get();
	foreach(['title','description','keywords'] as $prop){
		// $Page->{$prop}= $artDB[$prop];
		$Page->{$prop}= BlogKff::getArtDB()->get($prop);
	}
}

// *{{coreHead}}
ob_start();
?>

	<meta charset="utf-8">
	<?=$Page->get_headhtml()?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php $Page->get_title();?></title>
	<meta name="description" content="<?=$Page->get_description()?>">
	<meta name="keywords" content="<?=$Page->get_keywords()?>">

<?php
$Templater['coreHead']= ob_get_clean();

// *{{Title}}
ob_start();
	$Page->get_title();
$Templater['Title']= ob_get_clean();

// *{{coreFooter}}
ob_start();
	$Page->get_endhtml();
	echo Index_my_addon::profile('base');

$Templater['coreFooter']= ob_get_clean();


// $log->add("\$Templater = ",null,[$Templater]);

$log->add("Уровень буфера= ". ob_get_level());


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
		return "~\{\{$i\}\}~i";
	},array_keys($Templater)),
	array_values($Templater),
	$buf, 1, $count
);

// $log->add("Количество замен= ". $count,null,[$Page]);


echo $buf;
// *-> to flush in index