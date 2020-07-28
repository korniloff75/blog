<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

$page->headhtml .= 
'
<style>
.category-pages { font-weight: normal!important; }
.category-pages-name {}
.category-pages-count { font-size: 75%!important; }
.category-subpages {}
.category-subpages p {}
.category-subpages a {}

.spoiler p { 
  margin: 0!important; padding: 0!important; 
  line-height: 2em!important; 
}

.spoiler p a { 
  color: #434E74!important; 
  display: block; 
  height: 100%;
  /*padding: 1px 5px!important;*/
  
  background: url(/modules/admin_pages_category/img_js_css/link.svg) 5px 50% no-repeat;
  padding: 1px 0px 1px 30px!important;
  font-size: 14px!important;
}
.spoiler p a:hover { 
  /*color: #AE8FB1!important;*/ 
  text-decoration: none!important;
  background-color: #F5F6F7!important;
}

.category-pages input[type="checkbox"] + label:before { display: none!important; }
.category-pages input[type="checkbox"]:checked + label:before { display: none!important; }
.category-pages input[type="checkbox"] + label { padding: 0!important; margin: 0!important; }


.category-pages input[type=checkbox] + label {
  background: url(/modules/admin_pages_category/img_js_css/more-24px.svg) 5px 50% no-repeat!important;
  background-color: #CFD6DB!important;
  padding: 0!important;
  transition: none!important;
}

.category-pages input[type=checkbox]:checked + label {
  background: url(/modules/admin_pages_category/img_js_css/less-24px.svg) 5px 50% no-repeat!important;
  background-color: #ccc!important;
  padding: 0!important;
  transition: none!important;
}

input[id^="spoiler"]{
 display: none;
}
input[id^="spoiler"] + label {
 display: block;
 background: #CFD6DB; /*#5E5E5E*/
 border-radius: 2px;
 color: #000; /*##fff*/
 text-align: center;
 font-size: 18px;
 cursor: pointer;
 transition: all .6s;
 margin: 0 auto; 
 width: 100%;
 /*padding: 5px 0px 5px 0px;*/
 
 /* ширина */
 height: 35px!important;
 line-height: 1.9em!important;
}
input[id^="spoiler"]:checked + label {
 color: #333;
 background: #ccc;
}
input[id^="spoiler"] ~ .spoiler {
 /*width: 90%;*/
 height: 0;
 overflow: hidden;
 opacity: 0;
 margin: 5px auto 0; 
 /*
 padding: 5px;
 padding-left: 10px;
 */ 
 background: #eee;
 border: 1px solid #ddd;
  border-radius: 2px;
 transition: all .6s;
}
input[id^="spoiler"]:checked + label + .spoiler{
 height: auto;
 opacity: 1;
 margin-bottom: 5px;
}
</style>
';

if ( !function_exists('Get_PagesCategoryByName') ) {

  function Get_PagesCategoryByName($Name = '', $site_checkbox = '') {	
   if ($Name != '') {
		
	global $Config;	
	
	$arr = file(DR.'/modules/admin_pages_category/data/cat-ids-names.dat');
	$ids = array();
	$names = array();
		for ($i = 0, $count_arr = count($arr); $i < $count_arr; $i++) {
			$tmp = explode('|', $arr[$i]);				
			$ids[] = trim($tmp[0]);
			$names[] = trim($tmp[1]);
			
			// если имя найдено
			if ($Name == $names[$i]) {
				$Name = $names[$i];
				$Id = $ids[$i];
			}	
		}
			
	$content = '';
	
	// подкатегории
	if (file_exists(DR.'/modules/admin_pages_category/data/'.$Id.'/sub-ids-names.dat')) {
	  $sub = file(DR.'/modules/admin_pages_category/data/'.$Id.'/sub-ids-names.dat');
	} 
	else { 
	 $sub = array(); 
	}
	 $subids = array();
	 $subnames = array();
	 for ($f = 0, $count_sub = count($sub); $f < $count_sub; $f++)
	  {
	   $tmp = explode('|', $sub[$f]);	 
	   $subids[] = trim($tmp[0]);
	   $subnames[] = trim($tmp[1]);
	  }
	  
	 $count_subnames = count($sub);
	 
	 if ($count_subnames != 0) {
	 $content .= '<div class="category-pages"><input type="checkbox" id="spoiler'.$Id.'-ByName" '.$site_checkbox.'> <label class="category-pages-name" for="spoiler'.$Id.'-ByName">'.$Name.' <span class="category-pages-count">('.$count_subnames.')</span></label><div class="spoiler">'; 
	 }
		// подкатегории, вывод
		for ($j = 0; $j < $count_subnames; $j++) {
		   $id = $subids[$j]; if ($id == '') { $id = $Config->indexPage; }
		   $content .= '<p class="category-subpages"><a href="/'.($id == $Config->indexPage ? '' : $id).'">'.$subnames[$j].'</a></p>';
		}	
	 if ($count_subnames != 0) { $content .= '</div></div>'; }
  	
   } 
   else { 
	$content = 'Категория не найдена.'; 
   }
	 

  return $content;
  }
	
}


RETURN Null;

?>