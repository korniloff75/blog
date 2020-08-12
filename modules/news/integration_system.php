<?php
require(DR.'/modules/news/cfg.php');

function NewsBBCode($html){
	$html = trim($html[1]);
	$html = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$html);
	$html = str_replace('  ',' &nbsp;',$html);
	$html = preg_replace('/&quot;(.*?)&quot;/', '<span class="quot">&quot;\1&quot;</span>', $html);
	$html = preg_replace('/\'(.*?)\'/', '<span class="quot">\'\1\'</span>', $html);
	$html = str_replace("\n",'<br>', $html);
	$html = specfilter($html);
	return '<pre><code>'.$html.'</code></pre>';
}

function NewsFormatText($text){
	$text = preg_replace_callback('#\[code\](.*?)\[/code\]#si', 'NewsBBCode', $text);
	$text = preg_replace('#\[b\](.*?)\[/b\]#si', '<span style="font-weight: bold;">\1</span>', $text);
	$text = preg_replace('#\[red\](.*?)\[/red\]#si', '<span style="color: #E53935;">\1</span>', $text);
	$text = '<p>'.str_replace("\n",'</p><p>', trim($text)).'</p>';
	$text = specfilter($text);
	$text = str_replace('<p></p>', '', $text);
	return $text;
}

function NewsCategoryName($id){
	global $newsConfig;
	$return = false;
	foreach($newsConfig->cat as $key => $value){
		if ($id == $key){
			$return = $value;
		}
	}
	return $return;
}

function NewsCategory($cat, $col, $tpl = false, $tplNoNews = false, $start = false, $sort = 'reverse'){
	global $Config, $Page, $newsStorage, $newsConfig;
	$return = '';
	if($tpl == false){
		$tpl = $newsConfig->blokTemplate;
	}
	if($tplNoNews == false){
		$tplNoNews = '<p>Записей пока нет</p>';
	}
	if($cat){
		$listIdCat = json_decode($newsStorage->get('category'), true);
		$listIdNews = array_keys($listIdCat, $cat);

	}else{
		$listIdNews = json_decode($newsStorage->get('list'), true);
	}
	if($listIdNews == false){
		$return.= $tplNoNews;
	}else{
		if($sort == 'reverse'){
			//перевернули масив для вывода новостей в обратном порядке
			$listIdNews = array_reverse($listIdNews);
		}
		if($sort == 'random'){
			shuffle($listIdNews);
		}
		if(!$start){
			$start = 0;
		}

		var_dump(
			$newsConfig->cat,
			json_decode($newsStorage->get('category'), true),
			// $newsStorage->scan(),
			// $listIdNews,
			// $col,
			// $start
		);

		for($i = 0 + $start; $i < $col + $start; ++$i){
			if(empty($listIdNews[$i])) break;
			$newsParam = json_decode($newsStorage->get('news_'.$listIdNews[$i]));
			if ($newsParam != false){
				$categoryname = NewsCategoryName($newsParam->cat);
				if(!$categoryname) $categoryname = 'Без категории';

				$categoryuri = $newsParam->cat != ''?'/'.$newsConfig->idPage.'/category/'.$newsParam->cat:($Config->indexPage == $newsConfig->idPage?'/':'/'.$newsConfig->idPage);

				$out_prev = str_replace('#header#', $newsParam->header, $tpl);
				$out_prev = str_replace('#content#', $newsParam->prev, $out_prev);
				$out_prev = str_replace('#date#', date($newsConfig->formatDate, isset($newsParam->time)?$newsParam->time:strtotime($newsParam->date)), $out_prev);
				$out_prev = str_replace('#time#', date('H:i', isset($newsParam->time)?$newsParam->time:strtotime($newsParam->date)), $out_prev);

				$out_prev = str_replace('#img#', $newsParam->img, $out_prev);
				$out_prev = str_replace('#categoryname#', $categoryname, $out_prev);
				$out_prev = str_replace('#categoryuri#', $categoryuri, $out_prev);
				$out_prev = str_replace('#index#', $i, $out_prev);
				foreach($newsConfig->custom as $value){
					$out_prev = str_replace('#'.$value->id.'#', (isset($newsParam->custom->{$value->id})?$newsParam->custom->{$value->id}:''), $out_prev);
				}
				$return.=  str_replace('#uri#', '/'.$newsConfig->idPage.'/'.$listIdNews[$i], $out_prev);
			}
		}
	}
	return $return;
}
?>