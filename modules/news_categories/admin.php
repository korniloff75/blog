<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

require('cfg.dat');

?>
<script type="text/javascript">
function dellcategory(category){
return '<div class="a">Подтвердите удаление категории</div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'module.php?module=news_categories&act=dell&amp;category='+category+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php

if($act=='index'){
    if(is_dir('../modules/news_categories/file')){
	echo'<div class="header"><h1>Инициализация модуля</h1></div>
	<div class="content">
    <p>Все компоненты модуля установлены. Для начала работы модуля необходимо выполнить инициализацию.</p>
    <form name="settingform" action="module.php?module='.$MODULE.'" method="post">
    <INPUT TYPE="hidden" NAME="act" VALUE="addin1">
    <input type="submit" name="" value="Начать инициализацию">
    </form>
    </div>';
   }else{	
	echo'<div class="header"><h1>Управление модулем "Категории новостей"</h1></div>
	<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a> | <a href="module.php?module='.$MODULE.'&act=cfg_category">Общие настройки</a></div>';
	if(!file_exists('../modules/'.$Config->template.'/news.blok.php')){
	echo'<div class="error">В подключенном шаблоне отсутствуют компоненты необходимые для корректной работы расширения. <a href="module.php?module='.$MODULE.'&act=addin2" style="text-decoration: underline;">Загрузить компоненты</a>.</div>';
    }
	echo'
	<div class="content"><h2>Список категорий</h2>
	
	<div class="row">
		<form name="settingform" action="module.php?module='.$MODULE.'&act=category_search" method="post">
		<input style="width: 250px;" type="text" name="q" value="" placeholder="Поиск по заголовку" autofocus>
		<input type="submit" name="" value="Поиск"> 
		</form>
    </div>
	
	<table class="tables">
    <tr>
    <td class="tables_head" colspan="2">Категории</td>
	<td class="tables_head">Ссылка на страницу</td>
	<td class="tables_head">Директория</td>
	<td class="tables_head">Папка с фото</td>
    <td class="tables_head" style="text-align: right;"><a href="module.php?module=news_categories&act=new_category" class="button addlink" title="Создать категорию">Создать категорию</a></td>
    </tr>';
	if(file_exists('../modules/news_categories/list.dat')){
    $link_data = file('../modules/news_categories/list.dat');
    $nom = count($link_data);
    if($nom == 0){
    echo'<tr><td>Категории еще не созданы</td><td></td><td></td><td></td><td></td><td>---</td></tr>';
    }
    for($q = 0; $q < $nom; ++$q){
    $link_cfg = explode('^',$link_data[$q]);
	$newsStorage = new EngineStorage('module.'.$link_cfg[1].'');
    if(($listIdNews = json_decode($newsStorage->get('list'), true)) !== false){
    $col = count($listIdNews);
    }
    echo'<tr>
    <td class="img"><img src="../modules/news_categories/icon1.svg" alt=""></td>
	<td style="text-align: left;">'.$link_cfg[0].' ('.$col.')</td>
	<td>';
	if(file_exists('../data/pages/cfg_'.$link_cfg[2].'.dat')){
	echo'<a href="//'.SERVER.'/'.$link_cfg[2].'" target="_blank">'.SERVER.'/'.$link_cfg[2].'</a>';
	}else{
	echo'<span class="r">Страница еще не создана</span>';	
	}
	echo'</td>
	<td>/modules/'.$link_cfg[1].'</td>
	<td>/files/news_categories/'.$link_cfg[1].'</td>
	<td style="text-align: right;">
	<a href="module.php?module=news_categories&act=up_link&amp;str_file='.$q.'" title="Переместить вверх">Вверх</a> &nbsp; 
	<a href="module.php?module=news_categories&act=down_link&amp;str_file='.$q.'" title="Переместить вниз">Вниз</a> &nbsp; 
	<a href="module.php?module=news_categories&act=ed_category&id_category='.$link_cfg[1].'&amp;str_file='.$q.'" title="Открыть категорию">Открыть</a> &nbsp; 
	<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dellcategory(\''.$link_cfg[1].'\'));" title="Удалить категорию">Удалить</a>
	</td>
	</tr>';
    }
    }else{
    echo'<tr><td style="color:#f00;">Ошибка</td><td>&nbsp;</td><td></td><td></td><td>---</td></tr>';
    }
	echo'</table>
    </div>
    ';
}
}

if($act=='category_search'){
	
if(isset($_POST['q'])){$q = $_POST['q'];}
$result = 0;
echo'<div class="header"><h1>Управление модулем "Категории новостей"</h1></div>
	<div class="menu_page"><a href="module.php?module='.$MODULE.'">&#8592; Вернуться назад</a></div>
	<div class="content">';
	if(trim($q)){
    echo'<h2>Результат поиска</h2>';
	
    $link_data = file('../modules/news_categories/list.dat');
    $nom = count($link_data);
    for($i = 0; $i < $nom; ++$i){
    $link_cfg = explode('^',$link_data[$i]);
	if($q == $link_cfg[0]){
    echo'<table class="tables">
    <tr>
    <td class="tables_head" colspan="2">Категории</td>
	<td class="tables_head">Ссылка на страницу</td>
	<td class="tables_head">Директория</td>
	<td class="tables_head">Папка с фото</td>
    <td class="tables_head"></td>
    </tr>
	<tr>
    <td class="img"><img src="../modules/news_categories/icon1.svg" alt=""></td>
	<td style="text-align: left;">'.$link_cfg[0].'</td>
	<td><a href="//'.SERVER.'/'.$link_cfg[2].'" target="_blank">'.SERVER.'/'.$link_cfg[2].'</a></td>
	<td>/modules/'.$link_cfg[1].'</td>
	<td>/files/news_categories/'.$link_cfg[1].'</td>
	<td style="text-align: right;"> 
	<a href="module.php?module=news_categories&act=ed_theme&id_theme='.$link_cfg[1].'&amp;str_file='.$i.'" title="Открыть тему">Открыть</a> &nbsp; 
	<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dellcategory(\''.$link_cfg[1].'\'));" title="Удалить категорию">Удалить</a>
	</td>
	</tr>';
	$result = 1;	
    }
    }
	echo'</table>';
	if($result == 0){
    echo'<div class="msg">Ничего не найдено</div>';
    header('Refresh: 2; URL=module.php?module='.$MODULE.'');
    }
	}else{
    echo'<div class="msg">Ошибка! Поле поиска пустое.</div>';
    header('Refresh: 2; URL=module.php?module='.$MODULE.'');		
    }
    echo'</div>
    ';
}

if($act=='cfg_category'){
	
$check_st = ($news_style == 1)?' checked':'';
$check_lib = ($lib_jquery == 1)?' checked':'';
	
echo'<div class="header"><h1>Управление модулем "Категории новостей"</h1></div>
<div class="menu_page"><a href="module.php?module='.$MODULE.'">&#8592; Вернуться назад</a></div>
<div class="content">
<h2>Общие настройки</h2>
<form name="forma" action="module.php?module='.$MODULE.'" method="post">
<input type="hidden" name="act" value="addcfgcategory">
<table class="tblform">
<tr>
<td>Идентификатор страницы вывода:</td>
<td><input type="text" name="id_categorys" value="'.$id_categorys.'"></td>
</tr>
<tr>
<td>Текст ссылки в боковом блоке:</td>
<td><input type="text" name="txt_link" value="'.$txt_link.'"></td>
</tr>
<tr>
<td>Количество превью на странице</td>
<td><input type="text" name="amtpr_page" value="'.$amtpr_page.'" size="50" style="width:45px;"></td>
</tr>
<tr>
<td>Количество превью в боковом блоке</td>
<td><input type="text" name="nom_blok" value="'.$nom_blok.'" size="50" style="width:45px;"></td>
</tr>
<tr>
<td>Использовать стили шаблона:</td>
<td class="middle">
<input type="checkbox" value="y" name="news_style"'.$check_st.'>
</td>
</tr>
<tr>
<td>Подключить библиотеку jquery:</td>
<td class="middle">
<input type="checkbox" value="j" name="lib_jquery"'.$check_lib.'>
</td>
</tr>
<tr>
<tr>
<td><input type="submit" name="" value="Сохранить"></td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</div>
';
}

if($act=='new_category'){

if(file_exists('../modules/news_categories/list.dat')){
echo'<div class="header"><h1>Управление модулем "Категории новостей"</h1></div>
<div class="menu_page"><a href="module.php?module=news_categories">&#8592; Вернуться назад</a></div>
<div class="content">
<h2>Создание новой категории</h2>
<form name="forma" action="module.php?module=news_categories" method="post">
<INPUT TYPE="hidden" NAME="act" VALUE="addcategory">
<table class="tblform">
<tr>
<td>Название категории:</td>
<td><input type="text" name="name_category" id="header" value="" size="25"></td>
</tr>
<tr>
<td>Идентификатор страницы:</td>
<td><input type="text" name="id_page" id="id" value=""><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из названия категории</a></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="" value="Создать категорию"> &nbsp; <a href="module.php?module=news_categories">Вернуться назад</a></td>
</tr>
</table>
</form>
</div>';
}else{
echo'<div class="msg">Запрос неверен</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}
}

?>

<script type="text/javascript">

var iframefiles = '<div class="a"><iframe src="iframefiles.php?id=inputimg" width="100%" height="300" style="border:0;">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>'+
'<div class="b">'+
'<button type="button" onclick="document.getElementById(\'inputimg\').value = \'/modules/news_categories/default.jpg\';closewindow(\'window\');">Вставить фото по умолчанию</button> '+
'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
'</div>';

function random(n)
{
	var r = '';
	var arr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	var al = arr.length
	for( var i=0; i < n; i++ ){
		r += arr[Math.floor(Math.random() * al)];
	}
	return r;
}

</script>

<?php

if($act=='ed_category'){
	
if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
require('../modules/'.$id_category.'/cfg.php');

    $link_data = file('../modules/news_categories/list.dat');
    $nom = count($link_data);
    for($i = 0; $i < $nom; ++$i){
    $link_cfg = explode('^',$link_data[$i]);
	if($id_category == $link_cfg[1]){
	$name_category = $link_cfg[0];	
	}
	}
	
echo'<div class="header"><h1>Управление категорией "'.$name_category.'"</h1></div>
<div class="menu_page">
        <a class="link" href="module.php?module='.$MODULE.'">Все категории</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=ed_category&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Добавление новости</a>
		<a class="link " href="module.php?module='.$MODULE.'&amp;act=edit&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Редактирование новостей</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Комментарии пользователей</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки категории</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=info&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">RSS информация</a>
	</div>';
	if(!file_exists('../data/storage/module.'.$id_category.'/newsConfig.dat')){
	echo'<div class="error">Не создан файл конфигурации в хранилище информации для этой категории, перейдите в <a href="module.php?module='.$MODULE.'&amp;act=cfg&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'&amp;id=config">настройки категории</a> и нажмите на кнопку "Сохранить".</div>';
	}
	echo'
<div class="content">
<h2>Добавление новости</h2>
		<form name="form_name" action="module.php?module='.$MODULE.'" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addnews">
		<INPUT TYPE="hidden" NAME="id_category" VALUE="'.$id_category.'">
		<INPUT TYPE="hidden" NAME="str_file" VALUE="'.$str_file.'">
		<table class="tblform">
		<tr>
			<td>Заголовок новости:</td>
			<td><input type="text" name="header" id="header" value=""></td>
		</tr>
		
		<tr>
			<td class="top">Превью новости:</td>
			<td><TEXTAREA NAME="prev" ROWS="20" COLS="100" style="height:150px;">'.htmlspecialchars('<p>Превью новости</p>').'</TEXTAREA></td>
		</tr>
		<tr>
			<td class="top">Содержимое новости:</td>
			<td><TEXTAREA NAME="content" ROWS="20" COLS="100" style="height:250px;">'.htmlspecialchars('<p>Содержимое новости</p>').'</TEXTAREA></td>
		</tr>
		<tr>
			<td>Разрешить комментирование</td>
			<td class="middle"><INPUT TYPE="checkbox" NAME="comments" VALUE="y"></td>
		</tr>
		<tr>
			<td>URL иллюстр. картинки:</td>
			<td>
				<input type="text" name="img" id="inputimg" value="/modules/news_categories/default.jpg"> 
				<button type="button" onClick="openwindow(\'window\', 750, \'auto\', iframefiles);">Выбрать файл</button>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><img src="/modules/news_categories/default.jpg" alt="" id="img" style="width: 380px;"></td>
		</tr>
		<tr>
			<td>Ключевые слова (keywords):</td>
			<td><input type="text" name="keywords" value=""></td>
		</tr>
		<tr>
			<td>Описание (description):</td>
			<td><input type="text" name="description" value=""></td>
		</tr>
		<tr>
			<td>Идентификатор (исп. для URL):</td>
			<td><input type="text" name="id" id="id" value="'.uniqid().'"><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из заголовка новости</a></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Добавить новость</button> &nbsp; <a href="module.php?module='.$MODULE.'">Вернуться назад</a></td>
		</tr>
		</table>
		</form>
		</div>';

		if($Config->wysiwyg){
				if(Module::isWysiwyg($Config->wysiwyg)){
					require Module::pathRun($Config->wysiwyg, 'wysiwyg');
				}
		}

}

if($act=='edit'){
if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
require('../modules/'.$id_category.'/cfg.php');

    $link_data = file('../modules/news_categories/list.dat');
    $nom = count($link_data);
    for($i = 0; $i < $nom; ++$i){
    $link_cfg = explode('^',$link_data[$i]);
	if($id_category == $link_cfg[1]){
	$name_category = $link_cfg[0];	
	}
	}


?>
<script type="text/javascript">
function dell(url, n, u){
return '<div class="a">Подтвердите удаление новости: <i>' + n + ' (<a href="//' + u + '" target="_blank">' + u + '</a>)</i></div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \''+url+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php
		echo'<div class="header"><h1>Управление категорией "'.$name_category.'"</h1></div>
		<div class="menu_page">
		<a class="link" href="module.php?module='.$MODULE.'">Все категории</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=ed_category&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Добавление новости</a>
		<a class="link " href="module.php?module='.$MODULE.'&amp;act=edit&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Редактирование новостей</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Комментарии пользователей</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки категории</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=info&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">RSS информация</a>
	    </div>
		<div class="content">
		<h2>Редактирование новостей</h2>';
		if(($listIdNews = json_decode($newsStorage->get('list'), true)) == false){
			echo'<div class="msg">Новостей ещё не создано</div>';
		}else{
			
			
			echo'<table class="tables">
			<tr>
				<td class="tables_head" colspan="2">Заголовок новостей</td>
				<td class="tables_head">URL</td>
				<td class="tables_head">Комментирование</td>
				<td class="tables_head">Дата</td>
				<td class="tables_head">&nbsp;</td>
			</tr>';
			
			//перевернули масив для вывода новостей в обратном порядке
			$listIdNews = array_reverse($listIdNews);
			
			//
			$nom = count($listIdNews);
			
			//определили количество страниц
			$kol_page = ceil($nom / 50); 
			
			//проверка правельности переменной с номером страницы
			if(isset($_GET['nom_page'])){$nom_page = $_GET['nom_page'];}else{ $nom_page = 1; }
			if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $kol_page){ $nom_page = 1; }
			
			//начало навигации
			if($nom_page > 0){$i = ($nom_page - 1) * 50;}
			$var = $i + 50;
			
			while($i < $var){
				if($i < $nom){
					if($newsStorage->iss('news_'.$listIdNews[$i])){
						$newsParam = json_decode($newsStorage->get('news_'.$listIdNews[$i]));
						
						$comments = ($newsParam->comments == '1')?'<span style="color: green;">Включено</span>':'<span style="color: red;">Выключено</span>';
						echo'<tr>
						<td class="img"><img src="../modules/news_categories/icon2.svg" alt=""></td>
						<td><a href="module.php?module='.$MODULE.'&amp;act=editnews&amp;news='.$listIdNews[$i].'&amp;nom_page='.$nom_page.'&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">'.$newsParam->header.'</a></td>
						<td><a href="//'.SERVER.'/'.$newsConfig->idPage.'/'.$listIdNews[$i].'" target="_blank">'.SERVER.'/'.$newsConfig->idPage.'/'.$listIdNews[$i].'</a></td>
						<td>'.$comments.'</td>
						<td>'.(isset($newsParam->time)?date($newsConfig->formatDate, $newsParam->time):$newsParam->date).'</td>
						<td><a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\'module.php?module='.$MODULE.'&amp;act=dell&amp;id_pages='.$newsConfig->idPage.'&amp;news='.$listIdNews[$i].'&amp;nom_page='.$nom_page.'&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'\', \''.$newsParam->header.'\', \''.SERVER.'/'.$newsConfig->idPage.'/'.$listIdNews[$i].'\'));">Удалить</a></td>
						</tr>';
					}else{
						echo'<tr>
						<td>&nbsp;</td>
						<td style="color: red;">Error</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\'module.php?module='.$MODULE.'&amp;act=dell&amp;id_pages='.$newsConfig->idPage.'&amp;news='.$listIdNews[$i].'&amp;nom_page='.$nom_page.'&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'\',\'Error\',\'Error\'));">Удалить</a></td>
						</tr>';
					}
				}
				++$i;
			}
			echo'</table>';
			
			//навигация по номерам страниц
			if($kol_page > 1){//Если количество страниц больше 1, то показываем навигацию
				echo'<div style="margin-top: 25px; text-align: center;">';
				echo'Страницы: ';
				for($i = 1; $i <= $kol_page; ++$i){
					if($nom_page == $i){
						echo'<b>('.$i.')</b> ';
					}else{
						echo'<a href="module.php?module='.$MODULE.'&amp;act=edit&amp;nom_page='.$i.'">'.$i.'</a> ';
					}
				}
				echo'</div>';
			}
			//конец навигации
		}
		echo'</div>';
}

if($act=='editnews'){
	
	    if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
        if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
        require('../modules/'.$id_category.'/cfg.php');
		
        $link_data = file('../modules/news_categories/list.dat');
        $nom = count($link_data);
        for($i = 0; $i < $nom; ++$i){
        $link_cfg = explode('^',$link_data[$i]);
	    if($id_category == $link_cfg[1]){
	    $name_category = $link_cfg[0];	
	    }
	    }		
		
		
		$news = htmlspecialchars(specfilter($_GET['news']));
		$nom_page = htmlspecialchars(specfilter($_GET['nom_page']));
		
		if(($newsParam = json_decode($newsStorage->get('news_'.$news))) != false){
			echo'<div class="header"><h1>Управление категорией "'.$name_category.'"</h1></div>
			<div class="menu_page">
		    <a class="link" href="module.php?module='.$MODULE.'">Все категории</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=ed_category&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Добавление новости</a>
		    <a class="link " href="module.php?module='.$MODULE.'&amp;act=edit&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Редактирование новостей</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Комментарии пользователей</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки категории</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=info&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">RSS информация</a>
	        </div>
			<div class="content">
			<h2>Редактирование новости "'.$newsParam->header.'"</h2>
			<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
            <INPUT TYPE="hidden" NAME="act" VALUE="addedit">
			<INPUT TYPE="hidden" NAME="id_category" VALUE="'.$id_category.'">
		    <INPUT TYPE="hidden" NAME="str_file" VALUE="'.$str_file.'">
      	    <INPUT TYPE="hidden" NAME="news" VALUE="'.$news.'">
			<INPUT TYPE="hidden" NAME="nom_page" VALUE="'.$nom_page.'">
			<input type="hidden" name="time" value="'.(isset($newsParam->time)?$newsParam->time:strtotime($newsParam->date)).'">
			
			<table class="tblform">
			<tr>
				<td>Заголовок новости:</td>
				<td><input type="text" name="header" id="header" value="'.$newsParam->header.'"></td>
			</tr>
			
			<tr>
				<td class="top">Превью новости:</td>
				<td><TEXTAREA NAME="prev" ROWS="20" COLS="100" style="height: 150px;">'.htmlspecialchars($newsParam->prev).'</TEXTAREA></td>
			</tr>
			<tr>
				<td class="top">Содержимое новости:</td>
				<td><TEXTAREA NAME="content" ROWS="20" COLS="100" style="height: 250px;">'.htmlspecialchars($newsParam->content).'</TEXTAREA></td>
			</tr>';
			$checked = ($newsParam->comments == 1)?'checked':'';
			echo'
			<tr>
				<td>Разрешить комментирование</td>
				<td class="middle"><INPUT TYPE="checkbox" NAME="comments" VALUE="y" '.$checked.'></td>
			</tr>
			<tr>
				<td>URL иллюстр. картинки:</td>
				<td>
					<input type="text" name="img" id="inputimg" value="'.$newsParam->img.'"> 
					<button type="button" onClick="openwindow(\'window\', 700, \'auto\', iframefiles);">Выбрать файл</button>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><img src="'.$newsParam->img.'" alt="" id="img" style="width: 380px;"></td>
			</tr>
			<tr>
				<td>Ключевые слова (keywords):</td>
				<td><input type="text" name="keywords" value="'.$newsParam->keywords.'"></td>
			</tr>
			<tr>
				<td>Описание (description):</td>
				<td><input type="text" name="description" value="'.$newsParam->description.'"></td>
			</tr>
			
			<tr>
				<td>Идентификатор (исп. для URL):</td>
				<td><input type="text" name="id" id="id" value="'.$news.'"><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из заголовка новости</a></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="module.php?module='.$MODULE.'&amp;act=edit&amp;nom_page='.$nom_page.'&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Вернуться назад</a></td>
			</tr>
			</table>
			</form>
			</div>';
			if($Config->wysiwyg){
				if(Module::isWysiwyg($Config->wysiwyg)){
					require Module::pathRun($Config->wysiwyg, 'wysiwyg');
				}
			}
		}else{
			echo'<div class="msg">Не удалось получить параметры записи</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=edit&nom_page=<?php echo $nom_page;?>&amp;id_category=<?php echo $id_category;?>&amp;str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php
		}

}

if($act=='addcfgcategory'){
	
	$id_categorys = htmlspecialchars(specfilter($_POST['id_categorys']));
    $amtpr_page = htmlspecialchars(specfilter($_POST['amtpr_page']));
	$nom_blok = htmlspecialchars(specfilter($_POST['nom_blok']));
    $txt_link = htmlspecialchars(specfilter($_POST['txt_link']));
    $news_style = ($_POST['news_style'] == 'y')?'1':'0';
	$lib_jquery = ($_POST['lib_jquery'] == 'j')?'1':'0';
	
$inset = '<?php
$id_categorys="'.$id_categorys.'";
$amtpr_page="'.$amtpr_page.'";
$nom_blok="'.$nom_blok.'";
$txt_link="'.$txt_link.'";
$news_style="'.$news_style.'";
$lib_jquery="'.$lib_jquery.'";
?>';	

filefputs('../modules/news_categories/cfg.dat', $inset, 'w+');
	
echo'<div class="msg">Настройки успешно сохранены</div>';
System::notification('Изменены настройки модуля Категории новостей');

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=cfg_category\';', 3000);
</script>
<?php
}

if($act=='cfg'){
	
	if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
	if(isset($_GET['id'])){$id = $_GET['id'];}else{$id = '';}
    if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
    require('../modules/'.$id_category.'/cfg.php');

        $link_data = file('../modules/news_categories/list.dat');
        $nom = count($link_data);
        for($i = 0; $i < $nom; ++$i){
        $link_cfg = explode('^',$link_data[$i]);
	    if($id_category == $link_cfg[1]){
	    $name_category = $link_cfg[0];	
	    }
	    }
	
		$checked = ($newsConfig->commentEngine == 1)?'checked':'';
		$checkedz = ($newsConfig->style == 1)?'checked':'';
		
		echo'<div class="header"><h1>Управление категорией "'.$name_category.'"</h1></div>
			<div class="menu_page">
		    <a class="link" href="module.php?module='.$MODULE.'">Все категории</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=ed_category&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Добавление новости</a>
		    <a class="link " href="module.php?module='.$MODULE.'&amp;act=edit&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Редактирование новостей</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Комментарии пользователей</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки категории</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=info&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">RSS информация</a>
	        </div>
		<div class="content">
		<h2>Настройки категории</h2>
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcfg">
		<INPUT TYPE="hidden" NAME="id_category" VALUE="'.$id_category.'">
		<INPUT TYPE="hidden" NAME="id" VALUE="'.$id.'">
		<INPUT TYPE="hidden" NAME="str_file" VALUE="'.$str_file.'">
		
		<table class="tblform">
		
		<tr>
			<td>Заголовк RSS канала:</td>
			<td><input type="text" name="name_rss" value="'.$newsConfig->name_rss.'" size="50"></td>
		</tr>
		
		<tr>
			<td>Количество превью записей на странице:</td>
			<td><input type="text" name="navigation" value="'.$newsConfig->navigation.'" maxlength="3"></td>
		</tr>

		<tr>
			<td>Количество превью при выводе в блоке:</td>
			<td><input type="text" name="countInBlok" value="'.$newsConfig->countInBlok.'" maxlength="3"></td>
		</tr>
		
		<tr>
			<td>Вид превью при выводе в блоке:</td>
			<td>
				<SELECT NAME="sort_prev">';
				if($newsConfig->sortPrev == 0){
				echo'<OPTION VALUE="0" selected>Только текст
				<OPTION VALUE="1">Текст и фото превью
				<OPTION VALUE="2">Только ссылки';
				}elseif($newsConfig->sortPrev == 1){
				echo'<OPTION VALUE="0">Только текст
				<OPTION VALUE="1" selected>Текст и фото превью
				<OPTION VALUE="2">Только ссылки';
				}elseif($newsConfig->sortPrev == 2){
				echo'<OPTION VALUE="0">Только текст
				<OPTION VALUE="1">Текст и фото превью
				<OPTION VALUE="2" selected>Только ссылки';	
				}
				echo'</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>Формат вывода даты (Формат функции date):</td>
			<td><input type="text" name="formatDate" value="'.$newsConfig->formatDate.'"></td>
		</tr>
		
		<tr>
			<td>Идентификатор страницы с новостями:</td>
			<td><input type="text" name="idPage" value="'.$newsConfig->idPage.'"></td>
		</tr>
		
		<tr>
			<td>Идентификатор страницы пользователей:</td>
			<td><input type="text" name="idUser" value="'.$newsConfig->idUser.'"></td>
		</tr>
		
		<tr>
			<td>Шаблон для вывода превью:</td>
			<td class="middle">'.(file_exists(Module::pathRun($Config->template, 'news.prev.template'))?'<a class="link" target="_blank" href="files.php?act=editor&amp;dir=../modules/'.$Config->template.'&file=../modules/'.$Config->template.'/news.prev.template.php">Открыть редактор для правки шаблона</a>':'<span class="comment">Не предусмотрен</span>').'</td>
		</tr>
		
		<tr>
			<td>Шаблон для вывода новости:</td>
			<td class="middle">'.(file_exists(Module::pathRun($Config->template, 'news.content.template'))?'<a class="link" target="_blank" href="files.php?act=editor&amp;dir=../modules/'.$Config->template.'&file=../modules/'.$Config->template.'/news.content.template.php">Открыть редактор для правки шаблона</a>':'<span class="comment">Не предусмотрен</span>').'</td>
		</tr>
		<tr>
			<td>Ссылка на фото превью:</td>
			<td>
				<input type="text" name="img_category" id="inputimg" value="'.$newsConfig->imgCategory.'"> 
				<button type="button" onClick="openwindow(\'window\', 750, \'auto\', iframefiles);">Выбрать файл</button>
			</td>
		</tr>
		<tr>
			<td>Фото превью:</td>
			<td><img src="'.$newsConfig->imgCategory.'" alt="" id="img" style="width: 380px;"></td>
		</tr>
		<tr>
			<td>Использовать собственный сервис комментариев</td>
			<td class="middle"><INPUT TYPE="checkbox" NAME="commentEngine" VALUE="y" id="checkbox" '.$checked.'></td>
		</tr>
		<tr id="trCommentTemplate">
			<td class="top">Код сервиса комментариев:<br><span class="comment">Подробнее о сервисах комментариев <a href="http://my-engine.ru/newscomments">тут</a></span></td>
			<td><TEXTAREA NAME="commentTemplate" id="textareaCommentTemplate" ROWS="20" COLS="100" style="height:150px;">'.htmlspecialchars($newsConfig->commentTemplate).'</TEXTAREA></td>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Сохранить</button></td>
		</tr>
		</table>
		</form>
		</div>';
		?>
		<script type="text/javascript">
		function checked(){
			document.getElementById('trCommentTemplate').style.display = (document.getElementById('checkbox').checked)?'none':'';
		}
		document.getElementById('checkbox').onclick  = function(){
			checked();
			if(!document.getElementById('checkbox').checked){
				document.getElementById('textareaCommentTemplate').focus();
			}
		}
		checked();
		</script>
		<?php
}

?>
<script type="text/javascript">
var inputimg = document.getElementById('inputimg');
var lastinputimg = inputimg.value;
setInterval(function(){
	if (inputimg.value != lastinputimg) {
		document.getElementById('img').src = inputimg.value;
		lastinputimg = inputimg.value;
	}
}, 500);
</script>
<?php

if($act=='comment'){
if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
require('../modules/'.$id_category.'/cfg.php');

        $link_data = file('../modules/news_categories/list.dat');
        $nom = count($link_data);
        for($i = 0; $i < $nom; ++$i){
        $link_cfg = explode('^',$link_data[$i]);
	    if($id_category == $link_cfg[1]){
	    $name_category = $link_cfg[0];	
	    }
	    }	
	
	
	
		function ptext($text){
			$text = str_replace("\n",'<br>',$text);
			$text = specfilter($text);
			return $text;
		}
		?>
		<script type="text/javascript">
		var dell = '<div class="a">Подтвердите удаление выделенных комментариев</div>' +
			'<div class="b">' +
			'<button type="button" onClick="submitDell();">Удалить</button> '+
			'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
			'</div>';
			
		var listDell = '<div class="a"><span class="r">Внимание!</span> Очистится только список в панели администратора, комментарии опубликованные на страницах останутся не тронутыми</div>' +
			'<div class="b">' +
			'<button type="button" onClick="window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=listdellcoment&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';">Очистить</button> '+
			'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
			'</div>';
			
		var wDell = '<div class="a"><span class="r">Внимание!</span> Список последних комментариев переполнен. Рекомендуется очистить список, что-бы разгрузить систему.</div>' +
			'<div class="b">' +
			'<button type="button" onClick="window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=listdellcoment&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';">Очистить сейчас</button> '+
			'<button type="button" onclick="closewindow(\'window\');">Закрыть</button>'+
			'</div>';
			
		function submitDell(){
			document.form.act.value = "dellcoment";
			form.submit();
		}
		</script>
		<?php
		
		echo'<div class="header"><h1>Управление категорией "'.$name_category.'"</h1></div>
			<div class="menu_page">
		    <a class="link" href="module.php?module='.$MODULE.'">Все категории</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=ed_category&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Добавление новости</a>
		    <a class="link " href="module.php?module='.$MODULE.'&amp;act=edit&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Редактирование новостей</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Комментарии пользователей</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки категории</a>
		    <a class="link" href="module.php?module='.$MODULE.'&amp;act=info&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">RSS информация</a>
	        </div>';
		
		if ($newsConfig->commentEngine){
			
			
			echo'
			<div class="content">
		    <h2>Комментарии пользователей</h2>';
			 		
			if(($lastComments = json_decode($newsStorage->get('lastComments'), true)) == false){
				echo'<div class="row"><a class="button" href="module.php?module='.$MODULE.'&amp;act=cfgcomment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки комментариев</a></div>
				<div class="msg">Нет ни одного комментария</div>';
			}else{
							
				echo'<form name="form" action="module.php?module='.$MODULE.'&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'" method="post">
					<INPUT TYPE="hidden" NAME="act" VALUE="pubcoment">
					<INPUT TYPE="hidden" NAME="id_category" VALUE="'.$id_category.'">
		            <INPUT TYPE="hidden" NAME="str_file" VALUE="'.$str_file.'">
					
					<div class="row">
					<input type="submit" name="" value="Опубликовать выделенное" title="Опубликовать выделенные комментарии">
					<button type="button" onClick="openwindow(\'window\', 650, \'auto\', dell);" title="Удалить выделенные комментарии">Удалить выделенное</button>
					<button type="button" onClick="openwindow(\'window\', 650, \'auto\', listDell);" title="Очистить список последних комментариев">Очистить список</button>
					<a class="link button" href="module.php?module='.$MODULE.'&amp;act=cfgcomment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки комментариев</a>
					</div>
				';
				
				//перевернули масив для вывода новостей в обратном порядке
				$lastComments = array_reverse($lastComments);
				
				//
				$nom = count($lastComments);
				
				if ($nom > 3000){
					echo'<script type="text/javascript">openwindow(\'window\', 650, \'auto\', wDell);</script>';
				}
				
				//определили количество страниц
				$kol_page = ceil($nom / 50); 
				
				//проверка правельности переменной с номером страницы
				if(isset($_GET['nom_page'])){$nom_page = $_GET['nom_page'];}else{ $nom_page = 1; }
				if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $kol_page){ $nom_page = 1; }
				
				//начало навигации
				if($nom_page > 0){$i = ($nom_page - 1) * 50;}
				$var = $i + 50;
				
				while($i < $var){
					if($i < $nom){
						
						
						echo'<div class="box">
							<div><INPUT TYPE="checkbox" NAME="comment[]" VALUE="'.$lastComments[$i]['idComment'].'"> '.($lastComments[$i]['published']?'':'<span class="r">Не опубликованно</span>').' Страница: <a href="//'.SERVER.'/'.$newsConfig->idPage.'/'.$lastComments[$i]['idNews'].'" target="_blank">'.SERVER.'/'.$newsConfig->idPage.'/'.$lastComments[$i]['idNews'].'</a></div>';
							if(file_exists('../modules/users/images/'.$lastComments[$i]['login'].'.jpg')){
		                    echo'<h3 class="av"><img src="/modules/users/images/'.$lastComments[$i]['login'].'.jpg" width="30" height="30" alt=""> <span>'.$lastComments[$i]['login'].'</span></h3>';
		                    }else{
							echo'<h3 class="av"><img src="/modules/news_categories/user.png" width="30" height="30" alt=""> <span>'.$lastComments[$i]['login'].'</span></h3>';
							}						
							echo''.NewsFormatText($lastComments[$i]['text']).'
							<div class="comment">Написанно '.human_time(time() - $lastComments[$i]['time']).' назад ( '.date("d.m.Y H:i", $lastComments[$i]['time']).' ) ; '.($lastComments[$i]['status']=='user'?'Зарегистрированный':'Гость').'; IP '.$lastComments[$i]['ip'].'</div>
						</div>';
						
					}
					++$i;
				}
				echo'<div class="row">
					<input type="submit" name="" value="Опубликовать выделенное" title="Опубликовать выделенные комментарии">
					<button type="button" onClick="openwindow(\'window\', 650, \'auto\', dell);" title="Удалить выделенные комментарии">Удалить выделенное</button>
					</div>
				</form>';
				
				//навигация по номерам страниц
				if($kol_page > 1){//Если количество страниц больше 1, то показываем навигацию
					echo'<div style="margin-top: 25px; text-align: center;">';
					echo'Страницы: ';
					for($i = 1; $i <= $kol_page; ++$i){
						if($nom_page == $i){
							echo'<b>('.$i.')</b> ';
						}else{
							echo'<a href="module.php?module='.$MODULE.'&amp;act=comment&amp;nom_page='.$i.'">'.$i.'</a> ';
						}
					}
					echo'</div>';
				}
				//конец навигации
				
			}
			echo'</div>';
			
		}else{
			echo'<div class="msg">Используется сторонний сервис комментариев</div>';
		}
			
}

if($act=='cfgcomment'){
if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
require('../modules/'.$id_category.'/cfg.php');
		
		echo'<div class="header"><h1>Комментарии пользователей</h1></div>
		'.$menu_page.'
		<div class="menu_page">
			<a href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">&#8592; Вернуться назад</a>
		</div>
		
		
		<div class="content">
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcfgcomment">
		<INPUT TYPE="hidden" NAME="id_category" VALUE="'.$id_category.'">
		<INPUT TYPE="hidden" NAME="str_file" VALUE="'.$str_file.'">
		<table class="tblform">
		<tr>
			<td>Работа комментариев:</td>
			<td>
				<SELECT NAME="commentEnable" >
					<OPTION VALUE="0" '.($newsConfig->commentEnable == '0'?'selected':'').'>Выключено
					<OPTION VALUE="1" '.($newsConfig->commentEnable == '1'?'selected':'').'>Включено
				</SELECT><br><span class="comment">Эта настройка глобальна для всех новостей</span>
			</td>
		</tr>
		
		<tr>
			<td>Кто может писать комментарии:</td>
			<td>
				<SELECT NAME="commentRules" >
					<OPTION VALUE="0" '.($newsConfig->commentRules == '0'?'selected':'').'>Все пользователи
					<OPTION VALUE="1" '.($newsConfig->commentRules == '1'?'selected':'').'>Только зарегистрированные пользователи
					<OPTION VALUE="2" '.($newsConfig->commentRules == '2'?'selected':'').'>Только пользователи с преференциями
					<OPTION VALUE="3" '.($newsConfig->commentRules == '3'?'selected':'').'>Только администратор
				</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>Модерация перед публикацией:</td>
			<td>
				<SELECT NAME="commentModeration" >
					<OPTION VALUE="0" '.($newsConfig->commentModeration == '0'?'selected':'').'>Не модерировать, публиковать сразу
					<OPTION VALUE="1" '.($newsConfig->commentModeration == '1'?'selected':'').'>Модерировать не зарегистрированных пользователей и новичков
					<OPTION VALUE="2" '.($newsConfig->commentModeration == '2'?'selected':'').'>Модерировать всех кроме пользователей с преференциями
				</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>Количество сообщений новичка:</td>
			<td><input type="text" name="commentModerationNumPost" value="'.$newsConfig->commentModerationNumPost.'">
			<br><span class="comment">Максимальное количество сообщений при котором пользователь считается новичком</span>
			</td>
		</tr>
		
		<tr>
			<td>Макс. символов для одного комментария:</td>
			<td><input type="text" name="commentMaxLength" value="'.$newsConfig->commentMaxLength.'"></td>
		</tr>
		
		<tr>
			<td>Кол-во выводимых комментариев за раз:</td>
			<td><input type="text" name="commentNavigation" value="'.$newsConfig->commentNavigation.'"></td>
		</tr>
		
		<tr>
			<td>Макс. комментариев для одной новости:</td>
			<td><input type="text" name="commentMaxCount" value="'.$newsConfig->commentMaxCount.'"></td>
		</tr>
		
		<tr>
			<td>Задержка на проверку новых комментарий:</td>
			<td><input type="text" name="commentCheckInterval" value="'.$newsConfig->commentCheckInterval.'">
			<br><span class="comment">Задержка указывается в милисекундах. Если указать "0", то проверка на наличие новых комментариев выполняться не будет.</span>
			</td>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Вернуться назад</a></td>
		</tr>
		</table>
		</form>
		</div>';
}
		
if($act=='info'){
if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
require('../modules/'.$id_category.'/cfg.php');

        $link_data = file('../modules/news_categories/list.dat');
        $nom = count($link_data);
        for($i = 0; $i < $nom; ++$i){
        $link_cfg = explode('^',$link_data[$i]);
	    if($id_category == $link_cfg[1]){
	    $name_category = $link_cfg[0];	
	    }
	    }

		echo'<div class="header"><h1>Управление категорией "'.$name_category.'"</h1></div>
		<div class="menu_page">
		<a class="link" href="module.php?module='.$MODULE.'">Все категории</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=ed_category&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Добавление новости</a>
		<a class="link " href="module.php?module='.$MODULE.'&amp;act=edit&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Редактирование новостей</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Комментарии пользователей</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">Настройки категории</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=info&amp;id_category='.$id_category.'&amp;str_file='.$str_file.'">RSS информация</a>
	</div>
		<div class="content">
		<h2>RSS информация</h2>
			<p>Ваш RSS канал новостей находится по адресу <a href="/'.$newsConfig->idPage.'/rss.xml" target="_blank">'.SERVER.'/'.$newsConfig->idPage.'/rss.xml</a></p>
			<p>Для корректной работы с некоторыми агрегаторами, необходимо чтобы в <a href="setting.php" target="_blank">настройках движка</a> были разрешены "Произвольные GET параметры".</p>
			<p>RSS канал новостей был разработан согласно документации <a href="https://zen.yandex.ru/" target="_blank">Яндекс.Дзен</a> и <a href="https://pulse.mail.ru/" target="_blank">Pulse.Mail.ru</a>. Вы можете без проблем подключить свой сайт к этим агрегаторам.</p>
		</div>';
}

if($act=='addcategory'){
	
$name_category = htmlspecialchars(specfilter($_POST['name_category']));	
$id_page = htmlspecialchars(specfilter($_POST['id_page']));
	
if(trim($name_category)){
$f=fopen('../modules/news_categories/stat.dat','a+');
flock($f,LOCK_EX);
$count=fread($f,100);
@$count++;
ftruncate($f,0);
fwrite($f,$count);
fflush($f);
flock($f,LOCK_UN);
fclose($f);	
$idn=file_get_contents('../modules/news_categories/stat.dat');	
$id_category = 'news'.$idn.'';	
if(file_exists('../modules/news_categories/list.dat')){
$kod1 = ''.$name_category.'^'.$id_category.'^'.$id_page.'^';
$fp=fopen('../modules/news_categories/list.dat','a+');
fputs($fp,$kod1."\n");
fclose($fp);	
$dir = mkdir('../modules/'.$id_category.'/');
$dir = mkdir('../files/news_categories/'.$id_category.'/');
$dir = mkdir('../data/storage/module.'.$id_category.'/');
$file1 = file_get_contents('../modules/news_categories/files/integration_page.php');
file_put_contents('../modules/'.$id_category.'/integration_page.php', $file1);
$file2 = file_get_contents('../modules/news_categories/files/integration_blok.php');
file_put_contents('../modules/'.$id_category.'/integration_blok.php', $file2);
$file3 = file_get_contents('../modules/news_categories/files/cfg.php');
file_put_contents('../modules/'.$id_category.'/cfg.php', $file3);
$kod2 = 'name = "Категория новостей «'.$name_category.'»"
version = 2.0
developer = "<span style=\'color:green;\'>Расширение «Категории новостей»</span>"
site = ""
delete = 0
description = "<p>Модуль создан автоматически с помощью расширения «Категории новостей».<br>
Управление модулем осуществляется через административную панель управления<br> 
расширением «Категории новостей». <a href=\'module.php?module=news\'>Перейти в панель управления</a></p>"
';
$kod3 = '<?php
$module_news="module.'.$id_category.'";
$id_cat="'.$id_category.'";
$name_cat="'.$name_category.'";
$url_cat="'.$id_page.'";
?>';
filefputs('../modules/'.$id_category.'/info.ini', $kod2, 'w+');
filefputs('../modules/'.$id_category.'/cfg.dat', $kod3, 'w+');

$kod4 = 'var Comments={run:function(e){this.id=e.id,this.newCommentCheckInterval=e.newCommentCheckInterval,this.commentMaxLength=e.commentMaxLength,this.ticket_'.$id_category.'=e.ticket_'.$id_category.',this.loginFormCheck(),this.textFormCheck(),this.loadComments(0)},commentDellCheck:function(){var e=document.querySelectorAll(\'input[type="checkbox"]:checked\'),t=document.getElementById("commentDellButton");e.length>0?(t.innerHTML="Удалить выделенное ("+e.length+")",t.style.display=""):t.style.display="none"},commentDell:function(){var e=document.getElementById("commentDellButton");e.innerHTML="Удаление...";for(var t=new FormData,n=document.querySelectorAll(\'input[type="checkbox"]:checked\'),o=0;o<n.length;++o){t.append(n[o].name,n[o].value);var m=document.getElementById("comment"+n[o].value);m.parentNode.removeChild(m)}return t.append("ticket_'.$id_category.'",this.ticket_'.$id_category.'),JLoader.request(this.id+"/ajax/dellcomments",{data:t,success:function(t){"Error"!=t?e.innerHTML="Готово":e.innerHTML="Ошибка",setTimeout(function(){Comments.commentDellCheck()},3e3)},error:function(){e.innerHTML="Ошибка"}}),!1},toUser:function(e){window.location="#commentForm";var t=document.getElementById("textForm");t.value+="[b]"+e+"[/b], ",t.focus(),t.setSelectionRange(t.value.length,t.value.length)},clearCommentForm:function(){document.getElementById("loginForm")&&(document.getElementById("loginForm").value=""),document.getElementById("textForm")&&(document.getElementById("textForm").value=""),document.getElementById("captchaForm")&&(document.getElementById("captchaForm").value="",document.getElementById("captcha").src="/modules/captcha/captcha.php?"+Math.random())},loginFormCheck:function(){if(document.getElementById("loginForm")){var e=document.getElementById("loginForm"),t=document.getElementById("loginReport"),n=!1;e.oninput=function(){n&&clearTimeout(n);var o=setTimeout(function(){if(""!=e.value)if(e.value.length<36){var n=new FormData;n.append("login",e.value),JLoader.request(Comments.id+"/ajax/validlogin",{data:n,responseToId:"loginReport"})}else t.innerHTML="Превышен лимит символов";else t.innerHTML=""},2e3);n=o}}},textFormCheck:function(){if(document.getElementById("textForm")){var e=document.getElementById("textForm"),t=document.getElementById("textReport");e.oninput=function(){e.value.length>Comments.commentMaxLength?t.innerHTML="Превышен лимит символов":t.innerHTML=""}}},requestReport:function(e,t,n){if(document.getElementById("requestReport")){var o=document.getElementById("requestReport");o.innerHTML=e,o.style.display="",o.className=t+" animatShow",n&&setTimeout(function(){o.className+=" animatHide"},n)}},requestReportHide:function(){document.getElementById("requestReport").style.display="none"},submitCommentForm:function(){this.requestReport("Отправка...","grey"),document.getElementById("commentDellButton")&&(document.getElementById("commentDellButton").style.display="none");var e=new FormData(document.commentForm);return JLoader.request(this.id+"/ajax/addcomment",{data:e,success:function(e){Comments.clearCommentForm(),isNaN(e)?("Moderation"==e&&Comments.requestReport("Сообщение отправлено на модерацию","green"),"Captcha"==e&&Comments.requestReport("Символы на картинке не совпадают","red"),"Exists"==e&&Comments.requestReport("Логин уже существует","red"),"Error"==e&&Comments.requestReport("Ошибка при добавлении сообщения","red"),"Ticket_'.$id_category.'"==e&&Comments.requestReport("Ошибка при проверке безопасности","red"),"Ban"==e&&Comments.requestReport("Сообщения с вашего ip заблокированы","red")):(Comments.newCommentCheckApply(e),Comments.requestReport("Сообщение успешно добавлено","green")),setTimeout(function(){Comments.loadComments(0)},1e3)},error:function(e){Comments.requestReport("Произошла ошибка при запросе к серверу","red")}}),!1},loadComments:function(e){if(document.getElementById("loadCommentsButton")){var t=document.getElementById("loadCommentsButton");t.parentNode.removeChild(t)}document.getElementById("commentDellButton")&&(document.getElementById("commentDellButton").style.display="none");var n={};0==e?n.responseToId="comments":n.responseToIdAdd="comments",n.error=function(e){Comments.requestReport("Произошла ошибка при запросе к серверу","grey")},n.success=function(e){Comments.newCommentCheck(0)},JLoader.request(this.id+"/ajax/loadcomments/"+e,n)},newCommentCheck:function(e){0!=this.newCommentCheckInterval&&JLoader.request(this.id+"/ajax/newcommentcheck",{success:function(t){if(0!=e){if(e<t){var n=t-e;Comments.requestReport("Есть новые сообщения ("+n+\') <a href="javascript:void(0);"  onClick="Comments.newCommentShow();">Показать</a>\',"grey")}e>t&&Comments.newCommentCheckApply(t)}else Comments.newCommentCheckApply(t)}})},newCommentCheckApply:function(e){0!=this.newCommentCheckInterval&&(this.newCommentCheckIntervalId&&clearInterval(this.newCommentCheckIntervalId),this.newCommentCheckIntervalId=setInterval(function(){Comments.newCommentCheck(e)},this.newCommentCheckInterval))},newCommentShow:function(){this.requestReportHide(),this.loadComments(0)}};';

filefputs('../modules/'.$id_category.'/comments.min.js', $kod4, 'w+');

        $pages = System::listPages();
	    $nom = count($pages);
		
	    for($i=0;$i<$nom;$i++){
			 		
		if($pages[$i] == $id_page || !trim($id_page)){				

		    System::notification('Созданаи категория новостей "'.$name_category.'" с идентификатором '.$id_category.'', 'g');
            echo'<div class="msg">Категория успешно создана</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php			
			exit;
	    }
			
		}		
		$page = $id_page;
		
		Page::add(
				$page, 
				$name_category, 
				$name_category, 
				'', 
				'', 
				1, 
				$id_category, 
				'def/template', 
				'');
			if(is_dir('../breadcrumbs')){	
		    $cod1 = ''.$page.'<||>'.$name.'<||>'.time().''.mt_rand(0, 1000).'<||>';					
		    $fp=fopen('../modules/breadcrumbs/links.dat','a+');
            fputs($fp,$cod1."\n");
            fclose($fp);	    
		    }

        System::notification('Создана новая страница с идентификатором '.$page.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$page, 'g');

System::notification('Созданаи категория новостей "'.$name_category.'" с идентификатором '.$id_category.'', 'g');
echo'<div class="msg">Категория успешно создана</div>';
}else{
echo'<div class="msg">Ошибка</div>';
}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}else{
echo'<div class="msg">Введите название темы</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=new_category\';', 3000);
</script>
<?php
}
}

if($act=='dell'){
	
if(isset($_GET['category'])){
$category = $_GET['category'];
if(is_dir('../modules/'.$category.'/')){
	
    $fopen=@file('../modules/news_categories/list.dat');
    foreach($fopen as $key=>$value){  
    if(substr_count($value,$category)){
    array_splice($fopen, $key, 1);
    }
    }

    $f=fopen('../modules/news_categories/list.dat', 'w');
    for($i=0;$i<count($fopen);$i++){
    fwrite($f,$fopen[$i]);
    }
    fclose($f);
	
	if(is_dir('../files/'.$category.'')){delldir('../files/'.$category.'/');}
	if(is_dir('../modules/'.$category.'')){delldir('../modules/'.$category.'/');}
	System::notification('Удалена категория новостей с идентификатором "'.$category.'"', 'g');
	echo'<div class="msg">Категория удалена</div>';
	}else{
	echo'<div class="msg">Ошибка! Такой категории не существует</div>';
	}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
    }
	
if(isset($_GET['id_category'])){
$id_category = $_GET['id_category'];
$str_file = $_GET['str_file'];
require('../modules/'.$id_category.'/cfg.php');

$news = htmlspecialchars(specfilter($_GET['news']));
		$nom_page = htmlspecialchars(specfilter($_GET['nom_page']));
		if($newsStorage->delete('news_'.$news)){ // Удадляем новость
			
			//Удаляем страницу из списка
			$listIdNews = json_decode($newsStorage->get('list'), true); // Получили список ввиде массива
			if(($key = array_search($news, $listIdNews)) !== false){
				unset($listIdNews[$key]); // Удалили найденый элемент массива
			}
			$listIdNews = array_values($listIdNews); // Переиндексировали числовые индексы 
			$newsStorage->set('list', json_encode($listIdNews)); // Записали массив в виде json
			
			$newsStorage->delete('comments_'.$news); // Удаляем комментарии
			$newsStorage->delete('count_'.$news); // Удаляем счетчик комментариев
			
			System::notification('Удалена новость с идентификатором '.$news.'', 'g');
			echo'<div class="msg">Новость успешно удалена</div>';
		}else{
			System::notification('Ошибка при удалении новости с идентификатором '.$news.', страница не найдена или запрос некорректен', 'r');
			echo'<div class="msg">Ошибка при удалении новости</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=edit&nom_page=<?php echo $nom_page; ?>&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php	
	
	
}
}

if($act=='up_link'){
$str_file = htmlspecialchars(specfilter($_GET['str_file']));
if(is_numeric($str_file)){
$links_list = file('../modules/news_categories/list.dat');
$nom = count($links_list);
if($str_file > 0){
$up_str_file = $str_file - 1;
$tmp_str = $links_list[$up_str_file];//Верхнюю строку сохраняем в временную переменную
$links_list[$up_str_file] = $links_list[$str_file];//Верхнюю строку заменяем на нижнюю
$links_list[$str_file] = $tmp_str;//нижнюю заменяем на верхнюю которая была сохранена
//перезаписываем файл
$f = fopen('../modules/news_categories/list.dat', 'w+');
for($i = 0; $i < $nom; ++$i){
fputs($f,$links_list[$i]);
}
fclose($f);
echo'<div class="msg">Категория успешно перенесена</div>';
}else{
echo'<div class="msg">Ошибка</div>';
}
}else{
echo'<div class="msg">Ошибка</div>';
}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 2000);
</script>
<?php
}

if($act=='down_link'){
$str_file = htmlspecialchars(specfilter($_GET['str_file']));
if(is_numeric($str_file)){
$links_list = file('../modules/news_categories/list.dat');
$nom = count($links_list);
if($str_file < ($nom - 1)){
$down_str_file = $str_file + 1;
$tmp_str = $links_list[$down_str_file];//Нижнюю строку сохраняем во временную переменную
$links_list[$down_str_file] = $links_list[$str_file];//Нижнюю строку заменяем на верхнюю
$links_list[$str_file] = $tmp_str;//верхнюю заменяем на нижнюю, которая была сохранена
//перезаписываем файл
$f = fopen('../modules/news_categories/list.dat', 'w+');
for($i = 0; $i < $nom; ++$i){
fputs($f,$links_list[$i]);
}
fclose($f);
echo'<div class="msg">Категория успешно перенесена</div>';
}else{
echo'<div class="msg">Ошибка</div>';
}
}else{
echo'<div class="msg">Ошибка</div>';
}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 2000);
</script>
<?php
}


	if($act=='addnews'){
		
		$id_category = htmlspecialchars(specfilter($_POST['id_category']));
		$str_file = htmlspecialchars(specfilter($_POST['str_file']));
		
		require('../modules/'.$id_category.'/cfg.php');
		
		$param = array();
		$param['header'] = ($_POST['header'] == '')?'Без названия':htmlspecialchars(specfilter($_POST['header']));
		$param['keywords'] = htmlspecialchars(specfilter($_POST['keywords']));
		$param['description'] = htmlspecialchars(specfilter($_POST['description']));
		$param['img'] = htmlspecialchars(specfilter($_POST['img']));
		$param['prev'] = $_POST['prev'];
		$param['content'] = $_POST['content'];
		//$param['date'] = htmlspecialchars(specfilter($_POST['date'])); // удалено в 5.1.14
		$param['comments'] = ($_POST['comments'] == 'y')?'1':'0';
		// 5.1.14
		$param['time'] = time();
		$param['date'] = date($newsConfig->formatDate, $param['time']);

		$id = ($newsStorage->iss('news_'.$_POST['id']) == false && System::validPath($_POST['id']))?$_POST['id']:uniqid();
		
		if($newsStorage->set('news_'.$id, json_encode($param))){
			
			// Добавляем ID новости в список
			$listIdNews = json_decode($newsStorage->get('list'), true); // Получили список ввиде массива
			$listIdNews[] = $id;// Добавили новый элемент массива в конец
			$newsStorage->set('list', json_encode($listIdNews)); // Записали массив в виде json
			
			file_put_contents('../data/storage/module.'.$id_category.'/views_'.$_POST['id'].'.dat', '0');
			file_put_contents('../data/storage/module.'.$id_category.'/yes_'.$_POST['id'].'.dat', '0');
			file_put_contents('../data/storage/module.'.$id_category.'/no_'.$_POST['id'].'.dat', '0');
			
			echo'<div class="msg">Новость успешно добавлена</div>';
			
			System::notification('Добавлена новость с заголовком: '.$param['header']);
			
		}else{
			
			echo'<div class="msg">Произошла ошибка при добавлении новости</div>';
			
			System::notification('Произошла ошибка при добавлении новости', 'r');
			
		}
		
	 
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=ed_category&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php	
	}


