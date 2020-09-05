<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<?php $Page->get_headhtml();?>
<title><?php $Page->get_title();?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="description" content="<?php $Page->get_description();?>">
<meta name="keywords" content="<?php $Page->get_keywords();?>">
<!-- <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&family=Roboto+Condensed&display=swap" rel="stylesheet"> -->
<style>
<?= file_get_contents($kff::getPathFromRoot(__DIR__).'/style.min.css');?>
<?= file_get_contents('admin/include/windows/windows.css');?>
* {
	box-sizing: border-box;
}
</style>
<script>
<?php #echo file_get_contents(DR.'/admin/include/windows/windows.js');?>
// kff.checkLib('jQuery');
</script>

<script src="/admin/include/windows/windows.js"></script>

</head>


<body>
<div class="bgheader">
	<div class="container">
		<header>
			<!-- <div class="logo">
				<a href="/"><img src="/<?=$kff::getPathFromRoot(__DIR__)?>/images/ЛОГО_min.jpg" alt="<?php $Page->get_header();?>"></a>
			</div> -->

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
</div>

<div class="container bgcontent">

	<div class="content">
		<section id="cats">
		<?php
		if(function_exists('Get_PagesCategory')) echo Get_PagesCategory();
		?>
		</section>


		<main>
			<article>
				<?php if(!$Page->isIndexPage()):?><h1 class="name"><?php $Page->get_name();?></h1><?php endif;?>
				<?php $Page->get_content();?>
			</article>
		</main>

		<div class="sidebar">
			<?php $Page->get_column('right','<aside><div class="aside_content">#content#</div></aside>');?>
		</div>

	</div>



	<div class="ads">

	</div>

	<?php
	// $log->add('$Page = ',null,[$Page]);

	if(in_array($Page->module, ['feedback','kff_feedback'])):
	?>

	<div class="contact">
		<!-- <div class="map">
			<script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Ac0f6d89d5c475219392d254f4b1b2e8ed0a6f81ec72355edc8454bf43615a01a&amp;width=100%25&amp;height=400&amp;lang=ru_RU&amp;scroll=true"></script>
		</div> -->

		<div id="my_map" class="map" style="min-height:300px;">
			<!-- Карта -->
			<!-- <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script> -->
			<script>
			'use strict';

			kff.checkLib('ymaps', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU')
			.then(ymaps=>{ ymaps.ready(()=>{
				console.log('ymaps.Map = ', ymaps.Map, ymaps.ready, ymaps);

				var myMap = new ymaps.Map('my_map', {
					center: [ 45.47574, 34.21895 ],
					zoom: 8,
					controls: [],
				}, {
					// Optional
					// Задаем поиск по карте
					searchControlProvider: 'yandex#search'
				});
			})});

			</script>

		</div>

		<div class="addres" id="addres"><?='
			<h3>Регионы деятельности</h3>
			<p>Республика Крым.</p>
			<h3>написать через TELEGRAM</h3>
			<p>Даже если ваша учётная запись заблокирована за СПАМ, вы сможете написать мне через этого бота - <a target="_blank" href="https://t.me/js_master_bot">@js_master_bot</a></p>
		';?>
		</div>
	</div>


	<?php endif; ?>


	<footer>
		<a href="/" class="logo"><?=$Page->get_header();?> - <?=date('Y');?></a>
	</footer>

</div>
<div class="copiright">Сайт сделан на <a href="//my-engine.ru" rel="nofollow">My-Engine CMS</a></div>


<script>
'use strict';
// *top padding
UIkit.util.ready(()=>document.querySelector('.container.bgcontent').style.paddingTop= getComputedStyle(
	$('.bgheader')[0]
).height);

// *burger button
$('#menu').on('click', function(){
	var menu = document.getElementById('nav');
	menu.style.height = menu.style.height === menu.scrollHeight + 'px'
		? '0px'
		: menu.scrollHeight + 'px';
});
</script>

<?php
require_once $kff::$dir . '/contentEditable/init.php';

if(function_exists('CustomizeInit')) CustomizeInit();

$Page->get_endhtml();
?>

</body></html>