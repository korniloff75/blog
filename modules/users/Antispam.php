<?php
/**
 * /modules/users/Antispam.php
 */

$fname= './integration_page.php';

function fixForm()
{
	copy($fname, $fname.'.bak');

	$script= file_get_contents($fname);

	$script= preg_replace([
		'~<form name="forma" action="/\'\.\$URI\[1\]\.\'/addreg"[^>]+~ui',
		'~(\}else\{\s+?\$return = \'<p>Регистрация на сайте временно приостановлена</p>\';\s+?\})~ui',
	],[
		'<form name="forma" action="/pages/401.html" method="post"',
		'$return .= \'' . addslashes('<script>
		(function(d) {
		var form= d.querySelector(\'form[name="forma"]\');
		if(!form) return;
		form.onsubmit= function(e) {
			e.preventDefault();

			if(!d.getElementById(\'roscomnadzor\').checked){
				alert(\'Нужно дать согласие на обработку персональных данных\');
				return;
			}

			this.action= \'/user/addreg\';

			console.log(this.action);
			this.submit();
		}
	})(document);
	</script>') . "';\n$1",
	], $script, 1);

	file_put_contents($fname, $script);
}


// *init Antispam
if(isset($_GET['download'])){

header('Content-Disposition: attachment; filename=' . basename(__FILE__));
echo file_get_contents(__FILE__);
die;
}
elseif(file_exists($fname.'.bak')){
	die("<h2>" . realpath($fname) . " was fixed!</h2>");
}
else{
	fixForm();

	header('Location: /user/reg');
}