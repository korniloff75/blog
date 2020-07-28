<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

if(isset($URI[4])){
	header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
}


$searchTag = false;
if(isset($_GET['q'])){
	$q = $_GET['q'];
}elseif(isset($_POST['q'])){
	$q = $_POST['q'];
}elseif(isset($URI[2])){
	$q = '#'.urldecode($URI[2]);
	$searchTag = true;
}else{
	$q = '';
}
$q = htmlspecialchars(specfilter(trim($q)));


$Page->clear(); // Очистили страницу

if(!$searchTag){
$return = '
<form name="searchForm" action="/'.$URI[1].'" method="post">
<p class="pInput"><input type="text" name="q" value="'.$q.'" class="searchFormInput"></p>
<p class="pSubmit"><input type="submit" name="" value="Поиск" class="searchFormSubmit"></p>
</form>
';
}


if($q != '')
{
	
	$SearchStorage = new EngineStorage('module.search');
	
	if(!$searchTag){
		$logArray = $SearchStorage->getArray('log');
		$logCount = count($logArray);
		$logStr = '';
		if($logCount >= 100){
			for($i = 50; $i < 100; ++$i){
				$logStr.= $logArray[$i]."\r\n";
			}
			$SearchStorage->set('log', $logStr);
		}
		
		$SearchStorage->set('log', $q."\r\n", 'a+');
		unset($logArray, $logCount, $logStr);
	}

	



	if($SearchStorage->iss('searchIndex1')){
		$searchIndex = json_decode($SearchStorage->get('searchIndex1'), true);
		
		$searchName = array();
		$searchDescription = array();
		$searchKeywords = array();
		foreach($searchIndex as $value){
			
			if(($pos = mb_stripos($value['name'], $q, 0, 'UTF-8')) !== false){
				$value['pos'] = $pos;
				$searchName[] = $value;
			}elseif(($pos = mb_stripos($value['description'], $q, 0, 'UTF-8')) !== false){
				$value['pos'] = $pos;
				$searchDescription[] = $value;
			}elseif(($pos = mb_stripos($value['keywords'], $q, 0, 'UTF-8')) !== false){
				$value['pos'] = $pos;
				$searchKeywords[] = $value;
			}
			
		}
		
		function cmp($a, $b) {
			if ($a['pos'] == $b['pos']) {
				return 0;
			}
			return ($a['pos'] < $b['pos']) ? -1 : 1;
		}
		uasort($searchName, 'cmp');
		uasort($searchDescription, 'cmp');
		uasort($searchKeywords, 'cmp');
		
		$searchResult = array_merge($searchName, $searchDescription, $searchKeywords);

		unset($searchName, $searchDescription, $searchKeywords);
		
		//навигация и вывод
		$nom = count($searchResult);
		
		if ($nom == 0){
			$return.= '<p class="not_found">Ничего не найдено</p>';
		}else{
			
			//определили количество страниц
			$countPage = ceil($nom / 30); 
			
			//проверка правельности переменной с номером страницы
			if(isset($_GET['nom_page'])){$nom_page = $_GET['nom_page'];}else{ $nom_page = 1; }
			if(!is_numeric($nom_page) || $nom_page <= 0  || $nom_page > $countPage){
				header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
			}
			
			//начало навигации
			$i = ($nom_page - 1) * 30;
			$var = $i + 30;
			
			while($i < $var){
				if($i < $nom){
					$return.= '
						<p class="search">
						<a href="'.$searchResult[$i]['uri'].'" style="font-size: 120%;">'.$searchResult[$i]['name'].'</a><br>
						'.$searchResult[$i]['description'].'
						</p>
						';
				}
				++$i;
			}

			if($countPage > 1){	  
				//навигация по номерам страниц
				$return.= '<div class="navigation"><span class="navigation_header">Страницы: </span>';
				
				$a = $nom_page - 3;
				$b = $nom_page + 3;
				
				if($a > 1){
					$return.= '<a href="/'.$URI[1].'/'.($searchTag?$URI[2].'/get?':'get?q='.$q.'&amp;').'nom_page=1" class="link first">1</a>';
					if($a > 2){ $return.= '<span class="space">&nbsp;</span>'; }
				}
				while($a <= $b){
					if(($a > 0) && ($a <= $countPage)){
						if($nom_page == $a){
							$return.= '<span class="this">'.$a.'</span>';
						}else{
							$return.= '<a href="/'.$URI[1].'/'.($searchTag?$URI[2].'/get?':'get?q='.$q.'&amp;').'nom_page='.$a.'" class="link">'.$a.'</a>';
						}
					}
				++$a;
				}
				if($b < $countPage){
					if($b < ($countPage - 1)){ $return.= '<span class="space">&nbsp;</span>'; }
					$return.= '<a href="/'.$URI[1].'/'.($searchTag?$URI[2].'/get?':'get?q='.$q.'&amp;').'nom_page='.$countPage.'" class="link last">'.$countPage.'</a>';
				}
				
				$return.= '</div>';
				//конец навигации*/
			}
		}

		
	}
}
return $return;
?>