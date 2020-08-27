<?php
$modDir = $kff::getPathFromRoot(__DIR__);

$Page->headhtml.= '

<!-- Load UIKit -->
<link rel="stylesheet" href="/'.$modDir.'/css/uikit.min.css" />
<script src="/'.$modDir.'/js/uikit.min.js"></script>

';


return null;
?>