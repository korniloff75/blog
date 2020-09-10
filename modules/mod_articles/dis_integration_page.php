<?php
if (!class_exists('System')) exit; // Запрет прямого доступа


require('cfg.dat');

if($mod_style == 0){
$page->headhtml.= '<link href="/modules/mod_articles/style.min.css" rel="stylesheet">';
}

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {$protocol = 'https://';}else{$protocol = 'http://';}

if((isset($URI[2]) && ($URI[2] == '0' || $URI[2] == '1')) || (isset($URI[3]) && trim($URI[3]))){	
header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();	
}

if($MODULE_URI == '/rss_articles.xml'){
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:georss="http://www.georss.org/georss">
    <channel>
		<title>'.$name_rss.'</title>
		<atom:link href="'.$protocol.''.SERVER.'/'.$URI[1].'/rss.xml" rel="self" type="application/rss+xml" />
        <link>'.$protocol.''.SERVER.'/'.$URI[1].'</link>
        <description>'.$Page->description.'</description>
		<language>ru</language>';
		
		$arr = file('modules/mod_articles/data/list.dat');
		$arr = array_reverse($arr);
        $nom = count($arr);
        for($q = 0; $q < $nom; ++$q){
        $nom_file = trim($arr[$q]);
		require('modules/mod_articles/data/cfg_'.$nom_file.'.dat');
		$param_prev = file_get_contents('modules/mod_articles/data/prev_'.$nom_file.'.dat');
		$param_content = file_get_contents('modules/mod_articles/data/content_'.$nom_file.'.dat');
		if($q < 8){
		echo'
		<item>
			<title>'.$rubric.'</title>
			<link>'.$protocol.''.SERVER.'/'.$URI[1].'/'.$nom_file.'</link>
			<guid>'.$protocol.''.SERVER.'/'.$URI[1].'/'.$nom_file.'</guid>
			<media:rating scheme="urn:simple">nonadult</media:rating>
			<pubDate>'.$data_for_rss.' '.$time_for_rss.'</pubDate>
			<author>Administrator</author>
			<enclosure url="'.$protocol.''.SERVER.$link_img.'" type="image/jpeg" length="'.filesize(DR.'/'.$link_img).'"/>
			<description>
				<![CDATA['.trim(strip_tags($param_prev)).']]>
			</description>
			<content:encoded>
				<![CDATA['.trim(strip_tags($param_content)).']]>
			</content:encoded>
		</item>';
		}		
		}
		
echo'
	</channel>
</rss>';
ob_end_flush(); exit;
}	  
	  
if($MODULE_URI == '/' || ($MODULE_URI == '/'.$URI[2] && $URI[2] > 1)){

if(isset($URI[2])){$nom_page = $URI[2];}else{ $nom_page = 1; }
$arr_file = file('modules/mod_articles/data/list.dat');
$arr_file = array_reverse($arr_file);
$nom = count($arr_file);
$kol_page = ceil($nom / $amtpr_page);

if(isset($URI[2]) && $URI[2] > $kol_page){
header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
}
if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $kol_page){ $nom_page = 1; }
if($nom_page > 0){$i = ($nom_page - 1) * $amtpr_page;}
$var = $i + $amtpr_page;
$return = '';
if($nom > 0){		
while($i < $var){
if($i < $nom){
$nom_file = trim($arr_file[$i]);
require('data/cfg_'.$nom_file.'.dat');
if($show_article == 1){
$return.= '
<div class="prev-article">
<h2>'.$rubric.'</h2>';
if($show_img == 1){
$return.= '<div class="prev-img">
<img src="'.$link_img.'" alt="">
</div>';
}
$return.= '<div class="prev-txt">
'.file_get_contents('modules/mod_articles/data/prev_'.$nom_file.'.dat').'
</div>';
if($show_data == 1){
$return.= '<div class="data-article">'.$data_article.'</div>';
}
$return.= '<div class="btn-rdm"><a href="/'.$direct_article.'/'.$nom_file.'">'.$link_next.'</a></div>
</div>';
}
}
++$i;
}
}else{
$return.= '<div class="msg">Статьи еще не созданы!</div>';
}

if($nom > $amtpr_page){  
$return.= '<div id="pagination">
<ul class="pagination">';
if($kol_page > 10){
$a = $nom_page - 3;
$b = $nom_page + 3;
}else{
$a = $nom_page - 10;
$b = $nom_page + 10;	
}
$c = $nom_page - 1;
$d = $nom_page + 1;
$x = ceil($nom / $amtpr_page);
$y = $x + 1;
$z = $nom_page;
if($c > 1){
$pagination = '/'.$URI[1].'/'.$c.'';
}else{
$pagination = '/'.$URI[1].'';	
}
			
if($z < 2){
$return.= '<li class="disabled"><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}else{
$return.= '<li><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}
if($kol_page > 10){
if($nom_page > 4){$return.= '<li><a href="/'.$URI[1].'">1</a></li>';}
if($nom_page > 5){$return.= '<li class="punkt">...</li>';}
}
while($a <= $b){
if(($a > 0) && ($a <= $kol_page)){
if($nom_page == $a){
if($a == 1){
$return.= '<li class="active"><a href="/'.$URI[1].'">'.$a.'</a></li>';
}else{
$return.= '<li class="active"><a href="/'.$URI[1].'/'.$a.'">'.$a.'</a></li>';	
}
}else{
if($a == 1){
$return.= '<li><a href="/'.$URI[1].'">'.$a.'</a></li>';
}else{
$return.= '<li><a href="/'.$URI[1].'/'.$a.'">'.$a.'</a></li>';	
}
}
}
++$a;
}
if($kol_page > 10){
if($nom_page < ($kol_page - 4)){$return.= '<li class="punkt">...</li>';}
if($nom_page < ($kol_page - 3)){$return.= '<li><a href="/'.$URI[1].'/'.$kol_page.'">'.$kol_page.'</a></li>';}
}	
if($d > $kol_page){
$return.= '<li class="disabled"><a href="/'.$URI[1].'/'.$nom_page.'" aria-label="Next">&raquo;</a></li>';
}elseif($y > $d){
$return.= '<li><a href="/'.$URI[1].'/'.$d.'" aria-label="Next">&raquo;</a></li>';
}			
$return.= '</ul>
</div>
';
}

}elseif($MODULE_URI == '/'.$URI[2] && !is_numeric($URI[2])){

if(file_exists('modules/mod_articles/data/cfg_'.$URI[2].'.dat')){	
require('data/cfg_'.$URI[2].'.dat');
}else{
header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();	
}

if($show_article == 0){	
header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
}

if(isset($URI[2])){$page->clear();}

$page->title = $page->name = $rubric;
$page->description = file_get_contents('modules/mod_articles/seo/description_'.$URI[2].'.dat');
$page->keywords = file_get_contents('modules/mod_articles/seo/keywords_'.$URI[2].'.dat');
	
$return = ''.file_get_contents('modules/mod_articles/data/content_'.$URI[2].'.dat').'';

$return .= '
<div class="rdm">
';
if($show_data == 1){
$return.= '<div class="data-article">'.$data_article.'</div>';
}
if($show_btn == 1){
$return .= '<a class="rdm-linkback" href="#" onclick="history.back();return false;" title="Возвращаемся туда, от куда пришли">Вернуться назад</a> 
<a class="rdm-allback" href="/'.$URI[1].'" title="'.$link_back.'">'.$link_back.'</a>';
}
$return .= '</div>'; 
		
}

if(isset($URI[3])){
header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
}

return $return;
?>