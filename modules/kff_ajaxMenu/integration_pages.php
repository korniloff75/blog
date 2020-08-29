<?php

// $log->add('$Page',null,[$Page]);

ob_start();
?>

<div id="ajax-menu">

	<div id="response">

	</div>

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
		$('.active').removeClass();
		t.classList.add('active');

		$.post(t.href)
		.done((response)=>{
			// console.log('response2=',response);
			var $main = $(response).find('main');
			$main.find('#ajax-menu').remove();
			// console.log('response3=',response);
			$('main').html(
				$main.html()
			);
			console.log('main=',$('main').find('#ajax-menu'));
		})
		.always(()=>{
			$loader.hide();
		});
		return false;
	});

})
</script>

</div>
<?php
return ob_get_clean();