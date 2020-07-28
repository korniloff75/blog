<script type="text/javascript" src="/modules/ckeditor_4.5.8_standard/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	CKEDITOR.replaceAll(function( textarea, config ) {
		// An assertion function that needs to be evaluated for the <textarea>
		// to be replaced. It must explicitely return "false" to ignore a
		// specific <textarea>.
		// You can also customize the editor instance by having the function
		// modify the "config" parameter.
		
		// определяем высоту редактора 
		config.height = textarea.style.height != '' ? textarea.style.height : 300;
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