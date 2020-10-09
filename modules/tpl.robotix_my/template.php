<!DOCTYPE html>
<html lang="ru">

<head>
{{coreHead}}

<style>
<?= file_get_contents($kff::getPathFromRoot(__DIR__).'/style.min.css');?>
<?= file_get_contents('admin/include/windows/windows.css');?>
* {
	box-sizing: border-box;
}
.sidebar, .aside_content{
	background: #eee;
}
</style>

<script src="/admin/include/windows/windows.js"></script>

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
								echo'<a href="/user" class="in">Вход на сайт</a>';
							}
						}
					?>
				</div>
			</nav>
		</header>
	</div>
</div><!-- .bgheader -->

<div class="container bgcontent">

	<div class="content">

		<main>
			<article>
				<!-- <h1 class="name"><?php $Page->get_name();?></h1> -->
				<!-- <h1 class="name">{{Title}}</h1> -->
				<?php $Page->get_content();?>
			</article>
		</main>

		<div class="sidebar">
			<!-- <div uk-sticky="show-on-up:true;"> -->
				<?php $Page->get_column('right','<aside><div class="aside_content">#content#</div></aside>');?>
			<!-- </div> -->

		</div>

	</div>



	<div class="ads">

	</div>

	<?php
	// $log->add('$Page = ',null,[$Page]);

	if(in_array($Page->module, ['feedback','kff_feedback'])):
	?>

	<?php endif; ?>

</div><!-- .container.bgcontent -->


<footer>
	<div>
		<a href="/" class="logo"><?=$Page->get_header();?> - <?=date('Y');?></a>
	</div>
	<div class="copiright">
		Сайт сделан на <a href="//my-engine.ru" rel="nofollow">My-Engine CMS</a>
	</div>
</footer>


<script>
'use strict';
// *top padding
$(()=>{
	/* document.querySelector('.container.bgcontent').style.paddingTop= getComputedStyle(
		$('.bgheader')[0]
	).height; */

	// *AJAX nav
	// uk-sticky
	var targetSel = '.blog_content',
		$sidebar = $('.aside_content>ul.categories');
	// var blogNav = new kff.menu($('.categories'), targetSel);
		// console.log(bm);

	if($sidebar){
		<?php
		if($Page->module === 'kff_blog') {
			echo 'var blogNav = new kff.menu($sidebar, targetSel)';
		}
		?>

		// var stiky= $sidebar.attr('uk-sticky') + 'offset:' + $sidebar.prop('offsetTop') + ';';
		var stiky= $sidebar.attr('uk-sticky') + 'offset:' + parseInt(getComputedStyle($('.bgheader')[0]).height) + ';';
		console.log('stiky=',stiky);
		// !
		$sidebar.attr('uk-sticky', stiky);
	}

	/* // *AJAX history
	window.onpopstate = function(e) {
		if(!e.state || !e.state[targetSel]) return false;

		// console.log('e=',e);
		kff.render([targetSel], e.state[targetSel].html);

		bm.setActive(e.state[targetSel].href);
	} */
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