<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

require('cfg.dat');

$info = parse_ini_file('../modules/mod_articles/info.ini');
if(empty($info['name'])){$info['name'] = $dir;}
?>
<script type="text/javascript">
var iframefiles = '<div class="a"><iframe src="iframefiles.php?id=inputimg" width="100%" height="300" style="border:0;">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>'+
'<div class="b">'+
'<button type="button" onclick="document.getElementById(\'inputimg\').value = \'/modules/mod_articles/default.jpg\';closewindow(\'window\');">Вставить фото по умолчанию</button> '+
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
if($act=='info'){

$arr_file = file('../modules/mod_articles/data/list.dat');
$arr_file = array_reverse($arr_file);
$nom = count($arr_file);
if($nom == 0){
		echo'<div class="header"><h1>RSS информация</h1></div>
		<div class="menu_page"><a href="module.php?module=mod_articles">&#8592; Вернуться назад</a> | <a href="module.php?module=mod_articles&act=settings">Настройки модуля</a> | <a class="link" href="module.php?module=mod_articles&act=info">RSS информация</a></div>
	    <div class="content">
		    <p>RSS канал формируется автоматически. Сейчас не создано ни одной статьи, поэтому RSS канал еще не сформирован.</p>
		</div>';
	}else{
		echo'<div class="header"><h1>RSS информация</h1></div>
		<div class="menu_page"><a href="module.php?module=mod_articles">&#8592; Вернуться назад</a> | <a href="module.php?module=mod_articles&act=settings">Настройки модуля</a> | <a class="link" href="module.php?module=mod_articles&act=info">RSS информация</a></div>
	    <div class="content">
		    <p>RSS канал: <b>'.$name_rss.'</b></p>
			<p>RSS канал находится по адресу <a href="/'.$direct_article.'/rss_articles.xml" target="_blank">'.SERVER.'/'.$direct_article.'/rss_articles.xml</a></p>
			<p>Для корректной работы с некоторыми агрегаторами, необходимо чтобы в <a href="setting.php" target="_blank">настройках движка</a> были разрешены "Произвольные GET параметры".</p>
			<p>RSS канал был разработан согласно документации <a href="https://zen.yandex.ru/" target="_blank">Яндекс.Дзен</a> и <a href="https://pulse.mail.ru/" target="_blank">Pulse.Mail.ru</a>. Вы можете без проблем подключить свой сайт к этим агрегаторам.</p>
		</div>';		
	}

}