if($act=='addedit'){
	
	    $id_category = htmlspecialchars(specfilter($_POST['id_category']));
		$str_file = htmlspecialchars(specfilter($_POST['str_file']));
		
		require('../modules/'.$id_category.'/cfg.php');
		 
		$news = htmlspecialchars(specfilter($_POST['news']));
		$nom_page = htmlspecialchars(specfilter($_POST['nom_page']));
		$id_news = htmlspecialchars(specfilter($_POST['id'])); // Новый id для новости
		

		
			if(($newsParam = json_decode($newsStorage->get('news_'.$news))) != false){
				
				$newsParam->header = ($_POST['header'] == '')?'Без названия':htmlspecialchars(specfilter($_POST['header']));
				$newsParam->img = htmlspecialchars(specfilter($_POST['img']));
				$newsParam->keywords = htmlspecialchars(specfilter($_POST['keywords']));
				$newsParam->description = htmlspecialchars(specfilter($_POST['description']));
				$newsParam->prev = $_POST['prev'];
				$newsParam->content = $_POST['content'];
				//$param['date'] = htmlspecialchars(specfilter($_POST['date'])); // удалено в 5.1.14
				$newsParam->comments = ($_POST['comments'] == 'y')?'1':'0';
				// 5.1.14
				if(!isset($newsParam->time)){
					$newsParam->time = strtotime($newsParam->date);
					$newsParam->date = date($newsConfig->formatDate, $newsParam->time);
				}
				
				
				if($newsStorage->set('news_'.$news, json_encode($newsParam))){
					if($id_news != $news){
						if($newsStorage->iss('news_'.$id_news) == false && System::validPath($id_news)){
							
							if($newsStorage->set('news_'.$id_news, json_encode($newsParam)) == false){
								System::notification('Ошибка при записи ключа news_'.$id_news.'', 'r');
							}
							
							if($newsStorage->delete('news_'.$news) == false){
								System::notification('Ошибка при удалении ненужного ключа news_'.$news.'', 'r');
							}
							
							// Замена страницы в списке
							$listIdNews = json_decode($newsStorage->get('list'), true); // Получили список ввиде массива
							if(($key = array_search($news, $listIdNews)) !== false){
								$listIdNews[$key] = $id_news; // Заменили найденый элемент массива
							}
							$listIdNews = array_values($listIdNews); // Переиндексировали числовые индексы 
							$newsStorage->set('list', json_encode($listIdNews)); // Записали массив в виде json
							
							System::notification('Отредактирована новость со сменой идентификатора '.$news.' на идентификатор '.$id_news.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_news.'/'.$id_news, 'g');
							echo'<div class="msg">Новость успешно сохранена</div>';
						}else{
							System::notification('Отредактирована новость с неудачной попыткой смены идентификатора '.$news.' на идентификатор '.$id_news.', идентификатор '.$id_news.' уже существует или некорректен, ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_news.'/'.$news, 'g');
							echo'<div class="msg">Новость сохранена но идентификатор изменить не удалось</div>';
						}
					}else{
						System::notification('Отредактирована новость с идентификатором '.$id_news.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_news.'/'.$id_news, 'g');
						echo'<div class="msg">Новость успешно сохранена</div>';
					}
				}else{
					System::notification('Ошибка при сохранении страницы с идентификатором '.$news.', ошибка записи', 'r');
					echo'<div class="msg">Ошибка при сохранении страницы</div>';
					
				}
			}else{
				System::notification('Ошибка при сохранении страницы с идентификатором '.$news.', страница ненайдена', 'r');
				echo'<div class="msg">Неудалось получить параметры записи</div>';
			}
		
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=edit&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php
}

if($act=='addcfg'){
	
	$id_category = htmlspecialchars(specfilter($_POST['id_category']));
	$id = htmlspecialchars(specfilter($_POST['id']));
	$str_file = htmlspecialchars(specfilter($_POST['str_file']));
		
	require('../modules/'.$id_category.'/cfg.php');
		
		if( !is_numeric($_POST['navigation']) || 
			!is_numeric($_POST['countInBlok']) || 
			$_POST['formatDate'] == ''||
			!System::validPath($_POST['idPage']) || 
			!System::validPath($_POST['idUser'])
		){
			echo'<div class="msg">Не все поля заполнены, или заполнены неправильно</div>';
		}else{
            $newsConfig->name_rss = htmlspecialchars(specfilter($_POST['name_rss']));			
			$newsConfig->navigation = htmlspecialchars(specfilter($_POST['navigation']));
			$newsConfig->countInBlok = htmlspecialchars(specfilter($_POST['countInBlok']));
			$newsConfig->formatDate = htmlspecialchars(specfilter($_POST['formatDate']));
			$newsConfig->idPage = htmlspecialchars(specfilter($_POST['idPage']));;
			$newsConfig->idUser = htmlspecialchars(specfilter($_POST['idUser']));;
			$newsConfig->imgCategory = htmlspecialchars(specfilter($_POST['img_category']));;
			$newsConfig->prevTemplate = $_POST['prevTemplate'];
			$newsConfig->contentTemplate = $_POST['contentTemplate'];
			$newsConfig->commentTemplate = $_POST['commentTemplate'];
			$newsConfig->sortPrev = htmlspecialchars(specfilter($_POST['sort_prev']));
			$newsConfig->commentEngine = ($_POST['commentEngine'] == 'y')?'1':'0';
			$newsConfig->style = ($_POST['style'] == 'z')?'1':'0';
			
			
			if($newsStorage->set('newsConfig', json_encode($newsConfig))){
				if($id == 'config'){
				echo'<div class="msg">Файл конфигурации хранилища создан.</div>';
				System::notification('Изменена конфигурация хранилища информации');
			    }else{
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены параметры модуля новостей');
				}
			}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении параметров модуля новостей', 'r');
			}
			
			
		}
if($id == 'config'){
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=ed_category&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php
}else{
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=cfg&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php	
}	
}

