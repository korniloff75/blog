<?php
if(isset($_POST['no'])){
$no = $_POST['no'];
$id_pg = $_POST['id_pg'];
$module_news = $_POST['module_news'];

$no_count = file_get_contents('../../data/storage/'.$module_news.'/no_'.$id_pg.'.dat');	
$res = $no_count + $no;
$msg_box = $res;
$fp = fopen('../../data/storage/'.$module_news.'/no_'.$id_pg.'.dat', 'w'); // режим записи
fputs($fp,$res);
fclose($fp);

echo json_encode(array(
'result' => $msg_box
));
}
if(isset($_POST['yes'])){
$yes = $_POST['yes'];
$id_pg = $_POST['id_pg'];
$module_news = $_POST['module_news'];
			
$yes_count = file_get_contents('../../data/storage/'.$module_news.'/yes_'.$id_pg.'.dat');	
$res = $yes_count + $yes;
$msg_box = $res;
$fp = fopen('../../data/storage/'.$module_news.'/yes_'.$id_pg.'.dat', 'w'); // режим записи
fputs($fp,$res);
fclose($fp);

echo json_encode(array(
'result' => $msg_box
));
}        
?>