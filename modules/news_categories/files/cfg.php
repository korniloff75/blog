<?php
if (!isset($Config)) global $Config;

require('cfg.dat');

$newsStorage = new EngineStorage($module_news);
if($newsStorage->iss('newsConfig')){
	$newsConfig = json_decode($newsStorage->get('newsConfig'));
}
if(!isset($newsConfig)){
	$newsConfig = new stdClass();
}
// Настройки поумолчанию
if(!isset($newsConfig->name_rss)) $newsConfig->name_rss = $name_cat;
if(!isset($newsConfig->navigation)) $newsConfig->navigation = 8;
if(!isset($newsConfig->countInBlok)) $newsConfig->countInBlok = 3;
if(!isset($newsConfig->formatDate)) $newsConfig->formatDate = 'd.m.Y';
if(!isset($newsConfig->idPage)) $newsConfig->idPage = $url_cat;
if(!isset($newsConfig->idUser)) $newsConfig->idUser = 'user';
if(!isset($newsConfig->imgCategory)) $newsConfig->imgCategory = '/modules/news_categories/default.jpg';
$newsConfig->blokTemplate = file_exists(Module::pathRun($Config->template, 'news.blok.template'))?file_get_contents(Module::pathRun($Config->template, 'news.blok.template')):
'<article class="nblok">
<p style="padding-bottom:0px;"><a href="#uri#">#header#</a></p>
#content#
</article>';
$newsConfig->prevTemplate = file_exists(Module::pathRun($Config->template, 'news.prev.template'))?file_get_contents(Module::pathRun($Config->template, 'news.prev.template')):
'<article class="news">
<h2><a href="#uri#">#header#</a></h2>
<p class="i"><img src="#img#" alt="" style="width: 100%;"></p>
#content#
<p class="t">#date# | <a href="#uri#">Подробнее</a></p>
</article>';
$newsConfig->contentTemplate =  file_exists(Module::pathRun($Config->template, 'news.content.template'))?file_get_contents(Module::pathRun($Config->template, 'news.content.template')):
'<p><img src="#img#" alt="" style="width: 100%;"></p>
#content#
<p>#date# | <a href="#home#">Вернуться назад</a></p>';
if(!isset($newsConfig->commentTemplate)) $newsConfig->commentTemplate = "<!-- Source Comment -->\r\n";
if(!isset($newsConfig->commentEngine)) $newsConfig->commentEngine = 1;
if(!isset($newsConfig->commentEnable)) $newsConfig->commentEnable = 1;
if(!isset($newsConfig->commentRules)) $newsConfig->commentRules = 1;
if(!isset($newsConfig->style)) $newsConfig->style = 0;
if(!isset($newsConfig->commentModeration)) $newsConfig->commentModeration = 1;
if(!isset($newsConfig->commentModerationNumPost)) $newsConfig->commentModerationNumPost = 10;
if(!isset($newsConfig->commentMaxLength)) $newsConfig->commentMaxLength = 1000;
if(!isset($newsConfig->commentNavigation)) $newsConfig->commentNavigation = 100;
if(!isset($newsConfig->commentMaxCount)) $newsConfig->commentMaxCount = 1000;
if(!isset($newsConfig->commentCheckInterval)) $newsConfig->commentCheckInterval = 15000;
?>