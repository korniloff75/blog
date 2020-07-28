<?php
if(basename(SELF) == 'module.php' && isset($_GET['module']) && $_GET['module'] == 'news_categories'){
System::addAdminHeadHtml('<style>
.box h3.av{
overflow:hidden;display:block;	
}
.box h3.av img{
display:block;float:left;border-radius:50%;border:1px solid #ccc;	
}
.box h3.av span{
float:left;display:block;margin:7px 0;margin-left:10px;	
}
span.emo1{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo1.png) no-repeat;
background-size: 100% auto !important;
}
span.emo2{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo2.png) no-repeat;
background-size: 100% auto !important;
}
span.emo3{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo3.png) no-repeat;
background-size: 100% auto !important;
}
span.emo4{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo4.png) no-repeat;
background-size: 100% auto !important;
}
span.emo5{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo5.png) no-repeat;
background-size: 100% auto !important;
}
span.emo6{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo6.png) no-repeat;
background-size: 100% auto !important;
}
span.emo7{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo7.png) no-repeat;
background-size: 100% auto !important;
}
span.emo8{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo8.png) no-repeat;
background-size: 100% auto !important;
}
span.emo9{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo9.png) no-repeat;
background-size: 100% auto !important;
}
span.emo1{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo1.png) no-repeat;
background-size: 100% auto !important;
}
span.emo10{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo10.png) no-repeat;
background-size: 100% auto !important;
}
span.emo11{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo11.png) no-repeat;
background-size: 100% auto !important;
}
span.emo12{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo12.png) no-repeat;
background-size: 100% auto !important;
}
span.emo13{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo13.png) no-repeat;
background-size: 100% auto !important;
}
span.emo14{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo14.png) no-repeat;
background-size: 100% auto !important;
}
span.emo15{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo15.png) no-repeat;
background-size: 100% auto !important;
}
span.emo16{
display:inline-block;
width:18px;height:18px;
padding:0;
margin:0 2px;
background:url(/modules/news_categories/smails/emo16.png) no-repeat;
background-size: 100% auto !important;
}
</style>');

function NewsBBCode($html){
	$html = trim($html[1]);
	$html = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$html);
	$html = str_replace('  ',' &nbsp;',$html);
	$html = preg_replace('/&quot;(.*?)&quot;/', '<span class="quot">&quot;\1&quot;</span>', $html);
	$html = preg_replace('/\'(.*?)\'/', '<span class="quot">\'\1\'</span>', $html);
	$html = str_replace("\n",'<br>', $html);
	$html = specfilter($html);
	return '<pre><code>'.$html.'</code></pre>';
}

function NewsFormatText($text){
	$text = preg_replace_callback('#\[code\](.*?)\[/code\]#si', 'NewsBBCode', $text);
	$text = preg_replace('#\[b\](.*?)\[/b\]#si', '<span style="font-weight: bold;">\1</span>', $text);
	$text = preg_replace('#\[i\](.*?)\[/i\]#si', '<span style="font-style: italic;">\1</span>', $text);
	$text = preg_replace('#\[u\](.*?)\[/u\]#si', '<span style="text-decoration: underline;">\1</span>', $text);
	$text = preg_replace('#\[red\](.*?)\[/red\]#si', '<span style="color: #E53935;">\1</span>', $text);
    $text = preg_replace('#\[img\](.*?)\[/img\]#si', '<img src="\1" alt="">', $text);
    $text = preg_replace('#\[url\](.*?)\[/url\]#si', '<a href="\1" target="_blank">\1</a>', $text);
	$text = preg_replace('#\[emo1\](.*?)\[/emo1\]#si', '<span class="emo1"></span>', $text);
	$text = preg_replace('#\[emo2\](.*?)\[/emo2\]#si', '<span class="emo2"></span>', $text);
	$text = preg_replace('#\[emo3\](.*?)\[/emo3\]#si', '<span class="emo3"></span>', $text);
	$text = preg_replace('#\[emo4\](.*?)\[/emo4\]#si', '<span class="emo4"></span>', $text);
	$text = preg_replace('#\[emo5\](.*?)\[/emo5\]#si', '<span class="emo5"></span>', $text);
	$text = preg_replace('#\[emo6\](.*?)\[/emo6\]#si', '<span class="emo6"></span>', $text);
	$text = preg_replace('#\[emo7\](.*?)\[/emo7\]#si', '<span class="emo7"></span>', $text);
	$text = preg_replace('#\[emo8\](.*?)\[/emo8\]#si', '<span class="emo8"></span>', $text);
	$text = preg_replace('#\[emo9\](.*?)\[/emo9\]#si', '<span class="emo9"></span>', $text);
	$text = preg_replace('#\[emo10\](.*?)\[/emo10\]#si', '<span class="emo10"></span>', $text);
	$text = preg_replace('#\[emo11\](.*?)\[/emo11\]#si', '<span class="emo11"></span>', $text);
	$text = preg_replace('#\[emo12\](.*?)\[/emo12\]#si', '<span class="emo12"></span>', $text);
	$text = preg_replace('#\[emo13\](.*?)\[/emo13\]#si', '<span class="emo13"></span>', $text);
	$text = preg_replace('#\[emo14\](.*?)\[/emo14\]#si', '<span class="emo14"></span>', $text);
	$text = preg_replace('#\[emo15\](.*?)\[/emo15\]#si', '<span class="emo15"></span>', $text);
	$text = preg_replace('#\[emo16\](.*?)\[/emo16\]#si', '<span class="emo16"></span>', $text);
	$text = '<p>'.str_replace("\n",'</p><p>', trim($text)).'</p>';
	$text = specfilter($text);
	$text = str_replace('<p></p>', '', $text);
	return $text;
}
}
?>