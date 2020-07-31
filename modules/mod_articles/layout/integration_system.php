<?php
if($status == 'admin' && basename(SELF) == 'bloks.php' && is_dir('../modules/menu')){
System::addAdminHeadHtml('<style>
.content table:nth-child(1){
display:block;
height:30px;
visibility: hidden;
}
.content table:nth-child(1):before{
visibility: visible;
content:\'Главное меню в этом разделе не доступно, т.к. подключен модуль многоуровневого меню! Для управления Главным меню перейдите в панель управления модулем.\';
color:red;
}
</style>');
}
System::addAdminHeadHtml('<link rel="StyleSheet" href="../modules/layout/layout.min.css">
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>');

if($status == 'admin' && (basename(SELF) == 'index.php' || basename(SELF) == 'setting.php')){
if(file_exists('./uninstall.php')){
unlink('./uninstall.php');
}
}
if($status !== 'admin' && basename(SELF) == 'index.php'){
		
if(is_dir('../modules/statistic')){
	
	
$blok_data = file('../modules/statistic/statistics.dat');

$nom = count($blok_data);
$str = file_get_contents('../modules/statistic/max_visits.dat');

$difference = $nom - $str;

if($nom > $str){	
$result = array_slice($blok_data, $difference);
file_put_contents('../modules/statistic/statistics.dat', implode($result), LOCK_EX);	
}
}
}
?>