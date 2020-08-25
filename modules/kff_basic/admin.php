<?php

require_once 'cpDir.class.php';

link(__DIR__.'/cpDir.class.php', __DIR__.'/kff_custom/cpDir.class.php');

$dir = new linkDir(__DIR__.'/kff_custom', DR.'/kff_custom');

require_once DR.'/kff_custom/index_my_addon.php';

// $modDir = $kff::getPathFromRoot(__DIR__);

echo "<pre><h3>Изменения успешно внесены.</h3>";
ob_start();
print_r($dir->get_log());
ob_end_flush();
echo "</pre>";