<?php
require(DR.'/modules/news/cfg.php');
$listIdCat = json_decode($newsStorage->get('category'), true);
$listIdAll = json_decode($newsStorage->get('list'), true);
$return = '';
foreach($newsConfig->cat as $key => $value){
	$return.= '<div class="link link_category"><a href="/'.$newsConfig->idPage.'/category/'.$key.'">'.$value.' <span>('.(count(array_keys($listIdCat, $key))).')</span></a></div>';
}
if($newsConfig->indexCat == 0){
	$return.= '<div class="link link_category link_category_all"><a href="/'.($newsConfig->idPage != $Config->indexPage?$newsConfig->idPage:'').'">Все записи <span>('.count($listIdAll).')</span></a></div>';
}
return $return;
?>