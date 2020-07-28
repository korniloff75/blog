<?php
if (!isset($Config)) global $Config;
$turboStorage = new EngineStorage('module.turbo');
if($turboStorage->iss('turboConfig')){
	$turboConfig = json_decode($turboStorage->get('turboConfig'));
}
if(!isset($turboConfig)){
	$turboConfig = new stdClass();
}

// Настройки поумолчанию
if(!isset($turboConfig->turbo)) $turboConfig->turbo = 0;
if(!isset($turboConfig->turboItems)) $turboConfig->turboItems = 1000;
if(!isset($turboConfig->turboId)) $turboConfig->turboId = 'turbo';
if(!isset($turboConfig->turboCacheTime)) $turboConfig->turboCacheTime = 3600;
if(!isset($turboConfig->exceptions)) $turboConfig->exceptions = 'user, news, search, chat, fz152';
?>