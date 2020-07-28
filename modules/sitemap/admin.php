<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

if($act=='index'){
	echo'<div class="header"><h1>Генератор карты сайта</h1></div>
	<div class="menu_page"><a href="index.php">&#8592; Вернуться назад</a></div>
	<div class="content">';
	if(file_exists('../sitemap.xml')){
		echo'<p style="color:red;">В корневой папке сайта обнаружен файл sitemap.xml. Начиная с версии движка 5.1.24, sitemap.xml генерируется автоматически. Необходимо удалить этот файл из корневой папки сайта, чтобы модуль начал работать.</p>';
	}else{
		echo'<p>Ваш sitemap.xml находится по адресу <a href="/sitemap.xml" target="_blank">'.SERVER.'/sitemap.xml</a></p>
		<p>Для уменьшения нагрузки на сервер, каждые 24 часа, вывод sitemap.xml кешируется. Выможете сбросить кеш прямо сейчас, если необходимо вывести актуальный sitemap.xml.</p>
		<button onclick="window.location.href = \'module.php?module='.$MODULE.'&act=add\';">Сбросить кэш сейчас</button> &nbsp; </span>';
	}
	echo'</div>';
}
if($act=='add'){
	$sitemapStorage = new EngineStorage('module.sitemap');
	$sitemapStorage->delete('sitemap');
	System::notification('Сброс кеша sitemap.xml', 'g');
	echo'<div class="msg">Кеш карты сайты сброшен</div>';

?>
<script type="text/javascript">
setTimeout('window.location.href = \'module.php?module=<?php echo $MODULE;?>\';', 3000);
</script>
<?php
}
?>