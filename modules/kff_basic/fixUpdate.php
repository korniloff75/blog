<?php
/**
 * *Запускать после установки модуля kff_basic, а также после обновления движка с этим модулем
 * http://site.ru/fixUpdate.php
 * OR
 * http://site.ru/modules/kff_basic/fixUpdate.php
 * *Вносит изменения в основной движок, создавая резервные копии последней версии системных файлов.
 */

if(isset($_GET['download'])){
	header('Content-Disposition: attachment; filename=' . basename(__FILE__));
	die(file_get_contents(__FILE__));
}

define('DR', $_SERVER['DOCUMENT_ROOT']);
define('HOST', htmlspecialchars($_SERVER['HTTP_HOST']));
define('REQUEST_URI', htmlspecialchars($_SERVER['REQUEST_URI']));

// Автозагрузка системных классов
function autoloader($class) {
	@include_once DR.'/system/classes/' . $class . '.dat';
}
spl_autoload_register('autoloader');

// Загрузка PHP функций
require './system/function.dat';

// Получение сохраненых настроек системы
$Config = System::getConfig();

// Авторизация администратора
if(isset($_COOKIE['password'])){
	$password = $_COOKIE['password'];

	if(cipherPass($password, $Config->salt) == $Config->adminPassword){
		$status = 'admin';
		setcookie('password', $password, time() + $Config->timeAuth, '/');
	}else{
		$status = 'gost';
	}
}else{
	$status = 'gost';
}


$kff_path= DR.'/modules/kff_basic';
require_once "$kff_path/integration_system.php";

$log->add('AdmPanel::FIXED',null,['AdmPanel::FIXED'=>AdmPanel::FIXED]);



// *Проверяем маркер
if(isset($_GET['test'])){
	AdmPanel::fixSystem();
	echo '<pre><h2>Test mode</h2>';
	htmlspecialchars(var_dump(AdmPanel::$fixes));
	echo '</pre>';
}
elseif(($handle = @fopen("./index.php", "r")) && ($fstr = fgets($handle))){
	// *Файл не обработан
	if(strpos($fstr, AdmPanel::FIXED) === false){
		fclose($handle);

		// *Удаляем *.bak
		foreach($kff::$cfgDB->fixSystem as $i){
			if(file_exists($bak= DR.$i.'.bak')) unlink($bak);
		}

		AdmPanel::fixSystem();

		// *Antispam
		$usersPath= DR.'/modules/users';
		unlink($usersPath.'/integration_page.php.bak');
		// *Add redirect
		require_once $usersPath.'/Antispam.php';

		die('<h2>Файлы успешно обработаны!</h2><p>Для перехода в сайт -- перезагрузите эту страницу.</p>');
	}
	else{
		header('Location: /');
	}
}
// @fclose($handle);