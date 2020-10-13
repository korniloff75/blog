<?php
if (!class_exists('System')) exit; // Запрет прямого доступа
require(DR.'/modules/turbo/cfg.php');

if($Config->uriRule == 1){ // Разрешаем произвольные GET параметры
	if (strpos(REQUEST_URI, '?') !== false) {
		$URI = explode('/', substr(REQUEST_URI, 0, strpos(REQUEST_URI, '?')));
	}else{
		$URI = explode('/', REQUEST_URI);
	}
}
if($Config->uriRule == 2){ // Запрещаем произвольные GET параметры
	$URI = explode('/', REQUEST_URI);
}

if($URI[1] == $turboConfig->turboId && $turboConfig->turbo && isset($URI[2]) && !isset($URI[3])){
		
		$numRSS = basename($URI[2], ".xml");
		if(is_numeric($numRSS) == false || $numRSS < 1){
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
		}

		$listTurbo = System::listPages();
		
			// // Исключения
			$exceptions = explode(',', str_replace(' ', '', $turboConfig->exceptions));
			$listTurbo = array_diff($listTurbo, $exceptions);
			// Переиндексировали числовые индексы 
			$listTurbo = array_values($listTurbo);
        
		$nom = count($listTurbo);
		$countPage = ceil($nom / $turboConfig->turboItems);
		
		if($numRSS > $countPage){
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
		}

		$genTurboSource = false;
		if ($turboStorage->iss('turboSource'.$numRSS)){
			if ($turboStorage->time('turboSource'.$numRSS) + $turboConfig->turboCacheTime < time()){
				$genTurboSource = true;
			}
		}else{
			$genTurboSource = true;
		}

header('Content-Type: text/xml; charset=utf-8');
if($genTurboSource){
$inner = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
xmlns:yandex="http://news.yandex.ru"
xmlns:media="http://search.yahoo.com/mrss/"
xmlns:turbo="http://turbo.yandex.ru">
<channel>
<title>'.$Config->header.'</title>
<link>'.$Config->protocol.'://'.SERVER.'/</link>
<description>'.$Config->slogan.'</description>
<language>ru</language>';
$i = ($numRSS - 1) * $turboConfig->turboItems;
$var = $i + $turboConfig->turboItems;
while($i < $var && $i < $nom){
    if(Page::exists($listTurbo[$i])){
		$Page = new Page($listTurbo[$i], $Config);
		if($Page->show == 1){$inner.= '
        <item turbo="true">
        <title>'.$Page->name.'</title>
        <link>'.$Config->protocol.'://'.SERVER.'/'.($listTurbo[$i] != $Config->indexPage?$listTurbo[$i]:'').'</link>
        <pubDate>'.date("D, d M Y H:i:s O", $Page->time).'</pubDate>
        <turbo:content>
        <![CDATA[
        <header>
        <h1>'.$Page->name.'</h1>
        </header>
        '.trim(Page::contentDat($listTurbo[$i])).'
        ]]>
        </turbo:content>
        </item>';
		}
        
    }
    ++$i;
}
$inner.= '
</channel>
</rss>';
$turboStorage->set('turboSource'.$numRSS, $inner); // записали кеш
echo $inner; // вывели кеш
}else{
echo $turboStorage->get('turboSource'.$numRSS); // вывели кеш
}
ob_end_flush(); exit;	
}
?>