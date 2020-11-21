<?php
$ACTIVMENU = 'bloks';
require('../system/global.dat');
require('./include/start.dat');

if($status=='admin'){
	if($act=='index'){
		$info = Module::info($Config->template);
		
		
?>
<script type="text/javascript">
function dellblok(str_blok, l_o_r){
return '<div class="a">Подтвердите удаление блока</div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'bloks.php?act=dell_blok&amp;str_blok='+str_blok+'&amp;l_o_r='+l_o_r+'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}

function delllink(link_file, str_link){
return '<div class="a">Подтвердите удаление ссылки</div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'bloks.php?act=dell_link&amp;link_file='+link_file+'&amp;str_link='+str_link+'&amp;rkt=left\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}
</script>
<?php
		echo'<div class="header">
			<h1>Управление меню</h1>
		</div>
		
		<div class="content">';
		if($info['gorizont_menu'] == 0 && $info['left_menu'] == 0 && $info['right_menu'] == 0) 
			echo'<div class="msg">Шаблоном не предусмотрено редактировать меню</div>';
		
		
		
		if($info['gorizont_menu']){
			echo'<table class="tables">
			<tr>
				
				<td class="tables_head" colspan="2">Главное меню</td>
				<td class="tables_head" style="text-align: right;"><a href="bloks.php?act=new_link&link_file=gorizont" class="button addlink" title="Добавить ссылку">Добавить</a></td>
			</tr>';
			if(file_exists('../data/bloks/links_gorizont.dat')){
				$link_data = file('../data/bloks/links_gorizont.dat');
				$nom = count($link_data);
				if($nom == 0){
					echo'<tr><td class="img"><img src="include/link.svg" alt=""></td><td>Ссылки еще не созданы</td><td>---</td></tr>';
				}
				for($q = 0; $q < $nom; ++$q){
					$link_cfg = explode('<||>',$link_data[$q]);
					if($link_cfg[0] == 'page'){//если ссылка имеет тип На страницу движка
						echo'<tr><td class="img"><img src="include/link.svg" alt=""></td><td>'.$link_cfg[2].'</td><td style="text-align: right;"><a href="bloks.php?act=up_link&amp;link_file=gorizont&amp;str_link='.$q.'" title="Переместить ссылку вверх">Вверх</a> &nbsp; <a href="bloks.php?act=down_link&amp;link_file=gorizont&amp;str_link='.$q.'" title="Переместить ссылку вниз">Вниз</a> &nbsp; <a href="bloks.php?act=editor_link&amp;link_file=gorizont&amp;str_link='.$q.'&amp;rkt=gorizont" title="Редактировать ссылку">Редактировать</a> &nbsp; <a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', delllink(\'gorizont\', \''.$q.'\'));" title="Удалить ссылку">Удалить</a></td></tr>';
					}elseif($link_cfg[0] == 'http'){//если ссылка имеет тип Простая http ссылка
						echo'<tr>
						<td class="img"><img src="include/link.svg" alt=""></td><td>'.$link_cfg[2].'</td><td style="text-align: right;"><a href="bloks.php?act=up_link&amp;link_file=gorizont&amp;str_link='.$q.'" title="Переместить ссылку вверх">Вверх</a> &nbsp; <a href="bloks.php?act=down_link&amp;link_file=gorizont&amp;str_link='.$q.'" title="Переместить ссылку вниз">Вниз</a> &nbsp; <a href="bloks.php?act=editor_link&amp;link_file=gorizont&amp;str_link='.$q.'&amp;rkt=gorizont" title="Редактировать ссылку">Редактировать</a> &nbsp; <a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', delllink(\'gorizont\', \''.$q.'\'));" title="Удалить ссылку">Удалить</a></td></tr>';
					}
				}
			}else{
				echo'<tr><td style="background: url(include/link.svg) 50% 50% no-repeat; text-align: center;">&nbsp;</td><td>Ошибка</td><td>---</td></tr>';
			}
			echo'</table>';
		}
		
		
		
		if($info['left_menu']){
			echo'<table class="tables" >
			<tr>
				<td class="tables_head" colspan="2">Левая колонка</td>
				<td class="tables_head"><a href="bloks.php?act=new_blok&amp;l_o_r=left" class="button addlink" title="Добавить новый блок">Добавить</a></td>
			</tr>';
			if(file_exists('../data/bloks/left_bloks.dat')){
				$blok_data = file('../data/bloks/left_bloks.dat');
				$nom = count($blok_data);
				if($nom == 0){
					echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td>Блоки ещё не созданы</td><td style="text-align: right;">---</td></tr>';
				}
				for($i = 0; $i < $nom; ++$i){
					$blok_cfg = explode('<||>',$blok_data[$i]);
					$lin = '<a href="bloks.php?act=up_blok&amp;str_blok='.$i.'&amp;l_o_r=left" title="Переместить блок вверх">Вверх</a> &nbsp; 
							<a href="bloks.php?act=down_blok&amp;str_blok='.$i.'&amp;l_o_r=left" title="Переместить блок вниз">Вниз</a> &nbsp; 
							'.($info['right_menu']?'<a href="bloks.php?act=go_to_blok&amp;str_blok='.$i.'&amp;l_o_r=right" title="Переместить блок в правую колонку">Вправо</a> &nbsp; ':'').'
							<a href="bloks.php?act=editor_blok&amp;str_blok='.$i.'&amp;l_o_r=left" title="Редактировать блок">Редактировать</a> &nbsp; 
							<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dellblok(\''.$i.'\',\'left\'));" title="Удалить блок">Удалить</a>';
					if($blok_cfg[1] == 'links'){//если блок имеет тип Ссылки
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td><b>'.$blok_cfg[2].'</b></td><td style="text-align: right;">'.$lin.' &nbsp; <a href="bloks.php?act=new_link&amp;link_file='.$blok_cfg[0].'&amp;rkt=left" class="button addlink" title="Добавить ссылку в этот блок">Добавить</a></td></tr>';
						if(file_exists('../data/bloks/links_'.$blok_cfg[0].'.dat')){
							$link_data = file('../data/bloks/links_'.$blok_cfg[0].'.dat');
							$nom_0067 = count($link_data);
							if($nom_0067 == 0){
								echo'<tr><td class="img">&nbsp;</td><td><img src="include/link.svg" alt=""> &nbsp; Ссылки еще не созданы</td><td>---</td></tr>';
							}
							for($q = 0; $q < $nom_0067; ++$q){
								$link_cfg = explode('<||>',$link_data[$q]);
								if($link_cfg[0] == 'page'){//если ссылка имеет тип На страницу движка
									echo'<tr><td class="img">&nbsp;</td><td><img src="include/link.svg" alt=""> &nbsp; '.$link_cfg[2].'</td><td style="text-align: right;"><a href="bloks.php?act=up_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вверх">Вверх</a> &nbsp; <a href="bloks.php?act=down_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вниз">Вниз</a> &nbsp; <a href="bloks.php?act=editor_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Редактировать ссылку">Редактировать</a> &nbsp; <a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', delllink(\''.$blok_cfg[0].'\', \''.$q.'\'));"  title="Удалить ссылку">Удалить</a></td></tr>';
								}elseif($link_cfg[0] == 'http'){//если ссылка имеет тип Простая http ссылка
									echo'<tr><td class="img">&nbsp;</td><td><img src="include/link.svg" alt=""> &nbsp; '.$link_cfg[2].'</td><td style="text-align: right;"><a href="bloks.php?act=up_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вверх">Вверх</a> &nbsp; <a href="bloks.php?act=down_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вниз">Вниз</a> &nbsp; <a href="bloks.php?act=editor_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Редактировать ссылку">Редактировать</a> &nbsp; <a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', delllink(\''.$blok_cfg[0].'\', \''.$q.'\'));"  title="Удалить ссылку">Удалить</a></td></tr>';
								}
							}
						}else{
							echo'<tr><td class="img">&nbsp;</td><td> &nbsp; Ошибка</td><td>---</td></tr>';
						}
					}elseif($blok_cfg[1] == 'html'){//если блок имеет тип HTML
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td><b>'.$blok_cfg[2].'</b></td><td style="text-align: right;">'.$lin.'</td></tr>';
					}elseif($blok_cfg[1] == 'module'){//если блок имеет тип Модуль
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td><b>'.$blok_cfg[2].'</b></td><td style="text-align: right;">'.$lin.'</td></tr>';
					}else{
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td style="color: red;">Ошибка</td><td style="text-align: right;"><a href="bloks.php?act=dell_blok&amp;str_blok='.$i.'&amp;l_o_r=left" >Удалить</a></td></tr>';
					}
				}
			}else{
				echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td style="color: red;">Ошибка</td><td style="text-align: right;">---</td></tr>';
			}
			echo'</table>';
		}
		
		if($info['right_menu']){
			echo'<table class="tables" >
			<tr>
				<td class="tables_head"  colspan="2">Правая колонка</td>
				<td class="tables_head"><a href="bloks.php?act=new_blok&amp;l_o_r=right" class="button addlink" title="Добавить новый блок">Добавить</a></td>
			</tr>';
			if(file_exists('../data/bloks/right_bloks.dat')){
				$blok_data = file('../data/bloks/right_bloks.dat');
				$nom = count($blok_data);
				if($nom == 0){
					echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td>Блоки ещё не созданы</td><td style="text-align: right;">---</td></tr>';
				}
				for($i = 0; $i < $nom; ++$i){
					$blok_cfg = explode('<||>',$blok_data[$i]);
					$lin = '<a href="bloks.php?act=up_blok&amp;str_blok='.$i.'&amp;l_o_r=right" title="Переместить блок вверх">Вверх</a> &nbsp; 
							<a href="bloks.php?act=down_blok&amp;str_blok='.$i.'&amp;l_o_r=right" title="Переместить блок вниз">Вниз</a> &nbsp; 
							'.($info['left_menu']?'<a href="bloks.php?act=go_to_blok&amp;str_blok='.$i.'&amp;l_o_r=left" title="Переместить блок в левую колонку">Влево</a> &nbsp; ':'').'
							<a href="bloks.php?act=editor_blok&amp;str_blok='.$i.'&amp;l_o_r=right" title="Редактировать блок">Редактировать</a> &nbsp; 
							<a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dellblok(\''.$i.'\',\'right\'));" title="Удалить блок">Удалить</a>';
					if($blok_cfg[1] == 'links'){//если блок имеет тип Ссылки
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td><b>'.$blok_cfg[2].'</b></td><td style="text-align: right;">'.$lin.' &nbsp; <a href="bloks.php?act=new_link&amp;link_file='.$blok_cfg[0].'&amp;rkt=right" class="button addlink" title="Добавить ссылку в этот блок">Добавить</a></td></tr>';
						if(file_exists('../data/bloks/links_'.$blok_cfg[0].'.dat')){
							$link_data = file('../data/bloks/links_'.$blok_cfg[0].'.dat');
							$nom_0067 = count($link_data);
							if($nom_0067 == 0){
								echo'<tr><td  class="img">&nbsp;</td><td ><img src="include/link.svg" alt=""> &nbsp; Ссылки еще не созданы</td><td style="text-align: right;">---</td></tr>';
							}
							for($q = 0; $q < $nom_0067; ++$q){
								$link_cfg = explode('<||>',$link_data[$q]);
								if($link_cfg[0] == 'page'){//если ссылка имеет тип На страницу движка
									echo'<tr><td class="img">&nbsp;</td><td ><img src="include/link.svg" alt=""> &nbsp; '.$link_cfg[2].'</td><td style="text-align: right;"><a href="bloks.php?act=up_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вверх">Вверх</a> &nbsp; <a href="bloks.php?act=down_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вниз">Вниз</a> &nbsp; <a href="bloks.php?act=editor_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Редактировать ссылку">Редактировать</a> &nbsp; <a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', delllink(\''.$blok_cfg[0].'\', \''.$q.'\'));"  title="Удалить ссылку">Удалить</a></td></tr>';
								}elseif($link_cfg[0] == 'http'){//если ссылка имеет тип Простая http ссылка
									echo'<tr><td class="img">&nbsp;</td><td ><img src="include/link.svg" alt=""> &nbsp; '.$link_cfg[2].'</td><td style="text-align: right;"><a href="bloks.php?act=up_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вверх">Вверх</a> &nbsp; <a href="bloks.php?act=down_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Переместить ссылку вниз">Вниз</a> &nbsp; <a href="bloks.php?act=editor_link&amp;link_file='.$blok_cfg[0].'&amp;str_link='.$q.'" title="Редактировать ссылку">Редактировать</a> &nbsp; <a href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', delllink(\''.$blok_cfg[0].'\', \''.$q.'\'));"  title="Удалить ссылку">Удалить</a></td></tr>';
								}
							}
						}else{
							echo'<tr><td class="img">&nbsp;</td><td ><img src="include/link.svg" alt=""> &nbsp; Ошибка</td><td style="text-align: right;">---</td></tr>';
						}
					}elseif($blok_cfg[1] == 'html'){//если блок имеет тип HTML
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td><b>'.$blok_cfg[2].'</b></td><td style="text-align: right;">'.$lin.'</td></tr>';
					}elseif($blok_cfg[1] == 'module'){//если блок имеет тип Модуль
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td><b>'.$blok_cfg[2].'</b></td><td style="text-align: right;">'.$lin.'</td></tr>';
					}else{
						echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td style="color: red;">Ошибка</td><td style="text-align: right;"><a href="bloks.php?act=dell_blok&amp;str_blok='.$i.'&amp;l_o_r=right">Удалить</a></td></tr>';
					}
				}
			}else{
				echo'<tr><td class="img"><img src="include/blok.svg" alt=""></td><td style="color: red;">Ошибка</td><td style="text-align: right;">---</td></tr>';
			}
			echo'</table>';
		}
		echo'</div>';
	}
	
	if($act=='new_blok2'){
		$name_blok = htmlspecialchars(specfilter($_POST['name_blok']));
		$type_blok = htmlspecialchars(specfilter($_POST['type_blok']));
		$l_o_r = htmlspecialchars(specfilter($_POST['l_o_r']));
		$editor = $_POST['editor'];
		
		if($name_blok != '' && $type_blok != '' && ($l_o_r == 'left' || $l_o_r == 'right')){
			if($type_blok == 'links'){
				$new_id = time();
				$kod=''.$new_id.'<||>links<||>'.$name_blok.'<||>';
				filefputs('../data/bloks/'.$l_o_r.'_bloks.dat', $kod."\n", 'a+');
				filefputs('../data/bloks/links_'.$new_id.'.dat', '', 'w+');
			}elseif($type_blok == 'html'){
				//if(get_magic_quotes_gpc()){$editor = stripslashes ($editor);}//Удаляем слеши сами если magic_quotes включены
				$new_id = time();
				$kod=''.$new_id.'<||>html<||>'.$name_blok.'<||>';
				filefputs('../data/bloks/'.$l_o_r.'_bloks.dat', $kod."\n", 'a+');
				filefputs('../data/bloks/html_'.$new_id.'.dat', $editor, 'w+');
			}else{
				$kod=''.$type_blok.'<||>module<||>'.$name_blok.'<||>';
				filefputs('../data/bloks/'.$l_o_r.'_bloks.dat', $kod."\n", 'a+');
			}
			echo'<div class="msg">Блок успешно добавлен</div>';
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
	}
	
	if($act=='new_blok'){
		echo'
		<div class="header">
			<h1>Добавление нового блока</h1>
		</div>
		<div class="menu_page"><a href="bloks.php">&#8592; Вернуться назад</a></div>
		<div class="content">
		<form name="forma" action="bloks.php?" method="post">
		<INPUT TYPE="hidden" NAME="act" VALUE="new_blok2">
		<INPUT TYPE="hidden" NAME="l_o_r" VALUE="'.htmlspecialchars($_GET['l_o_r']).'">
		
		<table class="tblform">
		<tr>
			<td>Название блока:</td>
			<td><input type="text" name="name_blok" value="" size="25"></td>
		</tr>
		<tr>
			<td>Тип блока:</td>
			<td>
				<SELECT NAME="type_blok" onChange="document.getElementById(\'ed\').style.display = (document.getElementById(\'sel\').selected)?\'\':\'none\';">
				<OPTION VALUE="links" selected>Блок из ссылок на страницы сайта
				<OPTION VALUE="html" id="sel">Блок html кода';
				$listModules = System::listModules();
				foreach($listModules as $value){
					if(Module::isIntegrationBlok($value)){
						$info = Module::info($value);
						echo '<OPTION VALUE="'.$value.'">Модуль "'.$info['name'].' '.$info['version'].'"';
					}
				}
				echo'</SELECT>
			</td>
		</tr>
		<tr id="ed" style="display: none;">
			<td>&nbsp;</td>
			<td>
				<TEXTAREA id="editor" NAME="editor" ROWS="20" COLS="100" class="editor">'.htmlspecialchars('<p>Содержимое блока</p>').'</TEXTAREA>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="" value="Добавить блок"> &nbsp; <a href="bloks.php?">Вернуться назад</a></td>
		</tr>
		</table>
		
		</form>
		</div>';
	}
	
	if($act=='dell_blok'){
		$str_blok = htmlspecialchars(specfilter($_GET['str_blok']));
		$l_o_r = htmlspecialchars(specfilter($_GET['l_o_r']));
		
		if(is_numeric($str_blok) && ($l_o_r == 'left' || $l_o_r == 'right')){
			$bloks_list = file('../data/bloks/'.$l_o_r.'_bloks.dat');
			$cfg_blok = explode('<||>',$bloks_list[$str_blok]);
			if($cfg_blok[1] == 'links'){ unlink('../data/bloks/links_'.$cfg_blok[0].'.dat'); }
			if($cfg_blok[1] == 'html'){ unlink('../data/bloks/html_'.$cfg_blok[0].'.dat'); }
			//Удаляем из списка
			$nom = count($bloks_list);
			$f = fopen('../data/bloks/'.$l_o_r.'_bloks.dat', 'w+');
			for($i = 0; $i < $nom; ++$i){
				if($str_blok != $i){
					fputs($f,$bloks_list[$i]);
				}
			}
			fclose($f);
			echo'<div class="msg">Блок успешно удален</div>';
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
	}
	
	
	if($act=='editor_blok'){
		$str_blok = htmlspecialchars(specfilter($_GET['str_blok']));
		$l_o_r = htmlspecialchars(specfilter($_GET['l_o_r']));
		
		if(is_numeric($str_blok) && ($l_o_r == 'left' || $l_o_r == 'right')){
			$bloks_list = file('../data/bloks/'.$l_o_r.'_bloks.dat');
			$cfg_blok = explode('<||>',$bloks_list[$str_blok]);
			echo'<div class="header"><h1>Редактирование блока</h1></div>
			<div class="menu_page"><a href="bloks.php">&#8592; Вернуться назад</a></div>
			<div class="content">
			
			<form name="forma" action="bloks.php?" method="post">
			<INPUT TYPE="hidden" NAME="act" VALUE="editor_blok2">
			<INPUT TYPE="hidden" NAME="str_blok" VALUE="'.$str_blok.'">
			<INPUT TYPE="hidden" NAME="l_o_r" VALUE="'.$l_o_r.'">
			
			<table class="tblform">
			<tr>
				<td>Название блока:</td>
				<td><input type="text" name="name_blok" value="'.$cfg_blok[2].'" size="25"></td>
			</tr>
			<tr>
				<td>Тип блока:</td>
				<td>';
			echo'<SELECT NAME="type_blok" onChange="document.getElementById(\'ed\').style.display = (document.getElementById(\'sel\').selected)?\'\':\'none\';">';
			if($cfg_blok[1] == 'links'){
				echo'<OPTION VALUE="links" selected>Блок из ссылок на страницы сайта';
				echo'<OPTION VALUE="html" id="sel">Блок html кода';
			}elseif($cfg_blok[1] == 'html'){
				echo'<OPTION VALUE="links">Блок из ссылок на страницы сайта';
				echo'<OPTION VALUE="html" id="sel" selected>Блок html кода';
			}elseif($cfg_blok[1] == 'module'){
				echo'<OPTION VALUE="links">Блок из ссылок на страницы сайта';
				echo'<OPTION VALUE="html" id="sel">Блок html кода';
				if(!Module::exists($cfg_blok[0])){
					echo'<OPTION VALUE="'.$cfg_blok[0].'" selected> - Подключенный модуль не найден';
				}
			}
			
			
			$listModules = System::listModules();
			foreach($listModules as $value){
				if(Module::isIntegrationBlok($value)){
					$info = Module::info($value);
					echo '<OPTION VALUE="'.$value.'" '.($cfg_blok[0] == $value?'selected':'').'>Модуль "'.$info['name'].' '.$info['version'].'"';
				}
			}
			
			
			
			echo'</SELECT></td>
			</tr>';
			
			if($cfg_blok[1] == 'html'){
				$blok_content = (file_exists('../data/bloks/html_'.$cfg_blok[0].'.dat'))?htmlspecialchars(file_get_contents('../data/bloks/html_'.$cfg_blok[0].'.dat')):'';
			}else{$blok_content = '';}
			echo'<tr id="ed" style="display: none;">
				<td>&nbsp;</td>
				<td><TEXTAREA id="editor" NAME="editor" ROWS="20" COLS="100" class="editor">'.$blok_content.'</TEXTAREA></td>
			</tr>';
?>
<script type="text/javascript">
document.getElementById('ed').style.display = (document.getElementById('sel').selected)?'':'none';
</script>
<?php
			echo'<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="" value="Сохранить"> &nbsp; <a href="bloks.php?">Вернуться назад</a></td>
			</tr>
			</table>
			</form>
			</div>';
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
	}
      
      
	if($act=='editor_blok2'){
		$str_blok = htmlspecialchars(specfilter($_POST['str_blok']));
		$name_blok = htmlspecialchars(specfilter($_POST['name_blok']));
		$l_o_r = htmlspecialchars(specfilter($_POST['l_o_r']));
        $type_blok = htmlspecialchars(specfilter($_POST['type_blok']));
        $editor = $_POST['editor'];
		//if(get_magic_quotes_gpc()){$editor = stripslashes ($editor);}//Удаляем слеши сами если magic_quotes включены
		
		if($name_blok != '' && $type_blok != '' && ($l_o_r == 'left' || $l_o_r == 'right')){
			$bloks_list = file('../data/bloks/'.$l_o_r.'_bloks.dat');
			$cfg_blok = explode('<||>',$bloks_list[$str_blok]);
			
			//$new_id = ($cfg_blok[1] == 'module')?time():$cfg_blok[0];
			
			if($type_blok == 'links'){
				if(file_exists('../data/bloks/html_'.$cfg_blok[0].'.dat')){ unlink('../data/bloks/html_'.$cfg_blok[0].'.dat');}
				if(file_exists('../data/bloks/links_'.$cfg_blok[0].'.dat')){
					$inset = $cfg_blok[0].'<||>links<||>'.$name_blok.'<||>';
				}else{
					$new_id = time();
					filefputs('../data/bloks/links_'.$new_id.'.dat', '', 'w+');
					$inset = $new_id.'<||>links<||>'.$name_blok.'<||>';
				}
			}elseif($type_blok == 'html'){
				if(file_exists('../data/bloks/links_'.$cfg_blok[0].'.dat')){ unlink('../data/bloks/links_'.$cfg_blok[0].'.dat');}
				if(file_exists('../data/bloks/html_'.$cfg_blok[0].'.dat')){
					filefputs('../data/bloks/html_'.$cfg_blok[0].'.dat', $editor, 'w+');
					$inset = $cfg_blok[0].'<||>html<||>'.$name_blok.'<||>';
				}else{
					$new_id = time();
					filefputs('../data/bloks/html_'.$new_id.'.dat', $editor, 'w+');
					$inset = $new_id.'<||>html<||>'.$name_blok.'<||>';
				}
			}else{
				if(file_exists('../data/bloks/html_'.$cfg_blok[0].'.dat')){ unlink('../data/bloks/html_'.$cfg_blok[0].'.dat');}
				if(file_exists('../data/bloks/links_'.$cfg_blok[0].'.dat')){ unlink('../data/bloks/links_'.$cfg_blok[0].'.dat');}
				$inset = $type_blok.'<||>module<||>'.$name_blok.'<||>';
			}
				
			//Изменяем строку
			$bloks_list[$str_blok] = str_replace($bloks_list[$str_blok],$inset."\n",$bloks_list[$str_blok]);
			
			//перезаписываем файл
			$nom = count($bloks_list);
			$f = fopen('../data/bloks/'.$l_o_r.'_bloks.dat', 'w+');
			for($i = 0; $i < $nom; ++$i){
				fputs($f,$bloks_list[$i]);
			}
			fclose($f);
			
			echo'<div class="msg">Блок успешно изменен</div>';
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
	}
      
	if($act=='go_to_blok'){
		$str_blok = htmlspecialchars(specfilter($_GET['str_blok']));
		$l_o_r = htmlspecialchars(specfilter($_GET['l_o_r']));
		
		if(is_numeric($str_blok) && ($l_o_r == 'left' || $l_o_r == 'right')){
			$from_l_o_r = ($l_o_r == 'right')?'left':'right';
			//Удаляем блок из списка
			$bloks_list = file('../data/bloks/'.$from_l_o_r.'_bloks.dat');
			$nom = count($bloks_list);
			$f = fopen('../data/bloks/'.$from_l_o_r.'_bloks.dat', 'w+');
			for($i = 0; $i < $nom; ++$i){
				if($str_blok != $i){
					fputs($f,$bloks_list[$i]);
				}
			}
			fclose($f);
			//Записываем новый блок в список
			filefputs('../data/bloks/'.$l_o_r.'_bloks.dat', $bloks_list[$str_blok], 'a+');
			echo'<div class="msg">Блок успешно перенесен</div>';
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
	}
	
	if($act=='up_blok'){
		$str_blok = htmlspecialchars(specfilter($_GET['str_blok']));
		$l_o_r = htmlspecialchars(specfilter($_GET['l_o_r']));
		
		if(is_numeric($str_blok) && ($l_o_r == 'left' || $l_o_r == 'right')){
			
			$bloks_list = file('../data/bloks/'.$l_o_r.'_bloks.dat');
			$nom = count($bloks_list);
			
			if($str_blok > 0){
				$up_str_blok = $str_blok - 1;
				
				$tmp_str = $bloks_list[$up_str_blok];//Верхнюю строку сохраняем во временную переменную
				$bloks_list[$up_str_blok] = $bloks_list[$str_blok];//Верхнюю строку заменяем на нижнюю
				$bloks_list[$str_blok] = $tmp_str;//нижнюю заменяем на верхнюю, которая была сохранена
				
				//перезаписываем файл
				$f = fopen('../data/bloks/'.$l_o_r.'_bloks.dat', 'w+');
				for($i = 0; $i < $nom; ++$i){
					fputs($f,$bloks_list[$i]);
				}
				fclose($f);
				echo'<div class="msg">Блок успешно перенесен</div>';
			}else{
				echo'<div class="msg">Ошибка, выше переносить нельзя</div>';
			}
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 2000);
</script>
<?php
	}
	
	
	if($act=='down_blok'){
		$str_blok = htmlspecialchars(specfilter($_GET['str_blok']));
		$l_o_r = htmlspecialchars(specfilter($_GET['l_o_r']));
		
		if(is_numeric($str_blok) && ($l_o_r == 'left' || $l_o_r == 'right')){
			
			$bloks_list = file('../data/bloks/'.$l_o_r.'_bloks.dat');
			$nom = count($bloks_list);
			
			if($str_blok < ($nom - 1)){
				$down_str_blok = $str_blok + 1;
			
				$tmp_str = $bloks_list[$down_str_blok];//Нижнюю строку сохраняем во временную переменную
				$bloks_list[$down_str_blok] = $bloks_list[$str_blok];//Нижнюю строку заменяем на верхнюю
				$bloks_list[$str_blok] = $tmp_str;//верхнюю заменяем на нижнюю которая была сохранена
				
				//перезаписываем файл
				$f = fopen('../data/bloks/'.$l_o_r.'_bloks.dat', 'w+');
				for($i = 0; $i < $nom; ++$i){
					fputs($f,$bloks_list[$i]);
				}
				fclose($f);
				echo'<div class="msg">Блок успешно перенесен</div>';
			}else{
				echo'<div class="msg">Ошибка, ниже переносить нельзя</div>';
			}
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 2000);
</script>
<?php
	}

//////////////////////////////////////////////////--Управление ссылками!--////////////////////////////////////////////////////

     
	if($act=='new_link'){
		$link_file = htmlspecialchars(specfilter($_GET['link_file']));
		if(file_exists('../data/bloks/links_'.$link_file.'.dat')){
?>
<script type="text/javascript">
function sethttp(){
document.getElementById('page').style.display = 'none';
document.getElementById('http').style.display = '';
document.getElementById('type_link').value = 'http';
}
function setpage(){
document.getElementById('page').style.display = '';
document.getElementById('http').style.display = 'none';
document.getElementById('type_link').value = 'page';
}
</script>
<?php
			echo'<div class="header"><h1>Создание новой ссылки</h1></div>
			<div class="menu_page"><a href="bloks.php">&#8592; Вернуться назад</a></div>
			<div class="content">
			
			<form name="forma" action="bloks.php?" method="post">
			<INPUT TYPE="hidden" NAME="act" VALUE="new_link2">
			<INPUT TYPE="hidden" NAME="link_file" VALUE="'.$link_file.'">
			<INPUT TYPE="hidden" NAME="type_link" id="type_link" VALUE="page">
			<table class="tblform">
			<tr>
				<td>Название ссылки:</td>
				<td><input type="text" name="name_link" value="" size="25"></td>
			</tr>
			<tr id="page">
				<td>Ссылка на:</td>
				<td><SELECT NAME="page_link" >';
				$listPages = System::listPages();
				$listPages = array_reverse($listPages);//перевернули массив
				$nom = count($listPages);
				if($nom == 0){echo'<OPTION VALUE="">Страницы ещё не созданы';}
				for($i = 0; $i < $nom; ++$i){
					if(Page::exists($listPages[$i])){
						$Page = new Page($listPages[$i], $Config);
						echo'<OPTION VALUE="'.$listPages[$i].'">'.$Page->name.'';
					}
				}
				echo'</SELECT><br><a href="javascript:void(0);" onclick="sethttp();">Ввести ссылку вручную</a></td>
			</tr>
			<tr id="http" style="display: none;">
				<td>Ссылка на:</td>
				<td><input type="text" name="url_link" value="http://" size="25"><br><a href="javascript:void(0);" onclick="setpage();">Выбрать страницу из списка</a></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="" value="Создать ссылку"> &nbsp; <a href="bloks.php?">Вернуться назад</a></td>
			</tr>
			</table>
			</form>
			</div>';
     }
	else{
			echo'<div class="msg">Запрос неверен</div>';
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
		}
	}
	
	
	if($act=='new_link2'){
		$link_file = htmlspecialchars(specfilter($_POST['link_file']));
		$type_link = htmlspecialchars(specfilter($_POST['type_link']));
		$name_link = htmlspecialchars(specfilter($_POST['name_link']));
		$page_link = htmlspecialchars(specfilter($_POST['page_link']));
		$url_link = htmlspecialchars(specfilter($_POST['url_link']));
		
		if(file_exists('../data/bloks/links_'.$link_file.'.dat')){
			if($type_link == 'page'){
				if(file_exists('../data/pages/cfg_'.$page_link.'.dat') && file_exists('../data/pages/page_'.$page_link.'.dat')){
					$inset = 'page<||>'.$page_link.'<||>'.$name_link.'<||>';
					filefputs('../data/bloks/links_'.$link_file.'.dat', $inset."\n", 'a+');
					echo'<div class="msg">Ссылка успешно создана</div>';
				}else{
					echo'<div class="msg">Ошибка</div>';
				}
			}elseif($type_link == 'http'){
				$inset = 'http<||>'.$url_link.'<||>'.$name_link.'<||>';
				filefputs('../data/bloks/links_'.$link_file.'.dat', $inset."\n", 'a+');
				echo'<div class="msg">Ссылка успешно создана</div>';
			}else{
				echo'<div class="msg">Ошибка</div>';
			}
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
	}
	
	
	if($act=='dell_link'){
		$link_file = htmlspecialchars(specfilter($_GET['link_file']));
		$str_link = htmlspecialchars(specfilter($_GET['str_link']));
		if(file_exists('../data/bloks/links_'.$link_file.'.dat')){
			$links_list = file('../data/bloks/links_'.$link_file.'.dat');
			$nom = count($links_list);
			$f = fopen('../data/bloks/links_'.$link_file.'.dat', 'w+');
			for($i = 0; $i < $nom; ++$i){
				if($str_link != $i){
					fputs($f,$links_list[$i]);
				}
			}
			fclose($f);
			echo'<div class="msg">Ссылка успешно удалена</div>';
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
	}
	
	
	if($act=='editor_link'){
		$link_file = htmlspecialchars(specfilter($_GET['link_file']));
		$str_link = htmlspecialchars(specfilter($_GET['str_link']));
		if(file_exists('../data/bloks/links_'.$link_file.'.dat')){
?>
<script type="text/javascript">
function sethttp(){
document.getElementById('page').style.display = 'none';
document.getElementById('http').style.display = '';
document.getElementById('type_link').value = 'http';
}
function setpage(){
document.getElementById('page').style.display = '';
document.getElementById('http').style.display = 'none';
document.getElementById('type_link').value = 'page';
}
</script>
<?php
			$links_list = file('../data/bloks/links_'.$link_file.'.dat');
			$cfg_link = explode('<||>',$links_list[$str_link]);
			echo'<div class="header"><h1>Редактирование ссылки</h1></div>
			<div class="menu_page"><a href="bloks.php">&#8592; Вернуться назад</a></div>
			<div class="content">
			<form name="forma" action="bloks.php?" method="post">
			<INPUT TYPE="hidden" NAME="act" VALUE="editor_link2">
			<INPUT TYPE="hidden" NAME="link_file" VALUE="'.$link_file.'">
			<INPUT TYPE="hidden" NAME="str_link" VALUE="'.$str_link.'">
			<INPUT TYPE="hidden" NAME="type_link" id="type_link" VALUE="'.$cfg_link[0].'">
			<table class="tblform">
			<tr>
				<td>Название ссылки:</td>
				<td><input type="text" name="name_link" value="'.$cfg_link[2].'" size="25"></td>
			</tr>
			<tr id="page">
				<td>Ссылка на:</td>
				<td><SELECT NAME="page_link" >';
				$listPages = System::listPages();
				$listPages = array_reverse($listPages);//перевернули массив
				$nom = count($listPages);
				if($nom == 0){echo'<OPTION VALUE="">Страницы ещё не созданы';}
				for($i = 0; $i < $nom; ++$i){
					if(Page::exists($listPages[$i])){
						$Page = new Page($listPages[$i], $Config);
						if($cfg_link[0] == 'page'){$selected = ($cfg_link[1] == $listPages[$i])?'selected':'';}else{$selected = '';}
						echo'<OPTION VALUE="'.$listPages[$i].'" '.$selected.'>'.$Page->name.'';
					}
				}
				$tmp_url = ($cfg_link[0] == 'page')?'/':'';
				echo'</SELECT><br><a href="javascript:void(0);" onclick="sethttp();">Ввести ссылку вручную</a></td>
			</tr>
			<tr id="http">
				<td>Ссылка на:</td>
				<td><input type="text" name="url_link" value="'.$tmp_url.$cfg_link[1].'" size="25"><br><a href="javascript:void(0);" onclick="setpage();">Выбрать страницу из списка</a></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="" value="Сохранить"> &nbsp; <a href="bloks.php?">Вернуться назад</a></td>
			</tr>
			</table>
			</form>
			</div>';
if($cfg_link[0] == 'page'){
?>
<script type="text/javascript">
document.getElementById('page').style.display = '';
document.getElementById('http').style.display = 'none';
</script>
<?php
}
if($cfg_link[0] == 'http'){
?>
<script type="text/javascript">
document.getElementById('page').style.display = 'none';
document.getElementById('http').style.display = '';
</script>
<?php
}
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
	}

	if($act=="editor_link2"){
		$link_file = htmlspecialchars(specfilter($_POST['link_file']));
		$str_link = htmlspecialchars(specfilter($_POST['str_link']));
		$type_link = htmlspecialchars(specfilter($_POST['type_link']));
		$name_link = htmlspecialchars(specfilter($_POST['name_link']));
		$page_link = htmlspecialchars(specfilter($_POST['page_link']));
		$url_link = htmlspecialchars(specfilter($_POST['url_link']));
		
		if(file_exists('../data/bloks/links_'.$link_file.'.dat')){
			$links_list = file('../data/bloks/links_'.$link_file.'.dat');
			$cfg_link = explode('<||>',$links_list[$str_link]);
			if($type_link == 'page'){
				$inset = 'page<||>'.$page_link.'<||>'.$name_link.'<||>';
			}
			if($type_link == 'http'){
				$inset = 'http<||>'.$url_link.'<||>'.$name_link.'<||>';
			}
			$links_list[$str_link] = str_replace($links_list[$str_link], $inset."\n", $links_list[$str_link]);
			//перезаписываем файл
			$nom = count($links_list);
			$f = fopen('../data/bloks/links_'.$link_file.'.dat', 'w+');
			for($i = 0; $i < $nom; ++$i)
			{
				fputs($f,$links_list[$i]);
			}
			fclose($f);
			echo'<div class="msg">Ссылка успешно изменена</div>';
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 3000);
</script>
<?php
	}



	if($act=='up_link'){
		$link_file = htmlspecialchars(specfilter($_GET['link_file']));
		$str_link = htmlspecialchars(specfilter($_GET['str_link']));
		if(is_numeric($str_link)){
			$links_list = file('../data/bloks/links_'.$link_file.'.dat');
			$nom = count($links_list);
			if($str_link > 0){
				$up_str_link = $str_link - 1;
				$tmp_str = $links_list[$up_str_link];//Верхнюю строку сохраняем в временную переменную
				$links_list[$up_str_link] = $links_list[$str_link];//Верхнюю строку заменяем на нижнюю
				$links_list[$str_link] = $tmp_str;//нижнюю заменяем на верхнюю которая была сохранена
				//перезаписываем файл
				$f = fopen('../data/bloks/links_'.$link_file.'.dat', 'w+');
				for($i = 0; $i < $nom; ++$i){
					fputs($f,$links_list[$i]);
				}
				fclose($f);
				echo'<div class="msg">Ссылка успешно перенесена</div>';
			}else{
				echo'<div class="msg">Ошибка</div>';
			}
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 2000);
</script>
<?php
	}
	
	if($act=='down_link'){
		$link_file = htmlspecialchars(specfilter($_GET['link_file']));
		$str_link = htmlspecialchars(specfilter($_GET['str_link']));
		if(is_numeric($str_link)){
			$links_list = file('../data/bloks/links_'.$link_file.'.dat');
			$nom = count($links_list);
			if($str_link < ($nom - 1)){
				$down_str_link = $str_link + 1;
				$tmp_str = $links_list[$down_str_link];//Нижнюю строку сохраняем во временную переменную
				$links_list[$down_str_link] = $links_list[$str_link];//Нижнюю строку заменяем на верхнюю
				$links_list[$str_link] = $tmp_str;//верхнюю заменяем на нижнюю, которая была сохранена
				//перезаписываем файл
				$f = fopen('../data/bloks/links_'.$link_file.'.dat', 'w+');
				for($i = 0; $i < $nom; ++$i){
					fputs($f,$links_list[$i]);
				}
				fclose($f);
				echo'<div class="msg">Ссылка успешно перенесена</div>';
			}else{
				echo'<div class="msg">Ошибка</div>';
			}
		}else{
			echo'<div class="msg">Ошибка</div>';
		}
?>
<script type="text/javascript">
setTimeout('window.location.href = \'bloks.php?\';', 2000);
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

// Г.код из 2007
// Пишите на support@my-engine.ru ;)

require('include/end.dat');
?>