if($act=='addcfgcomment'){
	
	$id_category = htmlspecialchars(specfilter($_POST['id_category']));
	$str_file = htmlspecialchars(specfilter($_POST['str_file']));
		
	require('../modules/'.$id_category.'/cfg.php');
		
		if( !is_numeric($_POST['commentEnable'])||
			!is_numeric($_POST['commentRules'])||
			!is_numeric($_POST['commentModeration'])||
			!is_numeric($_POST['commentModerationNumPost'])||
			!is_numeric($_POST['commentMaxLength'])||
			!is_numeric($_POST['commentNavigation'])||
			!is_numeric($_POST['commentMaxCount'])||
			!is_numeric($_POST['commentCheckInterval'])){
			echo'<div class="msg">Не все поля заполнены, или заполнены неправильно</div>';
		}else{ 
			
			$newsConfig->commentEnable = $_POST['commentEnable'];
			$newsConfig->commentRules = $_POST['commentRules'];
			$newsConfig->commentModeration = $_POST['commentModeration'];
			$newsConfig->commentModerationNumPost = $_POST['commentModerationNumPost'];
			$newsConfig->commentMaxLength = $_POST['commentMaxLength'];
			$newsConfig->commentNavigation = $_POST['commentNavigation'];
			$newsConfig->commentMaxCount = $_POST['commentMaxCount'];
			$newsConfig->commentCheckInterval = $_POST['commentCheckInterval'];
			
			if($newsStorage->set('newsConfig', json_encode($newsConfig))){
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены параметры комментарий модуля новостей');
			}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении параметров комментарий модуля новостей', 'r');
			}
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=cfgcomment&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php	
}

