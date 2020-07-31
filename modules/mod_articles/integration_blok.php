<?php
require('cfg.dat');
$return = '';
$fopen = file('modules/mod_articles/data/list.dat');
$fopen = array_reverse($fopen);
$nom = count($fopen);
if($nom > 0){
if($amtpr_blok > $nom ){$amtpr_blok = $nom;}	
for($i=0;$i<$amtpr_blok;$i++){
$nom_file = trim($fopen[$i]);
require('data/cfg_'.$nom_file.'.dat');
if($show_article == 1){
$return.= '<div class="link"><a href="/'.$direct_article.'/'.$nom_file.'">'.$rubric.'</a></div>';
}	
}
$return.= '<div class="link" style="margin-top:15px;border:0;"><a href="/'.$direct_article.'">Все статьи</a></div>';
}else{
$return.= '<div class="msg">Статьи еще не созданы!</div>';
}
return $return;
?>