<?php
if (!class_exists('System')) exit; // Запрет прямого доступа

if ($Config->wysiwyg == "ckeditor_plus") 
{		
System::addAdminHeadHtml('
<style type="text/css">
.cke_dialog_contents input:focus, .cke_dialog_contents input, .cke_dialog_contents textarea {
	border:none;
	box-shadow:none;
}
</style>
');
}	

?>