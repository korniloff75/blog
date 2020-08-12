<?php
$blogStorage = new EngineStorage('module.blog');

if($blogStorage->iss('blogConfig')){
	$blogConfig = json_decode($blogStorage->get('blogConfig'));
}else{
	$blogConfig = json_decode('{
		"navigation":8,
		"name_rss":"Блог",
		"countInBlok":3,
		"navStyle":0,
		"libJquery":0,
		"formatDate":"d.m.Y",
		"idPage":"blog",
		"idUser":"user",
		"prevTemplate":"<article class=\"blog\">\r\n<h2><a href=\"#uri#\">#header#<\/a><\/h2>\r\n<p class=\"i\"><img src=\"#img#\" alt=\"\" style=\"width: 100%;\"><\/p>\r\n#content#\r\n<p class=\"t\">#date# | <a href=\"#uri#\">Подробнее<\/a><\/p>\r\n<\/article>\r\n",
		"contentTemplate":"<p><img src=\"#img#\" alt=\"\" style=\"width: 100%;\"><\/p>\r\n#content#\r\n<p>#date# | <a href=\"#home#\">Вернуться назад<\/a><\/p>\r\n",
		"commentTemplate":"<!-- Source Comment -->\r\n",
		"commentEngine":1,
		"commentEnable":1,
		"commentRules":1,
		"commentModeration":1,
		"commentModerationNumPost":10,
		"commentMaxLength":1000,
		"commentNavigation":100,
		"commentMaxCount":1000,
		"commentCheckInterval":15000
	}');
}

$blogConfig->prevTemplate = '
<div class="post-prev">
<h2>#header#</h2>
<div class="img-prev"><img src="#img#" alt=""></div>
<div class="txt-prev">
#content#
</div>
<div class="icon-prev"><span class="com"><span class="com-icon"></span> <span class="com-num">#com#</span></span>
<span class="views"><span class="views-icon"></span> <span class="views-num">#views#</span></span>
<span class="yes"><span class="yes-icon"></span> <span class="yes-num">#yes#</span></span>
<span class="no"><span class="no-icon"></span> <span class="no-num">#no#</span></span></div>
<div class="data">#date#</div>
<div class="btn-prev"><a href="#uri#">Подробнее</a></div>
</div>
';
$blogConfig->contentTemplate = '
<div class="post-page">
#content#
<div class="icon-prev"><span class="com"><span class="com-icon"></span> <span class="com-num">#com#</span></span>
<span class="views"><span class="views-icon"></span> <span class="views-num">#views#</span></span>
<span class="yes" id="yes"><span class="yes-icon"></span> <span class="yes-num">#yes#</span></span>
<span class="no" id="no"><span class="no-icon"></span> <span class="no-num">#no#</span></span></div>
<div class="data">#date#</div>
<div class="btn-back"><a href="#home#">Вернуться назад</a></div>
</div>
';
?>