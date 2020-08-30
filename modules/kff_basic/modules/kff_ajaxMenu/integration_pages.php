<?php

// $log->add('$Page',null,[$Page]);

ob_start();
?>
<style>
	#loading {display:none;}
</style>

<div id="ajax-menu">

	<div id="loading" uk-spinner class="uk-position-top-center uk-position-medium uk-position-fixed" style="z-index:100; "></div>


<script>
'use strict';
// window.addEventListener('load', function() {
kff.checkLib('jQuery')
.then($=> {
	var $nav = $('#nav'),
		$loader = $('#loading');

	$loader.hide();

	$nav.on('click', $e=>{
		var t= $e.target.closest('a');

		if(!t.href) return;

		$e.preventDefault();
		$e.stopPropagation();

		console.log(t);
		$loader.show();
		setActive(t.href);

		$.post(t.href)
		.done((response)=>{
			var $main = $(response).find('main');
			$main.find('#ajax-menu').remove();

			render(
				$main.html()
			);

			history.pushState({mainHtml: $main.html(), href: t.href}, '', t.href);
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
	function render (mainHtml) {
		$('main').html(
			mainHtml
		);
	}

	// *Active btn
	function setActive (href) {
		$('.active').removeClass();
		$nav.find('a').filter((ind,i)=> i.href === href).addClass('active');
	}

})
</script>

</div>
<?php
return ob_get_clean();