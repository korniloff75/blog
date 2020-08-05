<?php
$log->add('Module news_categories',null, [Module::exists('news_categories')]);
$log->add('Module mod_articles',null, [Module::exists('mod_articles')]);
$log->add('$Page->module',null, [$Page->module]);
// *contentEditable
if($status === 'admin' && isset($module_news)):?>

<style>
*[contenteditable="true"]{
	background-color: rgba(0, 0, 255, 0.151);
	outline: 1px solid blue;
	outline-offset: -1px;
}
*[contenteditable="true"]:hover{
	background-color: rgba(0, 0, 255, 0.055);
	outline: 1px solid blue;
	outline-offset: -1px;
}
*[contenteditable="true"]:focus{
	background-color: transparent;
	outline: 1px solid red;
	outline-offset: -1px;
}
</style>

<script src="https://cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script>

<script>
CKEDITOR.inlineAll();

var CE = {
	$item: $('article .news'),

	init: function() {
		// console.log('$rezult=', "\n");

		if(this.$item.length != 1) {
			console.log(
				`missing $item= `, this.$item,
			);
			return;
		}
		this.$item[0].contentEditable= true;

		this.save();

		/* $item.on('dblclick', (e)=>{
			e.currentTarget.contentEditable= true;
		}) */
	},

	save: function(){
		$('<img src="/kff_custom/assets/save.svg" style="cursor:pointer;" alt="SAVE" title="SAVE">')
		.insertAfter(this.$item)
		.on('click', this.request.bind(this));
	},
	request: function(e){
		console.log(
			// `$module_news= <?#=$module_news?>\n`,
			this.$item.html(), location
		);

		$.post(
			'/kff_custom/SaveNewsHandler.php?dev=1',
			{
				act: 'addedit',
				basePath: location.pathname,
				module_news: "<?=$module_news?>",
				content: this.$item.html(),
			}
		).then((response, status, xhr)=>{
			console.log(
				'status=', "<?=$status?>\n",
			);

			$('#logWrapper').html(response);

			var msg, color;
			if(status === 'success'){
				msg= 'Изменения успешно сохранены в файле.';
				color= 'green';
			} else {
				msg= 'Изменения не были сохранены. Не перегружайте страницу, чтобы не потерять их. Попробуйте изменить контент через админ-панель.';
				color= 'red';
			}
		$(`<p style="color:${color};">${msg}</p>`)
			.insertAfter(this.$item);
		});
	}
}

CE.init();
</script>
<?php endif;?>