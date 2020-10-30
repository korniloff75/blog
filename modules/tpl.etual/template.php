<?php
$templatePath= '/'. $kff::getPathFromRoot(__DIR__);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
{{coreHead}}

<style>
<?= file_get_contents(__DIR__.'/style.min.css');?>

* {
	box-sizing: border-box;
}
body {
	background: #eee url(<?=$templatePath?>/images/bg_drawing.png) 0 0 fixed;
}
.sidebar, .aside_content{
	background: #eee;
}
header {
	overflow: hidden;
	line-height: 50px;
	font-size: 14px;
}
header#logo > img{
	opacity: .9;
}
.bgcontent {
	padding-top: 0;
}
.uk-nav-center.uk-nav-parent-icon > .uk-parent > a::after {
	position: static !important;
}
footer *{
	color: #777;
}
footer a.logo{
	color: #999;
}
header .user_menu a {
	padding: 10px 20px 10px 18px;
	background: #111 url(/modules/tpl.etual/images/user.svg) 10px 47% no-repeat;
}
</style>

<?php
if($kff::is_adm()){
?>
	<link rel="stylesheet" href="/admin/include/windows/windows.css">
	<script async src="/admin/include/windows/windows.js"></script>
<?php
}
?>

</head>


<body>
<div class="bgheader">
	<div class="container">
		<header>
			<div class="hbr_menu">
				<a href="javascript:void(0);" id="menu"><svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
					<path d="M0 0h24v24H0z" fill="none"/>
					<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
				</svg></a>
			</div>

			<div class="search">
				<form name="search" action="/search" method="post">
					<input type="text" name="q" value="" placeholder="Поиск...">
				</form>
			</div>

			<nav id="nav">
				<?php $Page->get_menu('span');?>
				<div class="user_menu">
					<?php
						if($Config->registration){
							if($User->authorized){
								echo'<a href="/user" class="user">'.$User->login.'</a>';
							}else{
								echo'<a href="/user" class="in"></a>';
							}
						}
					?>
				</div>
			</nav>
		</header>
	</div>
</div><!-- .bgheader -->

<div class="container bgcontent">
	<header id="logo" class="uk-container uk-visible@s uk-text-center" >
		<img src="<?=$templatePath?>/images/logo.png" class="" draggable="false">
		<!-- <img src="<?=$templatePath?>/images/logo.jp2" class="" draggable="false"> -->
	</header>

	<div class="content">

		<main>
			<article>
				<?php $Page->get_content();?>
			</article>
		</main>

		<div class="sidebar">
			<!-- <div uk-sticky="show-on-up:true;"> -->
				<?php $Page->get_column('right','<aside><div class="aside_content">#content#</div></aside>');?>
			<!-- </div> -->

		</div>
	</div>

	<?php
	// $log->add('$Page = ',null,[$Page]);

	// if($Page->isIndexPage()):
	if(BlogKff::is_indexPage()):
	?>
	<h6 style="text-align: center; background: #eee;" class="uk-h2 uk-padding-small">Всегда найдется тот, кто сделает дешевле!</h6>
	<div>
		<!-- <img src="<?=$templatePath?>/images/noComment.jpg" class="uk-width-1" alt="Всегда найдется тот, кто сделает дешевле!"> -->
		<img uk-img data-src="/files/content_images/bad_examples/<?=random_int(1,10)?>.jpg" class="uk-width-1" alt="Всегда найдется тот, кто сделает дешевле!">
	</div>
	<?php endif; ?>


	<div class="ads">

	</div>



</div><!-- .container.bgcontent -->


<footer>
	<div>
		<a href="/" class="logo"> &copy; <?=$Page->get_header();?> - <?=date('Y');?></a>
	</div>

	<div class="copiright">
	<h5>Связь с разработчиком сайта</h5>
		<p><a target="_blank" href="https://t.me/js_master_bot">@js_master_bot</a></p>
		<p>Сайт сделан на <a href="//my-engine.ru" rel="nofollow">My-Engine CMS</a></p>
	</div>
</footer>


<script>
'use strict';
// *top padding
$(()=>{
	document.querySelector('.container.bgcontent').style.paddingTop= getComputedStyle(
		$('.bgheader')[0]
	).height;
});

var $nav= $('#nav');

// *burger button
$('#menu').on('click', function(){
	$nav.css(
		'height',
		$nav.css('height') === $nav.prop('scrollHeight') + 'px'
		? '0px'
		: $nav.prop('scrollHeight') + 'px'
	);
});


$(document).on('click', function(e){
	// var $t= $(e.target).closest($nav);

	if(parseInt($nav.css('height')) > 100) {
		$nav.css('height',0);
		// console.log($t, $nav.css('height'));
	}
});
</script>

{{coreFooter}}

</body></html>