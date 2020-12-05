<?php

if (!class_exists('System')) exit; // Запрет прямого доступа



$ChatStorage = new EngineStorage('module.chat');


function bbcode($html){
	return '<pre><code>'.trim($html[1]).'</code></pre>';
}


function ptext($text){
	$text = preg_replace_callback('#\[code\](.*?)\[/code\]#si', 'bbcode', $text);
	$text = preg_replace('#\[b\](.*?)\[/b\]#si', '<span style="font-weight: bold;">\1</span>', $text);
	$text = preg_replace('#\[red\](.*?)\[/red\]#si', '<span style="color: #E53935;">\1</span>', $text);
	$text = str_replace("\n",'<br>',$text);
	$text = specfilter($text);
	$text = str_replace('<br><br>','<br>',$text);
	return $text;
}





if(isset($URI[2])){
	// Обработка ajax
	if($URI[2] == 'ajax' && isset($URI[3])){
		header("Cache-Control: no-store, no-cache, must-revalidate");// не даем кешировать ajax тупым браузерам (IE)
		switch ($URI[3]) {
			case 'online':
				$onlineTime = is_numeric($URI[3]) ? (($URI[3] / 1000) + 10) : 120;


				$listOnline = $ChatStorage->iss('listOnline')?json_decode($ChatStorage->get('listOnline'), true):array();
				$listOnline[$User->login] = time();
				foreach($listOnline as $key => $value){
					if($value >= time() - $onlineTime){
						echo'<div><a href="user/'.$key.'" class="user">'.$key.'</a><span class="online">Онлайн</span><a href="javascript:void(0);" onClick="Chat.closeListOnline(); Chat.toUser(\''.$key.'\')" class="re">Написать</a></div>';
					}
				}
				arsort($listOnline);
				foreach($listOnline as $key => $value){
					if($value < time() - $onlineTime){
						echo'<div><a href="user/'.$key.'" class="user">'.$key.'</a><span class="time">'.human_time(time() - $value).' назад</span><a href="javascript:void(0);" onClick="Chat.closeListOnline(); Chat.toUser(\''.$key.'\')" class="re">Написать</a></div>';
					}
				}
				break;
			
			


			case 'newcheck':
				if($ChatStorage->iss('idMessage')){
					echo $ChatStorage->get('idMessage');
				}else{
					echo 0;
				}
				if($User->authorized){
					if(($listOnline = json_decode($ChatStorage->get('listOnline'), true)) == false){
						$listOnline = array();
					} 
					
					$listOnline[$User->login] = time();
					foreach($listOnline as $key => $value){
						if($value < time() - 604800){
							unset($listOnline[$key]);
						}
					}
					$ChatStorage->set('listOnline', json_encode($listOnline));
				}
				break;
			
			case 'loadchat':
				if(!$User->authorized){
					echo'Error';
				}elseif($ChatStorage->iss('messages')){
					$arrayMessages = json_decode($ChatStorage->get('messages'), true);
					if(isset($URI[4])){
						if(is_numeric($URI[4])){
							foreach($arrayMessages as $row){
								if($URI[4] < $row['id']){
									if(file_exists(DR.'/'.$Config->userAvatarDir.'/'.$row['login'].'.jpg')){
										$avatar_file = '/'.$Config->userAvatarDir.'/'.$row['login'].'.jpg?'.filemtime(DR.'/'.$Config->userAvatarDir.'/'.$row['login'].'.jpg');
									}else{
										$avatar_file = '/modules/users/avatar.png';
									}
									echo'<div class="post'.($row['login'] == $User->login?' my':'').'" id="id'.$row['id'].'">
<div class="avatar"><a href="/user/'.$row['login'].'"><img src="'.$avatar_file.'" alt="avatar" id="avatar"></a></div>
<div class="head">
<a href="/user/'.$row['login'].'" class="user">'.$row['login'].'</a>
<span class="time">'.date("d.m.y H:i", $row['time']).'</span> 
<a href="javascript:void(0);" onClick="Chat.toUser(\''.$row['login'].'\')" class="re">Ответить</a> 
'.($User->preferences > 0 || $status == 'admin' ? '<input type="checkbox" onClick="Chat.dellCheck();" name="checkedpost[]" value="'.$row['id'].'">':'').'
</div><div class="msg">'.ptext($row['message']).'</div></div>';
								}
							}
						}
					}
				}
				break;

			case 'add':

				// Проверка тикетов убрана 5.1.14

				if($User->authorized){
					// Обрабатываем форму от пользователя
					$message = trim(htmlspecialchars($_POST['msg']));

					// Команда модератора на чистку чата
					if($User->preferences > 0 || $status == 'admin'){
						if(strtolower($message) == 'clear'){
							$ChatStorage->delete('messages');
							$ChatStorage->delete('idMessage');
							$message = 'Почистили';
						}
					}
					
					if(strlen($message) <= 10000 && strlen($message) > 0){
						
						$idMessage = $ChatStorage->iss('idMessage')?$ChatStorage->get('idMessage'):0;
						++$idMessage;
						$ChatStorage->set('idMessage', $idMessage);
						
						$arrayMessages = json_decode($ChatStorage->get('messages'), true);
						$arrayMessages[] = array(
									'id' => $idMessage,
									'login' => $User->login,
									'message' => $message,
									'ip' => IP,
									'time' => time());
						
						$arrayCount = count($arrayMessages);
						if($arrayCount >= 200){
							$arrayStart = $arrayCount -  round(200 / 1.5);
							$arrayMessages = array_slice($arrayMessages, $arrayStart, $arrayCount);
						}
						
						if($ChatStorage->set('messages', json_encode($arrayMessages))){
							
							++$User->numPost;
							$User->save();
							
							echo $idMessage;
							
						}else{
							echo'Error';
						}
						unset($arrayMessages);
					}else{
						echo'Strlen';
					}
				}else{
					echo'Authorized';
				}
				
				break;

			case 'dell':
				if (md5($_POST['ticket'].$Config->ticketSalt) != $_COOKIE['ticketChat']){
					echo'Error'; // Ticket
				}elseif($ChatStorage->iss('messages') && ($User->preferences > 0 || $status == 'admin')){
					$arrayMessages = json_decode($ChatStorage->get('messages'), true);
					$count = 0;
					
					foreach($arrayMessages as $i => $row){
						if (in_array($row['id'], $_POST['checkedpost'])){
							unset($arrayMessages[$i]);
							++$count;
						}
					}

					if($count > 0){
						// Переиндексировали числовые индексы 
						$arrayMessages = array_values($arrayMessages); 
						// сохраняем массив комментов
						if($ChatStorage->set('messages', json_encode($arrayMessages))){
							echo $count;
						}else{ echo'Error'; }
					}else{ echo'Error'; }
				}else{ echo'Error'; }
				
				break;

			default :
				echo'Error';
				break;
		}
		ob_end_flush(); exit();
	}

	header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();

}else{

	$ticket = random(255);
	setcookie('ticketChat',md5($ticket.$Config->ticketSalt),time()+32000000,'/');


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php $Page->get_headhtml();?>
<title><?php $Page->get_title();?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="description" content="<?php $Page->get_description();?>">
<meta name="keywords" content="<?php $Page->get_keywords();?>">
<script type="text/javascript"><?php echo file_get_contents('modules/'.$Page->module.'/jloader/jloader.min.js');?></script>
<script type="text/javascript"><?php echo file_get_contents('modules/'.$Page->module.'/chat.min.js');?></script>
<style type="text/css"><?php echo file_get_contents('modules/'.$Page->module.'/style.css');?></style>
</head>
<body>
<div id="chat">

<div id="listonline">
<div id="loadlistonline"></div>
<div id="closelistonline"><a href="javascript:void(0);" onclick="Chat.closeListOnline();">Закрыть</a></div>
</div>

<h1 id="header">
<a href="/"><span class="header"><?php $Page->get_header();?></span>
<span class="name"><?php $Page->get_name();?></span>
</a>
</h1>
<?php if($User->authorized):?>
<img src="/modules/chat/down.svg" alt="" id="down" onclick="Chat.down();" title="Вниз">
<button type="button" id="dellButton" onclick="Chat.dell();">Удалить выделенное</button>
<div id="menu" >
<a href="/user" id="user"><?php echo $User->login;?></a><a href="javascript:void(0);" id="online"  onclick="Chat.openListOnline();">Кто в чате</a>
</div>

<div id="posts"></div>
<div id="report"></div>
<div id="form">
	<form name="form" action="#" method="post" onsubmit="Chat.submit(); return false;">
		<input type="hidden" name="ticket" value="<?php echo $ticket;?>">
		<textarea id="msg" name="msg" placeholder="Сообщение..." rows="1" onkeypress="if(event.keyCode==10||(event.ctrlKey && event.keyCode==13)){Chat.submit();}" required></textarea>
	</form>
	<img src="/modules/chat/send.svg" alt="" id="send" onclick="Chat.submit();" title="Отправить">
</div>
</div>
<script type="text/javascript">
	Chat.run({
		id: "<?php echo $URI[1];?>",
		ticket: "<?php echo $ticket;?>",
		checkInterval: 20000,
		maxLength: 1000
	});

	// Убираем шапку если открыто в iframe
	if (window!=window.top) {
		document.getElementById('header').style.display = 'none';
		if(document.getElementById('header')){
			document.getElementById('user').style.display = 'none';
		}
	}

// 	msgTextarea.style.height = '3000px';
// msgTextarea.style.maxHeight = '150px';
	setInterval(function(){
		
	}, 100);

</script>
<?php else:?>
<div id="menu"><a href="/user" id="gost">Вход</a></div>
<div id="errorAut">Для общения в нашем чате необходимо <a href="/user">авторизироваться</a> или <a href="/user/reg">зарегистрироваться</a></div>
<?php endif;?>
<?php $page->get_endhtml();?>
</body></html><?php

	ob_end_flush(); exit();

}

return false;
?>