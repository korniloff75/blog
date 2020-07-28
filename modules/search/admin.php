<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

$SearchStorage = new EngineStorage('module.search');

if($act=='index'){
	echo'<div class="header"><h1>Поиск по сайту</h1></div>
	<div class="content">';
	if($SearchStorage->iss('searchIndex1')){
		echo'<h3>Поисковой индекс сформирован</h3>';
	}else{
		echo'<h3>Поисковой индекс еще не сформирован</h3>';
	}
	
	echo'<p class="box">Если вы производили какие либо изменения на сайте и хотите чтобы они попали в поисковой индекс, нажмите "Проиндексировать сайт". 
		Помните, что индексация - это ресурсоемкий процесс, вы можете получить жалобу от вашего хостинг-провайдера если будете выполнять индексацию слишком часто.</p>';
		
	echo'<p class="row"><a href="module.php?module='.$MODULE.'&amp;act=add" class="button">Проиндексировать сайт</a></p>';
	
	$logArray = $SearchStorage->getArray('log');
	$logArray = array_reverse($logArray);//перевернули масив
	if ($logArray){
		echo'<table class="tables">
		<tr>
			<td class="tables_head" colspan="2">История поиска</td>
			<td></td>
		</tr>';
		foreach($logArray as $value){
			echo'<tr>
				<td class="img"><img src="include/page.svg" alt=""></td>
				<td>'.$value.'</td>
				<td></td>
			</tr>';
		}
		echo'</table>';
	}
	
	echo'</div>';
}
if($act=='add'){
	$listPages = System::listPages();
	$searchIndex = array();
	foreach($listPages as $value){
		$Page = new Page($value, $Config);
		if($Page->module == 'news'){
			// Индексирование новостей
			if (Module::exists('news')){
				$NewsStorage = new EngineStorage('module.news2');
				if(($listIdNews = json_decode($NewsStorage->get('list'), true)) != false){
					foreach($listIdNews as $idNews){
						if($NewsStorage->iss('news_'.$idNews)){
							$newsParam = json_decode($NewsStorage->get('news_'.$idNews));
							$searchIndex[] = array('uri' => '/'.$value.'/'.$idNews, 'name' => $newsParam->header, 'keywords' => $newsParam->keywords, 'description' => $newsParam->description);
						}
					}
				}
			}
		}
		$searchIndex[] = array('uri' => '/'.($value != $Config->indexPage?$value:''), 'name' => $Page->name, 'keywords' => $Page->keywords, 'description' => $Page->description);
	}
	$SearchStorage->set('searchIndex1', json_encode($searchIndex));
	System::notification('Сформирован новый поисковый индекс', 'g');
	echo'<div class="msg">Поисковый индекс успешно сформирован</div>';
	
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php	
}


?>