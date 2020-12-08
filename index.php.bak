<?php
require('./system/global.dat');
ob_start($Config->gzip?'ob_gzhandler':null);


//Обработка ЧПУ
if($Config->uriRule == 1){ // Разрешаем произвольные GET параметры
	if (strpos(REQUEST_URI, '?') !== false) {
		$URI = explode('/', substr(REQUEST_URI, 0, strpos(REQUEST_URI, '?')));
	}else{
		$URI = explode('/', REQUEST_URI);
	}
}
if($Config->uriRule == 2){ // Запрещаем произвольные GET параметры
	$URI = explode('/', REQUEST_URI);
}
if($URI[1] == ''){$URI[1] = $Config->indexPage;}
$MODULE_URI = array();
for($i = 2, $c = count($URI); $i < $c; ++$i){
	$MODULE_URI[] = $URI[$i];
}
$MODULE_URI = '/'.implode('/', $MODULE_URI);



//Обработка слешей в конце адресов, в зависимости от настроек редиректим или 404
$last_key_URI = count($URI) - 1;
if(strlen($URI[$last_key_URI]) == 0){
	if($Config->slashRule == '1'){
		$redirect_URI = '';
		foreach($URI as $v){
			if($v != '') $redirect_URI.= '/'.$v;
		}
		if($redirect_URI == '/'.$Config->indexPage){$redirect_URI = '/';}
		header(PROTOCOL.' 301 Moved Permanently');
		header('Location: '.$redirect_URI); ob_end_flush(); exit();
	}
	if($Config->slashRule == '2'){
		header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
	}
}



//Обработка страниц
if(Page::exists($URI[1])){
	$Page = new Page($URI[1], $Config);
	$page = $Page;// Склонировали для совместимости со старыми расширениями
	
	if( $Page->show == '1' || 
		$Page->show == '2' && $User->preferences > 0 ||
		$status == 'admin'){
		
		//Загрузка модуля для страницы
		if($Page->module != 'no/module'){
			if(Module::isIntegrationPage($Page->module)){
				$Page->content.= require(Module::pathRun($Page->module, 'integration_page'));
			}else{
				$Page->content = $Page->error;
			}
		}
		
		//Загрузка модулей для всех страниц
		foreach($RunModules->pages as $value){
			if(Module::isIntegrationPages($value)){
				$Page->content.= require(Module::pathRun($value, 'integration_pages'));
			}
		}
		
		// Фикс для ссылок
		if(isset($URI[2]) && $Page->module == 'no/module'// ошибка если есть продолжение ссылки но нет подключенных к странице модулей
			|| REQUEST_URI == '/'.$Config->indexPage) // ошибка если ссылка /index
		{
			header(PROTOCOL.' 404 Not Found'); require('./pages/404.html'); ob_end_flush(); exit();
		}
		
		// Вывод страницы по шаблону
		header('Content-type: text/html; charset=utf-8');
		if($Page->template != 'def/template' && Module::isTemlate($Page->template)){
			require(Module::pathRun($Page->template, 'template'));
		}elseif(Module::isTemlate($Config->template)){
			require(Module::pathRun($Config->template, 'template'));
		}else{
			require('./pages/template_not_found.html');
		}
		
		
		
	}else{
		header(PROTOCOL.' 404 Not Found'); require('./pages/404.html');
	}
}else{
	header(PROTOCOL.' 404 Not Found'); require('./pages/404.html');
}

// Загрузка модулей для выполнения после генерации, но перед выводом и закрытия кеша вывода
// Таким типом модуля можно производить манипуляции с кешем вывода
foreach($RunModules->end as $value){
	if(Module::isIntegrationEnd($value)){
		require(Module::pathRun($value, 'integration_end'));
	}
}
		
ob_end_flush();
?>