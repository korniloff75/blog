<?php
require('modules/news_categories/cfg.dat');

if($news_style == 0){$page->headhtml.= '<style>'.file_get_contents('modules/news_categories/main.min.css').'</style>';} 
   
return null;
?>