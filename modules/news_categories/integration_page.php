<?php  
require('cfg.dat');

if($news_style == 0){
$Page->headhtml.= '<link rel="stylesheet" href="/modules/news_categories/main.css" media="screen">';	
}

if(isset($URI[2])){$nom_page = $URI[2];}else{ $nom_page = 1; } 

$return = '<div class="prev-box">';

$arr_file = file('modules/news_categories/list.dat');
$nom = count($arr_file);
$kol_page = ceil($nom / $amtpr_page);

if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $kol_page){ $nom_page = 1; }
if($nom_page > 0){$i = ($nom_page - 1) * $amtpr_page;}
$var = $i + $amtpr_page;
if($nom > 0){		
while($i < $var){
if($i < $nom){	
$link_cfg = explode('^',$arr_file[$i]);
require('./modules/'.$link_cfg[1].'/cfg.php');

 if(is_dir('./data/storage/'.$module_news)){
	$img_category = $newsConfig->imgCategory;
 }else{
	$img_category = '/modules/news_categories/default.jpg'; 
 }
 
$return .= '<div class="prev-category">
<h2><a href="/'.$link_cfg[2].'">'.$link_cfg[0].'</a></h2>
<div class="prev-category-img"><a href="/'.$link_cfg[2].'"><span style="background:url('.$img_category.') no-repeat;"></span></a></div>
</div>';	

}
++$i;
}
}else{
$return .= '<div class="msg">Категории еще не созданы!</div>';
}
$return .= '</div>';

if($nom > $amtpr_page){  
$return.= '<div id="pagination">
<ul class="pagination">';
if($kol_page > 15){
$a = $nom_page - 3;
$b = $nom_page + 3;
}else{
$a = $nom_page - 15;
$b = $nom_page + 15;	
}
$c = $nom_page - 1;
$d = $nom_page + 1;
$x = ceil($nom / $amtpr_page);
$y = $x + 1;
$z = $nom_page;
if($c > 1){
$pagination = '/'.$id_categorys.'/'.$c.'';
}else{
$pagination = '/'.$id_categorys.'';	
}
			
if($z < 2){
$return.= '<li class="disabled"><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}else{
$return.= '<li><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}
if($kol_page > 15){
if($nom_page > 4){$return.= '<li><a href="/'.$id_categorys.'">1</a></li>';}
if($nom_page > 5){$return.= '<li class="punkt">...</li>';}
}
while($a <= $b){
if(($a > 0) && ($a <= $kol_page)){
if($nom_page == $a){
if($a == 1){
$return.= '<li class="active"><a href="/'.$id_categorys.'">'.$a.'</a></li>';
}else{
$return.= '<li class="active"><a href="/'.$id_categorys.'/'.$a.'">'.$a.'</a></li>';	
}
}else{
if($a == 1){
$return.= '<li><a href="/'.$id_categorys.'">'.$a.'</a></li>';
}else{
$return.= '<li><a href="/'.$id_categorys.'/'.$a.'">'.$a.'</a></li>';	
}
}
}
++$a;
}
if($kol_page > 15){
if($nom_page < ($kol_page - 4)){$return.= '<li class="punkt">...</li>';}
if($nom_page < ($kol_page - 3)){$return.= '<li><a href="/'.$id_categorys.'/'.$kol_page.'">'.$kol_page.'</a></li>';}
}	
if($d > $kol_page){
$return.= '<li class="disabled"><a href="/'.$id_categorys.'/'.$nom_page.'" aria-label="Next">&raquo;</a></li>';
}elseif($y > $d){
$return.= '<li><a href="/'.$id_categorys.'/'.$d.'" aria-label="Next">&raquo;</a></li>';
}			
$return.= '</ul>
</div>
';
}

return $return;
?>