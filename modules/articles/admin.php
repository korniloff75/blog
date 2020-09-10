<?php
if (!class_exists('System')) exit; // Запрет прямого доступа
?>

<script type="text/javascript">

var iframefiles = '<div class="a"><iframe src="iframefiles.php?id=inputimg" width="100%" height="300" style="border:0;">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>'+
'<div class="b">'+
'<button type="button" onclick="document.getElementById(\'inputimg\').value = \'/modules/articles/default.jpg\';closewindow(\'window\');">Вставить фото по умолчанию</button> '+
'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
'</div>';

var gotocfgcat = '<div class="a">Несохраненные данные могут быть утеряны</div>'+
'<div class="b">'+
'<button type="button" onclick="window.location.href = \'module.php?module=<?php echo $MODULE;?>&amp;act=cat\';">Перейти к управлению категориями</button> '+
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

function dell(url, n, u){
return '<div class="a">Подтвердите удаление статьи: <i>' + n + ' (<a href="//' + u + '" target="_blank">' + u + '</a>)</i></div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \''+url+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}

</script>
<?php
	$menu_page = '<div class="menu_page">
		<a class="link" href="module.php?module='.$MODULE.'&amp;">Добавление статьи</a>
		<a class="link " href="module.php?module='.$MODULE.'&amp;act=edit">Редактирование статьи</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=comment">Комментарии пользователей</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=cfg">Настройки модуля</a>
		<a class="link" href="module.php?module='.$MODULE.'&amp;act=info">RSS информация</a>
	</div>';

	if($act=='info'){
		echo'<div class="header"><h1>RSS информация</h1></div>
		'.$menu_page.'
		<div class="content">
			<p>Ваш RSS канал статей находится по адресу <a href="/'.$articlesConfig->idPage.'/rss.xml" target="_blank">'.SERVER.'/'.$articlesConfig->idPage.'/rss.xml</a></p>
			<p>Для корректной работы с некоторыми агрегаторами, необходимо чтобы в <a href="setting.php" target="_blank">настройках движка</a> были разрешены "Произвольные GET параметры".</p>
			<p>RSS канал статей был разработан согласно документации <a href="https://yandex.ru/articles" target="_blank">Яндекс.Статьи</a>, <a href="https://zen.yandex.ru/" target="_blank">Яндекс.Дзен</a> и <a href="https://pulse.mail.ru/" target="_blank">Pulse.Mail.ru</a>. Вы можете без проблем подключить свой сайт к этим и другим агрегаторам.</p>
			<h3>Источники для турбо страниц Яндекса</h3>';
			if($articlesConfig->turbo){
				echo'<p class="row">
				Ниже приведен список RSS источников, которые можно использовать для турбо страниц Яндекса. По мере заполнения, будет происходить разбивка: максимум 1000 записей для одного источника.
				</p>
				<p class="row"><a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo0">Отключить источники для турбо страниц</a> <a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo_cfg">Настройки турбо страниц</a></p>
				
				<p>Подробнее про турбостраницы читайте на сайте <a href="https://yandex.ru/dev/turbo/" target="_blank">Турбо-страницы Яндекса</a>.</p>';
			
				echo'<table class="tables">
				<tr>
					<td class="tables_head" colspan="2">RSS источники</td>
					<td class="tables_head" style="text-align: right;">&nbsp;</td>
				</tr>';	
				if(($listIdArticles = json_decode($articlesStorage->get('list'), true)) != false){
					// Исключения
					$turboExceptions = explode(',', str_replace(' ', '', $articlesConfig->turboExceptions));
					$listIdArticles = array_diff($listIdArticles, $turboExceptions);

					$nom = count($listIdArticles);
					$countPage = ceil($nom / $articlesConfig->turboItems);
					for($i = 1; $i <= $countPage; ++$i){
						echo'<tr>
						<td class="img"><img src="include/link.svg" alt=""></td>
						<td><a href="//'.SERVER.'/'.$articlesConfig->idPage.'/'.$articlesConfig->turboId.'/'.$i.'.xml" target="_blank">'.SERVER.'/'.$articlesConfig->idPage.'/'.$articlesConfig->turboId.'/'.$i.'.xml</a></td>
						<td></td>
						</tr>';

					}

				}else{
					echo'<tr>
					<td>Нет данных для вывода</td>
					<td></td>
					<td></td>
					</tr>';
				}
				echo'
				<tr>
				<td colspan="3" style="background-color: #eee; text-align:left;">Записей: '.$nom.';&nbsp; Источников: '.$countPage.';</td>
				</tr>
				</table>
				</div>';
			}else{
				echo'<p class="row"><a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo1">Включить источники для турбо страниц</a> <a class="button" href="module.php?module='.$MODULE.'&amp;act=turbo_cfg">Настройки турбо страниц</a></p>
					<p>Подробнее про турбостраницы читайте на сайте <a href="https://yandex.ru/dev/turbo/" target="_blank">Турбо-страницы Яндекса</a>.</p>';
			}


		
	}

	if($act=='turbo1'){
		$articlesConfig->turbo = 1;
		if($articlesStorage->set('articlesConfig', json_encode($articlesConfig))){
			echo'<div class="msg">Настройки успешно сохранены</div>';
			System::notification('Включен вывод источников для турбо страниц Яндекса');
		}else{
			echo'<div class="msg">Произошла ошибка записи настроек</div>';
			System::notification('Произошла ошибка при сохранении параметров модуля статей', 'r');
		}
		?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=info\';', 3000);
</script>
<?php
	}

	if($act=='turbo0'){
		$articlesConfig->turbo = 0;
		if($articlesStorage->set('articlesConfig', json_encode($articlesConfig))){
			echo'<div class="msg">Настройки успешно сохранены</div>';
			System::notification('Отключен вывод источников для турбо страниц Яндекса');
		}else{
			echo'<div class="msg">Произошла ошибка записи настроек</div>';
			System::notification('Произошла ошибка при сохранении параметров модуля статей', 'r');
		}
		?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=info\';', 3000);
