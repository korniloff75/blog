<?php
require('cfg.php');

?>

<script type="text/javascript">

var iframefiles = '<div class="a"><iframe src="iframefiles.php?id=inputimg" width="100%" height="300" style="border:0;">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>'+
'<div class="b">'+
'<button type="button" onclick="document.getElementById(\'inputimg\').value = \'/modules/blog/default.jpg\';closewindow(\'window\');">Вствить фото по умолчанию</button> '+
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

   if(isset($Config->colorTheme)){
    $dir_icon = ($Config->colorTheme == '_light')?'icon_light/':'icon_dark/';   
   }else{
	$dir_icon = '';   
   }

    if($act=='info'){
		echo'<div class="header"><h1>RSS информация</h1></div>
		<div class="menu_page">
			<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
		</div>
		<div class="content">';
		if(($listIdBlog = json_decode($blogStorage->get('list'), true)) == false){
			echo'<p>RSS канал блога формируется автоматически. Сейчас не создано ни одного поста, поэтому RSS канал еще не сформирован.</p>';
		}else{
			echo'<p>RSS канал Вашего блога находится по адресу <a href="/'.$blogConfig->idPage.'/rss_blog.xml" target="_blank">'.SERVER.'/'.$blogConfig->idPage.'/rss_blog.xml</a></p>
			<p>Для корректной работы с некоторыми агрегаторами, необходимо чтобы в <a href="setting.php" target="_blank">настройках движка</a> были разрешены "Произвольные GET параметры".</p>
			<p>RSS канал блога был разработан согласно документации <a href="https://zen.yandex.ru/" target="_blank">Яндекс.Дзен</a> и <a href="https://pulse.mail.ru/" target="_blank">Pulse.Mail.ru</a>. Вы можете без проблем подключить свой сайт к этим агрегаторам.</p>';
		}
		echo'</div>';
	}


	if($act=='index')
	{
	    if(is_dir('../modules/blog/file')){
	    echo'<div class="header"><h1>Инициализация модуля</h1></div>
	   <div class="content">
       <p>Все компоненты модуля установлены. Для начала работы модуля необходимо выполнить инициализацию.</p>
       <form name="settingform" action="module.php?module='.$MODULE.'" method="post">
       <INPUT TYPE="hidden" NAME="act" VALUE="addin">
       <input type="submit" name="" value="Начать инициализацию">
       </form
	   </div>';
        }else{			
		echo'<div class="header"><h1>Управление модулем "Блог"</h1></div>
		<div class="menu_page">
			<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
		</div>
		<div class="content">
		<h2>Добавление поста</h2>
		<form name="form_name" action="module.php?module='.$MODULE.'" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addblog">
		<table class="tblform">
		<tr>
			<td>Заголовок поста:</td>
			<td><input type="text" name="header" id="header" value=""></td>
		</tr>
		
		<tr>
			<td class="top">Превью поста:</td>
			<td><TEXTAREA NAME="prev" ROWS="20" COLS="100" style="height:150px;">'.htmlspecialchars('<p>Превью поста</p>').'</TEXTAREA></td>
		</tr>
		<tr>
			<td class="top">Содержимое поста:</td>
			<td><TEXTAREA NAME="content" ROWS="20" COLS="100" style="height:250px;">'.htmlspecialchars('<p>Содержимое поста</p>').'</TEXTAREA></td>
		</tr>
		<tr>
			<td>Разрешить комментирование</td>
			<td class="middle"><INPUT TYPE="checkbox" NAME="comments" VALUE="y"></td>
		</tr>
		<tr>
			<td>URL иллюстр. картинки:</td>
			<td>
				<input type="text" name="img" id="inputimg" value="/modules/blog/default.jpg"> 
				<button type="button" onClick="openwindow(\'window\', 750, \'auto\', iframefiles);">Выбрать файл</button>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><img src="/modules/blog/default.jpg" alt="" id="img" style="width: 380px;"></td>
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
            <td></td>
            <td><span class="r">Внимание! Чтобы пост попал в индекс поиска по сайту поля "Ключевые слова" и "Описание" должны быть заполнены.</span></td>
        </tr>
		<tr>
			<td>Идентификатор (исп. для URL):</td>
			<td><input type="text" name="id" id="id" value="'.uniqid().'"><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из заголовка новости</a></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Добавить пост</button> &nbsp; <a href="index.php?">Вернуться назад</a></td>
		</tr>
		</table>
		</form>
		</div>';
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
	 }	
	}
	
	if($act=='addblog')
	{
		if(trim($_POST['header'])){
		$param['header'] = htmlspecialchars(specfilter($_POST['header']));
		$param['keywords'] = htmlspecialchars(specfilter($_POST['keywords']));
		$param['description'] = htmlspecialchars(specfilter($_POST['description']));
		$param['img'] = htmlspecialchars(specfilter($_POST['img']));
		$param['prev'] = $_POST['prev'];
		$param['content'] = $_POST['content'];
		$param['comments'] = ($_POST['comments'] == 'y')?'1':'0';
		$param['time'] = time();
		$param['date'] = date($blogConfig->formatDate, $param['time']);
		
		
		$viewsPP = 0;
		$noPP = 0;
		$yesPP = 0;
		
		$id = ($blogStorage->iss('post_'.$_POST['id']) == false && System::validPath($_POST['id']))?$_POST['id']:time();
		
		            $views = $blogStorage->iss('views_'.$_POST['id'])?$blogStorage->get('views_'.$_POST['id']):0;
					$views+= $viewsPP;
					$blogStorage->set('views_'.$_POST['id'], $views);
					
					$no = $blogStorage->iss('no_'.$_POST['id'])?$blogStorage->get('no_'.$_POST['id']):0;
					$no+= $noPP;
					$blogStorage->set('no_'.$_POST['id'], $no);
					
					$yes = $blogStorage->iss('yes_'.$_POST['id'])?$blogStorage->get('yes_'.$_POST['id']):0;
					$yes+= $yesPP;
					$blogStorage->set('yes_'.$_POST['id'], $yes);
		
		if($blogStorage->set('post_'.$id, json_encode($param))){
			
			// Добавляем ID поста в список
			$listIdBlog = json_decode($blogStorage->get('list'), true); // Получили список ввиде массива
			$listIdBlog[] = $id;// Добавили новый элемент массива в конец
			$blogStorage->set('list', json_encode($listIdBlog)); // Записали массив в виде json
			
			echo'<div class="msg">Пост успешно добавлен  '.$desc.'</div>';
			
			System::notification('Добавлен пост с заголовком: '.$param['header']);
			
		}else{
			
			echo'<div class="msg">Произошла ошибка при добавлении поста</div>';
			
			System::notification('Произошла ошибка при добавлении поста', 'r');
			
		}
		}else{
		echo'<div class="msg">Введите заголовок поста.</div>';

        }		
			
		
		
	 
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php	
	}
	
	if($act=='cfg'){
		
		$checked = ($blogConfig->commentEngine == 1)?'checked':'';
		
		echo'<div class="header"><h1>Управление модулем "Блог"</h1></div>
		<div class="menu_page">
			<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
		</div>
		<div class="content">
		<h2>Настройки модуля</h2>
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcfg">
		<table class="tblform">
		<tr>
			<td>Заголовк RSS канала:</td>
			<td><input type="text" name="name_rss" value="'.$blogConfig->name_rss.'" size="50"></td>
		</tr>
		
		<tr>
			<td>Количество превью записей на странице:</td>
			<td><input type="text" name="navigation" value="'.$blogConfig->navigation.'" maxlength="3"></td>
		</tr>
		
		<tr>
			<td>Количество превью при выводе в блоке:</td>
			<td><input type="text" name="countInBlok" value="'.$blogConfig->countInBlok.'" maxlength="3"></td>
		</tr>
		
		<tr>
        <td>Использовать стили шаблона:</td>
        <td>';
        if($blogConfig->navStyle == '1'){
        $check_nav = ' checked';
        }else{
        $check_nav = '';	
        }
        echo'
        <input type="checkbox" value="z" name="nav_style"'.$check_nav.'>
        </td>
        </tr>
		<tr>
        <td>Подключить библиотеку jquery:</td>
        <td>';
        if($blogConfig->libJquery == '1'){
        $check_lib = ' checked';
        }else{
        $check_lib = '';	
        }
        echo'
        <input type="checkbox" value="j" name="lib_jquery"'.$check_lib.'>
        </td>
        </tr>
		<tr>
			<td>Формат вывода даты (Формат функции date):</td>
			<td><input type="text" name="formatDate" value="'.$blogConfig->formatDate.'"></td>
		</tr>
		
		<tr>
			<td>Идентификатор страницы с блогом:</td>
			<td><input type="text" name="idPage" value="'.$blogConfig->idPage.'"></td>
		</tr>
		
		<tr>
			<td>Идентификатор страницы с пользователей:</td>
			<td><input type="text" name="idUser" value="'.$blogConfig->idUser.'"></td>
		</tr>
		
		<tr>
			<td class="top">Шаблон для вывода превью:<br><span class="comment">Не трогайте если не знаете что это такое</span></td>
			<td><TEXTAREA NAME="prevTemplate" ROWS="20" COLS="100" style="height:150px;">'.htmlspecialchars($blogConfig->prevTemplate).'</TEXTAREA></td>
		</tr>
		
		<tr>
			<td class="top">Шаблон для вывода поста:<br><span class="comment">Не трогайте если не знаете что это такое</span></td>
			<td><TEXTAREA NAME="contentTemplate" ROWS="20" COLS="100" style="height:150px;">'.htmlspecialchars($blogConfig->contentTemplate).'</TEXTAREA></td>
		</tr>

		<tr>
			<td>Использовать собственный сервис комментариев</td>
			<td class="middle"><INPUT TYPE="checkbox" NAME="commentEngine" VALUE="y" id="checkbox" '.$checked.'></td>
		</tr>
				
		<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="modules.php?">Вернуться назад</a></td>
		</tr>
		</table>
		</form>
		</div>';
		?>
		<script type="text/javascript">
		function checked(){
			document.getElementById('trCommentTemplate').style.opacity = (document.getElementById('checkbox').checked)?'0.5':'1';
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
	
	if($act=='addcfg'){
		
		if( !is_numeric($_POST['navigation']) || 
			$_POST['formatDate'] == ''||
			!System::validPath($_POST['idPage']) || 
			!System::validPath($_POST['idUser'])
		){
			echo'<div class="msg">Не все поля заполнены, или заполнены неправильно</div>';
		}else{ 
			
			$blogConfig->name_rss = $_POST['name_rss'];
			$blogConfig->navigation = $_POST['navigation'];
			$blogConfig->countInBlok = htmlspecialchars(specfilter($_POST['countInBlok']));
			$blogConfig->formatDate = htmlspecialchars(specfilter($_POST['formatDate']));
			$blogConfig->idPage = $_POST['idPage'];
			$blogConfig->idUser = $_POST['idUser'];
			$blogConfig->prevTemplate = $_POST['prevTemplate'];
			$blogConfig->contentTemplate = $_POST['contentTemplate'];
			$blogConfig->commentTemplate = $_POST['commentTemplate'];
			$blogConfig->commentEngine = ($_POST['commentEngine'] == 'y')?'1':'0';
			
			if (isset($_POST['nav_style'])){$blogConfig->navStyle = '1';}else{$blogConfig->navStyle = '0';}
			if (isset($_POST['lib_jquery'])){$blogConfig->libJquery = '1';}else{$blogConfig->libJquery = '0';}
			
			if($blogStorage->set('blogConfig', json_encode($blogConfig))){
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены параметры модуля постов');
			}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении параметров модуля постов', 'r');
			}
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=cfg\';', 3000);
</script>
<?php	
	}
	
	if($act=='edit')
	{
?>
<script type="text/javascript">
function dell(url){
return '<div class="a">Подтвердите удаление поста</div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \''+url+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php
		
		echo'<div class="header"><h1>Управление модулем "Блог"</h1></div>
		<div class="menu_page">
			<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
		</div>
		<div class="content">
		 
		<h2>Список постов</h2>';
		
		
		if(($listIdBlog = json_decode($blogStorage->get('list'), true)) == false){
			echo'<div class="msg">Постов нет</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
		}else{
			
			
			echo'<table class="tables">
			<tr>
				<td class="tables_head" colspan="2">Заголовок поста</td>
				<td class="tables_head">URL</td>
				<td class="tables_head">Комментирование</td>
				<td class="tables_head">Дата</td>
				<td class="tables_head">&nbsp;</td>
			</tr>';
			
			//перевернули масив для вывода постов в обратном порядке
			$listIdBlog = array_reverse($listIdBlog);
			
			//
			$nom = count($listIdBlog);
			
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
					if($blogStorage->iss('post_'.$listIdBlog[$i])){
						$blogParam = json_decode($blogStorage->get('post_'.$listIdBlog[$i]));
						
						$comments = ($blogParam->comments == '1')?'<span style="color: green;">Включено</span>':'<span style="color: red;">Выключено</span>';
						echo'<tr>
						<td class="img"><img src="include/'.$dir_icon.'page.svg" alt=""></td>
						<td><a href="module.php?module='.$MODULE.'&amp;act=editblog&amp;blog='.$listIdBlog[$i].'&amp;nom_page='.$nom_page.'">'.$blogParam->header.'</a></td>
						<td><a href="//'.SERVER.'/'.$blogConfig->idPage.'/'.$listIdBlog[$i].'" target="_blank">'.SERVER.'/'.$blogConfig->idPage.'/'.$listIdBlog[$i].'</a></td>
						<td>'.$comments.'</td>
						<td>'.$blogParam->date.'</td>
						<td><a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\'module.php?module='.$MODULE.'&amp;act=dell&amp;blog='.$listIdBlog[$i].'&amp;id_page='.$blogConfig->idPage.'&amp;nom_page='.$nom_page.'\'));">Удалить</a></td>
						</tr>';
					}else{
						echo'<tr>
						<td>&nbsp;</td>
						<td style="color: red;">Error</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
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
	
	
	if($act=='dell')
	{
		$blog = htmlspecialchars(specfilter($_GET['blog']));
		$id_page = htmlspecialchars(specfilter($_GET['id_page']));
		$nom_page = htmlspecialchars(specfilter($_GET['nom_page']));
		if($blogStorage->delete('post_'.$blog)){ // Удадляем новость
			
			//Удаляем страницу из списка
			$listIdBlog = json_decode($blogStorage->get('list'), true); // Получили список ввиде массива
			if(($key = array_search($blog, $listIdBlog)) !== false){
				unset($listIdBlog[$key]); // Удалили найденый элемент массива
			}
			$listIdBlog = array_values($listIdBlog); // Переиндексировали числовые индексы 
			$blogStorage->set('list', json_encode($listIdBlog)); // Записали массив в виде json
			
			$blogStorage->delete('comments_'.$blog); // Удаляем комментарии
			$blogStorage->delete('count_'.$blog); // Удаляем счетчик комментариев
			$blogStorage->delete('views_'.$blog);
			$blogStorage->delete('yes_'.$blog);
			$blogStorage->delete('no_'.$blog);
			
			
			
			System::notification('Удален пост с идентификатором '.$blog.'', 'g');
			echo'<div class="msg">Пост успешно удален</div>';
		}else{
			System::notification('Ошибка при удалении поста с идентификатором '.$blog.', страница не найдена или запрос некорректен', 'r');
			echo'<div class="msg">Ошибка при удалении поста</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=edit&nom_page=<?php echo $nom_page; ?>\';', 3000);
</script>
<?php	
	}

	if($act=='editblog')
	{
		$blog = htmlspecialchars(specfilter($_GET['blog']));
		$nom_page = htmlspecialchars(specfilter($_GET['nom_page']));
		
		if(($blogParam = json_decode($blogStorage->get('post_'.$blog))) != false){
			echo'<div class="header"><h1>Управление модулем "Блог"</h1></div>
			<div class="menu_page">
			<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
		    </div>
			<div class="menu_page">
				<a href="module.php?module='.$MODULE.'&amp;act=edit&amp;nom_page='.$nom_page.'">&#8592; Вернуться назад</a>
			</div>
			<div class="content">
			<h2>Редактирование поста "'.$blogParam->header.'"</h2>
			<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
            <INPUT TYPE="hidden" NAME="act" VALUE="addedit">
      	    <INPUT TYPE="hidden" NAME="blog" VALUE="'.$blog.'">
      	    <INPUT TYPE="hidden" NAME="nom_page" VALUE="'.$nom_page.'">
			<INPUT TYPE="hidden" NAME="date" VALUE="'.$blogParam->date.'">
			<table class="tblform">
			<tr>
				<td>Заголовок поста:</td>
				<td><input type="text" name="header" id="header" value="'.$blogParam->header.'"></td>
			</tr>
			
			<tr>
				<td class="top">Превью поста:</td>
				<td><TEXTAREA NAME="prev" ROWS="20" COLS="100" style="height: 150px;">'.htmlspecialchars($blogParam->prev).'</TEXTAREA></td>
			</tr>
			<tr>
				<td class="top">Содержимое поста:</td>
				<td><TEXTAREA NAME="content" ROWS="20" COLS="100" style="height: 250px;">'.htmlspecialchars($blogParam->content).'</TEXTAREA></td>
			</tr>';
			$checked = ($blogParam->comments == 1)?'checked':'';
			echo'
			<tr>
				<td>Разрешить комментирование</td>
				<td class="middle"><INPUT TYPE="checkbox" NAME="comments" VALUE="y" '.$checked.'></td>
			</tr>
			<tr>
				<td>URL иллюстр. картинки:</td>
				<td>
					<input type="text" name="img" id="inputimg" value="'.$blogParam->img.'"> 
					<button type="button" onClick="openwindow(\'window\', 700, \'auto\', iframefiles);">Выбрать файл</button>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><img src="'.$blogParam->img.'" alt="" id="img" style="width: 380px;"></td>
			</tr>
			<tr>
				<td>Ключевые слова (keywords):</td>
				<td><input type="text" name="keywords" value="'.$blogParam->keywords.'"></td>
			</tr>
			<tr>
				<td>Описание (description):</td>
				<td><input type="text" name="description" value="'.$blogParam->description.'"></td>
			</tr>';
            if(!trim($blogParam->keywords) && !trim($blogParam->description)){
            echo'<tr>
                <td></td>
                <td><span class="r">Внимание! Чтобы пост попал в индекс поиска по сайту поля "Ключевые слова" и "Описание" должны быть заполнены.</span></td>
            </tr>';
            }
            echo'<tr>
				<td>Идентификатор (исп. для URL):</td>
				<td><input type="text" name="id" id="id" value="'.$blog.'"><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из заголовка новости</a></td>
		</tr>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="/'.$blogConfig->idPage.'/'.$blog.'" target="_blank">Перейти на страницу</a>
				 &nbsp; <a href="module.php?module='.$MODULE.'&amp;act=edit&amp;nom_page='.$nom_page.'">Вернуться назад</a>
				</td>
			</tr>
			</table>
			</form>
			</div>';
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
		}else{
			echo'<div class="msg">Не удалось получить параметры записи</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=edit&nom_page=<?php echo $nom_page;?>\';', 3000);
</script>
<?php
		}

	}
	  
	if($act=='addedit'){
		$blog = htmlspecialchars(specfilter($_POST['blog']));
		$nom_page = htmlspecialchars(specfilter($_POST['nom_page']));
		$id_blog = htmlspecialchars(specfilter($_POST['id'])); // Новый id для поста
		

		
			if($blogStorage->iss('post_'.$blog)){
				
				$param['header'] = ($_POST['header'] == '')?'Без названия':htmlspecialchars(specfilter($_POST['header']));
				$param['img'] = htmlspecialchars(specfilter($_POST['img']));
				$param['keywords'] = htmlspecialchars(specfilter($_POST['keywords']));
				$param['description'] = htmlspecialchars(specfilter($_POST['description']));
				$param['prev'] = $_POST['prev'];
				$param['content'] = $_POST['content'];
				$param['date'] = htmlspecialchars(specfilter($_POST['date']));
				$param['comments'] = ($_POST['comments'] == 'y')?'1':'0';
				
				
				if($blogStorage->set('post_'.$blog, json_encode($param))){
					if($id_blog != $blog){
						if($blogStorage->iss('post_'.$id_blog) == false && System::validPath($id_blog)){
							
							if($blogStorage->set('post_'.$id_blog, json_encode($param)) == false){
								System::notification('Ошибка при записи ключа post_'.$id_blog.'', 'r');
							}
							
							if($blogStorage->delete('post_'.$blog) == false){
								System::notification('Ошибка при удалении ненужного ключа post_'.$blog.'', 'r');
							}
							
							// Замена страницы в списке
							$listIdBlog = json_decode($blogStorage->get('list'), true); // Получили список ввиде массива
							if(($key = array_search($blog, $listIdBlog)) !== false){
								$listIdBlog[$key] = $id_blog; // Заменили найденый элемент массива
							}
							$listIdBlog = array_values($listIdBlog); // Переиндексировали числовые индексы 
							$blogStorage->set('list', json_encode($listIdBlog)); // Записали массив в виде json
							
							System::notification('Отредактирован пост со сменой идентификатора '.$blog.' на идентификатор '.$id_blog.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_blog.'/'.$id_blog, 'g');
							echo'<div class="msg">Пост успешно сохранен </div>';
						}else{
							System::notification('Отредактирован пост с неудачной попыткой смены идентификатора '.$blog.' на идентификатор '.$id_blog.', идентификатор '.$id_blog.' уже существует или некорректен, ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_blog.'/'.$blog, 'g');
							echo'<div class="msg">Пост сохранен, но идентификатор изменить не удалось</div>';
						}
					}else{
						
						System::notification('Отредактирован пост с идентификатором '.$id_blog.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_blog.'/'.$id_blog, 'g');
						echo'<div class="msg">Пост успешно сохранен </div>';
					}
				}else{
					System::notification('Ошибка при сохранении страницы с идентификатором '.$blog.', ошибка записи', 'r');
					echo'<div class="msg">Ошибка при сохранении страницы</div>';
					
				}
			}else{
				System::notification('Ошибка при сохранении страницы с идентификатором '.$blog.', страница ненайдена', 'r');
				echo'<div class="msg">Неудалось получить параметры записи</div>';
			}
		
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=edit&nom_page=<?php echo $nom_page; ?>\';', 3000);
</script>
<?php
	}
	  
	if($act=='comment')
	{
		function bbcode($html){
	$html = trim($html[1]);
	$html = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$html);
	$html = str_replace('  ',' &nbsp;',$html);
	$html = preg_replace('/&quot;(.*?)&quot;/', '<span class="quot">&quot;\1&quot;</span>', $html);
	$html = preg_replace('/\'(.*?)\'/', '<span class="quot">\'\1\'</span>', $html);
	$html = str_replace("\n",'<br>', $html);
	$html = specfilter($html);
	return '<pre><code>'.$html.'</code></pre>';
}


function ptext($text){
	$text = preg_replace_callback('#\[code\](.*?)\[/code\]#si', 'bbcode', $text);
	$text = preg_replace('#\[b\](.*?)\[/b\]#si', '<span style="font-weight: bold;">\1</span>', $text);
	$text = preg_replace('#\[i\](.*?)\[/i\]#si', '<span style="font-style: italic;">\1</span>', $text);
	$text = preg_replace('#\[u\](.*?)\[/u\]#si', '<span style="text-decoration: underline;">\1</span>', $text);
	$text = preg_replace('#\[red\](.*?)\[/red\]#si', '<span style="color: #E53935;">\1</span>', $text);
    $text = preg_replace('#\[img\](.*?)\[/img\]#si', '<img src="\1" alt="">', $text);
    $text = preg_replace('#\[url\](.*?)\[/url\]#si', '<a href="\1" target="_blank">\1</a>', $text);
	$text = str_replace("\n",'<br>',$text);
	$text = preg_replace('#\[emo1\](.*?)\[/emo1\]#si', '<span class="emo1"></span>', $text);
	$text = preg_replace('#\[emo2\](.*?)\[/emo2\]#si', '<span class="emo2"></span>', $text);
	$text = preg_replace('#\[emo3\](.*?)\[/emo3\]#si', '<span class="emo3"></span>', $text);
	$text = preg_replace('#\[emo4\](.*?)\[/emo4\]#si', '<span class="emo4"></span>', $text);
	$text = preg_replace('#\[emo5\](.*?)\[/emo5\]#si', '<span class="emo5"></span>', $text);
	$text = preg_replace('#\[emo6\](.*?)\[/emo6\]#si', '<span class="emo6"></span>', $text);
	$text = preg_replace('#\[emo7\](.*?)\[/emo7\]#si', '<span class="emo7"></span>', $text);
	$text = preg_replace('#\[emo8\](.*?)\[/emo8\]#si', '<span class="emo8"></span>', $text);
	$text = preg_replace('#\[emo9\](.*?)\[/emo9\]#si', '<span class="emo9"></span>', $text);
	$text = preg_replace('#\[emo10\](.*?)\[/emo10\]#si', '<span class="emo10"></span>', $text);
	$text = preg_replace('#\[emo11\](.*?)\[/emo11\]#si', '<span class="emo11"></span>', $text);
	$text = preg_replace('#\[emo12\](.*?)\[/emo12\]#si', '<span class="emo12"></span>', $text);
	$text = preg_replace('#\[emo13\](.*?)\[/emo13\]#si', '<span class="emo13"></span>', $text);
	$text = preg_replace('#\[emo14\](.*?)\[/emo14\]#si', '<span class="emo14"></span>', $text);
	$text = preg_replace('#\[emo15\](.*?)\[/emo15\]#si', '<span class="emo15"></span>', $text);
	$text = preg_replace('#\[emo16\](.*?)\[/emo16\]#si', '<span class="emo16"></span>', $text);
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
			'<button type="button" onClick="window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=listdellcoment\';">Очистить</button> '+
			'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
			'</div>';
			
		var wDell = '<div class="a"><span class="r">Внимание!</span> Список последних комментариев переполнен. Рекомендуется очистить список, что-бы разгрузить систему.</div>' +
			'<div class="b">' +
			'<button type="button" onClick="window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=listdellcoment\';">Очистить сейчас</button> '+
			'<button type="button" onclick="closewindow(\'window\');">Закрыть</button>'+
			'</div>';
			
		function submitDell(){
			document.form.act.value = "dellcoment";
			form.submit();
		}
		</script>
		<?php
		
		echo'<div class="header"><h1>Управление модулем "Блог"</h1></div>
		<div class="menu_page">
			<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
		</div>';
		
		
		if ($blogConfig->commentEngine){
			
			
			echo'
			<div class="content">
			<h2>Комментарии пользователей</h2>
			
			';
			 
			
			
			
			if(($lastComments = json_decode($blogStorage->get('lastComments'), true)) == false){
				echo'<div class="row"><a class="button" href="module.php?module='.$MODULE.'&amp;act=cfgcomment">Настройки комментариев</a></div>
				<p>Комментариев нет</p>';
			}else{
				
				
				
				echo'<form name="form" action="module.php?module='.$MODULE.'" method="post">
					<INPUT TYPE="hidden" NAME="act" VALUE="pubcoment">
					<div class="row">
					<input type="submit" name="" value="Опубликовать выделенное" title="Опубликовать выделенные комментарии">
					<button type="button" onClick="openwindow(\'window\', 650, \'auto\', dell);" title="Удалить выделенные комментарии">Удалить выделенное</button>
					<button type="button" onClick="openwindow(\'window\', 650, \'auto\', listDell);" title="Очистить список последних комментариев">Очистить список</button>
					<a class="link button" href="module.php?module='.$MODULE.'&amp;act=cfgcomment">Настройки комментариев</a>
					</div>
				';
				
				//перевернули масив для вывода постов в обратном порядке
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
							<div><INPUT TYPE="checkbox" NAME="comment[]" VALUE="'.$lastComments[$i]['idComment'].'"> '.($lastComments[$i]['published']?'':'<span class="r">Не опубликованно</span>').' Страница: <a href="//'.SERVER.'/'.$blogConfig->idPage.'/'.$lastComments[$i]['idblog'].'" target="_blank">'.SERVER.'/'.$blogConfig->idPage.'/'.$lastComments[$i]['idblog'].'</a></div>';
							if(file_exists('../modules/users/images/'.$lastComments[$i]['login'].'.jpg')){
		                    echo'<h3 class="av"><img src="/modules/users/images/'.$lastComments[$i]['login'].'.jpg" width="30" height="30" alt=""> <span>'.$lastComments[$i]['login'].'</span></h3>';
		                    }else{
							echo'<h3 class="av"><img src="/modules/blog/user.png" width="30" height="30" alt=""> <span>'.$lastComments[$i]['login'].'</span></h3>';
							}						
							echo'<p>'.ptext($lastComments[$i]['text']).'</p>
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
	
	
	
	if($act=='pubcoment'){
		// Даже и не пытайтесь разобраться ;)
		if(($lastComments = json_decode($blogStorage->get('lastComments'), true)) == false){
				echo'<div class="msg">Ошибка. Нет ни одного сообщения.</div>';
		}elseif(!isset($_POST['comment'])){
			echo'<div class="msg">Ошибка. Нет выбранных элементов.</div>';
		}else{
			$addComment = array();
			$countPP = 0;
			$viewsPP = 0;
			$noPP = 0;
			$yesPP = 0;
			foreach($lastComments as $key => $value){
				if(in_array($value['idComment'], $_POST['comment']) && $value['published'] == 0){
						++$countPP;
						$lastComments[$key]['published'] = 1;
						$addComment[$value['idblog']][] = array(
													'id' => $value['idComment'],
													'login' => $value['login'],
													'text' => $value['text'],
													'ip' => $value['ip'],
													'status' => $value['status'],
													'time' => $value['time']);
				}
			}
			$blogStorage->set('lastComments', json_encode($lastComments));
			unset($lastComments);
			
			
			foreach($addComment as $key => $value){
				$arrayComments = json_decode($blogStorage->get('comments_'.$key), true);
				
				foreach($value as $row){
					$arrayComments[] = $row;
					
					if(($CUser = User::getConfig($row['login'])) != false){
						++$CUser->numPost;
						User::setConfig($row['login'], $CUser);
					}
				}
				
				
				$arrayCount = count($arrayComments);
				if($arrayCount >= $blogConfig->commentMaxCount){
					$arrayStart = $arrayCount -  round($blogConfig->commentMaxCount / 1.5);
					$arrayComments = array_slice($arrayComments, $arrayStart, $arrayCount);
				}
				
				if($blogStorage->set('comments_'.$key, json_encode($arrayComments))){
					
					$count = $blogStorage->iss('count_'.$key)?$blogStorage->get('count_'.$key):0;
					$count+= $countPP;
					$blogStorage->set('count_'.$key, $count);
					
				}
			}
			echo'<div class="msg">Публикация успешно завершена</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=comment\';', 3000);
</script>
<?php	
	}
	
	
	
	if($act=='dellcoment'){
		if(($lastComments = json_decode($blogStorage->get('lastComments'), true)) == false){
				echo'<div class="msg">Ошибка. Нет ни одного сообщения.</div>';
		}else{
			$dellComment = array();
			foreach($lastComments as $key => $value){
				if(in_array($value['idComment'], $_POST['comment'])){
					$dellComment[$value['idblog']][] = $value['idComment'];
					unset($lastComments[$key]);
				}
			}
			
			$lastComments = array_values($lastComments);
			$blogStorage->set('lastComments', json_encode($lastComments));
			unset($lastComments);
			
			
			foreach($dellComment as $key => $value){
				$arrayComments = json_decode($blogStorage->get('comments_'.$key), true);
				foreach($arrayComments as $i => $row){
					if (in_array($row['id'], $value)){
						unset($arrayComments[$i]);						
					}
				}
				
				$arrayComments = array_values($arrayComments);
				$blogStorage->set('comments_'.$key, json_encode($arrayComments));
			}
			echo'<div class="msg">Удаление успешно завершено</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=comment\';', 3000);
</script>
<?php	
	}
		
	if($act=='listdellcoment'){
		$blogStorage->set('lastComments', json_encode(array()));
		echo'<div class="msg">Очистка успешно завершена</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=comment\';', 3000);
</script>
<?php	
	}
	
	
	if($act=='cfgcomment'){
		
		echo'<div class="header"><h1>Управление модулем "Блог"</h1></div>
		<div class="menu_page">
			<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование поста</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
			<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
		</div>
		<div class="menu_page">
			<a href="module.php?module='.$MODULE.'&amp;act=comment">&#8592; Вернуться назад</a>
		</div>
		
		
		<div class="content">
		<h2>Настройки комментариев</h2>
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcfgcomment">
		<table class="tblform">
		<tr>
			<td>Работа комментариев:</td>
			<td>
				<SELECT NAME="commentEnable" >
					<OPTION VALUE="0" '.($blogConfig->commentEnable == '0'?'selected':'').'>Выключено
					<OPTION VALUE="1" '.($blogConfig->commentEnable == '1'?'selected':'').'>Включено
				</SELECT><br><span class="comment">Эта настройка глобальна для всех постов</span>
			</td>
		</tr>
		
		<tr>
			<td>Кто может писать комментарии:</td>
			<td>
				<SELECT NAME="commentRules" >
					<OPTION VALUE="0" '.($blogConfig->commentRules == '0'?'selected':'').'>Все пользователи
					<OPTION VALUE="1" '.($blogConfig->commentRules == '1'?'selected':'').'>Только зарегистрированные пользователи
					<OPTION VALUE="2" '.($blogConfig->commentRules == '2'?'selected':'').'>Только пользователи с преференциями
					<OPTION VALUE="3" '.($blogConfig->commentRules == '3'?'selected':'').'>Только администратор
				</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>Модерация перед публикацией:</td>
			<td>
				<SELECT NAME="commentModeration" >
					<OPTION VALUE="0" '.($blogConfig->commentModeration == '0'?'selected':'').'>Не модерировать, публиковать сразу
					<OPTION VALUE="1" '.($blogConfig->commentModeration == '1'?'selected':'').'>Модерировать не зарегистрированных пользователей и новичков
					<OPTION VALUE="2" '.($blogConfig->commentModeration == '2'?'selected':'').'>Модерировать всех кроме пользователей с преференциями
				</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>Количество сообщений новичка:</td>
			<td><input type="text" name="commentModerationNumPost" value="'.$blogConfig->commentModerationNumPost.'">
			<br><span class="comment">Максимальное количество сообщений при котором пользователь считается новичком</span>
			</td>
		</tr>
		
		<tr>
			<td>Макс. символов для одного комментария:</td>
			<td><input type="text" name="commentMaxLength" value="'.$blogConfig->commentMaxLength.'"></td>
		</tr>
		
		<tr>
			<td>Кол-во выводимых комментариев за раз:</td>
			<td><input type="text" name="commentNavigation" value="'.$blogConfig->commentNavigation.'"></td>
		</tr>
		
		<tr>
			<td>Макс. комментариев для одной поста:</td>
			<td><input type="text" name="commentMaxCount" value="'.$blogConfig->commentMaxCount.'"></td>
		</tr>
		
		<tr>
			<td>Задержка на проверку новых комментарий:</td>
			<td><input type="text" name="commentCheckInterval" value="'.$blogConfig->commentCheckInterval.'">
			<br><span class="comment">Задержка указывается в милисекундах. Если указать "0", то проверка на наличие новых комментариев выполняться не будет.</span>
			</td>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="module.php?module='.$MODULE.'&amp;act=comment">Вернуться назад</a></td>
		</tr>
		</table>
		</form>
		</div>';
	}
	
	if($act=='addcfgcomment'){
		
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
			
			$blogConfig->commentEnable = $_POST['commentEnable'];
			$blogConfig->commentRules = $_POST['commentRules'];
			$blogConfig->commentModeration = $_POST['commentModeration'];
			$blogConfig->commentModerationNumPost = $_POST['commentModerationNumPost'];
			$blogConfig->commentMaxLength = $_POST['commentMaxLength'];
			$blogConfig->commentNavigation = $_POST['commentNavigation'];
			$blogConfig->commentMaxCount = $_POST['commentMaxCount'];
			$blogConfig->commentCheckInterval = $_POST['commentCheckInterval'];
			
			if($blogStorage->set('blogConfig', json_encode($blogConfig))){
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены параметры комментарий модуля постов');
			}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении параметров комментарий модуля постов', 'r');
			}
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=cfgcomment\';', 3000);
</script>
<?php	
	}
	
	
if($act=='addin'){
		
		$file1 = file_get_contents('../modules/blog/file/uninstall.php');
        file_put_contents('uninstall.php', $file1);
		$file2 = file_get_contents('../modules/blog/file/blogConfig.dat');
        file_put_contents('../data/storage/module.blog/blogConfig.dat', $file2);
		
		$dir = mkdir('../files/blog/');
		
echo'
		<div class="header">
			<h1>Инициализация модуля</h1>
		</div>
		
		<div class="content">		
		<div class="msg">
		<img src="../modules/blog/file/busy.gif" alt=""><br><br>
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
	
?>