if($act=='index'){
if(is_dir('../modules/mod_articles/file')){
echo'<div class="header"><h1>Инициализация модуля</h1></div>
<div class="content">
<p>Все компоненты модуля установлены. Для начала работы модуля необходимо выполнить инициализацию.</p>
<form name="settingform" action="module.php?module=mod_articles" method="post">
<INPUT TYPE="hidden" NAME="act" VALUE="addin">
<input type="submit" name="" value="Начать инициализацию">
</form>
</div>';
}else{	
?>
<script type="text/javascript">
function dellarticle(direct_article, nom_file){
return '<div class="a">Подтвердите удаление статьи</div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'module.php?module=mod_articles&act=dell_article&direct_article='+direct_article+'&nom_file='+nom_file+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php
	echo'<div class="header"><h1>Управление модулем "'.$info['name'].'"</h1></div>
	<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a> | <a href="module.php?module=mod_articles&act=settings">Настройки модуля</a> | <a class="link" href="module.php?module=mod_articles&act=info">RSS информация</a></div>
	<div class="content">
    <h2>Список статей</h2>
	
	<div class="row">
		<form name="settingform" action="module.php?module=mod_articles&act=article_search" method="post">
		<input style="width: 250px;" type="text" name="q" value="" placeholder="Поиск по заголовку" autofocus>
		<input type="submit" name="" value="Поиск"> 
		</form>
   </div>
	
	
	<table class="tables">
    <tr>
    <td class="tables_head" colspan="2">Информация о статье</td>
	<td class="tables_head">Ссылка на страницу</td>
	<td class="tables_head">Статус</td>
    <td class="tables_head" style="text-align: right;"><a href="module.php?module=mod_articles&act=add_article" class="button addlink" title="Добавить новость">Добавить статью</a></td>
    </tr>';
$arr_file = file('../modules/mod_articles/data/list.dat');
$arr_file = array_reverse($arr_file);
$nom = count($arr_file);
if($nom == 0){
echo'<tr>
<td colspan="5" style="text-align:left;">Статьи еще не созданы!</td>
</tr>';
}else{
$kol_page = ceil($nom / $amtpr_admin); 
if(isset($_GET['nom_page'])){$nom_page = $_GET['nom_page'];}else{ $nom_page = 1; }
if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $kol_page){ $nom_page = 1; }
if($nom_page > 0){$i = ($nom_page - 1) * $amtpr_admin;}
$var = $i + $amtpr_admin;
while($i < $var){
if($i < $nom){
$nom_file = trim($arr_file[$i]);
require('../modules/mod_articles/data/cfg_'.$nom_file.'.dat');
echo'<tr>
<td class="img"><img src="../modules/mod_articles/icon.svg" alt=""></td>
<td>
<b>Заголовок:</b> '.$rubric.'<br>
<b>Дата:</b> '.$data_article.'
</td>
<td><a href="//'.SERVER.'/'.$direct_article.'/'.$nom_file.'" target="_blank">'.SERVER.'/'.$direct_article.'/'.$nom_file.'</a></td>
<td>';
if($show_article == 1){
echo '<span style="color:#00C40D;">Опубликована</span>';
}else{
echo '<span style="color:#f00;">Не опубликована</span>';	
}
echo'</td>
<td>
<a href="module.php?module=mod_articles&act=editor&nom_file='.$nom_file.'" title="Редактировать статью">Редактировать</a> &nbsp;
<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dellarticle(\''.$direct_article.'\', \''.$nom_file.'\'));" title="Удалить статью">Удалить</a>
</td>
</tr>';
}
++$i;
}	
}
echo'</table>
</form>';
	
if($kol_page > 1){	  
echo'
<nav id="pagination">
<ul class="pagination">';
$a = $nom_page - 9;
$b = $nom_page + 9;
$c = $nom_page - 1;
$d = $nom_page + 1;
$x = ceil($nom / $amtpr_admin);
$y = $x + 1;
$z = $nom_page;
if($c > 1){
$pagination = 'module.php?module=mod_articles&nom_page='.$c.'';
}else{
$pagination = 'module.php?module=mod_articles';	
}			
if($z < 2){
echo'<li class="disabled"><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}else{
echo'<li><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}
while($a <= $b){
if(($a > 0) && ($a <= $kol_page)){
if($nom_page == $a){
if($a == 1){
echo'<li class="active"><a href="module.php?module=mod_articles">'.$a.'</a></li>';
}else{
echo'<li class="active"><a href="module.php?module=mod_articles&nom_page='.$a.'">'.$a.'</a></li>';	
}
}else{
if($a == 1){
echo'<li><a href="module.php?module=mod_articles">'.$a.'</a></li>';
}else{
echo'<li><a href="module.php?module=mod_articles&nom_page='.$a.'">'.$a.'</a></li>';	
}
}
}
++$a;
}	
if($y > $d){
echo'<li><a href="module.php?module=mod_articles&nom_page='.$d.'" aria-label="Next">&raquo;</a></li>';
}else{
echo'<li class="disabled"><a href="module.php?module=mod_articles&nom_page='.$nom_page.'" aria-label="Next">&raquo;</a></li>';
}			
echo'</ul>
</nav>';
}	
	
	echo'</div>';
}
}

if($act=='article_search'){
?>
<script type="text/javascript">
function dellarticle(direct_article, nom_file){
return '<div class="a">Подтвердите удаление статьи</div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'module.php?module=mod_articles&act=dell_article&direct_article='+direct_article+'&nom_file='+nom_file+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php	
if(isset($_POST['q'])){$q = $_POST['q'];}
$result = 0;
echo'<div class="header"><h1>Управление модулем "'.$info['name'].'"</h1></div>
<div class="menu_page"><a href="module.php?module=mod_articles">&#8592; Вернуться назад</a> | <a href="module.php?module=mod_articles&act=settings">Настройки модуля</a> | <a class="link" href="module.php?module=mod_articles&act=info">RSS информация</a></div>
<div class="content">';
if(trim($q)){
echo'<h2>Результат поиска</h2>';
$arr_file = file('../modules/mod_articles/data/list.dat');
$arr_file = array_reverse($arr_file);
$nom = count($arr_file);

for($i=0;$i<$nom ;$i++)
{
$nom_file = trim($arr_file[$i]);
require('../modules/mod_articles/data/cfg_'.$nom_file.'.dat');
if($q == $rubric){
echo'<table class="tables">
<tr>
<td class="tables_head" colspan="2">Информация о статье</td>
<td class="tables_head">Статус</td>
<td class="tables_head"></td>
</tr>
<tr>
<td class="img"><img src="../modules/mod_articles/icon.svg" alt=""></td>
<td>
<b>Заголовок:</b> '.$rubric.'<br>
<b>Дата:</b> '.$data_article.'
</td>
<td>';
if($show_article == 1){
echo '<span style="color:#00C40D;">Опубликована</span>';
}else{
echo '<span style="color:#f00;">Не опубликована</span>';	
}
echo'</td>
<td>
<a href="module.php?module=mod_articles&act=editor&nom_file='.$nom_file.'" title="Редактировать статью">Редактировать</a> &nbsp;
<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dellarticle(\''.$direct_article.'\', \''.$nom_file.'\'));" title="Удалить статью">Удалить</a>
</td>
</tr>';
$result = 1;	
}
}
echo'</table>
</form>';
if($result == 0){
echo'<p>Ничего не найдено</p>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}
}else{
echo'<div class="msg">Ошибка! Поле поиска пустое.</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php		
}	
echo'</div>';
}

if($act=='dell_article'){
if(isset($_GET['nom_file'])){$nom_file = $_GET['nom_file'];}



if(file_exists('../modules/mod_articles/data/cfg_'.$nom_file.'.dat')){unlink('../modules/mod_articles/data/cfg_'.$nom_file.'.dat');}
if(file_exists('../modules/mod_articles/data/content_'.$nom_file.'.dat')){unlink('../modules/mod_articles/data/content_'.$nom_file.'.dat');}
if(file_exists('../modules/mod_articles/data/prev_'.$nom_file.'.dat')){unlink('../modules/mod_articles/data/prev_'.$nom_file.'.dat');}
if(file_exists('../modules/mod_articles/seo/description_'.$nom_file.'.dat')){unlink('../modules/mod_articles/seo/description_'.$nom_file.'.dat');}
if(file_exists('../modules/mod_articles/seo/keywords_'.$nom_file.'.dat')){unlink('../modules/mod_articles/seo/keywords_'.$nom_file.'.dat');}

    $fopen = file('../modules/mod_articles/data/list.dat');
    foreach($fopen as $key=>$value){  
    if(substr_count($value,$nom_file)){
    array_splice($fopen, $key, 1);
    }
    }

    $f=fopen('../modules/mod_articles/data/list.dat', 'w');
    for($i=0;$i<count($fopen);$i++){
    fwrite($f,$fopen[$i]);
    }
    fclose($f);
	
echo'<div class="msg">Статья успешно удалена</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php	
}

if($act=='add_article'){

echo'<div class="header"><h1>Управление модулем "'.$info['name'].'"</h1></div>
<div class="menu_page"><a href="module.php?module=mod_articles">&#8592; Вернуться назад</a> | <a href="module.php?module=mod_articles&act=settings">Настройки модуля</a> | <a class="link" href="module.php?module=mod_articles&act=info">RSS информация</a></div>
<div class="content">
<h2>Добавление статьи</h2>
<p>&nbsp;</p>
<form name="forma" action="module.php?module=mod_articles" method="post">
<input type="hidden" name="act" value="addarticle">
<input type="hidden" name="amt_words" value="20">
<input type="hidden" name="show_article" value="1">
<input type="hidden" name="direct_article" value="'.$direct_article.'">
<table class="tblform">
<tr>
<td>Заголовок:</td>
<td><input type="text" name="rubric" id="header" value="" size="25"></td>
</tr>
<tr>
<td>Ключевые слова (keywords):</td>
<td><input type="text" name="keywords" value=""></td>
</tr>
<tr>
<td>Описание (description):</td>
<td><input type="text" name="description" value=""></td>
</tr>
</tr>
<tr>
<td></td>
<td><span class="r">Внимание! Чтобы статья попала в индекс поиска по сайту поля "Ключевые слова" и "Описание" должны быть заполнены.</span></td>
</tr>
<tr>
<td colspan="2">
<p>Содержание статьи:</p>
<textarea name="content" rows="20" cols="100" style="height:250px;"></textarea></td>
</tr>
<tr>
<td>Ссылка на фото превью:</td>
<td>
<input type="text" name="link_img" id="inputimg" value="/modules/mod_articles/default.jpg"> 
<button type="button" onClick="openwindow(\'window\', 750, \'auto\', iframefiles);">Выбрать файл</button>
</td>
</tr>
<tr>
<td><p>Фото превью:</p>
<img src="/modules/mod_articles/default.jpg" alt="" id="img" style="width: 380px;"></td>
<td><select name="show_img" style="margin-top:90px;">
<option value="1" selected>Включено
<option value="0">Отключено</select></td>
</tr>
<tr>
<td>Количество слов в превью:</td>
<td><input type="text" name="amt_words" value="25" size="50" style="width:45px;"></td>
</tr>
<tr>
<td>Статус:</td>
<td><select name="show_article">
<option value="1" selected>Опубликована
<option value="0">Не опубликована
</select>
</td>
</tr>
<tr>
<td>Идентификатор:</td>
<td><input type="text" name="id_article" id="id" value="'.uniqid().'"><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из заголовка новости</a></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="" value="Сохранить"> &nbsp; <a href="module.php?module=mod_articles">Вернуться назад</a></td>
</tr>
</table>
</form>
</div>';

}

if($act=='editor'){
	
if(isset($_GET['nom_file'])){$nom_file = $_GET['nom_file'];}
require('../modules/mod_articles/data/cfg_'.$nom_file.'.dat');
filefputs('../modules/mod_articles/data/prev_'.$new_id_article.'.dat', $newcr, 'w+');
$keywords = file_get_contents('../modules/mod_articles/seo/keywords_'.$nom_file.'.dat');
$description = file_get_contents('../modules/mod_articles/seo/description_'.$nom_file.'.dat');

echo'<div class="header"><h1>Управление модулем "'.$info['name'].'"</h1></div>
<div class="menu_page"><a href="module.php?module=mod_articles">&#8592; Вернуться назад</a> | <a href="module.php?module=mod_articles&act=settings">Настройки модуля</a> | <a class="link" href="module.php?module=mod_articles&act=info">RSS информация</a></div>
<div class="content">
<h2>Редактирование статьи "'.$rubric.'"</h2>
<p>&nbsp;</p>
<form name="forma" action="module.php?module=mod_articles" method="post">
<input type="hidden" name="act" value="ed_article">
<input type="hidden" name="direct_article" value="'.$direct_article.'">
<input type="hidden" name="id_article" value="'.$nom_file.'">
<input type="hidden" name="data_article" value="'.$data_article.'">
<input type="hidden" name="data_for_rss" value="'.$data_for_rss.'">
<input type="hidden" name="time_for_rss" value="'.$time_for_rss.'">
<table class="tblform">
<tr>
<td>Заголовок:</td>
<td><input type="text" name="rubric" value="'.$rubric.'" id="header" size="25"></td>
</tr>
<tr>
<td>Ключевые слова (keywords):</td>
<td><input type="text" name="keywords" value="'.$keywords.'"></td>
</tr>
<tr>
<td>Описание (description):</td>
<td><input type="text" name="description" value="'.$description.'"></td>
</tr>';
if(!trim($keywords) && !trim($description)){
echo'<tr>
<td></td>
<td><span class="r">Внимание! Чтобы статья попала в индекс поиска по сайту поля "Ключевые слова" и "Описание" должны быть заполнены.</span></td>
</tr>';
}
echo'<tr>
<td colspan="2">
<p>Содержание статьи:</p>
<textarea name="content" rows="20" cols="100" style="height:250px;">'.file_get_contents('../modules/mod_articles/data/content_'.$nom_file.'.dat').'</textarea></td>
</tr>
<tr>
<td>Ссылка на фото превью:</td>
<td>
<input type="text" name="link_img" value="'.$link_img.'" id="inputimg"> 
<button type="button" onClick="openwindow(\'window\', 750, \'auto\', iframefiles);">Выбрать файл</button>
</td>
</tr>
<tr>
<td><p>Фото превью:</p>
<img src="'.$link_img.'" alt="" id="img" style="width: 380px;"></td>
<td><select name="show_img" style="margin-top:90px;">';
if($show_img == 1){
echo'<option value="1" selected>Включено
<option value="0">Отключено';
}elseif($show_img == 0){
echo'<option value="1">Включено
<option value="0" selected>Отключено';
}
echo'</select></td>
</tr>
<tr>
<td>Количество слов в превью:</td>
<td><input type="text" name="amt_words" value="'.$amt_words.'" size="50" style="width:45px;"></td>
</tr>
<tr>
<td>Статус:</td>
<td><select name="show_article">';
if($show_article == 1){
echo'<option value="1" selected>Опубликована
<option value="0">Не опубликована';
}elseif($show_article == 0){
echo'<option value="1">Опубликована
<option value="0" selected>Не опубликована';
}
echo'</select>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="" value="Сохранить"> &nbsp; <a href="module.php?module=mod_articles">Вернуться назад</a> | <a href="/'.$direct_article.'/'.$nom_file.'" target="_blank">Перейти на страницу</a></td>
</tr>
</table>
</form>
</div>';
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
if($Config->wysiwyg){
	if(Module::isWysiwyg($Config->wysiwyg)){
		require Module::pathRun($Config->wysiwyg, 'wysiwyg');
	}
}

if($act=='settings'){
		
$check_nav = ($mod_style == 1)?' checked':'';	
	
echo'<div class="header"><h1>Управление модулем "'.$info['name'].'"</h1></div>
<div class="menu_page"><a href="module.php?module=mod_articles">&#8592; Вернуться назад</a> | <a href="module.php?module=mod_articles&act=settings">Настройки модуля</a> | <a class="link" href="module.php?module=mod_articles&act=info">RSS информация</a></div>
<div class="content">
<h2>Настройки модуля</h2>
<p>&nbsp;</p>
<form name="forma" action="module.php?module=mod_articles" method="post">
<input type="hidden" name="act" value="addsettings">
<table class="tblform">
<tr>
<td>Идентификатор страницы с модулем:</td>
<td><input type="text" name="direct_article" value="'.$direct_article.'" size="50"></td>
</tr>
<tr>
<td>Заголовк RSS канала:</td>
<td><input type="text" name="name_rss" value="'.$name_rss.'" size="50"></td>
</tr>
<tr>
<td>Количество превью:</td>
<td></td>
</tr>
<tr>
<td>- На странице превью статей</td>
<td><input type="text" name="amtpr_page" value="'.$amtpr_page.'" size="50" style="width:45px;"></td>
</tr>
<tr>
<td>- В боковом блоке</td>
<td><input type="text" name="amtpr_blok" value="'.$amtpr_blok.'" size="50" style="width:45px;"></td>
</tr>
<tr>
<td>- В админпанели</td>
<td><input type="text" name="amtpr_admin" value="'.$amtpr_admin.'" size="50" style="width:45px;"></td>
</tr>
<tr>
<tr>
<td>Текст кнопки перехода к статье:</td>
<td><input type="text" name="link_next" value="'.$link_next.'" size="50"></td>
</tr>
<tr>
<td>Текст кнопки для перехода назад:</td>
<td><input type="text" name="link_back" value="'.$link_back.'" size="50"></td>
</tr>
<tr>
<td>Вывод копок на странице статьи:</td>
<td><select name="show_btn">';
if($show_btn == 1){
echo'<option value="1" selected>Включен
<option value="0">Отключен';
}elseif($show_btn == 0){
echo'<option value="1">Включен
<option value="0" selected>Отключен';
}
echo'</select>
</td>
</tr>
<tr>
<td>Вывод даты:</td>
<td><select name="show_data">';
if($show_data == 1){
echo'<option value="1" selected>Включен
<option value="0">Отключен';
}elseif($show_data == 0){
echo'<option value="1">Включен
<option value="0" selected>Отключен';
}
echo'</select>
</td>
</tr>
<tr>
<td>Использовать стили шаблона:</td>
<td class="middle"><input type="checkbox" value="z" name="mod_style" '.$check_nav.'></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="" value="Сохранить"></td>
</tr>
</table>
</form>
</div>';
}

if($act=='addsettings'){

    $direct_article = htmlspecialchars(specfilter($_POST['direct_article']));
	$name_rss = htmlspecialchars(specfilter($_POST['name_rss']));
    $amtpr_page = htmlspecialchars(specfilter($_POST['amtpr_page']));
	$amtpr_blok = htmlspecialchars(specfilter($_POST['amtpr_blok']));
    $amtpr_admin = htmlspecialchars(specfilter($_POST['amtpr_admin']));	
	$link_next = htmlspecialchars(specfilter($_POST['link_next']));
    $link_back = htmlspecialchars(specfilter($_POST['link_back']));
	$show_btn = htmlspecialchars(specfilter($_POST['show_btn']));
	$show_data = htmlspecialchars(specfilter($_POST['show_data']));
	$mod_style = ($_POST['mod_style'] == 'z')?'1':'0';
	
$inset = '<?php
$direct_article="'.$direct_article.'";
$name_rss="'.$name_rss.'";
$amtpr_page="'.$amtpr_page.'";
$amtpr_blok="'.$amtpr_blok.'";
$amtpr_admin="'.$amtpr_admin.'";
$link_next="'.$link_next.'";
$link_back="'.$link_back.'";
$show_btn="'.$show_btn.'";
$show_data="'.$show_data.'";
$mod_style="'.$mod_style.'";
?>';	

filefputs('../modules/mod_articles/cfg.dat', $inset, 'w+');
	
echo'<div class="msg">Настройки успешно сохранены</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=mod_articles&act=settings\';', 3000);
</script>
<?php
}

if($act=='addarticle'){

    $new_rubric = htmlspecialchars(specfilter($_POST['rubric']));
    $new_data_article = date("d.m.Y");
	$new_data_for_rss = date("D, d M Y");
	$new_time_for_rss = date("H:i:s O");
	$new_content = $_POST['content'];
    $new_keywords = htmlspecialchars(specfilter($_POST['keywords']));
    $new_description = htmlspecialchars(specfilter($_POST['description']));	
	$new_id_article = htmlspecialchars(specfilter($_POST['id_article']));
    $new_link_img = htmlspecialchars(specfilter($_POST['link_img']));
    $new_show_img = htmlspecialchars(specfilter($_POST['show_img']));
	$new_amt_words = htmlspecialchars(specfilter($_POST['amt_words']));
    $new_show_article = htmlspecialchars(specfilter($_POST['show_article']));
    
$inset = '<?php
$rubric="'.$new_rubric.'";
$data_article="'.$new_data_article.'";
$data_for_rss="'.$new_data_for_rss.'";
$time_for_rss="'.$new_time_for_rss.'";
$link_img="'.$new_link_img.'";
$show_img="'.$new_show_img.'";
$amt_words="'.$new_amt_words.'";
$show_article="'.$new_show_article.'";
?>';

$new_prev_txt = preg_replace('/<img (.*?)>/', '', $new_content);
$new_prev_txt = trim(preg_replace("/[\r\n]+/m"," ", $new_prev_txt));
$new_prev_txt = str_replace('<p>', '', $new_prev_txt);
$new_prev_txt = str_replace('</p>', '', $new_prev_txt);				 
$newcr = ''; 
$newm = explode(' ', $new_prev_txt);
$space = ' ';
for ($i=0; $i<$new_amt_words; $i++){$newcr = $newcr.$space.$newm[$i];}	

filefputs('../modules/mod_articles/data/list.dat', $new_id_article."\n", 'a+');	
filefputs('../modules/mod_articles/data/cfg_'.$new_id_article.'.dat', $inset, 'w+');
filefputs('../modules/mod_articles/data/prev_'.$new_id_article.'.dat', $newcr, 'w+');
filefputs('../modules/mod_articles/data/content_'.$new_id_article.'.dat', $new_content, 'w+');
filefputs('../modules/mod_articles/seo/keywords_'.$new_id_article.'.dat', $new_keywords, 'w+');
filefputs('../modules/mod_articles/seo/description_'.$new_id_article.'.dat', $new_description, 'w+');

	
echo'<div class="msg">Статья успешно добавлена</div>';


?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=mod_articles\';', 3000);
</script>
<?php
}

if($act=='ed_article'){

    $new_rubric = htmlspecialchars(specfilter($_POST['rubric']));
    $new_data_article = htmlspecialchars(specfilter($_POST['data_article']));
	$new_data_for_rss = htmlspecialchars(specfilter($_POST['data_for_rss']));
	$new_time_for_rss = htmlspecialchars(specfilter($_POST['time_for_rss']));
	$new_content = $_POST['content'];
    $new_keywords = htmlspecialchars(specfilter($_POST['keywords']));
    $new_description = htmlspecialchars(specfilter($_POST['description']));	
	$new_id_article = htmlspecialchars(specfilter($_POST['id_article']));
    $new_link_img = htmlspecialchars(specfilter($_POST['link_img']));
    $new_show_img = htmlspecialchars(specfilter($_POST['show_img']));
	$new_amt_words = htmlspecialchars(specfilter($_POST['amt_words']));
    $new_show_article = htmlspecialchars(specfilter($_POST['show_article']));
    
$inset = '<?php
$rubric="'.$new_rubric.'";
$data_article="'.$new_data_article.'";
$data_for_rss="'.$new_data_for_rss.'";
$time_for_rss="'.$new_time_for_rss.'";
$link_img="'.$new_link_img.'";
$show_img="'.$new_show_img.'";
$amt_words="'.$new_amt_words.'";
$show_article="'.$new_show_article.'";
?>';

$new_prev_txt = preg_replace('/<img (.*?)>/', '', $new_content);
$new_prev_txt = trim(preg_replace("/[\r\n]+/m"," ", $new_prev_txt));
$new_prev_txt = str_replace('<p>', '', $new_prev_txt);
$new_prev_txt = str_replace('</p>', '', $new_prev_txt);				 
$newcr = ''; 
$newm = explode(' ', $new_prev_txt);
$space = ' ';
for ($i=0; $i<$new_amt_words; $i++){$newcr = $newcr.$space.$newm[$i];}	
	
filefputs('../modules/mod_articles/data/cfg_'.$new_id_article.'.dat', $inset, 'w+');
filefputs('../modules/mod_articles/data/prev_'.$new_id_article.'.dat', $newcr, 'w+');
filefputs('../modules/mod_articles/data/content_'.$new_id_article.'.dat', $new_content, 'w+');
filefputs('../modules/mod_articles/seo/keywords_'.$new_id_article.'.dat', $new_keywords, 'w+');
filefputs('../modules/mod_articles/seo/description_'.$new_id_article.'.dat', $new_description, 'w+');
	
echo'<div class="msg">Изменения сохранены</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=mod_articles&act=editor&nom_file=<?php echo $new_id_article;?>\';', 3000);
</script>
<?php
}

if($act=='addin'){
	
		$file1 = file_get_contents('../modules/mod_articles/file/uninstall.php');
        file_put_contents('uninstall.php', $file1);
		
		$dir = mkdir('../files/mod_articles/');
		
echo'
		<div class="header">
			<h1>Инициализация модуля</h1>
		</div>
		
		<div class="content">		
		<div class="msg">
		<img src="../modules/mod_articles/file/busy.gif" alt=""><br><br>
		Подождите! Проводится инициализация модуля.
		</div>
		</div>
		';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'uninstall.php?&module=mod_articles\';', 3000);
</script>
<?php
}
?>