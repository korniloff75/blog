<?php
require('../system/global.dat');
require('./include/start.dat');

if($status=='admin'){
	if(isset($_GET['page'])){$page = $_GET['page'];}elseif(isset($_POST['page'])){$page = $_POST['page'];}else{$page = $Config->indexPage;}
	if(isset($_GET['new_page'])){$new_page = $_GET['new_page'];}elseif(isset($_POST['new_page'])){$new_page = $_POST['new_page'];}else{$new_page='off';}
	$dub = isset($_GET['dub'])?$_GET['dub']:0;
	$page = htmlspecialchars(specfilter($page));

	if($act=='index'){
		if(Page::exists($page) || $new_page=='on'){
			
			if($new_page=='on'){
				$cfg_page['name'] = '';
				$cfg_page['title'] = '';
				$cfg_page['keywords'] = '';
				$cfg_page['description'] = '';
				$cfg_page['module'] = 'no/module';
				$cfg_page['show'] = 1;
				$cfg_page['template'] = 'def/template';
				$editor = '<p></p>';
				$id_page = uniqid();
				echo'<div class="header"><h1>Создание новой страницы</h1></div>';
			}else{
				$obj = new Page($page, $Config);
				$cfg_page['name'] = $obj->name;
				$cfg_page['title'] = $obj->title;
				$cfg_page['keywords'] = $obj->keywords;
				$cfg_page['description'] = $obj->description;
				$cfg_page['module'] = $obj->module;
				$cfg_page['show'] = $obj->show;
				$cfg_page['template'] = $obj->template;
				$editor = $obj->content();
				$editor = htmlspecialchars($editor);
				$id_page = $dub==1?uniqid():$page;
				if($dub == 1){
					echo'<div class="header"><h1>Создание дубликата страницы</h1></div>';
				}else{
					echo'<div class="header"><h1>Редактирование страницы</h1></div>';
				}
				
			}
			
			echo'
			<div class="menu_page"><a  href="pages.php">&#8592; Вернуться назад</a></div>
			<div class="content">
			<form name="form_name" action="editor.php?" method="post">
			<INPUT TYPE="hidden" NAME="act" VALUE="add">
			<INPUT TYPE="hidden" NAME="page" VALUE="'.$page.'">';
			if($new_page=='on') echo'<INPUT TYPE="hidden" NAME="new_page" VALUE="on">';
			if($dub == 1) echo'<INPUT TYPE="hidden" NAME="dub" VALUE="1">';
			echo'
			
			
			<div style="margin-bottom: 10px;">
				<div class="comment">Содержимое страницы</div>
				<div>
					<TEXTAREA id="editor" class="editor" NAME="editor" ROWS="20" COLS="100">'.$editor.'</TEXTAREA>
				</div>';
			if($Config->wysiwyg){
				if(Module::isWysiwyg($Config->wysiwyg)){
					require Module::pathRun($Config->wysiwyg, 'wysiwyg');
				}
			}
			echo'</div>
			<table class="tblform">
			<tr>
				<td class="tdleft">Название страницы:</td>
				<td><input type="text" name="new_cfg_name" id="name" value="'.$cfg_page['name'].'"></td>
			</tr>
			
			<tr>
				<td class="tdleft">Титульный заголовок (title):</td>
				<td><input type="text" name="new_cfg_title" value="'.$cfg_page['title'].'"></td>
			</tr>
			
			<tr>
				<td class="tdleft">Ключевые слова (keywords):</td>
				<td><input type="text" name="new_cfg_keywords" value="'.$cfg_page['keywords'].'"></td>
			</tr>
			
			<tr>
				<td class="tdleft">Описание (description):</td>
				<td><input type="text" name="new_cfg_description" value="'.$cfg_page['description'].'"></td>
			</tr>
			
			<tr>
				<td class="tdleft">Модуль для страницы:</td>
				<td>';
		
				echo'<SELECT NAME="new_cfg_module">';
				echo'<OPTION VALUE="no/module" '.($cfg_page['module'] == 'no/module'?'selected':'').'>Страница без модуля';
				$listModules = System::listModules();
				foreach($listModules as $value){
					if(Module::isIntegrationPage($value)){
						$info = Module::info($value);
						echo '<OPTION VALUE="'.$value.'" '.($cfg_page['module'] == $value?'selected':'').'>'.$info['name'].' '.$info['version'];
					}
				}
				echo'</SELECT>
				</td>
			</tr>
			
			<tr>
				<td class="tdleft">Доступность для просмотра:</td>
				<td>
					<SELECT NAME="new_cfg_show">';
					echo'<OPTION VALUE="1" '.($cfg_page['show'] == 1?'selected':'').'>Всем пользователям';
					echo'<OPTION VALUE="2" '.($cfg_page['show'] == 2?'selected':'').'>Пользователям с преференциями и администратору';
					echo'<OPTION VALUE="0" '.($cfg_page['show'] == 0?'selected':'').'>Только администратору';
					echo'</SELECT>
				</td>
			</tr>
			<tr>
				<td class="tdleft">Шаблон для вывода:</td>
				<td>
					<SELECT NAME="new_cfg_template">
					<OPTION VALUE="def/template"'.($cfg_page['template'] == 'def/template'?' selected':'').'>Шаблон по умолчанию';
					$listModules = System::listModules();
					foreach($listModules as $value){
							if(Module::isTemlate($value)){
								$info = Module::info($value);
								echo'<OPTION VALUE="'.$value.'"'.($cfg_page['template'] == $value?' selected':'').'>'.$info['name'];
							}
					}
					echo'</SELECT>
				</td>
			</tr>
			<tr>
				<td class="tdleft">Идентификатор (исп. для URL):</td>
				<td>
					<input type="text" name="new_cfg_id_page" id="id" value="'.$id_page.'"'.($id_page==$Config->indexPage?' readonly':'').'>
					'.($id_page==$Config->indexPage?'<br><span class="comment">Идентификатор главной страницы изменять нельзя, вы можете его переназначить в настройках системы.</span>':'').' 
					 <br><a href="javascript:void(0);" onclick="document.getElementById(\'id\').value = urlRusLat(document.getElementById(\'name\').value)">Сгенерировать из названия страницы</a>
				</td>
			</tr>
			
			<tr>
				<td class="tdleft">&nbsp;</td>
				<td><button type="button" onClick="submit();">Сохранить</button> &nbsp; <a href="pages.php">Вернуться назад без сохранения</a></td>
			</tr>
			</table>
			</form>
			</div>';
		}else{
			if($page == '')$page = 'НЕИЗВЕСТНО';
			System::notification('Ошибка при открытии страницы с идентификатором '.$page.', страница не найдена', 'r');
			echo'<div class="msg">Ошибка при открытии страницы</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'pages.php?\';', 3000)
</script>
<?php
		}
	}

	if($act=='add'){
		//var_dump($_POST);
		if($new_page=='on' || $_POST['dub'] == 1){
			$page = $_POST['new_cfg_id_page'];
			
			if(System::validPath($page)){
				if(Page::exists($page)){
					$page = $page.'_'.uniqid();
					echo'<div class="msg">Страница успешно создана, но с другим идентификатором, т.к введенный уже занят</div>';
				}else{
					echo'<div class="msg">Страница успешно создана</div>';
				}
			}else{
				$page = uniqid();
				echo'<div class="msg">Страница успешно создана, но с другим идентификатором, т.к введенный был некорректен</div>';
			}
			System::notification('Создана новая страница с идентификатором '.$page.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$page, 'g');
		}else{
			if($page != $_POST['new_cfg_id_page'] && $page != $Config->indexPage){
				if (Page::rename($page, $_POST['new_cfg_id_page'])){
					System::notification('Отредактирована страница со сменой идентификатора '.$page.' на идентификатор '.$_POST['new_cfg_id_page'].', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$_POST['new_cfg_id_page'].'', 'g');
					
					$page = $_POST['new_cfg_id_page'];
					echo'<div class="msg">Страница успешно сохранена</div>';
				}else{
					System::notification('Отредактирована страница с неудачной сменой идентификатора '.$page.' на идентификатор '.$_POST['new_cfg_id_page'].', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.$page, 'g');
					echo'<div class="msg">Страница успешно сохранена, но без смены идентификатора</div>';
				}
			}else{
				System::notification('Отредактирована страница с идентификатором '.$page.', ссылка на страницу http://'.$_SERVER['SERVER_NAME'].'/'.($page == $Config->indexPage?'':$page), 'g');
				
				echo'<div class="msg">Страница успешно сохранена</div>';
			}
		}
		
		
		Page::add(
				$page, 
				$_POST['new_cfg_name'], 
				$_POST['new_cfg_title'], 
				$_POST['new_cfg_keywords'], 
				$_POST['new_cfg_description'], 
				$_POST['new_cfg_show'], 
				$_POST['new_cfg_module'], 
				$_POST['new_cfg_template'], 
				$_POST['editor']);
		
		setcookie('lastEditPage',htmlspecialchars($_POST['new_cfg_id_page']),time()+32000000,'/');
?>
<script type="text/javascript">
var nfa = '<div class="a" style="text-align: center;">'+
	'<a href="editor.php?page=<?php echo htmlspecialchars($_POST['new_cfg_id_page']);?>">Вернуться к редактированию страницы</a><br><br>или<br><br><a href="pages.php?">Вернуться к списку страниц</a>'+
	'</div>' +
	'</form>';
setTimeout(function(){
	openwindow('window', 370, 'auto', nfa);
}, 1000);
setTimeout('window.location.href = \'pages.php?\';', 5000);
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