</script>
<?php
	}


	if($act=='turbo_cfg'){
		echo'<div class="header"><h1>Настройки турбо страниц</h1></div>
		'.$menu_page.'
		<div class="menu_page">
			<a href="module.php?module='.$MODULE.'&amp;act=info">&#8592; Вернуться назад</a>
		</div>
		<div class="content">
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
		<INPUT TYPE="hidden" NAME="act" VALUE="turbo_addcfg">
		<table class="tblform">
		
		<tr>
			<td>Количество item для одного источника:</td>
			<td><input type="text" name="turboItems" value="'.$articlesConfig->turboItems.'"><br>
			<span class="comment">Уменьшайте это число если получаете уведомление от яндекса о превышении веса загружаемого источника</span></td>
		</tr>

		<tr>
			<td>Идентификатор страницы источников:</td>
			<td><input type="text" name="turboId" value="'.$articlesConfig->turboId.'"><br>
			<span class="comment">Этот идентификатор используется для формирования уникального адреса на источники</span></td>
		</tr>
		
		<tr>
			<td>Время кеширования сформированных источников:</td>
			<td>
				<select name="turboCacheTime">
					<option value="0"'.($articlesConfig->turboCacheTime == '0'?' selected':'').'>Не кешировать (Не рекомендуется)
					<option value="900"'.($articlesConfig->turboCacheTime == '900'?' selected':'').'>15 минут
					<option value="1800"'.($articlesConfig->turboCacheTime == '1800'?' selected':'').'>30 минут
					<option value="3600"'.($articlesConfig->turboCacheTime == '3600'?' selected':'').'>1 час (По умолчанию)
					<option value="10800"'.($articlesConfig->turboCacheTime == '10800'?' selected':'').'>3 часа
					<option value="21600"'.($articlesConfig->turboCacheTime == '21600'?' selected':'').'>6 часов
					<option value="43200"'.($articlesConfig->turboCacheTime == '43200'?' selected':'').'>12 часов
					<option value="86400"'.($articlesConfig->turboCacheTime == '86400'?' selected':'').'>1 день
					<option value="259200"'.($articlesConfig->turboCacheTime == '259200'?' selected':'').'>3 дня
					<option value="604800"'.($articlesConfig->turboCacheTime == '604800'?' selected':'').'>1 неделю
					<option value="1209600"'.($articlesConfig->turboCacheTime == '1209600'?' selected':'').'>2 недели
					<option value="2678400"'.($articlesConfig->turboCacheTime == '2678400'?' selected':'').'>1 месяц
					<option value="8035200"'.($articlesConfig->turboCacheTime == '8035200'?' selected':'').'>3 месяца
					<option value="16070400"'.($articlesConfig->turboCacheTime == '16070400'?' selected':'').'>6 месяцев
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="top">
				Исключения для турбо страниц:
			</td>
			<td><textarea name="turboExceptions" style="height:150px;">'.$articlesConfig->turboExceptions.'</textarea><br>
			<span class="comment">Введите идентификаторы страниц, которым нужно запретить отображение на турбо страницах яндекса.<br>
			Идентификаторы должны быть введены через запятую.</span>
			</td>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="module.php?module='.$MODULE.'&act=info">Вернуться назад</a></td>
		</tr>
		</table>
		</form>
		
		</div>';
	}


	if($act=='turbo_addcfg'){
		 
		$articlesConfig->turboItems = is_numeric($_POST['turboItems'])?$_POST['turboItems']:1000;
		$articlesConfig->turboId = System::validPath($_POST['turboId'])?$_POST['turboId']:'turbo';
		$articlesConfig->turboCacheTime = is_numeric($_POST['turboCacheTime'])?$_POST['turboCacheTime']:3600;
		$articlesConfig->turboExceptions = htmlspecialchars($_POST['turboExceptions']);


		if($articlesStorage->set('articlesConfig', json_encode($articlesConfig))){
			echo'<div class="msg">Настройки успешно сохранены</div>';
			System::notification('Сохранены настройки турбо страниц в статьях');
		}else{
			echo'<div class="msg">Произошла ошибка записи настроек</div>';
			System::notification('Произошла ошибка при сохранении турбо страниц в статьях', 'r');
		}
		?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=turbo_cfg\';', 3000);
