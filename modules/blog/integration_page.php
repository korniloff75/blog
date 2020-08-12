<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

require('cfg.php');

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

// rss
if($MODULE_URI == '/rss_blog.xml'){
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {$protocol = 'https://';}else{$protocol = 'http://';}
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:georss="http://www.georss.org/georss">
    <channel>
		<title>'.$blogConfig->name_rss.'</title>
		<atom:link href="'.$protocol.''.SERVER.'/'.$URI[1].'/rss.xml" rel="self" type="application/rss+xml" />
        <link>'.$protocol.''.SERVER.'/'.$URI[1].'</link>
        <description>'.$Page->description.'</description>
		<language>ru</language>';
		if(($listIdBlog = json_decode($blogStorage->get('list'), true)) != false){
			//перевернули масив для вывода новостей в обратном порядке
			$listIdBlog = array_reverse($listIdBlog);

			for($i = 0; $i < 8; $i++){
				if(isset($listIdBlog[$i])){
					if($blogStorage->iss('post_'.$listIdBlog[$i])){
						$blogParam = json_decode($blogStorage->get('post_'.$listIdBlog[$i]));
		echo'
		<item>
			<title>'.$blogParam->header.'</title>
			<link>'.$protocol.''.SERVER.'/'.$URI[1].'/'.$listIdBlog[$i].'</link>
			<guid>'.$protocol.''.SERVER.'/'.$URI[1].'/'.$listIdBlog[$i].'</guid>
			<media:rating scheme="urn:simple">nonadult</media:rating>
			<pubDate>'.date("D, d M Y H:i:s O", isset($blogParam->time)?$blogParam->time:strtotime($blogParam->date)).'</pubDate>
			<author>Administrator</author>
			<enclosure url="'.$protocol.''.SERVER.$blogParam->img.'" type="image/jpeg" length="'.filesize(DR.'/'.$blogParam->img).'"/>
			<description>
				<![CDATA['.trim(strip_tags($blogParam->prev)).']]>
			</description>
			<content:encoded>
				<![CDATA['.trim(strip_tags($blogParam->content)).']]>
			</content:encoded>
		</item>';
					}
				}
			}
		}
echo'
	</channel>
</rss>';
ob_end_flush(); exit;
}

