<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<?php $page->get_headhtml();?>
<title><?php $page->get_title();?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="description" content="<?php $page->get_description();?>">
<meta name="keywords" content="<?php $page->get_keywords();?>">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&family=Roboto+Condensed&display=swap" rel="stylesheet">
<style>
<?php echo file_get_contents('modules/tpl.robotix/style.min.css');?>
<?php echo file_get_contents('modules/tpl.robotix/windows/windows.css');?>
</style>
<script>
<?php echo file_get_contents('modules/tpl.robotix/windows/windows.js');?>
<?php echo file_get_contents('modules/tpl.robotix/jloader/jloader.js');?>
</script>

<!--Slick-->
<link href="/modules/tpl.robotix/slick/slick.css" rel="stylesheet">
<link href="/modules/tpl.robotix/slick/slick-theme.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="/modules/tpl.robotix/slick/slick.min.js"></script>


</head>
<body>
<div class="bgheader">
	<div class="container">
		<header>
			<div class="logo">
				<a href="/"><img src="/modules/tpl.robotix/images/logo.png" alt="<?php $page->get_header();?>"></a>
			</div>

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
				<?php $page->get_menu('span');?>
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
	
	<?php if($Page->isIndexPage()):?>
		<div class="bgslider">
			<div class="slider">
				<section>
					<div class="flex">
						<div class="i1">
							<h1 class="editable" id="s1r1"><?=$Customize->iss('s1r1')?$Customize->get('s1r1'):'Современные технологии для работы и отдыха';?></h1>
							<p class="editable" id="s1r2"><?=$Customize->iss('s1r2')?$Customize->get('s1r2'):'Мы обеспечим вам продуктивную работу в комфортных условиях';?></p>
							<p class="btns editable" id="s1r3"><?=$Customize->iss('s1r3')?$Customize->get('s1r3'):'<a href="/" target="_blank">Узнать подробнее</a>';?></p>
						</div>
						<div class="i2">
							<p class="editable" id="s1r4"><?=$Customize->iss('s1r4')?$Customize->get('s1r4'):'<img src="/modules/tpl.robotix/images/slider1.jpg" alt="">';?></p>
						</div>
					</div>
				</section>
				<section>
					<div class="flex">
						<div class="i1">
							<h1 class="editable" id="s2r1"><?=$Customize->iss('s2r1')?$Customize->get('s2r1'):'Продажа и сопровождение оборудования в офисы';?></h1>
							<p class="editable" id="s2r2"><?=$Customize->iss('s2r2')?$Customize->get('s2r2'):'Авторизованный сервисный центр, работа напрямую с производителями';?></p>
							<p class="btns editable" id="s2r3"><?=$Customize->iss('s2r3')?$Customize->get('s2r3'):'<a href="/" target="_blank">Узнать подробнее</a>';?></p>
						</div>
						<div class="i2">
							<p class="editable" id="s2r4"><?=$Customize->iss('s2r4')?$Customize->get('s2r4'):'<img src="/modules/tpl.robotix/images/slider2.jpg" alt="">';?></p>
						</div>
					</div>
				</section>
				<section>
					<div class="flex">
						<div class="i1">
							<h1 class="editable" id="s3r1"><?=$Customize->iss('s3r1')?$Customize->get('s3r1'):'Сплоченная команда - секрет успеха нашей компании';?></h1>
							<p class="editable" id="s3r2"><?=$Customize->iss('s3r2')?$Customize->get('s3r2'):'У нас работают только опытные сотрудники прошедшие строжайший отбор';?></p>
							<p class="btns editable" id="s3r3"><?=$Customize->iss('s3r3')?$Customize->get('s3r3'):'<a href="/" target="_blank">Узнать подробнее</a>';?></p>
						</div>
						<div class="i2">
							<p class="editable"  id="s3r4"><?=$Customize->iss('s3r4')?$Customize->get('s3r4'):'<img src="/modules/tpl.robotix/images/slider3.jpg" alt="">';?></p>
						</div>
					</div>
				</section>
			</div>
			<div class="slick-dots"></div>
		</div>
		
	
		<div class="grid">
			<section  class="editable" id="c1"><?=$Customize->iss('c1')?$Customize->get('c1'):'
				<h2>Офисы</h2>
				<p><img src="/modules/tpl.robotix/images/card1.jpg" alt=""></p>
				<p>Оснастим офисы самым современным оборудованием</p>
				<p><a href="/">Узнать подробней &#8594;</a></p>
			';?>
			</section>
			<section class="editable" id="c2"><?=$Customize->iss('c2')?$Customize->get('c2'):'
				<h2>Кабинеты</h2>
				<p><img src="/modules/tpl.robotix/images/card2.jpg" alt=""></p>
				<p>Организуем рабочее пространство кабинетов</p>
				<p><a href="/">Узнать подробней &#8594;</a></p>
			';?>
			</section>
			<section class="editable" id="c3"><?=$Customize->iss('c3')?$Customize->get('c3'):'
				<h2>Отдых</h2>
				<p><img src="/modules/tpl.robotix/images/card3.jpg" alt=""></p>
				<p>Оборудуем комнаты отдыха для сотрудников компании</p>
				<p><a href="/">Узнать подробней &#8594;</a></p>
			';?>
			</section>
			<section class="editable" id="c4"><?=$Customize->iss('c4')?$Customize->get('c4'):'
				<h2>Помощники</h2>
				<p><img src="/modules/tpl.robotix/images/card4.jpg" alt=""></p>
				<p>Оснастим роботами помощниками для посетителей</p>
				<p><a href="/">Узнать подробней &#8594;</a></p>
			';?>
			</section>
		</div>
	<?php endif;?>


	<div class="content">
		<main>
			<article>
				<?php if(!$Page->isIndexPage()):?><h1 class="name"><?php $page->get_name();?></h1><?php endif;?>
				<?php $page->get_content();?>
			</article>
		</main>
		
		<div class="sidebar">
			<?php $page->get_column('right','<aside><div class="aside_content">#content#</div></aside>');?>
		</div>
		
	</div>



	<div class="ads">
		<div class="anews">
			<?php // Вывод последней новости
				echo NewsCategory(false, 1, '
											<article><h2><a href="#uri#">#header#</a></h2>
											<p><img src="#img#" alt="#header#"></p>
											#content#
											<p><a href="#uri#">Читать подробнее &#8594;</a></p></article>
										');
			?>
		</div>
		<div class="bnews">
			<h3 class="bhnews editable" id="nh"><?=$Customize->iss('nh')?$Customize->get('nh'):'Другие новости';?></h3>
			<?php // Вывод последних 5 новостей, кроме самой последней
				echo NewsCategory(false, 5, '
											<article>
											<h3><a href="#uri#">#header#</a></h3>
											#content#
											</article>
										', '<p>Новостей пока нет</p>', 1);
			?>
		</div>
	</div>

	<div class="contact">
		<div class="map">
			<script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Ac0f6d89d5c475219392d254f4b1b2e8ed0a6f81ec72355edc8454bf43615a01a&amp;width=100%25&amp;height=400&amp;lang=ru_RU&amp;scroll=true"></script>
		</div>
		<div class="addres editable" id="addres"><?=$Customize->iss('addres')?$Customize->get('addres'):'
			<h3>Регионы деятельности</h3>
			<p>Московская область, Нижегородская область, Костромская область, республика Марий Эл, республика Чувашия, республика Мордовия, Владимирская область.</p>
			<h3>Адрес офиса в Москве</h3>	
			<p>108841, Московская обл. г. Москва, пл. Ленина, д. 1а</p>
			<h3>Контактные телефоны</h3>
			<ul>
				<li>+7 (123) 456-78-90</li>
				<li>+7 (098) 765-43-21</li>
				<li>+7 (345) 678-90-12</li>
			</ul>
		';?>
		</div>
	</div>


	<footer>
		<a href="/" class="logo"><?=$Page->get_header();?> - <?=date('Y');?></a>
	</footer>

</div>
<div class="copiright">Сайт сделан на <a href="//my-engine.ru">My-Engine CMS</a></div>


<!-- Page script -->
<script>
$('.slider').slick({
	autoplay: true,
	arrows: false,
	dots: true
});

document.getElementById('menu').onclick = function(){
	var menu = document.getElementById('nav');
	if(menu.style.height == menu.scrollHeight + 'px'){
		menu.style.height = '0px';
	}else{
		menu.style.height = menu.scrollHeight + 'px';
	}
}
</script>

<?php if(function_exists('CustomizeInit')) CustomizeInit(); ?>

<?php $page->get_endhtml();?>
</body></html>