</script>
<?php
	}


	if($act=='index')
	{
		if($_GET['dub'] == '1'){
			$articles = htmlspecialchars(specfilter($_GET['articles']));
			if(($articlesParam = json_decode($articlesStorage->get('articles_'.$articles))) != false){
				$param['header'] = 'Дубликат статьи';
			}
		}
		
		
		echo'<div class="header"><h1>Добавление статьи</h1></div>
		'.$menu_page.'
		<div class="content">
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addarticles">
		<table class="tblform">
		<tr>
			<td>Заголовок статьи:</td>
			<td><input type="text" name="header" id="header" value=""></td>
		</tr>
		
		<tr>
			<td class="top">Превью статьи:</td>
			<td><TEXTAREA NAME="prev" ROWS="20" COLS="100" style="height:150px;">'.htmlspecialchars('<p>Превью статьи</p>').'</TEXTAREA></td>
		</tr>
		<tr>
			<td class="top">Содержимое статьи:</td>
			<td><TEXTAREA NAME="content" ROWS="20" COLS="100" style="height:250px;">'.htmlspecialchars('<p>Содержимое статьи</p>').'</TEXTAREA></td>
		</tr>
		<tr>
			<td>Разрешить комментирование</td>
			<td class="middle"><INPUT TYPE="checkbox" NAME="comments" VALUE="y"></td>
		</tr>
		<tr>
			<td>URL иллюстр. картинки:</td>
			<td>
				<input type="text" name="img" id="inputimg" value="/modules/articles/default.jpg"> 
				<button type="button" onClick="openwindow(\'window\', 750, \'auto\', iframefiles);">Выбрать файл</button>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><img src="/modules/articles/default.jpg" alt="" id="img" style="width: 380px;"></td>
		</tr>
		<tr>
			<td>Ключевые слова (keywords):</td>
			<td><input type="text" name="keywords" value=""></td>
		</tr>
		<tr>
			<td>Описание (description):</td>
			<td><input type="text" name="description" value=""></td>
		</tr>
		<td>Категория статьи:</td>
		<td>
			<select name="cat">
			<option value="">Без категории';
			foreach($articlesConfig->cat as $key => $value){
				echo'<option value="'.$key.'"'.($articlesParam->cat == $key?' selected':'').'>'.$value;
			}
			echo'</select><br>
			<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', gotocfgcat);">Перейти к управлению категориями</a>
		</td>
		<tr>
			<td>Идентификатор (исп. для URL):</td>
			<td><input type="text" name="id" id="id" value="'.uniqid().'"><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из заголовка статьи</a></td>
		</tr>';
//Custom
		foreach($articlesConfig->custom as $value){
			echo'<tr>
				<td>'.$value->name.':</td>
				<td>
					'.($value->type == 'input'?'<input type="text" name="custom['.$value->id.']" value="">':'').'
					'.($value->type == 'textarea'?'<textarea name="custom['.$value->id.']"  style="height:150px;"></textarea>':'').'
				</td>
			</tr>';
		}

		echo'<tr>
			<td>&nbsp;</td>
			<td><button type="button" onClick="submit();">Добавить статью</button> &nbsp; <a href="index.php?">Вернуться назад</a></td>
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
	
	if($act=='addarticles')
	{
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
		$param['date'] = date($articlesConfig->formatDate, $param['time']);
		// 5.1.18
		$param['cat'] = htmlspecialchars(specfilter($_POST['cat']));
		// 5.1.20
		$array = array();
		foreach($_POST['custom'] as $key => $value){
			$array[htmlspecialchars($key)] = $value;
		}
		$param['custom'] = $array;
		//




		$id = ($articlesStorage->iss('articles_'.$_POST['id']) == false && System::validPath($_POST['id']))?$_POST['id']:uniqid();
		
		if($articlesStorage->set('articles_'.$id, json_encode($param))){
			
			// Добавляем ID статьи в список
			$listIdArticles = json_decode($articlesStorage->get('list'), true); // Получили список ввиде массива
			$listIdArticles[] = $id;// Добавили новый элемент массива в конец
			$articlesStorage->set('list', json_encode($listIdArticles)); // Записали массив в виде json

			// Добавляем в категории 
			$listIdCat = json_decode($articlesStorage->get('category'), true); // Получили список ввиде массива
			$listIdCat[$id] = $param['cat'];// Добавили новый элемент массива в конец
			$articlesStorage->set('category', json_encode($listIdCat)); // Записали массив в виде json
			
			echo'<div class="msg">Статья успешно добавлена</div>';
			
			System::notification('Добавлена статья с заголовком: '.$param['header']);
			
		}else{
			
			echo'<div class="msg">Произошла ошибка при добавлении статьи</div>';
			
			System::notification('Произошла ошибка при добавлении статьи', 'r');
			
		}
		
	 
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&\';', 3000);
</script>
<?php	
	}
	
	if($act=='cfg'){
		
		$checked = ($articlesConfig->commentEngine == 1)?'checked':'';
		
		echo'<div class="header"><h1>Настройки модуля</h1></div>
		'.$menu_page.'
		<div class="content">
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcfg">
		<table class="tblform">
		
		<tr>
			<td>Количество превью записей на странице:</td>
			<td><input type="text" name="navigation" value="'.$articlesConfig->navigation.'" maxlength="3"></td>
		</tr>

		<tr>
			<td>Количество превью при выводе в блоке:</td>
			<td><input type="text" name="countInBlok" value="'.$articlesConfig->countInBlok.'" maxlength="3"></td>
		</tr>
		
		<tr>
			<td>Формат вывода даты (Формат функции date):</td>
			<td><input type="text" name="formatDate" value="'.$articlesConfig->formatDate.'"></td>
		</tr>
		
		<tr>
			<td>Идентификатор страницы со статьями:</td>
			<td><input type="text" name="idPage" value="'.$articlesConfig->idPage.'"></td>
		</tr>
		
		<tr>
			<td>Идентификатор страницы пользователей:</td>
			<td><input type="text" name="idUser" value="'.$articlesConfig->idUser.'"></td>
		</tr>
		
		<tr>
			<td>Шаблон для вывода превью:</td>
			<td class="middle">'.(file_exists(Module::pathRun($Config->template, 'articles.prev.template'))?'<a class="link" target="_blank" href="files.php?act=editor&amp;dir=../modules/'.$Config->template.'&file=../modules/'.$Config->template.'/articles.prev.template.php">Открыть редактор для правки шаблона</a>':'<span class="comment">Не предусмотрен</span>').'</td>
		</tr>
		
		<tr>
			<td>Шаблон для вывода статьи:</td>
			<td class="middle">'.(file_exists(Module::pathRun($Config->template, 'articles.content.template'))?'<a class="link" target="_blank" href="files.php?act=editor&amp;dir=../modules/'.$Config->template.'&file=../modules/'.$Config->template.'/articles.content.template.php">Открыть редактор для правки шаблона</a>':'<span class="comment">Не предусмотрен</span>').'</td>
		</tr>

		<tr>
			<td>Категории статей:</td>
			<td class="middle"><a href="module.php?module='.$MODULE.'&amp;act=cat">Перейти к управлению категориями</a></td>
		</tr>

		<tr>
			<td>Дополнительные поля:</td>
			<td class="middle"><a href="module.php?module='.$MODULE.'&amp;act=custom">Перейти к настройкам дополнительных полей</a></td>
		</tr>

		<tr>
			<td>Выводимая категория на начальной странице:</td>
			<td>
				<select name="indexCat">
				<option value="0">Выводить из всех категорий';
				foreach($articlesConfig->cat as $key => $value){
					echo'<option value="'.$key.'"'.($articlesConfig->indexCat === $key?' selected':'').'>'.$value;
				}
				echo'<option value="-1"'.($articlesConfig->indexCat == '-1'?' selected':'').'>Не выводить статьи на начальной странице
				</select>
			</td>
		</tr>

		<tr>
			<td>Выводимая категория в боковой блок:</td>
			<td>
				<select name="blokCat">
				<option value="0">Выводить из всех категорий';
				foreach($articlesConfig->cat as $key => $value){
					echo'<option value="'.$key.'"'.($articlesConfig->blokCat === $key?' selected':'').'>'.$value;
				}
				echo'</select>
			</td>
		</tr>

		<tr>
			<td>Использовать собственный сервис комментариев</td>
			<td class="middle"><INPUT TYPE="checkbox" NAME="commentEngine" VALUE="y" id="checkbox" '.$checked.'></td>
		</tr>
		<tr id="trCommentTemplate">
			<td class="top">Код сервиса комментариев:<br><span class="comment">Подробнее о сервисах комментариев <a href="http://my-engine.ru/articlescomments">тут</a></span></td>
			<td><TEXTAREA NAME="commentTemplate" id="textareaCommentTemplate" ROWS="20" COLS="100" style="height:150px;">'.htmlspecialchars($articlesConfig->commentTemplate).'</TEXTAREA></td>
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
	
	if($act=='addcfg'){
		
		if( !is_numeric($_POST['navigation']) || 
			!is_numeric($_POST['countInBlok']) || 
			$_POST['formatDate'] == ''||
			!System::validPath($_POST['idPage']) || 
			!System::validPath($_POST['idUser'])
		){
			echo'<div class="msg">Не все поля заполнены, или заполнены неправильно</div>';
		}else{ 
			$articlesConfig->navigation = htmlspecialchars(specfilter($_POST['navigation']));
			$articlesConfig->countInBlok = htmlspecialchars(specfilter($_POST['countInBlok']));
			$articlesConfig->formatDate = htmlspecialchars(specfilter($_POST['formatDate']));
			$articlesConfig->idPage = htmlspecialchars(specfilter($_POST['idPage']));
			$articlesConfig->idUser = htmlspecialchars(specfilter($_POST['idUser']));
			// $articlesConfig->prevTemplate = $_POST['prevTemplate'];
			$articlesConfig->contentTemplate = $_POST['contentTemplate'];
			$articlesConfig->commentTemplate = $_POST['commentTemplate'];
			$articlesConfig->commentEngine = ($_POST['commentEngine'] == 'y')?'1':'0';
			$articlesConfig->blokCat = htmlspecialchars(specfilter($_POST['blokCat']));
			$articlesConfig->indexCat = htmlspecialchars(specfilter($_POST['indexCat']));
			
			if($articlesStorage->set('articlesConfig', json_encode($articlesConfig))){
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены параметры модуля статей');
			}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении параметров модуля статей', 'r');
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

		
		echo'<div class="header"><h1>Редактирование статей</h1></div>
		'.$menu_page.'
		<div class="content">
		
		<div class="row">
		<form action="module.php?module='.$MODULE.'&amp;act=search" method="post">
		<input style="width: 250px;" type="text" name="q" value="" placeholder="Поиск по публикациям">
		<input type="submit" name="" value="Поиск"> 
		</form>
		</div>
		';
		 
		
		
		
		if(($listIdArticles = json_decode($articlesStorage->get('list'), true)) == false){
			echo'<div class="msg">Статьи ещё не созданы</div>';
		}else{
			
			
			echo'<div class="listrez">';
			
			//перевернули масив для вывода статей в обратном порядке
			$listIdArticles = array_reverse($listIdArticles);
			
			//
			$nom = count($listIdArticles);
			
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
					if($articlesStorage->iss('articles_'.$listIdArticles[$i])){
						$articlesParam = json_decode($articlesStorage->get('articles_'.$listIdArticles[$i]));
						
						$comments = ($articlesParam->comments == '1')?'<span style="color: green;">Включено</span>':'<span style="color: red;">Выключено</span>';
						// echo'<tr>
						// <td class="img"><img src="include/page.svg" alt=""></td>
						// <td><a href="module.php?module='.$MODULE.'&amp;act=editarticles&amp;articles='.$listIdArticles[$i].'&amp;nom_page='.$nom_page.'">'.$articlesParam->header.'</a></td>
						// <td><a href="//'.SERVER.'/'.$articlesConfig->idPage.'/'.$listIdArticles[$i].'" target="_blank">'.SERVER.'/'.$articlesConfig->idPage.'/'.$listIdArticles[$i].'</a></td>
						// <td>'.$comments.'</td>
						// <td>'.(isset($articlesParam->time)?date($articlesConfig->formatDate, $articlesParam->time):$articlesParam->date).'</td>
						// <td><a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\'module.php?module='.$MODULE.'&amp;act=dell&amp;articles='.$listIdArticles[$i].'&amp;nom_page='.$nom_page.'\', \''.$articlesParam->header.'\', \''.SERVER.'/'.$articlesConfig->idPage.'/'.$listIdArticles[$i].'\'));">Удалить</a></td>
						// </tr>';

						echo'<div class="item item_page">
						<div class="right_menu">
						<a href="module.php?module='.$MODULE.'&amp;act=editarticles&amp;articles='.$listIdArticles[$i].'&amp;dub=1">Создать дубликат</a>
						<a href="module.php?module='.$MODULE.'&amp;act=editarticles&amp;articles='.$listIdArticles[$i].'&amp;nom_page='.$nom_page.'">Редактировать</a>
						<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\'module.php?module='.$MODULE.'&amp;act=dell&amp;articles='.$listIdArticles[$i].'&amp;nom_page='.$nom_page.'\', \''.$articlesParam->header.'\', \''.SERVER.'/'.$articlesConfig->idPage.'/'.$listIdArticles[$i].'\'));">Удалить</a>
						</div>
						<div class="name_page"><img src="include/page.svg" alt=""> <a href="module.php?module='.$MODULE.'&amp;act=editarticles&amp;articles='.$listIdArticles[$i].'&amp;nom_page='.$nom_page.'">'.$articlesParam->header.'</a></div>
						<div>URL: <a href="//'.SERVER.'/'.$articlesConfig->idPage.'/'.$listIdArticles[$i].'" target="_blank">'.SERVER.'/'.$articlesConfig->idPage.'/'.$listIdArticles[$i].'</a></div>
						<div>Дата редактирования: '.(isset($articlesParam->time)?date($articlesConfig->formatDate, $articlesParam->time):$articlesParam->date).'</div>
						</div>';
					}else{
						echo'<div class="item item_page">
						<div style="color: red;">Error: индекс не связан ни с одной страницей</div>
						<div>Index: '.$listIdArticles[$i].'</div>
						<div class="right_menu">
						<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\'module.php?module='.$MODULE.'&amp;act=dell&amp;articles='.$listIdArticles[$i].'&amp;nom_page='.$nom_page.'\', \''.$articlesParam->header.'\', \''.SERVER.'/'.$articlesConfig->idPage.'/'.$listIdArticles[$i].'\'));">Удалить</a>
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
						echo'<a href="module.php?module='.$MODULE.'&amp;act=edit&amp;nom_page='.$i.'">'.$i.'</a> ';
					}
				}
				echo'</div>';
			}
			//конец навигации
		}
		echo'</div>';
	}


	if($act=='search'){
		function mb_ucfirst($text) {
			return mb_strtoupper(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($text, 1, null, 'UTF-8');
		}

		$q = isset($_POST['q'])?htmlspecialchars(mb_ucfirst(trim($_POST['q']))):'';

		echo'<div class="header"><h1>Поиск по публикациям</h1></div>
		'.$menu_page.'
		<div class="content">
		<div class="row">
			<form name="form_name" action="module.php?module='.$MODULE.'&amp;act=search" method="post">
			<input type="text"style="width: 250px;" name="q" value="'.$q.'" placeholder="Введите запрос" autofocus>
			<input type="submit" name="" value="Поиск">
			</form>
		</div>
		<p>Результаты поиска:</p>
		';

		if(($listIdArticles = json_decode($articlesStorage->get('list'), true)) == false){
			echo'<div class="msg">Статьи ещё не созданы</div>';
		}elseif($q != ''){
			
			// $pages = System::listPages();
			$listIdArticles = array_reverse($listIdArticles);//перевернули масив
			
			$pSearchName = array(); // Пустой массив результатов
			$pSearchTitle = array(); // Пустой массив результатов
			$pSearchKeywords = array(); // Пустой массив результатов
			$pSearchDescription = array(); // Пустой массив результатов
			$pSearchID = array(); // Пустой массив результатов
			
			foreach($listIdArticles as $value){
				if($articlesStorage->iss('articles_'.$value)){
					$articlesParam = json_decode($articlesStorage->get('articles_'.$value));

					if(mb_stripos($articlesParam->header, $q, 0, 'UTF-8') !== false){
						$pSearchName[] = array('header' => $articlesParam->header, 'time' => $articlesParam->time, 'id' => $value);
					}elseif(mb_stripos($articlesParam->title, $q, 0, 'UTF-8') !== false){
						$pSearchTitle[] = array('header' => $articlesParam->header, 'time' => $articlesParam->time, 'id' => $value);
					}elseif(mb_stripos($articlesParam->keywords, $q, 0, 'UTF-8') !== false){
						$pSearchKeywords[] = array('header' => $articlesParam->header, 'time' => $articlesParam->time, 'id' => $value);
					}elseif(mb_stripos($articlesParam->description, $q, 0, 'UTF-8') !== false){
						$pSearchDescription[] = array('header' => $articlesParam->header, 'time' => $articlesParam->time, 'id' => $value);
					}elseif(mb_stripos($value, $q, 0, 'UTF-8') !== false){
						$pSearchID[] = array('header' => $articlesParam->header, 'time' => $articlesParam->time, 'id' => $value);
					}
						
				}
			}

			
			$pSearchResult = array_merge($pSearchName, $pSearchTitle, $pSearchKeywords, $pSearchDescription, $pSearchID);
			echo'<div class="listrez">';
			$i = 0; // Счетчик показов
			foreach($pSearchResult as $value){
				//var_dump($key );

				echo'<div class="item item_page">
				<div class="right_menu">
				<a href="module.php?module='.$MODULE.'&amp;act=editarticles&amp;articles='.$value['id'].'&amp;dub=1">Создать дубликат</a>
				<a href="module.php?module='.$MODULE.'&amp;act=editarticles&amp;articles='.$value['id'].'&amp;">Редактировать</a>
				<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\'module.php?module='.$MODULE.'&amp;act=dell&amp;articles='.$value['id'].'&amp;\', \''.$value['header'].'\', \''.SERVER.'/'.$articlesConfig->idPage.'/'.$value['id'].'\'));">Удалить</a>
				</div>
				<div class="name_page"><img src="include/page.svg" alt=""> <a href="module.php?module='.$MODULE.'&amp;act=editarticles&amp;articles='.$value['id'].'&amp;">'.preg_replace('#'.$q.'#ius', '<span class="r">'.$q.'</span>', $value['header']).'</a></div>
				<div>URL: <a href="//'.SERVER.'/'.$articlesConfig->idPage.'/'.$value['id'].'" target="_blank">'.SERVER.'/'.$articlesConfig->idPage.'/'.$value['id'].'</a></div>
				<div>Дата редактирования: '.(isset($value['time'])?date($articlesConfig->formatDate, $value['time']):$value['date']).'</div>
				</div>';
				
				++$i; if($i == 100) break; // Ограничение показов
			}
			if($i == 0){
				echo'<div class="item item_page">
				<div>Ничего не найдено</div>
				</div>';
			}
			echo'</div>';
			
		}

		echo'</div>';

	}
	

	if($act=='dell')
	{
		$articles = htmlspecialchars(specfilter($_GET['articles']));
		$nom_page = htmlspecialchars(specfilter($_GET['nom_page']));
		if($articlesStorage->delete('articles_'.$articles)){ // Удадляем статью
			
			//Удаляем страницу из списка
			$listIdArticles = json_decode($articlesStorage->get('list'), true); // Получили список ввиде массива
			if(($key = array_search($articles, $listIdArticles)) !== false){
				unset($listIdArticles[$key]); // Удалили найденый элемент массива
			}
			$listIdArticles = array_values($listIdArticles); // Переиндексировали числовые индексы 
			$articlesStorage->set('list', json_encode($listIdArticles)); // Записали массив в виде json

			// Удаляем страницу из категорий
			$listIdCat = json_decode($articlesStorage->get('category'), true); // Получили список ввиде массива
			unset($listIdCat[$articles]);
			$articlesStorage->set('category', json_encode($listIdCat)); // Записали массив в виде json
			
			$articlesStorage->delete('comments_'.$articles); // Удаляем комментарии
			$articlesStorage->delete('count_'.$articles); // Удаляем счетчик комментариев
			
			System::notification('Удалена статья с идентификатором '.$articles.'', 'g');
			echo'<div class="msg">Статья успешно удалена</div>';
		}else{
			System::notification('Ошибка при удалении статьи с идентификатором '.$articles.', страница не найдена или запрос некорректен', 'r');
			echo'<div class="msg">Ошибка при удалении статьии</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=edit&nom_page=<?php echo $nom_page; ?>\';', 3000);
</script>
<?php	
	}

	if($act=='editarticles')
	{
		$articles = htmlspecialchars(specfilter($_GET['articles']));
		$nom_page = htmlspecialchars(specfilter($_GET['nom_page']));

		if(($articlesParam = json_decode($articlesStorage->get('articles_'.$articles))) != false){
			echo'<div class="header"><h1>'.($_GET['dub'] == '1'?'Создание дубликата статьи':'Редактирование статей').'</h1></div>
			'.$menu_page.'
			<div class="menu_page">
				<a href="module.php?module='.$MODULE.'&amp;act=edit&amp;nom_page='.$nom_page.'">&#8592; Вернуться назад</a>
			</div>
			<div class="content">
			<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post" style="margin:0px; padding:0px;">
			<INPUT TYPE="hidden" NAME="act" VALUE="'.($_GET['dub'] == '1'?'addarticles':'addedit').'">
      	    <INPUT TYPE="hidden" NAME="articles" VALUE="'.$articles.'">
			<INPUT TYPE="hidden" NAME="nom_page" VALUE="'.$nom_page.'">
			<input type="hidden" name="time" value="'.(isset($articlesParam->time)?$articlesParam->time:strtotime($articlesParam->date)).'">
			
			<table class="tblform">
			<tr>
				<td>Заголовок статьи:</td>
				<td><input type="text" name="header" id="header" value="'.$articlesParam->header.'"></td>
			</tr>
			
			<tr>
				<td class="top">Превью статьи:</td>
				<td><TEXTAREA NAME="prev" ROWS="20" COLS="100" style="height: 150px;">'.htmlspecialchars($articlesParam->prev).'</TEXTAREA></td>
			</tr>
			<tr>
				<td class="top">Содержимое статьи:</td>
				<td><TEXTAREA NAME="content" ROWS="20" COLS="100" style="height: 250px;">'.htmlspecialchars($articlesParam->content).'</TEXTAREA></td>
			</tr>';
			$checked = ($articlesParam->comments == 1)?'checked':'';
			echo'
			<tr>
				<td>Разрешить комментирование</td>
				<td class="middle"><INPUT TYPE="checkbox" NAME="comments" VALUE="y" '.$checked.'></td>
			</tr>
			<tr>
				<td>URL иллюстр. картинки:</td>
				<td>
					<input type="text" name="img" id="inputimg" value="'.$articlesParam->img.'"> 
					<button type="button" onClick="openwindow(\'window\', 700, \'auto\', iframefiles);">Выбрать файл</button>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><img src="'.$articlesParam->img.'" alt="" id="img" style="width: 380px;"></td>
			</tr>
			<tr>
				<td>Ключевые слова (keywords):</td>
				<td><input type="text" name="keywords" value="'.$articlesParam->keywords.'"></td>
			</tr>

			<tr>
				<td>Описание (description):</td>
				<td><input type="text" name="description" value="'.$articlesParam->description.'"></td>
			</tr>

			<td>Категория статьи:</td>
			<td>
				<select name="cat">
				<option value="">Без категории';
				foreach($articlesConfig->cat as $key => $value){
					echo'<option value="'.$key.'"'.($articlesParam->cat == $key?' selected':'').'>'.$value;
				}
				echo'</select><br>
				<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', gotocfgcat);">Перейти к управлению категориями</a>
			</td>
			
			<tr>
				<td>Идентификатор (исп. для URL):</td>
				<td><input type="text" name="id" id="id" value="'.($_GET['dub'] == '1'?uniqid():$articles).'"><br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'header\').value)">Сгенерировать из заголовка статьи</a></td>
			</tr>';


			foreach($articlesConfig->custom as $value){
				echo'<tr>
					<td>'.$value->name.':</td>
					<td>
						'.($value->type == 'input'?'<input type="text" name="custom['.$value->id.']" value="'.(isset($articlesParam->custom->{$value->id})?htmlspecialchars($articlesParam->custom->{$value->id}):'').'">':'').'
						'.($value->type == 'textarea'?'<textarea name="custom['.$value->id.']"  style="height:150px;">'.(isset($articlesParam->custom->{$value->id})?htmlspecialchars($articlesParam->custom->{$value->id}):'').'</textarea>':'').'
					</td>
				</tr>';
			}

			echo'<tr>
				<td>&nbsp;</td>
				<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="module.php?module='.$MODULE.'&amp;act=edit&amp;nom_page='.$nom_page.'">Вернуться назад</a></td>
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
		$articles = htmlspecialchars(specfilter($_POST['articles']));
		$nom_page = htmlspecialchars(specfilter($_POST['nom_page']));
		$id_articles = htmlspecialchars(specfilter($_POST['id'])); // Новый id для статьи
		$cat = htmlspecialchars(specfilter($_POST['cat'])); // Новый id для статьи

		
			if(($articlesParam = json_decode($articlesStorage->get('articles_'.$articles))) != false){
				
				$articlesParam->header = ($_POST['header'] == '')?'Без названия':htmlspecialchars(specfilter($_POST['header']));
				$articlesParam->img = htmlspecialchars(specfilter($_POST['img']));
				$articlesParam->keywords = htmlspecialchars(specfilter($_POST['keywords']));
				$articlesParam->description = htmlspecialchars(specfilter($_POST['description']));
				$articlesParam->prev = $_POST['prev'];
				$articlesParam->content = $_POST['content'];
				//$param['date'] = htmlspecialchars(specfilter($_POST['date'])); // удалено в 5.1.14
				$articlesParam->comments = ($_POST['comments'] == 'y')?'1':'0';
				// 5.1.14
				if(!isset($articlesParam->time)){
					$articlesParam->time = strtotime($articlesParam->date);
					$articlesParam->date = date($articlesConfig->formatDate, $articlesParam->time);
				}
				// 5.1.18
				$newCat = $articlesParam->cat != $cat?true:false;
				$articlesParam->cat = $cat;
				// 5.1.20
				$array = array();
				foreach($_POST['custom'] as $key => $value){
					$array[htmlspecialchars($key)] = $value;
				}
				$articlesParam->custom = $array;
				//

				
				if($articlesStorage->set('articles_'.$articles, json_encode($articlesParam))){
					if($newCat){
						// Замена категории
						$listIdCat = json_decode($articlesStorage->get('category'), true); // Получили список ввиде массива
						$listIdCat[$articles] = $articlesParam->cat;
						$articlesStorage->set('category', json_encode($listIdCat)); // Записали массив в виде json
					}
					if($id_articles != $articles){
						if($articlesStorage->iss('articles_'.$id_articles) == false && System::validPath($id_articles)){
							
							if($articlesStorage->set('articles_'.$id_articles, json_encode($articlesParam)) == false){
								System::notification('Ошибка при записи ключа articles_'.$id_articles.'', 'r');
							}
							
							if($articlesStorage->delete('articles_'.$articles) == false){
								System::notification('Ошибка при удалении ненужного ключа articles_'.$articles.'', 'r');
							}
							
							// Замена страницы в списке
							$listIdArticles = json_decode($articlesStorage->get('list'), true); // Получили список ввиде массива
							if(($key = array_search($articles, $listIdArticles)) !== false){
								$listIdArticles[$key] = $id_articles; // Заменили найденый элемент массива
							}
							$listIdArticles = array_values($listIdArticles); // Переиндексировали числовые индексы 
							$articlesStorage->set('list', json_encode($listIdArticles)); // Записали массив в виде json

							// Замена страницы в категории
							$listIdCat = json_decode($articlesStorage->get('category'), true); // Получили список ввиде массива
							$newArr = array();
							foreach($listIdCat as $key => $value){
								if($articles == $key){
									$newArr[$id_articles] = $value;
								}else{
									$newArr[$key] = $value;
								}
							}
							$articlesStorage->set('category', json_encode($newArr)); // Записали массив в виде json
							
							System::notification('Отредактирована статья со сменой идентификатора '.$articles.' на идентификатор '.$id_articles.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_articles.'/'.$id_articles, 'g');
							echo'<div class="msg">Статья успешно сохранена</div>';
						}else{
							System::notification('Отредактирована статья с неудачной попыткой смены идентификатора '.$articles.' на идентификатор '.$id_articles.', идентификатор '.$id_articles.' уже существует или некорректен, ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_articles.'/'.$articles, 'g');
							echo'<div class="msg">Статья сохранена но идентификатор изменить не удалось</div>';
						}
					}else{
						System::notification('Отредактирована статья с идентификатором '.$id_articles.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$id_page_articles.'/'.$id_articles, 'g');
						echo'<div class="msg">Статья успешно сохранена</div>';
					}
				}else{
					System::notification('Ошибка при сохранении страницы с идентификатором '.$articles.', ошибка записи', 'r');
					echo'<div class="msg">Ошибка при сохранении страницы</div>';
					
				}
			}else{
				System::notification('Ошибка при сохранении страницы с идентификатором '.$articles.', страница ненайдена', 'r');
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
		
		echo'<div class="header"><h1>Комментарии пользователей</h1></div>
		'.$menu_page.'';
		
		
		if ($articlesConfig->commentEngine){
			
			
			echo'
			<div class="content">
			
			
			';
			 
			
			
			
			if(($lastComments = json_decode($articlesStorage->get('lastComments'), true)) == false){
				echo'<div class="row"><a class="button" href="module.php?module='.$MODULE.'&amp;act=cfgcomment">Настройки комментариев</a></div>
				<div class="msg">Нет ни одного комментария</div>';
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
				
				//перевернули масив для вывода статей в обратном порядке
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
				echo'<div class="listrez row">';
				while($i < $var){
					if($i < $nom){
						
						
						echo'<div class="item">
							<div><INPUT TYPE="checkbox" NAME="comment[]" VALUE="'.$lastComments[$i]['idComment'].'"> '.($lastComments[$i]['published']?'':'<span class="r">Не опубликованно</span>').' Страница: <a href="//'.SERVER.'/'.$articlesConfig->idPage.'/'.$lastComments[$i]['idArticles'].'" target="_blank">'.SERVER.'/'.$articlesConfig->idPage.'/'.$lastComments[$i]['idArticles'].'</a></div>
							<h3><img src="include/user.svg" alt=""> '.$lastComments[$i]['login'].' </h3>
							'.ArticlesFormatText($lastComments[$i]['text']).'
							<div class="comment">Написанно '.human_time(time() - $lastComments[$i]['time']).' назад ( '.date("d.m.Y H:i", $lastComments[$i]['time']).' ) ; '.($lastComments[$i]['status']=='user'?'Зарегистрированный':'Гость').'; IP '.$lastComments[$i]['ip'].'</div>
						</div>';
						
					}
					++$i;
				}
				echo'</div>';
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
		if(($lastComments = json_decode($articlesStorage->get('lastComments'), true)) == false){
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
						$addComment[$value['idArticles']][] = array(
													'id' => $value['idComment'],
													'login' => $value['login'],
													'text' => $value['text'],
													'ip' => $value['ip'],
													'status' => $value['status'],
													'time' => $value['time']);
				}
			}
			$articlesStorage->set('lastComments', json_encode($lastComments));
			unset($lastComments);
			
			
			foreach($addComment as $key => $value){
				$arrayComments = json_decode($articlesStorage->get('comments_'.$key), true);
				
				foreach($value as $row){
					$arrayComments[] = $row;
					
					if(($CUser = User::getConfig($row['login'])) != false){
						++$CUser->numPost;
						User::setConfig($row['login'], $CUser);
					}
				}
				
				
				$arrayCount = count($arrayComments);
				if($arrayCount >= $articlesConfig->commentMaxCount){
					$arrayStart = $arrayCount -  round($articlesConfig->commentMaxCount / 1.5);
					$arrayComments = array_slice($arrayComments, $arrayStart, $arrayCount);
				}
				
				if($articlesStorage->set('comments_'.$key, json_encode($arrayComments))){
					
					$count = $articlesStorage->iss('count_'.$key)?$articlesStorage->get('count_'.$key):0;
					$count+= $countPP;
					$articlesStorage->set('count_'.$key, $count);
					
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
		if(($lastComments = json_decode($articlesStorage->get('lastComments'), true)) == false){
				echo'<div class="msg">Ошибка. Нет ни одного сообщения.</div>';
		}else{
			$dellComment = array();
			foreach($lastComments as $key => $value){
				if(in_array($value['idComment'], $_POST['comment'])){
					$dellComment[$value['idArticles']][] = $value['idComment'];
					unset($lastComments[$key]);
				}
			}
			// Переиндексировали числовые индексы 
			$lastComments = array_values($lastComments); 
			$articlesStorage->set('lastComments', json_encode($lastComments));
			unset($lastComments);
			
			
			foreach($dellComment as $key => $value){
				$arrayComments = json_decode($articlesStorage->get('comments_'.$key), true);
				foreach($arrayComments as $i => $row){
					if (in_array($row['id'], $value)){
						unset($arrayComments[$i]);
					}
				}
				// Переиндексировали числовые индексы 
				$arrayComments = array_values($arrayComments); 
				$articlesStorage->set('comments_'.$key, json_encode($arrayComments));
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
		$articlesStorage->set('lastComments', json_encode(array()));
		echo'<div class="msg">Очистка успешно завершена</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=comment\';', 3000);
</script>
<?php	
	}
	
	
	if($act=='cfgcomment'){
		
		echo'<div class="header"><h1>Комментарии пользователей</h1></div>
		'.$menu_page.'
		<div class="menu_page">
			<a href="module.php?module='.$MODULE.'&amp;act=comment">&#8592; Вернуться назад</a>
		</div>
		
		
		<div class="content">
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcfgcomment">
		<table class="tblform">
		<tr>
			<td>Работа комментариев:</td>
			<td>
				<SELECT NAME="commentEnable" >
					<OPTION VALUE="0" '.($articlesConfig->commentEnable == '0'?'selected':'').'>Выключено
					<OPTION VALUE="1" '.($articlesConfig->commentEnable == '1'?'selected':'').'>Включено
				</SELECT><br><span class="comment">Эта настройка глобальна для всех статей</span>
			</td>
		</tr>
		
		<tr>
			<td>Кто может писать комментарии:</td>
			<td>
				<SELECT NAME="commentRules" >
					<OPTION VALUE="0" '.($articlesConfig->commentRules == '0'?'selected':'').'>Все пользователи
					<OPTION VALUE="1" '.($articlesConfig->commentRules == '1'?'selected':'').'>Только зарегистрированные пользователи
					<OPTION VALUE="2" '.($articlesConfig->commentRules == '2'?'selected':'').'>Только пользователи с преференциями
					<OPTION VALUE="3" '.($articlesConfig->commentRules == '3'?'selected':'').'>Только администратор
				</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>Модерация перед публикацией:</td>
			<td>
				<SELECT NAME="commentModeration" >
					<OPTION VALUE="0" '.($articlesConfig->commentModeration == '0'?'selected':'').'>Не модерировать, публиковать сразу
					<OPTION VALUE="1" '.($articlesConfig->commentModeration == '1'?'selected':'').'>Модерировать не зарегистрированных пользователей и новичков
					<OPTION VALUE="2" '.($articlesConfig->commentModeration == '2'?'selected':'').'>Модерировать всех кроме пользователей с преференциями
				</SELECT>
			</td>
		</tr>
		
		<tr>
			<td>Количество сообщений новичка:</td>
			<td><input type="text" name="commentModerationNumPost" value="'.$articlesConfig->commentModerationNumPost.'">
			<br><span class="comment">Максимальное количество сообщений при котором пользователь считается новичком</span>
			</td>
		</tr>
		
		<tr>
			<td>Макс. символов для одного комментария:</td>
			<td><input type="text" name="commentMaxLength" value="'.$articlesConfig->commentMaxLength.'"></td>
		</tr>
		
		<tr>
			<td>Кол-во выводимых комментариев за раз:</td>
			<td><input type="text" name="commentNavigation" value="'.$articlesConfig->commentNavigation.'"></td>
		</tr>
		
		<tr>
			<td>Макс. комментариев для одной статьи:</td>
			<td><input type="text" name="commentMaxCount" value="'.$articlesConfig->commentMaxCount.'"></td>
		</tr>
		
		<tr>
			<td>Задержка на проверку новых комментарий:</td>
			<td><input type="text" name="commentCheckInterval" value="'.$articlesConfig->commentCheckInterval.'">
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
			
			$articlesConfig->commentEnable = $_POST['commentEnable'];
			$articlesConfig->commentRules = $_POST['commentRules'];
			$articlesConfig->commentModeration = $_POST['commentModeration'];
			$articlesConfig->commentModerationNumPost = $_POST['commentModerationNumPost'];
			$articlesConfig->commentMaxLength = $_POST['commentMaxLength'];
			$articlesConfig->commentNavigation = $_POST['commentNavigation'];
			$articlesConfig->commentMaxCount = $_POST['commentMaxCount'];
			$articlesConfig->commentCheckInterval = $_POST['commentCheckInterval'];
			
			if($articlesStorage->set('articlesConfig', json_encode($articlesConfig))){
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены параметры комментарий модуля статей');
			}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении параметров комментарий модуля статей', 'r');
			}
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=cfgcomment\';', 3000);
</script>
<?php	
	}
	
	
	
	
	











	if($act=='cat'){?>
<script type="text/javascript">
function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}
function upcat(elem) {
    elem.parentNode.parentNode.parentNode.insertBefore(elem.parentNode.parentNode, elem.parentNode.parentNode.previousSibling);
}
function downcat(elem) {
    elem.parentNode.parentNode.parentNode.insertBefore(elem.parentNode.parentNode, elem.parentNode.parentNode.nextSibling.nextSibling);
}
function addcat(){
	var cat = document.getElementById("cat");
	var ranndCat = random(5);
	var inner = '<tr><td>'+
				'<span class="comment">Идентификатор</span><br>'+
				'<input type="text" name="idCat[]" value="'+ ranndCat +'">'+
			'</td><td>'+
				'<span class="comment">Название категории</span><br>'+
				'<input type="text" name="nameCat[]" value="Категория '+ ranndCat +'">'+
			'</td><td>'+
				'<span class="comment"></span><br>'+
				'<button type="button" onClick="upcat(this);">Вверх</button> <button type="button" onClick="downcat(this);">Вниз</button> <button type="button" onClick="dellcat(this);">Удалить</button>'+
			'</td></tr>';
	cat.insertAdjacentHTML('beforeend', inner);
}
function dellcat(elem){
	elem.parentNode.parentNode.parentNode.removeChild(elem.parentNode.parentNode);
}
</script>
<?php	
		echo'<div class="header"><h1>Категории статей</h1></div>
		'.$menu_page.'
		<div class="menu_page">
			<a href="module.php?module='.$MODULE.'&amp;act=cfg">&#8592; Вернуться назад</a>
		</div>
		<div class="content">
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcat">
		<table class="tables">
		<tbody id="cat">';
		
		foreach($articlesConfig->cat as $key => $value){
			echo '<tr><td>
				<span class="comment">Идентификатор</span><br>
				<input type="text" name="idCat[]" value="'.$key.'">
			</td><td>
				<span class="comment">Название категории</span><br>
				<input type="text" name="nameCat[]" value="'.$value.'">
			</td><td>
				<span class="comment"></span><br>
				<button type="button" onClick="upcat(this);">Вверх</button> <button type="button" onClick="downcat(this);">Вниз</button> <button type="button" onClick="dellcat(this);">Удалить</button>
			</td></tr>';
		}

		echo'</tbody>
		</table>
		<p></p>
		<p><button type="button" onClick="addcat();">Добавить категорию</button> &nbsp; <button type="button" onClick="submit();">Сохранить изменения</button></p>
		</form>
		</div>';
	}
	
	if($act=='addcat'){
		$countCat = count($_POST['idCat']);
		$array = array();
		for($i=0; $i<$countCat; ++$i){
			if(System::validPath($_POST['idCat'][$i])){
				$idCat = htmlspecialchars($_POST['idCat'][$i]);
				$nameCat = htmlspecialchars($_POST['nameCat'][$i]);
				$array[$idCat] = $nameCat;
			}
			
		}
		$articlesConfig->cat = $array;
		// var_dump($articlesConfig);
		if($articlesStorage->set('articlesConfig', json_encode($articlesConfig))){
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены категории модуля статей');
		}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении категорий модуля статей', 'r');
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=cat\';', 3000);
</script>
<?php	
	}
//Custom
	if($act=='custom'){?>
<script type="text/javascript">
function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}
function upCustom(elem) {
    elem.parentNode.parentNode.parentNode.insertBefore(elem.parentNode.parentNode, elem.parentNode.parentNode.previousSibling);
}
function downCustom(elem) {
    elem.parentNode.parentNode.parentNode.insertBefore(elem.parentNode.parentNode, elem.parentNode.parentNode.nextSibling.nextSibling);
}
function addCustom(){
	var custom = document.getElementById("custom");
	var ranndCustom = random(5);
	var inner = '<tr><td>'+
				'<span class="comment">Идентификатор</span><br>'+
				'<input type="text" name="idCustom[]" value="'+ ranndCustom +'">'+
			'</td><td>'+
				'<span class="comment">Тип поля</span><br>'+
				'<select name="typeCustom[]">'+
					'<option value="input" selected>Однострочное поле'+
					'<option value="textarea">Многострочное поле'+
				'</select>'+
			'</td><td>'+
				'<span class="comment">Название поля</span><br>'+
				'<input type="text" name="nameCustom[]" value="Поле '+ ranndCustom +'">'+
			'</td><td>'+
				'<span class="comment"></span><br>'+
				'<button type="button" onClick="upCustom(this);">Вверх</button> <button type="button" onClick="downCustom(this);">Вниз</button> <button type="button" onClick="dellCustom(this);">Удалить</button>'+
			'</td></tr>';
	custom.insertAdjacentHTML('beforeend', inner);
}
function dellCustom(elem){
	elem.parentNode.parentNode.parentNode.removeChild(elem.parentNode.parentNode);
}
</script>
<?php	
		// var_dump($articlesConfig->custom);
		echo'<div class="header"><h1>Настройка дополнительных полей</h1></div>
		'.$menu_page.'
		<div class="menu_page">
			<a href="module.php?module='.$MODULE.'&amp;act=cfg">&#8592; Вернуться назад</a>
		</div>
		<div class="content">
		<p>В этом разделе вы можете задать какие дополнительные поля нужно вводить при добавлении статьи. 
		Идентификаторы, которые вы тут укажите, будут работать как дополнительные хештеги для шаблонов. Информацию про хештеги вы можете узнать на <a href="http://my-engine.ru/" target="_blank">нашем сайте</a>.</p>
		<form name="form_name" action="module.php?module='.$MODULE.'&amp;" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="addcustom">
		<table class="tables">
		<tbody id="custom">';
		
		foreach($articlesConfig->custom as $value){
			echo '<tr><td>
				<span class="comment">Идентификатор</span><br>
				<input type="text" name="idCustom[]" value="'.$value->id.'">
			</td><td>
				<span class="comment">Тип поля</span><br>
				<select name="typeCustom[]">
					<option value="input" '.($value->type == 'input'?'selected':'').'>Однострочное поле
					<option value="textarea" '.($value->type == 'textarea'?'selected':'').'>Многострочное поле
				</select>
			</td><td>
				<span class="comment">Название поля</span><br>
				<input type="text" name="nameCustom[]" value="'.$value->name.'">
			</td><td>
				<span class="comment"></span><br>
				<button type="button" onClick="upCustom(this);">Вверх</button> <button type="button" onClick="downCustom(this);">Вниз</button> <button type="button" onClick="dellCustom(this);">Удалить</button>
			</td></tr>';
		}

		echo'</tbody>
		</table>
		<p></p>
		<p><button type="button" onClick="addCustom();">Добавить новое поле</button> &nbsp; <button type="button" onClick="submit();">Сохранить изменения</button></p>
		</form>
		</div>';
	}

	if($act=='addcustom'){
		if (isset($_POST['idCustom'])){
			$countCat = count($_POST['idCustom']);
		}else{
			$countCat = 0;
		}
		$array = array();
		for($i=0; $i<$countCat; ++$i){
			$array[$i]['id'] = htmlspecialchars($_POST['idCustom'][$i]);
			$array[$i]['type'] = htmlspecialchars($_POST['typeCustom'][$i]);
			$array[$i]['name'] = htmlspecialchars($_POST['nameCustom'][$i]);
		}
		$articlesConfig->custom = $array;
		// print_r($articlesConfig);
		if($articlesStorage->set('articlesConfig', json_encode($articlesConfig))){
				echo'<div class="msg">Настройки успешно сохранены</div>';
				System::notification('Изменены дополнительные поля модуля статей');
		}else{
				echo'<div class="msg">Произошла ошибка записи настроек</div>';
				System::notification('Произошла ошибка при сохранении дополнительных полей модуля статей', 'r');
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>&act=custom\';', 3000);
</script>
<?php	
	}

?>
