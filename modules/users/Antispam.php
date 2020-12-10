<?php
/**
 * /modules/users/Antispam.php
 */

$fname= __DIR__ . '/integration_page.php';

function fixForm()
{
	global $fname;
	copy($fname, $fname.'.bak');

	$script= file_get_contents($fname);

	$script= preg_replace([
		'~<form name="forma" action="/\'\.\$URI\[1\]\.\'/addreg"[^>]+~ui',
		'~(\}else\{\s+?\$return = \'<p>Регистрация на сайте временно приостановлена</p>\';\s+?\})~ui',
	],[
		'<form name="forma" action="/pages/401.html" method="get"',
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
			this.method= \'post\';

			console.log(this.action);
			this.submit();
		}
	})(document);
	</script>') . "';\n$1",
	], $script, 1);

	file_put_contents($fname, $script);
}



if(isset($_GET['download'])){

header('Content-Disposition: attachment; filename=' . basename(__FILE__));
echo file_get_contents(__FILE__);
die;
}
// *Restore
elseif(isset($_GET['restore'])){
	echo '<script>location.replace(confirm("Вы уверены?")? "?restore_confirmed": "/user/reg")</script>';
}
elseif(isset($_GET['restore_confirmed'])){
	rename($fname.'.bak', $fname);
	header('Location: /user/reg');
}
// *was fixed
elseif(file_exists($fname.'.bak')){
	die("<h2>" . realpath($fname) . " was fixed!</h2>
	<p><a href='?restore'>Восстановить</a> системные файлы (удаление антиспама)?</p>");
}
// *init Antispam
else{
	fixForm();

	header('Location: /user/reg');
}