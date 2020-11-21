<?php
require('../system/global.dat');
setcookie('password','','0','/');
System::notification('Выполнен выход из панели управления IP '.IP.' UA '.UA.'', 'g');
header('Location: index.php');
?>