<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

// Функция преобразования символов в нижний регистр
function my_StrToLower($string) {
	return function_exists('mb_strtolower') ? mb_strtolower($string, 'UTF-8') : strtolower($string);
	}

// Функция транслитерации
function my_Translit($string) {
	$string = (string) $string; // convert to string value
	$string = strip_tags($string); // remove HTML tags
	$string = str_replace(array('\n', '\r'), ' ', $string); // remove the carriage return
	$string = preg_replace('/\s+/', ' ', $string); // remove duplicate spaces
	$string = trim($string); // remove spaces at the beginning and end of the line
	$string = my_StrToLower($string); // translate the string to lowercase (sometimes you need to set the locale)
	$string = strtr($string, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
	$string = preg_replace('/[^0-9a-z-_ \.]/i', '', $string); // clear the string of invalid characters
	$count = substr_count($string, '.');
	$string = preg_replace('/\./', '', $string, --$count); // remove all points except the last one
	$string = str_replace(' ', '-', $string); // replace spaces with a minus sign
	return $string;
	}

// Функция удаления директории (категории)
function my_rmRec($path) {
  if (is_file($path)) return unlink($path);
  if (is_dir($path)) {
    foreach(scandir($path) as $p) if (($p!='.') && ($p!='..'))
      my_rmRec($path.DIRECTORY_SEPARATOR.$p);
    return rmdir($path);
    }
  return false;
  }

// Функция перемещения
function my_array_move(&$a, $oldpos, $newpos) {
	if ($oldpos==$newpos) {return;}
	array_splice($a,max($newpos,0),0,array_splice($a,max($oldpos,0),1));
}

// Функция вывода
function Get_PagesCategory($Who = '', $block = '') {
 global $Config;
 $arr = file(DR.'/modules/admin_pages_category/data/cat-ids-names.dat');
 $ids = array();
 $names = array();

  for ($i = 0, $count_arr = count($arr); $i < $count_arr; $i++)
	{
     $tmp = explode('|', $arr[$i]);
	 $ids[] = trim($tmp[0]);
	 $names[] = trim($tmp[1]);
	 $site_checkbox[] = trim($tmp[2]);
	 $admin_checkbox[] = trim($tmp[3]);
	}

  $content = '';
  if ($Who == 'admin') {
  $content .= '<form method=\'post\' id=\'pc-cfg-form\' action=\'pages.php?\' style=\'display: inline;\'>';
  }
  for ($i = 0, $count_arr = count($arr); $i < $count_arr; $i++) {
	$arr[$i] = trim($arr[$i]);

	// подкатегории
	if (file_exists(DR.'/modules/admin_pages_category/data/'.$ids[$i].'/sub-ids-names.dat')) {
	  $sub = file(DR.'/modules/admin_pages_category/data/'.$ids[$i].'/sub-ids-names.dat');
	}
	else {
	 $sub = array();
	}
	 $subids = array();
	 $subnames = array();
	 for ($f = 0, $count_sub = count($sub); $f < $count_sub; $f++)
	  {
	   $tmp = explode('|', $sub[$f]);
	   $subids[] = trim($tmp[0]);
	   $subnames[] = trim($tmp[1]);
	  }

	 $count_subnames = count($sub);
	 if ($Who == 'admin') {
	 $content .= '<b><a style=\"font-size: 115%;\" href=\"javascript:void(0);\" onclick=\"openwindow(\'window\', 400, \'auto\', rename_cat_form(\''.$names[$i].'\',\''.$ids[$i].'\'));\">'.$names[$i].'</b></a> ('.$count_subnames.') <span><a href=\"javascript:void(0);\" onclick=\"openwindow(\'window\', 400, \'auto\', my_array_move(\'pages.php?&amp;act=dir_move&amp;do=up&amp;name_cat='.$arr[$i].'\'));\">вверх</a> | <a href=\"javascript:void(0);\" onclick=\"openwindow(\'window\', 400, \'auto\', my_array_move(\'pages.php?&amp;act=dir_move&amp;do=down&amp;name_cat='.$arr[$i].'\'));\">вниз</a> | <a href=\"javascript:void(0);\" onclick=\"openwindow(\'window\', 400, \'auto\', my_array_move(\'pages.php?&amp;act=array_move_delete&amp;name_cat='.$arr[$i].'\'));\">удалить</a> | <span class=\'comment\'>Открыть спойлер: <input type=\'checkbox\' name=\'site_checkbox_'.$i.'\' value=\'checked\' '.$site_checkbox[$i].'> на сайте &nbsp; <input type=\'checkbox\' name=\'admin_checkbox_'.$i.'\' value=\'checked\' '.$admin_checkbox[$i].'> в админке</span> </span><br>';
	 }
	 /*
	 else { $content .= '
	 <div class="category-pages"><p class="name-category-pages">'.$names[$i].' <span class="name-category-pages-count">('.$count_subnames.')<span><p></div>';
	 }
	 */

	 if ($count_subnames != 0) {
	 if ($Who == 'admin') {
	 $content .= '<input type=\"checkbox\" id=\"spoiler'.$i.'\" '.$admin_checkbox[$i].'> <label for=\"spoiler'.$i.'\">&harr;</label><div class=\"spoiler\">';
	 }
	 else {
	 $content .= '<div class="category-pages"><input type="checkbox" id="spoiler'.$ids[$i].'-'.$i.''.$block.'" '.$site_checkbox[$i].'> <label class="category-pages-name" for="spoiler'.$ids[$i].'-'.$i.''.$block.'">'.$names[$i].' <span class="category-pages-count">('.$count_subnames.')</span></label><div class="spoiler">';
	 }
	 }
		// подкатегории, вывод
		for ($j = 0; $j < $count_subnames; $j++) {

		   $id = $subids[$j]; if ($id == '') { $id = $Config->indexPage; }

		   if ($Who == 'admin') {
		   $content .= '<span><b> &nbsp; <a href=\"editor.php?page='.$id.'\">'.$subnames[$j].'</a></b> | <a href=\"/'.($id == $Config->indexPage ? '' : $id).'\" target=\"_blank\">'.$id.'</a> | <a href=\"javascript:void(0);\" onclick=\"openwindow(\'window\', 400, \'auto\', my_array_move(\'pages.php?&amp;act=subpage_move&amp;do=up&amp;name_subcat='.$subids[$j].'|'.$subnames[$j].'&amp;dir_cat='.$ids[$i].'\'));\">вверх</a> | <a href=\"javascript:void(0);\" onclick=\"openwindow(\'window\', 400, \'auto\', my_array_move(\'pages.php?&amp;act=subpage_move&amp;do=down&amp;name_subcat='.$subids[$j].'|'.$subnames[$j].'&amp;dir_cat='.$ids[$i].'\'));\">вниз</a> | <a href=\"javascript:void(0);\" onclick=\"openwindow(\'window\', 400, \'auto\', my_array_move(\'pages.php?&amp;act=subpage_move&amp;do=delete&amp;name_subcat='.$subids[$j].'|'.$subnames[$j].'&amp;dir_cat='.$ids[$i].'\'));\">удалить</a><br></span>';
		   }
		   else {
		   $content .= '<p class="category-subpages"><a href="/'.($id == $Config->indexPage ? '' : $id).'">'.$subnames[$j].'</a></p>';
		   }
		}
	 if ($count_subnames != 0) { $content .= '</div></div>'; }
  }
  if ($Who == 'admin') {
  $content .= '<input type=\'hidden\' name=\'act\' value=\'save_cfg\'><input type=\'hidden\' name=\'count_names\' value=\''.count($names).'\'><p style=\'padding-top: 5px;\'><a href=\'#\' onclick=\"document.getElementById(\'pc-cfg-form\').submit(); return false;\">Cохранить настройки спойлера</a></p></form>';
  }

  if (!$content) { $content = 'Нет категорий.'; }
  return $content;
 }

// Функция выбора подкатегории
function ch_subpages() {
 $arr = file(DR.'/modules/admin_pages_category/data/cat-ids-names.dat');
 $ids = array();
 $names = array();

  for ($i = 0, $count_arr = count($arr); $i < $count_arr; $i++)
	{
     $tmp = explode('|', $arr[$i]);
	 $ids[] = trim($tmp[0]);
	 $names[] = trim($tmp[1]);
	}

$img = '../modules/admin_pages_category/dir.png';

$content = 'var ch_script = "<form action=\'pages.php?\' method=\'post\' id=\'to_cat\'> <input type=\'hidden\' name=\'act\' value=\'add_to_cat\'> <input type=\'hidden\' id=\'name_page\' name=\'name_page\' value=\'\'> <input type=\'hidden\' id=\'id_page\' name=\'id_page\' value=\'\'> <input type=\'hidden\' id=\'name_cat\' name=\'name_cat\' value=\'\'> <input type=\'hidden\' id=\'id_cat\' name=\'id_cat\' value=\'\'> <div class=\'a\'><div style=\'min-height: 111px; max-height: 221px; overflow: auto;\'>';

$count_names = count($names);
for ($i = 0; $i < $count_names; $i++) :
$content .= '<input type=\'hidden\' id=\'id_cat'.$i.'\' name=\'id_cat'.$i.'\' value=\''.$ids[$i].'\'><input type=\'hidden\' id=\'name_cat'.$i.'\' name=\'name_cat'.$i.'\' value=\''.$names[$i].'\'><div style=\'clear: both; height: 40px; border: 1px solid #e5e5e5; background-color: #f9f9f9; margin: 1px 1px; padding: 6px;\'><img style=\'float:left; margin: 0 6px 0 0;\' src=\''.$img.'\' width=\'40\' height=\'35\' alt=\'\'><p style=\'margin: 10px;\'>'.$names[$i].'<a class=\'button0\' style=\'float: right; margin: 0px;\' href=\'javascript:void(0);\' onclick=\"document.querySelector(\'#name_cat\').value = document.querySelector(\'#name_cat'.$i.'\').value; document.querySelector(\'#id_cat\').value = document.querySelector(\'#id_cat'.$i.'\').value; document.querySelector(\'#to_cat\').submit(); return false;\">Выбрать</a></div>';
endfor;

$content .= '</div></div><div class=\'b\' style=\'clear:both;\'><button type=\'button\' onclick=\"closewindow(\'window\');\">Отмена</button></div></form>"';

if ($count_names == 0) { $content = 'var ch_script = "<div class=\'a\'><div style=\'overflow: auto;\'>Нет категорий.</div><div class=\'b\' style=\'clear:both;\'><button type=\'button\' onclick=\"closewindow(\'window\');\">Закрыть</button></div>"'; }
return $content;
}


System::addAdminHeadHtml('
<style>
input[id^="spoiler"]{
 display: none;
}
input[id^="spoiler"] + label {
 display: block;
 background: #5E5E5E;
 color: #fff;
 text-align: center;
 font-size: 20px;
 cursor: pointer;
 transition: all .6s;
 border-radius: 2px;

 float: left;
 width: 30px;
}
input[id^="spoiler"]:checked + label {
 color: #333;
 background: #ccc;
}
input[id^="spoiler"] ~ .spoiler {
 width: 90%;
 height: 0;
 overflow: hidden;
 opacity: 0;
 margin: 10px auto 0;
 padding: 10px;
 background: #eee;
 border: 1px solid #ccc;
 transition: all .6s;

 line-height: 1.8em;
}
input[id^="spoiler"]:checked + label + .spoiler{
 height: auto;
 opacity: 1;
 padding: 10px;

 margin-bottom: 10px;
}
</style>
');



$pages_cat = '"' .Get_PagesCategory('admin'). '"';

$ch_subpages = ch_subpages();



System::addAdminEndHtml('
<script>

function my_array_move(url){
var cat_move = "<div class=\'a\'>Подтвердите действие</div>" +
	"<div class=\'b\'>" +
	"<button type=\'button\' onclick=\"window.location.href = \'"+url+"\';\">Подтвеждаю</button> "+
	"<button type=\'button\' onclick=\"closewindow(\'window\');\">Отмена</button>"+
	"</div>";

return cat_move;
}

function rename_cat_form(old_cat_name, old_cat_id) {
  var rename_cat_form = "<form name=\'rename_cat_form\' action=\'pages.php?\' method=\'post\'><div class=\'a\'>"+
    "<input type=\'hidden\' name=\'act\' value=\'rename_pages_cat\'>"+
	"<div>Название категории:</div>"+
	"<input type=\'hidden\' name=\'old_cat_name\' id=\'old_cat_name\' value=\'"+old_cat_name+"\'>"+
	"<input type=\'hidden\' name=\'old_cat_id\' id=\'old_cat_id\' value=\'"+old_cat_id+"\'>"+
	"<input type=\'text\' name=\'new_cat_name\' id=\'new_cat_name\' value=\'"+old_cat_name+"\'>"+
	"<div class=\'b\'><input type=\'submit\' name=\'\' value=\'Переименовать\'>&nbsp;<button type=\'button\' onclick=\"closewindow(\'window\');\">Отмена</button>"+
	"</div></form>";

	return rename_cat_form;
}

/* function get_page_info(pos) {
	document.querySelector(\'#name_page\').value = document.getElementsByClassName(\'tables\')[0].rows[pos].cells[1].innerText;
	document.querySelector(\'#id_page\').value = document.getElementsByClassName(\'tables\')[0].rows[pos].cells[2].innerText;
}

function get_page_info_search(pos) { // для страницы поиска
	var string = document.getElementsByClassName(\'tables\')[0].rows[pos].cells[1].querySelector(\'a\').innerText.toLowerCase();
	string = string[0].toUpperCase() + string.substring(1);
	document.querySelector(\'#name_page\').value = string;
	document.querySelector(\'#id_page\').value = document.getElementsByClassName(\'tables\')[0].rows[pos].cells[1].querySelectorAll(\'a\')[1].innerText;
} */

function get_page_info(pos) {
	document.querySelector(\'#name_page\').value = document.querySelectorAll(\'.name_page a\')[pos].innerText;
	var array = document.querySelectorAll(\'.name_page a\')[pos].href.split("=");
	document.querySelector(\'#id_page\').value = array[1];
}

function get_page_info_search(pos) { // для страницы поиска
	var string = document.querySelectorAll(\'.name_page a\')[pos].innerText.toLowerCase();
	string = string[0].toUpperCase() + string.substring(1);
	document.querySelector(\'#name_page\').value = string;
	var array = document.querySelectorAll(\'.name_page a\')[pos].href.split("=");
	document.querySelector(\'#id_page\').value = array[1];
}


var add_cat_form = "<form name=\'add_cat_form\' action=\'pages.php?\' method=\'post\' enctype=\'multipart/form-data\'><div class=\'a\'>"+
    "<input type=\'hidden\' name=\'act\' value=\'add_pages_cat\'>"+
	"<div>Название категории:</div>"+
	"<input type=\'text\' name=\'new_pages_cat_name\' id=\'new_pages_cat_name\'>"+
	"<div class=\'b\'><input type=\'submit\' name=\'\' value=\'Добавить\'>&nbsp;<button type=\'button\' onclick=\"closewindow(\'window\');\">Отмена</button>"+
	"</div></form>";

'.$ch_subpages.';

	   if (document.getElementsByClassName("header")[0]) {

		  if (document.getElementsByClassName("header")[0].innerText.trim() == "Управление страницами" || document.getElementsByClassName("header")[0].innerText.trim() == "Поиск по страницам") {
			// document.querySelector(".menu_page").innerHTML += " | <a href=\'#\' onclick=\"openwindow(\'window\', 400, \'auto\', add_cat_form);\"><b>Добавить категорию</b></a>";
			document.querySelector(".menu_page").innerHTML += "<a href=\'#\' onclick=\"openwindow(\'window\', 400, \'auto\', add_cat_form);\"><img style=\'padding-right: 5px; padding-left: 15px; position: relative; top: 8px;\' src=\'../modules/admin_pages_category/dir_plus.png\' width=\'30\' height=\'25\' alt=\'\'>Добавить категорию</a>";
			document.querySelector(".row").innerHTML += "<h3>Список категорий <span style=\'font-size: 90%; font-weight: normal;\'>| <a href=\'https://money.yandex.ru/to/410012986152433\' target=\'_blank\'>Поддержать!</a></span></h3>" + '.$pages_cat.';


				var img =  document.querySelectorAll(".name_page")
				var j = 0;
				for (var i = 0; i < img.length; i++) {

						if (document.getElementsByClassName("header")[0].innerText.trim() == "Управление страницами") {
						img[i].innerHTML += "<span data-pos=\'"+j+"\' style=\'cursor: pointer;\' title=\'Добавить в категорию\' onclick=\"openwindow(\'window\', 400, \'auto\', ch_script); get_page_info(this.getAttribute(\'data-pos\'));\"><img style=\'float: left; padding-right: 5px; margin: 5px 0 0 0;\' src=\'../modules/admin_pages_category/dir_plus.png\' width=\'30\' height=\'25\' alt=\'\'></span>";
						}
						else { // если страница поиска
						img[i].innerHTML += "<span data-pos=\'"+j+"\' style=\'cursor: pointer;\' title=\'Добавить в категорию\' onclick=\"openwindow(\'window\', 400, \'auto\', ch_script); get_page_info_search(this.getAttribute(\'data-pos\'));\"><img style=\'float: left; padding-right: 5px; margin: 5px 0 0 0;\' src=\'../modules/admin_pages_category/dir_plus.png\' width=\'30\' height=\'25\' alt=\'\'></span>";
						}

						j++;
				}

		  }

	   }

</script>
');



// Сохранение настроек
if ($act=='save_cfg') {
//var_dump($_POST);

for ($i = 0, $count_names = intval($_POST['count_names']); $i < $count_names; $i++) {
	$site_checkbox[] = @$_POST["site_checkbox_$i"];
	$admin_checkbox[]= @$_POST["admin_checkbox_$i"];
}

$arr = file(DR.'/modules/admin_pages_category/data/cat-ids-names.dat');
file_put_contents(DR.'/modules/admin_pages_category/data/cat-ids-names.dat', '');

$ids = array();
$names = array();
$insert = '';
  for ($i = 0, $count_arr = count($arr); $i < $count_arr; $i++) {
     $tmp = explode('|', $arr[$i]);
	 $ids[] = trim($tmp[0]);
	 $names[] = trim($tmp[1]);

	 $insert = $ids[$i] .'|'. $names[$i] .'|'. $site_checkbox[$i] .'|'. $admin_checkbox[$i];
	 file_put_contents(DR.'/modules/admin_pages_category/data/cat-ids-names.dat', $insert. "\r\n", FILE_APPEND);
}

echo'<div class="msg">Настройки сохранены</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php\';', 3000);
</script>
<?php
	}



// Добавление категории
if ($act=='add_pages_cat') {

$new_pages_cat_name = htmlspecialchars($_POST['new_pages_cat_name']);
$new_pages_cat_id = my_Translit($new_pages_cat_name);

if (!file_exists(DR.'/modules/admin_pages_category/data/'.$new_pages_cat_id)) {
	mkdir(DR.'/modules/admin_pages_category/data/'.$new_pages_cat_id);
	file_put_contents(DR.'/modules/admin_pages_category/data/cat-ids-names.dat', $new_pages_cat_id . '|' . $new_pages_cat_name . "\r\n", FILE_APPEND);
}
echo'<div class="msg">Категория добавлена</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php\';', 3000);
</script>
<?php
	}



// Переименование категории
if ($act=='rename_pages_cat') {

$arr_file = file(DR.'/modules/admin_pages_category/data/cat-ids-names.dat');

$old_cat_name = htmlspecialchars($_POST['old_cat_name']);
$new_cat_name = htmlspecialchars($_POST['new_cat_name']);

$old_cat_id = htmlspecialchars($_POST['old_cat_id']);
$new_cat_id = my_Translit($new_cat_name);

		$key = 0; $str = '';
		foreach($arr_file as $key => $val)
		 {
		  $val = str_replace($old_cat_name, $new_cat_name, $val);
		  $val = str_replace($old_cat_id, $new_cat_id, $val);
		  $str .= $val;
		 }

	rename(DR.'/modules/admin_pages_category/data/'.$old_cat_id, DR.'/modules/admin_pages_category/data/'.$new_cat_id);
	file_put_contents(DR.'/modules/admin_pages_category/data/cat-ids-names.dat', $str); // запись в файл
	echo'<div class="msg">Категория переименована</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php\';', 3000);
</script>
<?php
	}



// Добавление в категорию
if ($act=='add_to_cat') {

$name_cat = htmlspecialchars($_POST['name_cat']); // категория
$id_cat = htmlspecialchars($_POST['id_cat']);
$name_page = htmlspecialchars($_POST['name_page']); // страница (подкатегория)
// $id_page = htmlspecialchars($_POST['id_page']);
$id_page = htmlspecialchars($_POST['id_page']);
$id_page = explode('/', $id_page); $id_page = $id_page[1]; if ($id_page == '') { $id_page = $Config->indexPage; }
//echo $name_cat . '<br>' . $id_cat . '<br>' . $name_page . '<br>' . $id_page;

file_put_contents(DR.'/modules/admin_pages_category/data/'.$id_cat.'/sub-ids-names.dat', $id_page . '|' . $name_page . "\r\n", FILE_APPEND);
echo'<div class="msg">Страница добавлена в категорию</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php\';', 3000);
</script>
<?php
	}



// Перемещение страницы (подкатегории)
if($act=='subpage_move')
	{
		$do = $_GET['do']; // действие

		$dir_cat = htmlspecialchars($_GET['dir_cat']); // категория
		$name_subcat = htmlspecialchars($_GET['name_subcat']); // id|имя
		$arr_file = file(DR.'/modules/admin_pages_category/data/'.$dir_cat.'/sub-ids-names.dat');

		$key = 0;
		foreach ( $arr_file as $key => $val )
		 {
			if ( strpos($val, $name_subcat, 0) !== false ) // ищем совпадение
			{
				$index = $key;
			}
		 }

        if ($do == 'up') { my_array_move($arr_file, $index, $index - 1); } // вверх
		elseif ($do == 'down') { my_array_move($arr_file, $index, $index + 1); } // вниз
		elseif ($do == 'delete') { unset($arr_file[$index]); } // удалить

		$key = 0; $str = '';
		foreach($arr_file as $key => $val)
		 {
		  $str .= $val;
		 }

		file_put_contents(DR.'/modules/admin_pages_category/data/'.$dir_cat.'/sub-ids-names.dat', $str); // запись в файл
		if ($do == 'up') { echo'<div class="msg">Страница перемещена вверх</div>'; }
		elseif ($do == 'down') { echo'<div class="msg">Страница перемещена вниз</div>'; }
		elseif ($do == 'delete') { echo'<div class="msg">Страница удалена из категории</div>'; }
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php\';', 3000);
</script>
<?php
	}



// Перемещение категории
if($act=='dir_move')
	{
		$do = $_GET['do']; // действие

		$name_cat = $_GET['name_cat']; // id|имя
		$arr_file = file(DR.'/modules/admin_pages_category/data/cat-ids-names.dat');

		$key = 0;
		foreach ( $arr_file as $key => $val )
		 {
			if ( strpos($val, $name_cat, 0) !== false ) // ищем совпадение
			{
				$index = $key;
			}
		 }

        if ($do == 'up') { my_array_move($arr_file, $index, $index - 1); } // вверх
		elseif ($do == 'down') { my_array_move($arr_file, $index, $index + 1); } // вниз

		$key = 0; $str = '';
		foreach($arr_file as $key => $val)
		 {
		  $str .= $val;
		 }

		file_put_contents(DR.'/modules/admin_pages_category/data/cat-ids-names.dat', $str); // запись в файл

		if ($do == 'up') { echo'<div class="msg">Категория перемещена вверх</div>'; }
		elseif ($do == 'down') { echo'<div class="msg">Категория перемещена вниз</div>'; }
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php\';', 3000);
</script>
<?php
	}



// Удаление категории со всем содержимым
if($act=='array_move_delete')
	{
		$name_cat = $_GET['name_cat']; // id|имя
		$id_cat = explode('|', $name_cat); $id_cat = $id_cat[0]; //id
		$arr_file = file(DR.'/modules/admin_pages_category/data/cat-ids-names.dat');

		$key = 0;
		foreach ( $arr_file as $key => $val )
		 {
			if ( strpos($val, $name_cat, 0) !== false ) // ищем совпадение
			{
				$index = $key;
			}
		 }

        unset($arr_file[$index]); // удаляем

		$key = 0; $str = '';
		foreach ( $arr_file as $key => $val )
		 {
		  $str .= $val;
		 }

		if ($name_cat && $id_cat) {
		 file_put_contents(DR.'/modules/admin_pages_category/data/cat-ids-names.dat', $str); // запись в файл
		 my_rmRec(DR.'/modules/admin_pages_category/data/'.$id_cat.'');
		 echo'<div class="msg">Категория удалена</div>';
		}
		else {
		 echo'<div class="msg">Не удалось удалить категорию</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php\';', 3000);
</script>
<?php
	}



?>