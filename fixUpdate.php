<?php
/**
 * *Запускать после установки модуля kff_basic, а также после обновления движка с этим модулем
 * http://site.ru/fixUpdate.php
 * *Вносит изменения в основной движок, создавая резервные копии
 */
define('HOST', htmlspecialchars($_SERVER['HTTP_HOST']));
define('REQUEST_URI', htmlspecialchars($_SERVER['REQUEST_URI']));

$kff_path= __DIR__.'/modules/kff_basic';

require_once "$kff_path/integration_system.php";

AdmPanel::fixSystem();