// Обработка ajax
if(isset($URI[3]) && isset($URI[4])){
	if($URI[3] == 'ajax'){
		header("Cache-Control: no-store, no-cache, must-revalidate");// не даем кешировать ajax тупым браузерам (IE)
		switch ($URI[4]) {
			
			case 'newcommentcheck':
				if($blogStorage->iss('count_'.$URI[2])){
					echo $blogStorage->get('count_'.$URI[2]);
				}else{
					echo 0;
				}
				break;
			
			case 'addcomment':
				if (md5($_POST['ticket_com'].$Config->ticketSalt) != $_COOKIE['ticket_com']){
					echo'Ticket_com';
				}elseif($User->authorized){
					
					if($blogConfig->commentRules > 1 && $User->preferences == 0){
						// ошибка если нехватает префов
						echo'Error';
					}else{
						// Обрабатываем форму от авторизированных
						
						if($blogStorage->iss('post_'.$URI[2])){
							
								// Обрабатываем форму от пользователя
								$textForm = trim(htmlspecialchars($_POST['text']));
								
								if(mb_strlen($textForm, 'utf-8') <= $blogConfig->commentMaxLength && mb_strlen($textForm, 'utf-8') > 0){
									
									
									$idComment = $blogStorage->iss('idComment')?$blogStorage->get('idComment'):0;
									++$idComment;
									$blogStorage->set('idComment', $idComment);
									
									
									if($blogConfig->commentModeration == 0){$published = 1;}
									elseif($blogConfig->commentModeration == 1){$published = ($User->numPost >= $blogConfig->commentModerationNumPost)?1:0;}
									elseif($blogConfig->commentModeration == 2){$published = ($User->preferences > 0)?1:0;}
									else{$published = 0;}
									
									if ($published){
										$arrayComments = json_decode($blogStorage->get('comments_'.$URI[2]), true);
										$arrayComments[] = array(
													'id' => $idComment,
													'login' => $User->login,
													'text' => $textForm,
													'ip' => IP,
													'status' => 'user',
													'time' => time());
										
										$arrayCount = count($arrayComments);
										if($arrayCount >= $blogConfig->commentMaxCount){
											$arrayStart = $arrayCount -  round($blogConfig->commentMaxCount / 1.5);
											$arrayComments = array_slice($arrayComments, $arrayStart, $arrayCount);
										}
										
										if($blogStorage->set('comments_'.$URI[2], json_encode($arrayComments))){
											
											++$User->numPost;
											$User->save();
											
											$count = $blogStorage->iss('count_'.$URI[2])?$blogStorage->get('count_'.$URI[2]):0;
											++$count;
											$blogStorage->set('count_'.$URI[2], $count);
											
											echo $count;
											
										}else{
											echo'Error';
										}
										unset($arrayComments);
										
									}else{
										echo'Moderation';
									}
									
									
									
									
									
									
									// в список последних 
									$lastComments = json_decode($blogStorage->get('lastComments'), true);
									$lastComments[] = array(
												'idComment' => $idComment,
												'idblog' => $URI[2],
												'login' => $User->login,
												'text' => $textForm,
												'ip' => IP,
												'status' => 'user',
												'published' => $published,
												'time' => time());
									$blogStorage->set('lastComments', json_encode($lastComments));
									
									
								}else{
									echo'Error';
								}
							
						}else{
							echo'Error';
						}
					}
				}else{
					if($blogStorage->iss('post_'.$URI[2])){
						if(array_search(IP, $Config->ipBan)){
							echo'Ban';
						}elseif($blogConfig->commentRules > 0){
							// ошибка необходимости авторизироваться
							echo'Error';
						}else{
							// Обрабатываем форму от гостей
							$loginForm = htmlspecialchars(specfilter($_POST['login']));
							$textForm = trim(htmlspecialchars($_POST['text']));
							
							if (md5(strtolower($_POST['captcha']).$Config->ticketSalt) != $_COOKIE['captcha']){
								echo'Captcha';
							}elseif(System::validPath($loginForm) && mb_strlen($loginForm, 'utf-8') < 36 && mb_strlen($textForm, 'utf-8') <= $blogConfig->commentMaxLength && mb_strlen($textForm, 'utf-8') > 0){
								if (User::exists($loginForm)){
									echo'Exists';
								}else{
									
									
									$idComment = $blogStorage->iss('idComment')?$blogStorage->get('idComment'):0;
									++$idComment;
									$blogStorage->set('idComment', $idComment);
									
									
									
									$published = ($blogConfig->commentModeration == 0)?1:0;
									
									
									if ($published){
										$arrayComments = json_decode($blogStorage->get('comments_'.$URI[2]), true);
										$arrayComments[] = array(
													'id' => $idComment,
													'login' => $loginForm,
													'text' => $textForm,
													'ip' => IP,
													'status' => 'gost',
													'time' => time());
										
										$arrayCount = count($arrayComments);
										if($arrayCount >= $blogConfig->commentMaxCount){
											$arrayStart = $arrayCount -  round($blogConfig->commentMaxCount / 1.5);
											$arrayComments = array_slice($arrayComments, $arrayStart, $arrayCount);
										}
										
										if($blogStorage->set('comments_'.$URI[2], json_encode($arrayComments))){
											
											$count = $blogStorage->iss('count_'.$URI[2])?$blogStorage->get('count_'.$URI[2]):0;
											++$count;
											$blogStorage->set('count_'.$URI[2], $count);
											echo $count;
											
										}else{
											echo'Error';
										}
										unset($arrayComments);
										
									}else{
										echo'Moderation';
									}
									
									
									// в список последних 
									$lastComments = json_decode($blogStorage->get('lastComments'), true);
									$lastComments[] = array(
												'idComment' => $idComment,
												'idblog' => $URI[2],
												'login' => $loginForm,
												'text' => $textForm,
												'ip' => IP,
												'status' => 'gost',
												'published' => $published,
												'time' => time());
									$blogStorage->set('lastComments', json_encode($lastComments));
									
								}
							}else{
								echo'Error';
							}
							setcookie('captcha','',time(),'/');// Обнулили куки
						}
					}else{
						echo'Error';
					}
				}
				break;
				
			case 'validlogin':
				if (System::validPath($_POST['login'])){
					if (User::exists($_POST['login'])){
						echo $_POST['login'].' уже существует';
					}
				}else{
					echo 'Недопустимые символы';
				}
				break;
			
			
			
			case 'dellcomments':
				if (md5($_POST['ticket_com'].$Config->ticketSalt) != $_COOKIE['ticket_com']){
					echo'Ticket_com';
				}elseif($blogStorage->iss('post_'.$URI[2]) && ($User->preferences > 0 || $status == 'admin')){
					$arrayComments = json_decode($blogStorage->get('comments_'.$URI[2]), true);
					$count = 0;
					
					// foreach($_POST['comment'] as $value){
						// if(isset($arrayComments[$value])){
							// unset($arrayComments[$value]);
							// ++$count;
						// }
					// }
					
					foreach($arrayComments as $i => $row){
						if (in_array($row['id'], $_POST['comment'])){
							unset($arrayComments[$i]);
							++$count;
						}
					}
					
					if($count > 0){
						// Переиндексировали числовые индексы 
						$arrayComments = array_values($arrayComments); 
						// сохраняем массив комментов
						if($blogStorage->set('comments_'.$URI[2], json_encode($arrayComments))){
							echo $count;
						}else{ echo'Error'; }
					}else{ echo'Error'; }
				}else{ echo'Error'; }
				break;
			
			case 'loadcomments':
				if (is_numeric($URI[5]) && $URI[5] >= 0){
					if($blogStorage->iss('comments_'.$URI[2])){
						$arrayComments = json_decode($blogStorage->get('comments_'.$URI[2]), true);
						
						for($i = count($arrayComments) - $URI[5] - 1, $x = $i - $blogConfig->commentNavigation, $count = 0; $i >= $x; --$i){
							if($i < 0) break;
							echo'<div class="comment" id="comment'.$arrayComments[$i]['id'].'">
								<div class="head">
								<div class="avatar">';
									if(file_exists('./modules/users/images/'.$arrayComments[$i]['login'].'.jpg')){
									echo'<img style="border-radius:50%;" src="/modules/users/images/'.$arrayComments[$i]['login'].'.jpg" alt="">';
									}else{
									echo'<img src="/modules/blog/user.png" alt="">';	
									}
									echo'</div>
									<div class="info">
									<a class="name" href="/'.$blogConfig->idUser.'/'.$arrayComments[$i]['login'].'" class="author">'.$arrayComments[$i]['login'].'</a>
									'.($arrayComments[$i]['status'] == 'gost'?'<span class="gost">Гость</span>':'').'
									<br><span class="time">'.human_time(time() - $arrayComments[$i]['time']).' назад</span>
									'.($blogConfig->commentRules == 0 || $User->authorized ? '<a class="knob" href="javascript:void(0);"  onClick="Comments.toUser(\''.$arrayComments[$i]['login'].'\')" class="re">Ответить</a>':'').'
									'.($User->preferences > 0 || $status == 'admin' ? '<input type="checkbox" onClick="Comments.commentDellCheck();" name="comment[]" value="'.$arrayComments[$i]['id'].'">':'').'
								</div>	
								</div>
								<div class="comment-txt">'.ptext($arrayComments[$i]['text']).'</div>
							</div>';
							++$count;
						}
						if ($x > 0){
							echo'<button type="button" id="loadCommentsButton" onclick="Comments.loadComments('.($URI[5] + $blogConfig->commentNavigation + 1).');">Загрузить ещё</button>';
						}
						if ($count == 0){
							echo'<div class="noComments">Нет ни одного комментария</div>';
						}
						
					}else{
						echo'<div class="noComments">Нет ни одного комментария</div>';
					}
				}else{
					echo 'Ошибка при загрузки сообщений';
				}
				break;
				
			default :
				echo'Error';
				break;
		}
		 ob_end_flush(); exit;
	}
}

