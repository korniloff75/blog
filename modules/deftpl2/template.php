<?php
if(file_exists('modules/statistic/draw.php')){require('modules/statistic/draw.php');}
if($URI[1] != 'user' && $URI[2] == 'add'){$page->name = $page->title = 'Результат отправки сообщения';}
if($URI[2]=='changepassword'){$page->title = $page->name = 'Восстановление доступа';}
if($URI[2]=='changepassword2'){$page->title = $page->name = 'Восстановление доступа';}
if($URI[2]=='addreg1'){$page->title = $page->name = 'Регистрация';}
if($URI[2]=='echeck1'){$page->title = $page->name = 'Подтверждение email адреса';}
if($URI[2]=='upload2'){$page->title = $page->name = 'Загрузка аватара';}
$info = Module::info($Config->template);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<!-- <meta charset="utf-8">
<title><?php $page->get_title();?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="description" content="<?php $page->get_description();?>">
<meta name="keywords" content="<?php $page->get_keywords();?>">
<link rel="icon" type="image/vnd.microsoft.icon" href="/files/deftpl2/favicon.ico">
<link rel="shortcut icon" type="image/x-icon" href="/files/deftpl2/favicon.ico"> -->

{{coreHead}}

<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=cyrillic" rel="stylesheet">
<link href="/modules/deftpl2/css/style.min.css" rel="stylesheet">

<style>
<?php
require('style.min.css');
if(Module::exists('news')){
	require('default.min.css');
	}
?>

#nav {
	transition: height 1s 0s;
}
.content main {
	/* width: calc(100% - 240px); */
	min-height: 400px;
	flex: 1 1 auto;
}
.content .sidebar_right {
	width: 240px;
	flex: 0 0 auto;
	padding-top: 2em;
}
</style>

<?php
if($kff::is_admPanel()){
?>
	<link rel="stylesheet" href="/admin/include/windows/windows.css">
	<script async src="/admin/include/windows/windows.js"></script>
<?php
}
?>
</head>

<body>
<header>
<div class="container">

<div class="logo"><a href="/"><?php $page->get_header();?></a></div>
<?php if(is_dir('./modules/users')){
echo'<div class="user_menu">';
if($User->authorized){
echo'<a href="/user" class="user">'.$User->login.'</a>';
}else{
echo'<a href="/user" class="in">Вход</a> / <a href="/user/reg" class="reg">Регистрация</a>';
}
echo'</div>';
}
?>
<div class="slogan"><?php $page->get_slogan();?></div>

<!-- Поиск -->
<?php /* if(is_dir('./modules/search') || is_dir('./modules/mod_search')){?>
<div class="search">
<form name="search" action="/search" method="post">
<input type="text" name="q" value="" placeholder="Поиск...">
</form>
</div>
<?php } */?>


</div>
</header>
<div class="hbr_menu">
<a href="javascript:void(0);" id="menu" class="button uk-hidden@m">Меню</a>
</div>
<nav id="nav">
<div class="container">
<?php if(is_dir('./modules/menu')){?>
<ul class="menu">
<?php require('./modules/menu/integration_page.php');?>
</ul>
<?php }else{?>
<ul>
<?php $page->get_menu('li');?>
</ul>
<?php }?>
</div>
</nav>
<?php if($Page->isIndexPage() && is_dir('./modules/slider')){?>
<div class="container">
<?php require('./modules/slider/integration_page.php');?>
</div>
<?php }?>

<div class="container">
<div class="content uk-flex uk-flex-wrap">
<main class="uk-width-expand uk-width-1@m">
<?php if($URI[1] !== 'index' && is_dir('./modules/breadcrumbs')):
require('./modules/breadcrumbs/integration_page.php');
endif;?>
<article>
<!-- <h1><?php $page->get_name();?></h1> -->
<?php if($Page->module == 'users' || $Page->module == 'mail'){
require('./modules/'.$Config->template.'/'.$Page->module.'.php');
}else{
$Page->get_content();
}?>
</article>
</main>

<!-- <div class="sidebar_left">
<?php $page->get_column('left','<aside><h2>#name#</h2><div class="aside_content">#content#</div></aside>');?>
</div> -->

<div class="sidebar sidebar_right">
<?php $page->get_column('right','<aside><h2>#name#</h2><div class="aside_content">#content#</div></aside>');?>
</div>

</div>
</div>
<div class="bgfooter">
<div class="container">
<footer>
<div class="logo">
<?php
if(date('Y') == $info['data_copy']){?>
&copy; <?php echo $info['data_copy'];?> г.&emsp;<a href="/"><?php $Page->get_header();?></a><br>
<?php }else{?>
&copy; <?php echo $info['data_copy'];?>-<?php echo date('Y');?> г.&emsp;<a href="/"><?php $Page->get_header();?></a><br>
<?php }?>
</div>
<div class="nav">
<?php $page->get_menu('span');
?>
</div>
</footer>
</div>
</div>
<div class="copyrighted">
<?php if(file_exists('./data/pages/cfg_fz152.dat')){
$file_cfg=explode('<||>',file_get_contents('./data/pages/cfg_fz152.dat'));
if($file_cfg[4] == 1){?>
<a href="/fz152">Политика конфиденциальности</a>
<?php }
}
if(!file_exists('./system/classes/dco.dat')){?>
 | <a href="//my-engine.ru">Работает на My Engine CMS</a> | <a href="//3shaga.ru">Дизайн 3shaga.ru</a>
<?php }?>
</div>

<script>
'use strict';
// *top padding
var $nav= $('#nav');

// *burger button
$('#menu').on('click', function(e){
	e.stopPropagation();

	$nav.prop('scrollHeight')
	? $nav.css({
		display: '',
		height: '0px'
	})
	: $nav.css({
		display: 'block',
	}), $nav.css({
		height: $nav.prop('scrollHeight') + 'px'
	})

	/* $nav.css({
		// display: 'block',
		height: parseInt($nav.css('height')) === $nav.prop('scrollHeight')
		? 0
		: $nav.prop('scrollHeight') + 'px'
	}); */
	console.log(parseInt($nav.css('height')), $nav.prop('scrollHeight'), (parseInt($nav.css('height')) === $nav.prop('scrollHeight')));
});


$(document).on('click', function(e){
	// var $t= $(e.target).closest($nav);

	if(parseInt($nav.css('height')) > 100) {
		$nav.css({
		display: '',
		height: '0px'
	});
		// console.log($t, $nav.css('height'));
	}
});
</script>

{{coreFooter}}

</body></html>