<?php
require(DR.'/modules/articles/cfg.php');

function ArticlesBBCode($html){
	$html = trim($html[1]);
	$html = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$html);
	$html = str_replace('  ',' &nbsp;',$html);
	$html = preg_replace('/&quot;(.*?)&quot;/', '<span class="quot">&quot;\1&quot;</span>', $html);
	$html = preg_replace('/\'(.*?)\'/', '<span class="quot">\'\1\'</span>', $html);
	$html = str_replace("\n",'<br>', $html);
	$html = specfilter($html);
	return '<pre><code>'.$html.'</code></pre>';
}

function ArticlesFormatText($text){
	$text = preg_replace_callback('#\[code\](.*?)\[/code\]#si', 'ArticlesBBCode', $text);
	$text = preg_replace('#\[b\](.*?)\[/b\]#si', '<span style="font-weight: bold;">\1</span>', $text);
	$text = preg_replace('#\[red\](.*?)\[/red\]#si', '<span style="color: #E53935;">\1</span>', $text);
	$text = '<p>'.str_replace("\n",'</p><p>', trim($text)).'</p>';
	$text = specfilter($text);
	$text = str_replace('<p></p>', '', $text);
	return $text;
}

function ArticlesCategoryName($id){
	global $articlesConfig;
	$return = false;
	foreach($articlesConfig->cat as $key => $value){
		if ($id == $key){
			$return = $value;
		}
	}
	return $return;
}

function ArticlesCategory($cat, $col, $tpl = false, $tplNoArticles = false, $start = false, $sort = 'reverse'){
	global $Config, $Page, $articlesStorage, $articlesConfig;
	$return = '';
	if($tpl == false){
		$tpl = $articlesConfig->blokTemplate;
	}
	if($tplNoArticles == false){
		$tplNoArticles = '<p>Записей пока нет</p>';
	}
	if($cat){
		$listIdCat = json_decode($articlesStorage->get('category'), true);
		$listIdArticles = array_keys($listIdCat, $cat);
		
	}else{
		$listIdArticles = json_decode($articlesStorage->get('list'), true); 
	}
	if($listIdArticles == false){
		$return.= $tplNoArticles;
	}else{
		if($sort == 'reverse'){
			//перевернули масив для вывода статьей в обратном порядке
			$listIdArticles = array_reverse($listIdArticles);
		}
		if($sort == 'random'){
			shuffle($listIdArticles);
		}
		if(!$start){
			$start = 0;
		}
		for($i = 0 + $start; $i < $col + $start; ++$i){
			$articlesParam = json_decode($articlesStorage->get('articles_'.$listIdArticles[$i]));
			if ($articlesParam != false){
				$categoryname = NewsCategoryName($articlesParam->cat);
				if(!$categoryname) $categoryname = 'Без категории';
				
				$categoryuri = $articlesParam->cat != ''?'/'.$articlesConfig->idPage.'/category/'.$articlesParam->cat:($Config->indexPage == $articlesConfig->idPage?'/':'/'.$articlesConfig->idPage);

				$out_prev = str_replace('#header#', $articlesParam->header, $tpl);
				$out_prev = str_replace('#content#', $articlesParam->prev, $out_prev);
				$out_prev = str_replace('#date#', date($articlesConfig->formatDate, isset($articlesParam->time)?$articlesParam->time:strtotime($articlesParam->date)), $out_prev);
				$out_prev = str_replace('#time#', date('H:i', isset($articlesParam->time)?$articlesParam->time:strtotime($articlesParam->date)), $out_prev);
				
				$out_prev = str_replace('#img#', $articlesParam->img, $out_prev);
				$out_prev = str_replace('#categoryname#', $categoryname, $out_prev);
				$out_prev = str_replace('#categoryuri#', $categoryuri, $out_prev);
				$out_prev = str_replace('#index#', $i, $out_prev);
				foreach($articlesConfig->custom as $value){
					$out_prev = str_replace('#'.$value->id.'#', (isset($articlesParam->custom->{$value->id})?$articlesParam->custom->{$value->id}:''), $out_prev);
				}
				$return.=  str_replace('#uri#', '/'.$articlesConfig->idPage.'/'.$listIdArticles[$i], $out_prev);
			}
		}
	}
	return $return;
}
?>
