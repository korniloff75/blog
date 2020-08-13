<script type="text/javascript" src="/modules/ckeditor_plus/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	CKEDITOR.replaceAll(function( textarea, config ) {
		// An assertion function that needs to be evaluated for the <textarea>
		// to be replaced. It must explicitely return "false" to ignore a
		// specific <textarea>.
		// You can also customize the editor instance by having the function
		// modify the "config" parameter.
		
		// определяем высоту редактора 
		config.height = textarea.style.height != '' ? textarea.style.height : 300;
		
		config.extraPlugins = 'youtube';
		config.language = 'ru';
		//config.skin = 'moono-lisa';
		config.skin = 'kama';
		
		// File manager
		// Specify the path to DOCUMENT_ROOT
		config.filebrowserBrowseUrl = '/modules/ckeditor_plus/ckfsys/browser/default/browser.php?Connector=/modules/ckeditor_plus/ckfsys/connectors/php/connector.php';
		config.filebrowserImageBrowseUrl = '/modules/ckeditor_plus/ckfsys/browser/default/browser.php?type=Image&Connector=/modules/ckeditor_plus/ckfsys/connectors/php/connector.php';
	});
	
	// разрешить теги <style>
    CKEDITOR.config.protectedSource.push(/<(style)[^>]*>.*<\/style>/ig);
    // разрешить теги <script>
    CKEDITOR.config.protectedSource.push(/<(script)[^>]*>.*<\/script>/ig);
    // разрешить php-код
    CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);
    // разрешить любой код: <!--dev-->код писать вот тут<!--/dev-->
    CKEDITOR.config.protectedSource.push(/<!--dev-->[\s\S]*<!--\/dev-->/g);
</script>