<?php
require('cfg.php');

$blogStorage = new EngineStorage('module.blog');
$return = '';
if(($listIdBlog = json_decode($blogStorage->get('list'), true)) == false){
		$return.= '<div class="msg">Записей пока нет</div>';
}else{
		//перевернули масив для вывода новостей в обратном порядке
		$listIdBlog = array_reverse($listIdBlog);
		for($i = 0; $i < $blogConfig->countInBlok; ++$i){
			$blogParam = json_decode($blogStorage->get('post_'.$listIdBlog[$i]));
			if ($blogParam != false){
				$return.= '<div class="link"><a href="/'.$blogConfig->idPage.'/'.$listIdBlog[$i].'">'.$blogParam->header.'</a></div>';
			}
		}
		
		$return.= '<div class="link" style="margin-top:15px;border:0;"><a href="/'.$blogConfig->idPage.'">Все посты</a></div>';
}
return $return;
?>