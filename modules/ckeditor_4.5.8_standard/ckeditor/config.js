/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// Common config injected by examples building script.
	// config.uploadUrl = kff.URI.join('/');
	// config.filebrowserBrowseUrl = '/modules/ckeditor_4.5.8_standard/kff.Explorer/index.php';
	// config.filebrowserImageBrowseUrl = '/apps/ckfinder/3.4.5/ckfinder.html?type=Images';

	// config.filebrowserUploadUrl = '/apps/ckfinder/3.4.5/core/connector/php/connector.php?command=QuickUpload&type=Files';
	// config.filebrowserUploadUrl = kff.URI.join('/');
	// console.log('config.filebrowserUploadUrl=',config.filebrowserUploadUrl);

	// config.filebrowserImageUploadUrl = kff.URI.join('/');
	// config.baseFloatZIndex = 10005;

	// *Allow use tags & scripts
	config.protectedSource.push(/<(script)[^>]*>.*<\/\1>/ig);
	config.protectedSource.push(/<\?[\s\S]*?\?>/g);// разрешить php-код
	config.allowedContent = true;

	// *Не кодировать спецсимволы в decimal
	config.entities = false;
	// *Сохранять переводы строк
	// config.protectedSource.push( /\n{1}/g );

	config.toolbarGroups = [
		{ name: 'clipboard', groups: ['clipboard'] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document', groups: ['mode', 'document', 'doctools'] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
		{ name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
		{ name: 'styles' },
		{ name: 'colors' },
	];

	// The toolbar groups arrangement, optimized for two toolbar rows.
	/* 	config.toolbarGroups = [
			// { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ]},
			{ name: 'document',	   groups: [ 'mode' ]},
			{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
			{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
			{ name: 'links' },
			{ name: 'insert' },
			{ name: 'forms' },
			{ name: 'tools' },
			{ name: 'others' },
			'/',
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
			{ name: 'styles' },
			{ name: 'colors' },
			// { name: 'about' }
		]; */


	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Subscript,Superscript';

	// Set the most common block elements.
	// config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';

};

// *Пытаюсь добавить свою кнопку
/* CKEDITOR.plugins.add('insertvar',
	{
		init(editor) {
			const style = new CKEDITOR.style({ element: 'var' });
			const styleCommand = new CKEDITOR.styleCommand(style);
			const command = editor.addCommand('insertvar', styleCommand);
			editor.attachStyleStateChange(style, state => {
				if (!editor.readOnly)
					command.setState(state);
			});

			editor.ui.addButton('InsertVar', {
				label: 'Insert Variable',
				icon: 'about',
				command: 'insertvar',
				toolbar: 'insert,99'
			});
		}
	}); */