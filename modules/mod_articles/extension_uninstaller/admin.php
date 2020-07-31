<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

if($act=='index'){
?>
<script type="text/javascript">	
function dell(m, n){
return '<div class="a">Подтвердите удаление расширения: <i>' + n + '</i></div>' +
	'<div class="b">' +
	'<button type="button" onClick="window.location.href = \'module.php?module=extension_uninstaller&amp;act=dell&amp;mod='+m +'\';">Удалить</button> '+
	'<button type="button" onclick="closewindow(\'window\');">Отмена</button>'+
	'</div>';
}

</script>
<?php
		echo'
		<div class="header">
			<h1>Управление модулем "Extension Uninstaller"</h1>
		</div>
		<div class="menu_page">
		<a href="index.php?">&#8592; Вернуться назад</a>	
		</div>
		<div class="content">
		<h2>Удаление расширений и модулей</h2>
        <table class="tblform">';
		
		$modules = System::listModules();
		foreach($modules as $value){
			$info = Module::info($value);
			if(!$info['delete']){
			echo''.($info['developer']=='Три Шага'?'
            <tr>
			<td class="middle"><b>'.$info['name'].'</b></td>
			<td class="middle"><a class="button addlink" href="javascript:void(0);" onclick="openwindow(\'window\', 650, \'auto\', dell(\''.$value.'\', \''.$info['name'].'\'));">Удалить</a></td>
			</tr>':'').'';			
			}
		}
		echo'</table>
                </div>';
	}

	
	if($act=='dell'){
		$module = htmlspecialchars($_GET['mod']);
		if(Module::exists($module)){
			$info = Module::info($module);
			if($module == $Config->template){
				System::notification('Попытка удаления работающего шаблона '.$info['name'].'', 'g');
				echo'<div class="msg">Работающий шаблон удалять нельзя, выберите в настройках другой шаблон для<br>вывода и повторите попытку удаления.</div>';
			}else{
                // Удаление категории новостей				
				if($module == 'news_categories'){
					
					$link_data = file('../modules/news_categories/list.dat');
                    $nom = count($link_data);
					for($q = 0; $q < $nom; ++$q){
					$link_cfg = explode('^',$link_data[$q]);

					
					   // Удаление  страниц с категориями
					    if(file_exists('../data/pages/cfg_'.$link_cfg[2].'.dat')){Page::delete($link_cfg[2]);}
						// Удаление  категории из хранилища
					    if(is_dir('../data/storage/module.'.$link_cfg[1].'')){ delldir('../data/storage/module.'.$link_cfg[1].'/');}
					    // Удаление пунктов меню с темами 
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$link_cfg[2])){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
                        // Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$link_cfg[2].'.dat')){unlink('../modules/menu/data/links_'.$link_cfg[2].'.dat');}
						}						
						// Удаление  блока категории из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$link_cfg[1])){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блока категории из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$link_cfg[1])){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление директорий с категориями
					    if(is_dir('../modules/'.$link_cfg[1].'')){delldir('../modules/'.$link_cfg[1].'/');}
					}
										
					if(file_exists('../modules/news_categories/cfg.dat')){require('../modules/news_categories/cfg.dat');}
					// Удаление страницы c модулем
					if(file_exists('../data/pages/cfg_'.$id_categorys.'.dat')){Page::delete($id_categorys);}
					// Удаление пункта меню категории новостей
					$fopen=@file('../data/bloks/links_gorizont.dat');
                    foreach($fopen as $key=>$value){  
                    if(substr_count($value,$id_categorys)){
                    array_splice($fopen, $key, 1);
                    }
                    }
                    $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                    for($i=0;$i<count($fopen);$i++){
                    fwrite($f,$fopen[$i]);
                    }
                    fclose($f);
					    // Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$id_categorys.'.dat')){unlink('../modules/menu/data/links_'.$id_categorys.'.dat');}
						}					
	                    // Удаление категории новостей из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление категории новостей из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						
					if(file_exists('../modules/'.$Config->template.'/news.blok.php')){unlink('../modules/'.$Config->template.'/news.blok.php');}	
				
				}
				
				// Удаление блога				
				if($module == 'blog'){
					
					    if(file_exists('../modules/blog/cfg.php')){require('../modules/blog/cfg.php');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$blogConfig->idPage.'.dat')){Page::delete($blogConfig->idPage);}
						// Удаление  из хранилища
					    if(is_dir('../data/storage/module.blog')){ delldir('../data/storage/module.blog/');}
					    // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$blogConfig->idPage)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
	                    // Удаление блога из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блога из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
				
				}
				
				// Удаление новостного блока				
				if($module == 'mod_news'){
					
					    if(file_exists('../modules/mod_news/cfg.php')){require('../modules/mod_news/cfg.php');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$newsConfig->idPage.'.dat')){Page::delete($newsConfig->idPage);}
						// Удаление  из хранилища
					    if(is_dir('../data/storage/module.news')){ delldir('../data/storage/module.news/');}
					    // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$newsConfig->idPage)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
	                    // Удаление блога из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блога из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						
					if(file_exists('../modules/'.$Config->template.'/news.blok.php')){unlink('../modules/'.$Config->template.'/news.blok.php');}	
				
				}
				
				// Удаление модуля статей
				if($module == 'mod_articles'){
				       
					    if(file_exists('../modules/mod_articles/cfg.dat')){require('../modules/mod_articles/cfg.dat');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$direct_article.'.dat')){Page::delete($direct_article);}
				        // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$direct_article)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
	                    // Удаление блога из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блога из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
				
				}
  
                // Удаление каталог статей
				if($module == 'articles'){
					
					$link_data = file('../modules/articles/data/list.dat');
                    $nom = count($link_data);
					for($q = 0; $q < $nom; ++$q){
                    $link_cfg = explode('^',$link_data[$q]);
					    // Удаление  страниц с категориями
						if(file_exists('../modules/'.$link_cfg[1].'/cfg.dat')){require('../modules/'.$link_cfg[1].'/cfg.dat');}						
					    if(file_exists('../data/pages/cfg_'.$folder_url.'.dat')){Page::delete($folder_url);}
					    // Удаление пунктов меню с темами 
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$folder_url)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$folder_url.'.dat')){unlink('../modules/menu/data/links_'.$folder_url.'.dat');}
						}
					    // Удаление  блока категории из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$link_cfg[1])){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блока категории из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$link_cfg[1])){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление  директорий с категориями
					    if(is_dir('../modules/'.$link_cfg[1].'')){delldir('../modules/'.$link_cfg[1].'/');}
					}
					
					    if(file_exists('../modules/articles/cfg.dat')){require('../modules/articles/cfg.dat');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$id_articles.'.dat')){Page::delete($id_articles);}
				        // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$id_articles)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$id_articles.'.dat')){unlink('../modules/menu/data/links_'.$id_articles.'.dat');}
						}
	                    // Удаление блога из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блога из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
				
				}

                // Удаление RSS reader
				if($module == 'rss_reader'){
					
					$link_data = file('../modules/rss_reader/data/list.dat');
                    $nom = count($link_data);
					for($q = 0; $q < $nom; ++$q){
                    $link_cfg = explode('^',$link_data[$q]);
					    // Удаление  страниц с RSS лентой
						if(file_exists('../modules/'.$link_cfg[1].'/cfg.dat')){require('../modules/'.$link_cfg[1].'/cfg.dat');}
					    if(file_exists('../data/pages/cfg_'.$cfg_link.'.dat')){Page::delete($cfg_link);}					
					   // Удаление  директорий с RSS лентой
					    if(is_dir('../modules/'.$link_cfg[1].'')){delldir('../modules/'.$link_cfg[1].'/');}
                        // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$cfg_link)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$link_cfg[1].'.dat')){unlink('../modules/menu/data/links_'.$link_cfg[1].'.dat');}
						}
					    // Удаление  блока RSS лентой из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$link_cfg[1])){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блока RSS лентой из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$link_cfg[1])){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
					}
					
					
					    if(file_exists('../modules/rss_reader/cfg.dat')){require('../modules/rss_reader/cfg.dat');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$id_rsspage.'.dat')){Page::delete($id_rsspage);}
				        // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$id_rsspage)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$id_rsspage.'.dat')){unlink('../modules/menu/data/links_'.$id_rsspage.'.dat');}
						}
	                    // Удаление блога из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блога из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
					
                if(is_dir('../modules/rss_last')){delldir('../modules/rss_last/');}
				 
				}
				
				// Удаление RSS ленты
				if($module == 'rss_tape'){
					
				        if(file_exists('../modules/rss_tape/cfg.dat')){require('../modules/rss_tape/cfg.dat');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$cfg_link.'.dat')){Page::delete($cfg_link);}
				        // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$cfg_link)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
	                    // Удаление блога из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блога из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
				 
				}
				
				if($module == 'urban_style'){
					if(is_dir('../files/otzyvy')){delldir('../files/otzyvy/');}
					if(file_exists('../data/pages/cfg_otzyvy.dat')){Page::delete('otzyvy');}						
					}
				if($module == 'mod_gallery'){
					   if(is_dir('../modules/fancybox')){delldir('../modules/fancybox/');}
					   if(file_exists('../modules/mod_gallery/cfg.dat')){require('../modules/mod_gallery/cfg.dat');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$cfg_link.'.dat')){Page::delete($cfg_link);}
				        // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$cfg_link)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
					
				}
				
				if($module == 'galleries'){
					
					if(is_dir('../modules/fancybox')){delldir('../modules/fancybox/');}
					if(file_exists('../modules/galleries/cfg.dat')){require('../modules/galleries/cfg.dat');}
					    // Удаление страницы c модулем
					    if(file_exists('../data/pages/cfg_'.$id_galleries.'.dat')){Page::delete($id_galleries);}
				        // Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$id_galleries)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$id_galleries.'.dat')){unlink('../modules/menu/data/links_'.$id_galleries.'.dat');}
						}
						// Удаление блога из левого блока
						$fopen=@file('../data/bloks/left_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/left_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление блога из правого блока
						$fopen=@file('../data/bloks/right_bloks.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$module)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/right_bloks.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						
                        $link_data = file('../modules/galleries/data/list.dat');
                        $nom = count($link_data);
						for($q = 0; $q < $nom; ++$q){
						$link_cfg = explode('^',$link_data[$q]);
                        if(file_exists('../modules/'.$link_cfg[1].'/cfg.dat')){require('../modules/'.$link_cfg[1].'/cfg.dat');}
						// Удаление страницы c фотогалереями
					    if(file_exists('../data/pages/cfg_'.$id_pagemod.'.dat')){Page::delete($id_pagemod);}
						// Удаление пункта меню
					    $fopen=@file('../data/bloks/links_gorizont.dat');
                        foreach($fopen as $key=>$value){  
                        if(substr_count($value,$id_pagemod)){
                        array_splice($fopen, $key, 1);
                        }
                        }
                        $f=fopen('../data/bloks/links_gorizont.dat', 'w');
                        for($i=0;$i<count($fopen);$i++){
                        fwrite($f,$fopen[$i]);
                        }
                        fclose($f);
						// Удаление файлов подпунктов меню
						if(is_dir('../modules/menu')){
						if(file_exists('../modules/menu/data/links_'.$id_pagemod.'.dat')){unlink('../modules/menu/data/links_'.$id_pagemod.'.dat');}
						}
						// Удаление  директорий с фотоагалереей
			            if(is_dir('../modules/'.$link_cfg[1].'')){delldir('../modules/'.$link_cfg[1].'/');}							
						}
					
				}
                // Удаление  модуля поиска
				if($module == 'mod_search'){
			    if(is_dir('../data/storage/module.search')){ delldir('../data/storage/module.search');}
				if(is_dir('../data/storage/module.autoindexsearch')){ delldir('../data/storage/module.autoindexsearch');}
				}
				// Удаление  категории из хранилища
				if(file_exists('../modules/'.$module.'/template.php')){
			    if(is_dir('../data/storage/module.customize.'.$module.'')){ delldir('../data/storage/module.customize.'.$module.'/');}
				}
                // Удаление  директорий с картинками
			    if(is_dir('../files/'.$module.'')){delldir('../files/'.$module.'/');}
				// Удаление  директорий с модулем
			    if(is_dir('../modules/'.$module.'')){delldir('../modules/'.$module.'/');}				
							
                System::notification('Удалено расширение '.$info['name'].'', 'g');
				echo'<div class="msg">Расширение успешно удалено</div>';
		    }
		}
		
?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=extension_uninstaller\';', 3000);
</script>
<?php
	}
	
?>