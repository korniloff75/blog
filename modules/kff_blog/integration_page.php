<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

ob_start();

$Blog = new BlogKff;

$Blog->Render();

?>



<?php
return ob_get_clean();