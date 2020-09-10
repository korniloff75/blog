<?php
if (!isset($Config)) global $Config;
$articlesStorage = new EngineStorage('module.articles2');
if($articlesStorage->iss('articlesConfig')){
	$articlesConfig = json_decode($articlesStorage->get('articlesConfig'));
}
if(!isset($articlesConfig)){
	$articlesConfig = new stdClass();
}
// Настройки поумолчанию
if(!isset($articlesConfig->navigation)) $articlesConfig->navigation = 8;
if(!isset($articlesConfig->countInBlok)) $articlesConfig->countInBlok = 3;
if(!isset($articlesConfig->formatDate)) $articlesConfig->formatDate = 'd.m.Y';
if(!isset($articlesConfig->idPage)) $articlesConfig->idPage = 'articles';
if(!isset($articlesConfig->idUser)) $articlesConfig->idUser = 'user';

$articlesConfig->blokTemplate = file_exists(Module::pathRun($Config->template, 'articles.blok.template'))?file_get_contents(Module::pathRun($Config->template, 'articles.blok.template')):
'<article class="nblok">
<p style="padding-bottom:0px;"><a href="#uri#">#header#</a></p>
#content#
<p>Категория: <a href="#categoryuri#">#categoryname#</a></p>
</article>';

$articlesConfig->prevTemplate = file_exists(Module::pathRun($Config->template, 'articles.prev.template'))?file_get_contents(Module::pathRun($Config->template, 'articles.prev.template')):
'<article class="articles">
<h2><a href="#uri#">#header#</a></h2>
<p class="i"><img src="#img#" alt="" style="width: 100%;"></p>
#content#
<p class="t">#date# | Категория: <a href="#categoryuri#">#categoryname#</a> | <a href="#uri#">Подробнее</a></p>
</article>';

$articlesConfig->contentTemplate =  file_exists(Module::pathRun($Config->template, 'articles.content.template'))?file_get_contents(Module::pathRun($Config->template, 'articles.content.template')):
'<p><img src="#img#" alt="" style="width: 100%;"></p>
#content#
<p>#date# | Категория: <a href="#categoryuri#">#categoryname#</a></p>';

if(!isset($articlesConfig->commentTemplate)) $articlesConfig->commentTemplate = "<!-- Source Comment -->\r\n";
if(!isset($articlesConfig->commentEngine)) $articlesConfig->commentEngine = 1;
if(!isset($articlesConfig->commentEnable)) $articlesConfig->commentEnable = 1;
if(!isset($articlesConfig->commentRules)) $articlesConfig->commentRules = 1;
if(!isset($articlesConfig->commentModeration)) $articlesConfig->commentModeration = 1;
if(!isset($articlesConfig->commentModerationNumPost)) $articlesConfig->commentModerationNumPost = 10;
if(!isset($articlesConfig->commentMaxLength)) $articlesConfig->commentMaxLength = 1000;
if(!isset($articlesConfig->commentNavigation)) $articlesConfig->commentNavigation = 100;
if(!isset($articlesConfig->commentMaxCount)) $articlesConfig->commentMaxCount = 1000;
if(!isset($articlesConfig->commentCheckInterval)) $articlesConfig->commentCheckInterval = 15000;
if(!isset($articlesConfig->cat)) $articlesConfig->cat = array();
if(!isset($articlesConfig->blokCat)) $articlesConfig->blokCat = 0;
if(!isset($articlesConfig->indexCat)) $articlesConfig->indexCat = 0;
if(!isset($articlesConfig->turbo)) $articlesConfig->turbo = 0;
if(!isset($articlesConfig->turboItems)) $articlesConfig->turboItems = 1000;
if(!isset($articlesConfig->turboId)) $articlesConfig->turboId = 'turbo';
if(!isset($articlesConfig->turboCacheTime)) $articlesConfig->turboCacheTime = 3600;
if(!isset($articlesConfig->custom)) $articlesConfig->custom = array();
if(!isset($articlesConfig->turboExceptions)) $articlesConfig->turboExceptions = 'test1, test2, test3';

?>
