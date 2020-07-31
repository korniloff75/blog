<?php
$listIdNews = array_reverse($listIdNews);
// var_dump($listIdNews);

// for($i = 0; $i < $newsConfig->countInBlok; ++$i){
for($i = 0; $i < $newsConfig->countInBlok; $i++){
	if(empty($listIdNews[$i])) continue;

	$newsParam = json_decode($newsStorage->get('news_'.$listIdNews[$i]));
	if ($newsParam != false){
		if($newsConfig->sortPrev == 2){
			$return.= '<div class="link"><a href="/'.$newsConfig->idPage.'/'.$listIdNews[$i].'">'.$newsParam->header.'</a></div>';
		}else{
			$return.= '<div class="prev-news">';
			if($newsConfig->sortPrev == 1){
				$return.= '<h3>'.$newsParam->header.'</h3>
				<div class="prev-img">
				<span style="background:url('.$newsParam->img.') no-repeat;background-size: 100% auto;background-position:50% 50%;"></span>
				</div>';
			}else{
				$return.= '<h3><a href="/'.$newsConfig->idPage.'/'.$listIdNews[$i].'">'.$newsParam->header.'</a></h3>';
			}
			$return.= '<div class="prev-txt">
			'.$newsParam->prev.'</div>
			<div class="data-news">'.$newsParam->date.'</div>';
			if($newsConfig->sortPrev == 1){
				$return.= '<div class="btn-rdm"><a href="/'.$newsConfig->idPage.'/'.$listIdNews[$i].'">Подробнее</a></div>';
			}
			$return.= '</div>';
		}
	}
}
?>