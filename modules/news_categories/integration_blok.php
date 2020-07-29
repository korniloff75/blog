<?php
require('cfg.dat');

$return = '';
$link_data = file('modules/news_categories/list.dat');
$nom = count($link_data);
if($nom < $nom_blok){$nom_blok = $nom;}
if($nom > 0){
for($q = 0; $q < $nom_blok; ++$q){
$link_cfg = explode('^',$link_data[$q]);

$newsStorage = new EngineStorage('module.'.$link_cfg[1].'');
if(($listIdNews = json_decode($newsStorage->get('list'), true)) != false){
$col = count($listIdNews);
}
$return .= '<div class="link"><a href="/'.$link_cfg[2].'">'.$link_cfg[0].' ('.$col.')</a></div>';
}
$return.= '<div class="link" style="margin-top:15px;"><a href="/'.$id_categorys.'">'.$txt_link.'</a></div>';
}else{
$return .= '<div class="msg">Категорий нет!</div>';
}

return $return;
?>