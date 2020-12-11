<?php
if($status=='admin'){
if($act=='index'){

echo'<div class="header"><h1>Инициализация шаблона</h1></div>
<div class="content">
<p>Все компоненты шаблона установлены. Для успешной работы шаблона необходимо выполнить инициализацию.</p>
<form name="settingform" action="module.php?module='.$MODULE.'" method="post">
<INPUT TYPE="hidden" NAME="act" VALUE="addin">
<input type="submit" name="" value="Начать инициализацию">
</form>
</div>
';
}

if($act=='addin'){
			
		$file1 = file_get_contents('../modules/'.$MODULE.'/file/uninstall.php');
        file_put_contents('uninstall.php', $file1);
		$dir = mkdir('../files/'.$MODULE.'/');
		$dir = mkdir('../modules/users/images/');
		$file2 = file_get_contents('../modules/'.$MODULE.'/file/favicon.ico');
        file_put_contents('../files/'.$MODULE.'/favicon.ico', $file2);
				
echo'
		<div class="header">
		<h1>Инициализация шаблона</h1>
		</div>
		
		<div class="content">		
		<div class="msg">
		<img src="../modules/'.$MODULE.'/file/busy.gif" alt=""><br><br>
		Подождите! Проводится инициализация шаблона.
		</div>
		</div>
		';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'uninstall.php?&template_in=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}

}
?>