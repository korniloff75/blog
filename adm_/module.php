<?php
require('../system/global.dat');
require('./include/start.dat');

if($status=='admin'){
	
	if(isset($_GET['module'])){$MODULE = $_GET['module'];}
	elseif(isset($_POST['module'])){$MODULE = $_POST['module'];}
	
	if(Module::isAdminPage($MODULE)){
		require Module::pathRun($MODULE, 'admin');	
	}else{
		?>
<div class="msg">Произошла ошибка в запросе</div>
<script type="text/javascript">
setTimeout('window.location.href = \'index.php?\';', 3000);
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