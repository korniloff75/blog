<?php
require('../system/global.dat');
require('./include/start.dat');

if($status=='admin'){
	if($act=='index'){
		
		
		if(isset($_GET['template_in'])){
			
		$template_in = $_GET['template_in'];
		
        echo'
		<div class="header">
			<h1>Инициализация шаблона</h1>
		</div>
		
		<div class="content">		
		<div class="msg">Инициализация шаблона завершена.</div>
		</div>
		';
		
		unlink('../modules/'.$template_in.'/admin.php');
		delldir('../modules/'.$template_in.'/file');

?>
<script type="text/javascript">
setTimeout('window.location.href = \'setting.php\';', 3000);
</script>
<?php					
		}
		
		
	}
	
}
require('./include/end.dat');
?>