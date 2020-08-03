<?php
require('cfg.php');
require('cfg.dat');
$newsStorage = new EngineStorage($module_news);
$return = '';
if(($listIdNews = json_decode($newsStorage->get('list'), true)) == false){
$return.= '<div class="msg">Записей пока нет</div>';
}else{
if(file_exists('./modules/'.$Config->template.'/news.blok.php')){
require('./modules/'.$Config->template.'/news.blok.php');
$return.= '<div class="link" style="margin-top:15px;border:0;"><a href="/'.$newsConfig->idPage.'">Все новости</a></div>';
}else{
		//перевернули масив для вывода новостей в обратном порядке
		$listIdNews = array_reverse($listIdNews);
		for($i = 0; $i < $newsConfig->countInBlok; ++$i){
			$newsParam = json_decode($newsStorage->get('news_'.$listIdNews[$i]));
			if ($newsParam != false){
				$out_prev = str_replace('#header#', $newsParam->header, $newsConfig->blokTemplate);
				$out_prev = str_replace('#content#', $newsParam->prev, $out_prev);
				$out_prev = str_replace('#date#', $newsParam->date, $out_prev);
				$out_prev = str_replace('#img#', $newsParam->img, $out_prev);
				$return.=  str_replace('#uri#', '/'.$newsConfig->idPage.'/'.$listIdNews[$i], $out_prev);
			}
		}	
}
}
return $return;
?>