if($act=='pubcoment'){
	    
		$id_category = htmlspecialchars(specfilter($_POST['id_category']));
	    $str_file = htmlspecialchars(specfilter($_POST['str_file']));
		
	    require('../modules/'.$id_category.'/cfg.php');
	
		// Даже и не пытайтесь разобраться ;)
		if(($lastComments = json_decode($newsStorage->get('lastComments'), true)) == false){
				echo'<div class="msg">Ошибка. Нет ни одного сообщения.</div>';
		}elseif(!isset($_POST['comment'])){
			echo'<div class="msg">Ошибка. Нет выбранных элементов.</div>';
		}else{
			$addComment = array();
			$countPP = 0;
			foreach($lastComments as $key => $value){
				if(in_array($value['idComment'], $_POST['comment']) && $value['published'] == 0){
						++$countPP;
						$lastComments[$key]['published'] = 1;
						$addComment[$value['idNews']][] = array(
													'id' => $value['idComment'],
													'login' => $value['login'],
													'text' => $value['text'],
													'ip' => $value['ip'],
													'status' => $value['status'],
													'time' => $value['time']);
				}
			}
			$newsStorage->set('lastComments', json_encode($lastComments));
			unset($lastComments);
			
			
			foreach($addComment as $key => $value){
				$arrayComments = json_decode($newsStorage->get('comments_'.$key), true);
				
				foreach($value as $row){
					$arrayComments[] = $row;
					
					if(($CUser = User::getConfig($row['login'])) != false){
						++$CUser->numPost;
						User::setConfig($row['login'], $CUser);
					}
				}
				
				
				$arrayCount = count($arrayComments);
				if($arrayCount >= $newsConfig->commentMaxCount){
					$arrayStart = $arrayCount -  round($newsConfig->commentMaxCount / 1.5);
					$arrayComments = array_slice($arrayComments, $arrayStart, $arrayCount);
				}
				
				if($newsStorage->set('comments_'.$key, json_encode($arrayComments))){
					
					$count = $newsStorage->iss('count_'.$key)?$newsStorage->get('count_'.$key):0;
					$count+= $countPP;
					$newsStorage->set('count_'.$key, $count);
					
				}
			}
			echo'<div class="msg">Публикация успешно завершена</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=comment&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php	
}

if($act=='dellcoment'){
	
	    if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
        if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
        require('../modules/'.$id_category.'/cfg.php');
	
		if(($lastComments = json_decode($newsStorage->get('lastComments'), true)) == false){
				echo'<div class="msg">Ошибка. Нет ни одного сообщения.</div>';
		}else{
			$dellComment = array();
			foreach($lastComments as $key => $value){
				if(in_array($value['idComment'], $_POST['comment'])){
					$dellComment[$value['idNews']][] = $value['idComment'];
					unset($lastComments[$key]);
				}
			}
			// Переиндексировали числовые индексы 
			$lastComments = array_values($lastComments); 
			$newsStorage->set('lastComments', json_encode($lastComments));
			unset($lastComments);
			
			
			foreach($dellComment as $key => $value){
				$arrayComments = json_decode($newsStorage->get('comments_'.$key), true);
				foreach($arrayComments as $i => $row){
					if (in_array($row['id'], $value)){
						unset($arrayComments[$i]);
					}
				}
				// Переиндексировали числовые индексы 
				$arrayComments = array_values($arrayComments); 
				$newsStorage->set('comments_'.$key, json_encode($arrayComments));
			}
			echo'<div class="msg">Удаление успешно завершено</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=comment&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php	
}

if($act=='listdellcoment'){
	
	    if(isset($_GET['id_category'])){$id_category = $_GET['id_category'];}
        if(isset($_GET['str_file'])){$str_file = $_GET['str_file'];}
        require('../modules/'.$id_category.'/cfg.php');
	
		$newsStorage->set('lastComments', json_encode(array()));
		echo'<div class="msg">Очистка успешно завершена</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=comment&id_category=<?php echo $id_category;?>&str_file=<?php echo $str_file;?>\';', 3000);
</script>
<?php	
}

if($act=='addin1'){
		
		$file1 = file_get_contents('../modules/news_categories/file/uninstall.php');
        file_put_contents('uninstall.php', $file1);
		file_put_contents('../system/classes/dco.dat', '');

		
		$dir = mkdir('../files/news_categories');
				
echo'
		<div class="header">
			<h1>Инициализация модуля</h1>
		</div>
		
		<div class="content">		
		<div class="msg">
		<img src="../modules/news_categories/file/busy.gif" alt=""><br><br>
		Подождите! Проводится инициализация модуля.
		</div>
		</div>
		';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'uninstall.php?&module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}

if($act=='addin2'){
		
		$file1 = file_get_contents('../modules/news_categories/tmp/news.blok.template.php');
        file_put_contents('../modules/'.$Config->template.'/news.blok.template.php', $file1);
		$file2 = file_get_contents('../modules/news_categories/tmp/news.content.template.php');
        file_put_contents('../modules/'.$Config->template.'/news.content.template.php', $file2);
		$file3 = file_get_contents('../modules/news_categories/tmp/news.prev.template.php');
        file_put_contents('../modules/'.$Config->template.'/news.prev.template.php', $file3);
		$file4 = file_get_contents('../modules/news_categories/tmp/news.blok.php');
        file_put_contents('../modules/'.$Config->template.'/news.blok.php', $file4);

				
echo'<div class="msg">Компоненты успешно загружены.</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}
?>