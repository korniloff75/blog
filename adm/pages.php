<?php
require('../system/global.dat');
require('./include/start.dat');
?>
<script type="text/javascript">
function dell(page, n, u){
return '<div class="a">Подтвердите удаление страницы: <i>' + n + ' (<a href="//' + u + '" target="_blank">' + u + '</a>)</i></div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'pages.php?act=dell&amp;page='+page+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php
if($status=='admin'){
	if($act=='index'){

		echo'<div class="header">
			<h1>Управление страницами</h1>
			
		</div>
		<div class="menu_page">
		<a class="link" href="editor.php?new_page=on">Создать новую страницу</a>
		<a class="link" href="editor.php?page='.$Config->indexPage.'">Редактировать главную страницу</a>';
		if(isset($_COOKIE['lastEditPage'])){
			echo'<a class="link" href="editor.php?page='.htmlspecialchars($_COOKIE['lastEditPage']).'">Редактировать последнюю измененную страницу</a>';
		}
		echo'</div>
		
		
		<div class="content">
		<div class="row">
		<form action="pages.php?act=search" method="post">
		<input style="width: 250px;" type="text" name="q" value="" placeholder="Поиск по страницам">
		<input type="submit" name="" value="Поиск"> 
		</form>
		</div>
		<h3>Список всех страниц</h3>
		<div class="listrez">
		';

		// <h3>Страницы</h3>
		// <div class="listrez">
		// <table class="tables tables-pages">
		// <tr>
		// 	<td class="tables_head" colspan="2">Название страниц</td>
		// 	<td class="tables_head">URL</td>
		// 	<td class="tables_head">Доступность</td>
		// 	<td class="tables_head" style="text-align: center;">Дата изменения</td>
		// 	<td class="tables_head" style="text-align: right;">&nbsp;</td>
		// </tr>
		$pages = System::listPages();
		$pages = array_reverse($pages);//перевернули масив
		$nom = count($pages);
		
		//определили количество страниц
		$navigation = 50;
		$kol_page = ceil($nom / $navigation); 
		
		//проверка правильности переменной с номером страницы
		if(isset($_GET['nom_page'])){$nom_page = $_GET['nom_page'];}else{ $nom_page = 1; }
		if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $kol_page){ $nom_page = 1; }
		
		//начало навигации
		$i = ($nom_page - 1) * $navigation;
		$var = $i + $navigation;
		
		while($i < $var){
			if($i < $nom){
				if(Page::exists($pages[$i])){
					$Page = new Page($pages[$i], $Config);
					if($Page->show == 1){
						$tmp_34 = '<span class="g">Доступно всем</span>';
					}elseif($Page->show == 2){
						$tmp_34 = '<span class="r">Доступно пользователям с преференциями</span>';
					}elseif($Page->show == 0){
						$tmp_34 = '<span class="r">Доступно только администратору</span>';
					}else{
						$tmp_34 = '<span class="r">Error</span>';
					}

					$tmp_url = $pages[$i]!=$Config->indexPage?$pages[$i]:'';
					
					echo'<div class="item item_page">
					<div class="right_menu">
					<a href="editor.php?page='.$pages[$i].'&amp;dub=1">Создать дубликат</a>
					<a href="editor.php?page='.$pages[$i].'">Редактировать</a>
					<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\''.$pages[$i].'\', \''.$Page->name.'\', \''.SERVER.'/'.$tmp_url.'\'));">Удалить</a>
					</div>
					<div class="name_page"><img src="include/page.svg" alt=""> <a href="editor.php?page='.$pages[$i].'">'.$Page->name.'</a></div>
					<div>URL: <a href="//'.SERVER.'/'.$tmp_url.'" target="_blank">'.SERVER.'/'.$tmp_url.'</a> ('.$tmp_34.')</div>
					<div>Дата редактирования: '.date("d.m.Y H:i", $Page->time).'</div>
					</div>';
				}else{
					echo'<div class="item item_page">
					<div style="color: red;">Error: индекс не связан ни с одной страницей</div>
					<div>Index: '.$pages[$i].'</div>
					<div class="right_menu">
					<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\''.$pages[$i].'\', \''.$Page->name.'\', \''.SERVER.'/'.$tmp_url.'\'));">Удалить</a>
					</div>
					</div>';
				}
			}
			++$i;
		}
		echo'</div>';
		
		//навигация по номерам страниц
		if($kol_page > 1){//Если количество страниц больше 1, то показываем навигацию
			echo'<div style="margin-top: 25px; text-align: center;">';
			echo'Страницы: ';
			
			for($i = 1; $i <= $kol_page; ++$i){
				if($nom_page == $i){
					echo'<b>('.$i.')</b> ';
				}else{
					echo'<a href="?nom_page='.$i.'">'.$i.'</a> ';
				}
			}
			echo'</div>';
		}
		//конец навигации
		echo'</div>';
	}
	

	if($act=='search'){
		if(!function_exists('mb_stripos')){
			echo'<div class="header">
				<h1>Поиск по страницам</h1>
			</div>
			<div class="menu_page"><a href="pages.php">&#8592; Вернуться назад</a></div>
			<div class="content">
				<p>На сервере не установлено php расширение "mbstring". Это расширение позволяет производить поиск по русскоязычным символам. Обратитесь к администратору вашего сервера для установки данного расширения. 
				Администраторы могут воспользоваться <a href="https://www.php.net/manual/ru/book.mbstring.php" target="_blank">документацией</a>.</p>
			</div>';
		}else{
			function mb_ucfirst($text) {
				return mb_strtoupper(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($text, 1, mb_strlen($text, 'UTF-8'), 'UTF-8');
			}
			$q = isset($_POST['q'])?htmlspecialchars(mb_ucfirst(trim($_POST['q']))):'';
			
			echo'<div class="header">
				<h1>Поиск по страницам</h1>
			</div>
			<div class="menu_page"><a href="pages.php">&#8592; Вернуться назад</a></div>
			
			<div class="content">
				<div class="row">
				<form name="form_name" action="pages.php?act=search" method="post">
				<input type="text"style="width: 250px;" name="q" value="'.$q.'" placeholder="Введите запрос" autofocus>
				<input type="submit" name="" value="Поиск">
				</form>
				</div>
				<h3>Результаты поиска:</h3>
				';
				
				if($q != ''){
					
					$pages = System::listPages();
					$pages = array_reverse($pages);//перевернули масив
					
					$pSearchName = array(); // Пустой массив результатов
					$pSearchTitle = array(); // Пустой массив результатов
					$pSearchKeywords = array(); // Пустой массив результатов
					$pSearchDescription = array(); // Пустой массив результатов
					$pSearchID = array(); // Пустой массив результатов
					
					foreach($pages as $value){
						if(Page::exists($value)){
							$Page = new Page($value);
							if(mb_stripos($Page->name, $q, 0, 'UTF-8') !== false){
								$pSearchName[] = array('name' => $Page->name, 'time' => $Page->time, 'show' => $Page->show, 'id' => $value);
							}elseif(mb_stripos($Page->title, $q, 0, 'UTF-8') !== false){
								$pSearchTitle[] = array('name' => $Page->name, 'time' => $Page->time, 'show' => $Page->show, 'id' => $value);
							}elseif(mb_stripos($Page->keywords, $q, 0, 'UTF-8') !== false){
								$pSearchKeywords[] = array('name' => $Page->name, 'time' => $Page->time, 'show' => $Page->show, 'id' => $value);
							}elseif(mb_stripos($Page->description, $q, 0, 'UTF-8') !== false){
								$pSearchDescription[] = array('name' => $Page->name, 'time' => $Page->time, 'show' => $Page->show, 'id' => $value);
							}elseif(mb_stripos($value, $q, 0, 'UTF-8') !== false){
								$pSearchID[] = array('name' => $Page->name, 'time' => $Page->time, 'show' => $Page->show, 'id' => $value);
							}

						}
					}

					
					$pSearchResult = array_merge($pSearchName, $pSearchTitle, $pSearchKeywords, $pSearchDescription, $pSearchID);
					echo'<div class="listrez">';
					$i = 0; // Счетчик показов
					foreach($pSearchResult as $value){
						//var_dump($key );

						if($value['show'] == 1){
							$tmp_34 = '<span class="g">Доступно всем</span>';
						}elseif($value['show'] == 2){
							$tmp_34 = '<span class="r">Доступно пользователям с преференциями</span>';
						}elseif($value['show'] == 0){
							$tmp_34 = '<span class="r">Доступно только администратору</span>';
						}else{
							$tmp_34 = '<span class="r">Error</span>';
						}

						$tmp_url = $value['id']!=$Config->indexPage?$value['id']:'';

						
						echo'<div class="item item_page">
						<div class="right_menu">
						<a href="editor.php?page='.$value['id'].'&amp;dub=1">Создать дубликат</a>
						<a href="editor.php?page='.$value['id'].'">Редактировать</a>
						<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\''.$value['id'].'\', \''.$value['name'].'\', \''.SERVER.'/'.$tmp_url.'\'));">Удалить</a>
						</div>
						<div class="name_page"><img src="include/page.svg" alt=""> <a href="editor.php?page='.$value['id'].'">'.preg_replace('#'.$q.'#ius', '<span class="r">'.$q.'</span>', $value['name']).'</a></div>
						<div>URL: <a href="//'.SERVER.'/'.$tmp_url.'" target="_blank">'.SERVER.'/'.$tmp_url.'</a> ('.$tmp_34.')</div>
						<div>Дата редактирования: '.date("d.m.Y H:i", $value['time']).'</div>
						</div>';
						
						++$i; if($i == 100) break; // Ограничение показов
					}
					if($i == 0){
						echo'<div class="item item_page">
						<div>Ничего не найдено</div>
						</div>';
					}
					echo'</div>';
					
				}else{echo'<div class="msg">Ошибка в запросе</div>';}
			
			echo'</div>';
		}
	}

    

	if($act=='dell'){
		$page = htmlspecialchars(specfilter($_GET['page']));
		if(Page::exists($page) && $page != $Config->indexPage){
			Page::delete($page);
			System::notification('Удалена страница с идентификатором '.$page.'', 'g');
			echo'<div class="msg">Страница успешно удалена</div>';
		}else{
			System::notification('Ошибка при удалении страницы с идентификатором '.$page.', страница не найдена или запрос некорректен', 'r');
			echo'<div class="msg">Ошибка при удалении страницы</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php?\';', 3000);
</script>
<?php
	}
}else{
echo'<div class="msg">Необходимо выполнить авторизацию</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'index.php?\';', 3000);
</script>
<?php
}
require('include/end.dat');
?>