/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.uiColor = '#AADC6E';
	config.language = 'ru';
	config.allowedContent = true;
	config.protectedSource.push(/<(script)[^>]*>.*<\/\1>/ig);
	config.protectedSource.push(/<\?[\s\S]*?\?>/g);// разрешить php-код
};