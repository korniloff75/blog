<?php

// $log->add('$Page=',null,[$Page]);

DbJSON::$convertPath = false;
$DB = new DbJSON(__DIR__.'/cfg.json');

$cfg = $DB->get();

ob_start();
?>
<style>
	#loading {display:none;}
</style>

<div id="ajax-menu">

	<div id="loading" uk-spinner class="uk-position-top-center uk-position-medium uk-position-fixed" style="z-index:100; "></div>


<script data-file="<?=basename(__DIR__)?>">
'use strict';
// window.addEventListener('load', function() {
kff.checkLib('jQuery')
.then($=> {
	var
		navSelector = '<?=$cfg['nav_selector']?>' || '#nav',
		mainSelector = '<?=$cfg['main_selector']?>' || 'main',
		$nav = $(navSelector),
		$loader = $('#loading');

	$loader.hide();

	$nav.on('click', $e=>{
		var t= $e.target.closest('a');

		if(!t.href) return;

		$e.preventDefault();
		$e.stopPropagation();

		// console.log(t);
		$loader.show();
		setActive(t.href);

		$.post(t.href)
		.done((response)=>{
			var $main = $(response).find(mainSelector),
				$sysInfo = $(response).find('.core.info'),
				$log = $(response).find('.log');
			$main.find('#ajax-menu').remove();

			console.log(mainSelector);

			render(
				$main.html(), $sysInfo.html(), $log.html()
			);

			history.pushState({
				mainHtml: $main.html(),
				href: t.href
			}, '', t.href);
			// console.log('main=',$('main').find('#ajax-menu'));
		})
		.always(()=>{
			$loader.hide();
		});
		return false;
	});

	window.onpopstate = function(e) {
		render(e.state.mainHtml);
		setActive(e.state.href);
	}

	// *Change content
	function render (mainHtml,sysInfo, log) {
		// console.log(mainHtml);
		$(mainSelector).html(
			mainHtml
		);
		<?php if($kff::is_adm()): ?>
		$('.core.info').html(
			sysInfo
		);
		<?php endif ?>
		$('.log').html(
			log
		);

		kff.highlight('.log');
	}

	// *Active btn
	function setActive (href) {
		$('.active').removeClass();
		$nav.find('a').filter((ind,i)=> i.href === href).addClass('active');
		// console.log($nav.css('height'));
		// *Hide nav
		if(parseInt($nav.css('height')) > 100) {
			$nav.css('height',0);
		}
	}

})
</script>

</div><!-- #ajax-menu -->
<?php
return ob_get_clean();