$return = '';

$showact = isset($URI[2])?$URI[2]:'nav';

if($showact != 'nav'){
	
	$URI[2] = htmlspecialchars(specfilter($URI[2]));
	
	if(isset($URI[3]) || !$blogParam = json_decode($blogStorage->get('post_'.$URI[2]))){
		
		header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
		
	}else{
	    $page->headhtml.= '
<meta property="og:url"                content="/'.$URI[1].'/'.$URI[2].'" />
<meta property="og:type"               content="article" />
<meta property="og:title"              content="'.$blogParam->header.'" />
<meta property="og:description"        content="'.$blogParam->description.'" />
<meta property="og:image"              content="'.$blogParam->img.'" />
';
		$page->title = $blogParam->header;
		$page->name = $page->title;
		$page->keywords = $blogParam->keywords;
		$page->description = $blogParam->description;
		$page->headhtml.= '';
		$page->headhtml.= '';
		$page->headhtml.= '<style>'.file_get_contents('modules/'.$page->module.'/style.min.css').'</style>
		<script>'.file_get_contents('modules/'.$page->module.'/jloader/jloader.min.js').'</script>
		<script>'.file_get_contents('modules/'.$page->module.'/comments.min.js').'</script>';
		if($blogConfig->navStyle == '0'){$page->headhtml.= '<style>'.file_get_contents('modules/'.$page->module.'/main.min.css').'</style>';}

        if($blogConfig->libJquery == 1){$page->endhtml.= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>';}
		$page->endhtml.= '<script>'.file_get_contents('modules/blog/script.js').'</script>
		<script>
	function bbtag(ltag, rtag){
		var obj = document.getElementById(\'textForm\');
		obj.focus();
		if(document.selection){
			sel = document.selection.createRange();
			if (sel.parentElement() == textarea)  sel.text = ltag + sel.text + rtag;
		}else if(typeof(obj.selectionStart) == "number"){
			var start = obj.selectionStart;
			var end = obj.selectionEnd;
			var value = obj.value;
			obj.select();
			if(start != end){
				obj.value = value.substr(0,start) + ltag + value.substr(start, end - start) + rtag + value.substr(end);
			}else{
				obj.value = value.substr(0,start)  + ltag + rtag + value.substr(start);
			}
			var endteg = ltag.length + rtag.length + end;
			obj.setSelectionRange(endteg,endteg);
		}
	}
</script>';
		
		$ip = '';
        $ip_user = $_SERVER['REMOTE_ADDR'];
        $page_viewed = $URI[2];
        setcookie('ip_user',$ip_user,time()+3600,'/');
        setcookie('page_viewed',$page_viewed,time()+3600,'/');
        if(isset($_COOKIE['ip_user'])){$ip = $_COOKIE['ip_user'];}
        if(isset($_COOKIE['page_viewed'])){$page_viewed = $_COOKIE['page_viewed'];}
		
		$arrayComments = json_decode($blogStorage->get('comments_'.$URI[2]), true);
		$col_com = count($arrayComments);

		$page->clear();// Очистили страницу перед выводом
		
		$out_content = str_replace('#content#', $blogParam->content, $blogConfig->contentTemplate);
		$out_content = str_replace('#date#', $blogParam->date, $out_content);
		$out_content = str_replace('#img#', $blogParam->img, $out_content);
		$out_content = str_replace('#com#', $col_com, $out_content);
		$out_content = str_replace('#views#', ''.file_get_contents('data/storage/module.blog/views_'.$URI[2].'.dat').'', $out_content);
		$out_content = str_replace('#yes#', ''.file_get_contents('data/storage/module.blog/yes_'.$URI[2].'.dat').'', $out_content);
		$out_content = str_replace('#no#', ''.file_get_contents('data/storage/module.blog/no_'.$URI[2].'.dat').'', $out_content);
		$return.= str_replace('#home#','/'.($URI[1] != 'index'?$URI[1]:''), $out_content);
		
		if($ip == '' || $page_viewed !== $URI[2]){
		$f=fopen('data/storage/module.blog/views_'.$URI[2].'.dat','a+');
        flock($f,LOCK_EX);
        $count=fread($f,100);
        @$count++;
        ftruncate($f,0);
        fwrite($f,$count);
        fflush($f);
        flock($f,LOCK_UN);
        fclose($f);
		}
		
		$return.= '
			<INPUT TYPE="hidden" NAME="id_pg" id="id_pg" VALUE="'.$URI[2].'">';
		
		if($blogConfig->commentEngine && $blogConfig->commentEnable && $blogParam->comments){
			
			$ticket_com = random(255);
			setcookie('ticket_com',md5($ticket_com.$Config->ticketSalt),time()+32000000,'/');
			
			$arrayComments = json_decode($blogStorage->get('comments_'.$URI[2]), true);
			$col_com = count($arrayComments);
			
			
					
			$return.= '
			    
				<div id="block-comments">
				<h3>Комментарии ('.$col_com.')</h3>';
			
			if($User->authorized){
				
				if($blogConfig->commentRules > 1 && $User->preferences == 0){
					// Показываем сообщение об ошибки если нехватает префов
					$return.= '<div id="errorPref">В данный момент Вы не можете оставлять сообщения</div>';
					
					
				}else{
					// Показываем форму для авторизированных
					
					$return.= '
					<form name="commentForm" action="#" method="post" onsubmit="return false;">
					<INPUT TYPE="hidden" NAME="ticket_com" VALUE="'.$ticket_com.'">
					<INPUT TYPE="hidden" NAME="act" VALUE="add">
					<div class="textarea_bar_blog">
							<a title="Полужирный шрифт" class="bold-icon" href="javascript:void(0);" onclick="bbtag(\'[b]\', \'[/b]\');"></a>
							<a title="Курсив" class="italic-icon" href="javascript:void(0);" onclick="bbtag(\'[i]\', \'[/i]\');"></a>
							<a title="Подчеркивание" class="underline-icon" href="javascript:void(0);" onclick="bbtag(\'[u]\', \'[/u]\');"></a>
							<a title="Цвет текста красный" class="red-icon" href="javascript:void(0);" onclick="bbtag(\'[red]\', \'[/red]\');"></a>
							<a title="Вставить код" class="code-icon" href="javascript:void(0);" onclick="bbtag(\'[code]\', \'[/code]\');"></a>
							<a title="Вставить ссылку" class="url-icon" href="javascript:void(0);" onclick="bbtag(\'[url]\', \'[/url]\');"></a>
							<a title="Втавить изображение" class="img-icon" href="javascript:void(0);" onclick="bbtag(\'[img]\', \'[/img]\');"></a><br>
							<a title="Вставить смайл" class="emo1" href="javascript:void(0);" onclick="bbtag(\'[emo1]\', \'[/emo1]\');"></a>
							<a title="Вставить смайл" class="emo2" href="javascript:void(0);" onclick="bbtag(\'[emo2]\', \'[/emo2]\');"></a>
							<a title="Вставить смайл" class="emo3" href="javascript:void(0);" onclick="bbtag(\'[emo3]\', \'[/emo3]\');"></a>
							<a title="Вставить смайл" class="emo4" href="javascript:void(0);" onclick="bbtag(\'[emo4]\', \'[/emo4]\');"></a>
							<a title="Вставить смайл" class="emo5" href="javascript:void(0);" onclick="bbtag(\'[emo5]\', \'[/emo5]\');"></a>
							<a title="Вставить смайл" class="emo6" href="javascript:void(0);" onclick="bbtag(\'[emo6]\', \'[/emo6]\');"></a>
							<a title="Вставить смайл" class="emo7" href="javascript:void(0);" onclick="bbtag(\'[emo7]\', \'[/emo7]\');"></a>
							<a title="Вставить смайл" class="emo8" href="javascript:void(0);" onclick="bbtag(\'[emo8]\', \'[/emo8]\');"></a>
							<a title="Вставить смайл" class="emo9" href="javascript:void(0);" onclick="bbtag(\'[emo9]\', \'[/emo9]\');"></a>
							<a title="Вставить смайл" class="emo10" href="javascript:void(0);" onclick="bbtag(\'[emo10]\', \'[/emo10]\');"></a>
							<a title="Вставить смайл" class="emo11" href="javascript:void(0);" onclick="bbtag(\'[emo11]\', \'[/emo11]\');"></a>
							<a title="Вставить смайл" class="emo12" href="javascript:void(0);" onclick="bbtag(\'[emo12]\', \'[/emo12]\');"></a>
							<a title="Вставить смайл" class="emo13" href="javascript:void(0);" onclick="bbtag(\'[emo13]\', \'[/emo13]\');"></a>
							<a title="Вставить смайл" class="emo14" href="javascript:void(0);" onclick="bbtag(\'[emo14]\', \'[/emo14]\');"></a>
							<a title="Вставить смайл" class="emo15" href="javascript:void(0);" onclick="bbtag(\'[emo15]\', \'[/emo15]\');"></a>
							<a title="Вставить смайл" class="emo16" href="javascript:void(0);" onclick="bbtag(\'[emo16]\', \'[/emo16]\');"></a>
					</div>
					<div class="comment-form">
					<div class="txt">Сообщение <span id="textReport"></span><br><textarea id="textForm" name="text" required></textarea></div>
					<div class="btn1"><input type="submit" id="submit-btn" onclick="Comments.submitCommentForm();" value="Отправить"></div>
					</div>
					</form>
					';
					
					
				}
				
			}else{
				
				if($blogConfig->commentRules > 0){
					// Показываем сообщение об необходимости авторизироваться
					$return.= '<div id="errorAuth">Чтобы оставлять сообщения необходимо авторизоваться</div>';
				}else{
					// Показываем форму для гостей
					$return.= '
					<form name="commentForm" action="#" method="post" onsubmit="return false;">
					<INPUT TYPE="hidden" NAME="ticket_com" VALUE="'.$ticket_com.'">
					<INPUT TYPE="hidden" NAME="act" VALUE="add">
					<div class="comment-form">
					<div class="login-input">Логин <span id="loginReport"></span><br><input id="loginForm" type="text" name="login" value="" required></div>
					<div class="txt">Сообщение <span id="textReport"></span><br><textarea id="textForm" name="text" required></textarea></div>';                      
						$return.= ''.(Module::exists('captcha')?'
							<div class="captcha"><img id="captcha" src="/modules/captcha/captcha.php?rand='.rand(0, 99999).'" alt="captcha"  onclick="document.getElementById(\'captcha\').src = \'/modules/captcha/captcha.php?\' + Math.random()" style="cursor:pointer;"></div>
							<div class="sign">Введите символы с картинки<br><input id="captchaForm" type="text" name="captcha" value="" required></div>
							<div style="font-size:12px; opacity: 0.7;clear:both;">Для обновления символов нажмите на картинку</div>
						':'').'';
						
						$return.= '<div class="btn2"><input type="submit" id="submit-btn" onclick="Comments.submitCommentForm();" value="Отправить"></div>
					</div>
					</form>
					';
					
					
				}
				
			}
			
			
			$return.= '
				<div id="requestReport"></div>
				<div id="comments">Загрузка...</div>
			';
			
			if($User->preferences > 0 || $status == 'admin'){
				$return.= '<button type="button" id="commentDellButton" onclick="Comments.commentDell();">Удалить выделенное</button>';
			}
			$return.= '
				<script>
					Comments.run({
						id: "'.$URI[2].'",
						ticket_com: "'.$ticket_com.'",
						newCommentCheckInterval: '.$blogConfig->commentCheckInterval.',
						commentMaxLength: '.$blogConfig->commentMaxLength.'
					});
				</script>
			</div>';
			
		}else{
			$return.= ($blogParam->comments == '1')?$blogConfig->commentTemplate:'';
		}
	}
}else{

	if($blogConfig->navStyle == '0'){$page->headhtml.= '<style>'.file_get_contents('modules/'.$page->module.'/main.min.css').'</style>';}
	
	if(isset($URI[4]) || isset($URI[2]) && isset($URI[3]) == false){
		header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
	}
	
	if(isset($URI[2])){
		$page->clear(); 
	}
		
	if(($listIdBlog = json_decode($blogStorage->get('list'), true)) == false){
		$return.= '<div class="msg">Записей пока нет</div>';
	}else{
		
		//перевернули масив для вывода новостей в обратном порядке
		$listIdBlog = array_reverse($listIdBlog);
		
		//
		$nom = count($listIdBlog);
		
		//определили количество страниц
		$countPage = ceil($nom / $blogConfig->navigation); 
		
		//проверка правельности переменной с номером страницы
		if(isset($URI[3])){$nom_page = $URI[3];}else{ $nom_page = 1; }
		if(!is_numeric($nom_page) || $nom_page <= 0 || $nom_page > $countPage){
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
		}
		
		//начало навигации
		$i = ($nom_page - 1) * $blogConfig->navigation;
		$var = $i + $blogConfig->navigation;
		
		while($i < $var){
			if($i < $nom){
				if($blogStorage->iss('post_'.$listIdBlog[$i])){
					$blogParam = json_decode($blogStorage->get('post_'.$listIdBlog[$i]));
										
					$arrayComments = json_decode($blogStorage->get('comments_'.$listIdBlog[$i]), true);
		            $col_com = count($arrayComments);
					
					$out_prev = str_replace('#header#', $blogParam->header, $blogConfig->prevTemplate);
					$out_prev = str_replace('#content#', $blogParam->prev, $out_prev);
					$out_prev = str_replace('#date#', $blogParam->date, $out_prev);
					$out_prev = str_replace('#img#', $blogParam->img, $out_prev);
					$out_prev = str_replace('#com#', $col_com, $out_prev);
					$out_prev = str_replace('#views#', ''.file_get_contents('data/storage/module.blog/views_'.$listIdBlog[$i].'.dat').'', $out_prev);
		            $out_prev = str_replace('#yes#', ''.file_get_contents('data/storage/module.blog/yes_'.$listIdBlog[$i].'.dat').'', $out_prev);
		            $out_prev = str_replace('#no#', ''.file_get_contents('data/storage/module.blog/no_'.$listIdBlog[$i].'.dat').'', $out_prev);
					$return.=  str_replace('#uri#', '/'.$URI[1].'/'.$listIdBlog[$i], $out_prev);
				}
			}
			++$i;
		}
	  

		if($nom > $blogConfig->navigation){  
$return.= '<div id="pagination">
<ul class="pagination">';
if($countPage > 15){
$a = $nom_page - 3;
$b = $nom_page + 3;
}else{
$a = $nom_page - 15;
$b = $nom_page + 15;	
}
$c = $nom_page - 1;
$d = $nom_page + 1;
$x = ceil($nom / $amtpr_page);
$y = $x + 1;
$z = $nom_page;
if($c > 1){
$pagination = '/'.$URI[1].'/nav/'.$c.'';
}else{
$pagination = '/'.$URI[1].'';	
}
			
if($z < 2){
$return.= '<li class="disabled"><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}else{
$return.= '<li><a href="'.$pagination.'" aria-label="Previous">&laquo;</a></li>';
}
if($countPage > 15){
if($nom_page > 4){$return.= '<li><a href="/'.$URI[1].'">1</a></li>';}
if($nom_page > 5){$return.= '<li class="punkt">...</li>';}
}
while($a <= $b){
if(($a > 0) && ($a <= $countPage)){
if($nom_page == $a){
if($a == 1){
$return.= '<li class="active"><a href="/'.$URI[1].'">'.$a.'</a></li>';
}else{
$return.= '<li class="active"><a href="/'.$URI[1].'/nav/'.$a.'">'.$a.'</a></li>';	
}
}else{
if($a == 1){
$return.= '<li><a href="/'.$URI[1].'">'.$a.'</a></li>';
}else{
$return.= '<li><a href="/'.$URI[1].'/nav/'.$a.'">'.$a.'</a></li>';	
}
}
}
++$a;
}
if($countPage > 15){
if($nom_page < ($countPage - 4)){$return.= '<li class="punkt">...</li>';}
if($nom_page < ($countPage - 3)){$return.= '<li><a href="/'.$URI[1].'/nav/'.$countPage.'">'.$countPage.'</a></li>';}
}	
if($d > $countPage){
$return.= '<li class="disabled"><a href="/'.$URI[1].'/nav/'.$nom_page.'" aria-label="Next">&raquo;</a></li>';
}elseif($y > $d){
$return.= '<li><a href="/'.$URI[1].'/nav/'.$d.'" aria-label="Next">&raquo;</a></li>';
}			
$return.= '</ul>
</div>
';
}	

		
		
		
	}
	
	
}
return $return;
?>