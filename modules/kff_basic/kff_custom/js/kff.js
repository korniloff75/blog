'use strict';

var kff = {
	/**
	 * *Асинхронная подгрузка библиотек
	 * @param {string} name - global name of var
	 * @param {string} src - path to lib
	 */
	checkLib: function (name, src) {
		return new Promise((resolve, reject) => {
			if(typeof window[name] !== 'undefined') {
				return resolve(window[name]);
			}

			var $_= document.createElement('script');
			$_.src= src || 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js';

			console.info(name + ' отсутствует! Загружаем из ' + $_.src);
			// $_.async= false;
			document.head.append($_);
			$_.onload= ()=>resolve(window[name]);
		});
	},

	/**
	 * Базовая подсветка кодов
	 * @param {string} selector
	 */
	highlight: function (selector) {
		if(!`1`) return;
		selector= selector||'.log';
		if(selector instanceof NodeList) {
			// todo
		}
		var node= document.querySelector(selector);

		if(!node) return;

		var styleBox = document.createElement('style'),
			storage = {
				'i_block': [],
				'strings': [],
				'comments': [],
			},
			safe = { '<': '<', '>': '>', '&': '&' };

		styleBox.textContent = `
		${selector} {
			white-space: pre-wrap;
			padding: 1em;
			font: 1em consolas !important;
		}
		${selector} .strings{color:#f99 !important}/* Строки красные */
		${selector} .func{color:#77a !important}
		${selector} .kwrd{font-weight:bold !important}
		${selector} .kwrd_2{ color:#99f;}
		${selector} .i_block{color:yellow}
		${selector} .INFO{color:green;}
		${selector} .WARNING{color:orange;}
		${selector} .ERROR{color:red;}
		`;

		document.querySelector('head').appendChild(styleBox);

		node.innerHTML = node.textContent
		// *Маскируем HTML
		.replace(/[<>&]/g, function (m){
			return safe[m];
		})
		// *Убираем блоки [...]
		.replace(/^\s*\[.+?\]/gm, function(m){
			m= m.replace(/:(\d+)/, ':<span class="kwrd kwrd_2">$1</span>')
			.replace(/(INFO|WARNING|ERROR)/, '<span class="$1">$1</span>');

			storage.i_block.push(m); return '~~~i_block'+(storage.i_block.length-1)+'~~~';})
		// *Убираем строки
		.replace(/([^\\])((?:'(?:\\'|[^'])*')|(?:"(?:\\"|[^"])*"))/g, function(m, f, s){
			storage.strings.push(s); return f+'~~~strings'+(storage.strings.length-1)+'~~~';
		})
		// *Убираем комменты
		.replace(/([^\\])(?:\/\/|\#)[^\n]*$|\/\*[\s\S]*?\*\//gm, function(m, f){
			storage.comments.push(m); return f+'~~~comments'+(storage.comments.length-1)+'~~~';
		})

		// *Выделяем ключевые слова
		.replace(/\b(var|function|typeof|throw|new\s+.+?|return|if|for|in|while|break|do|continue|switch|case)\b([^a-z0-9\$_])/gi, '<span class="kwrd">$1</span>$2')
		// *Выделяем ключевые слова 2 тип
		.replace(/(\w+?\:\:|\b\w+?\s*=)/g, '<span class="kwrd_2">$1</span>')
		// *Выделяем скобки
		.replace(/(\{|\}|\]|\[|\|)/gi, '<span class="gly">$1</span>')
		// *Выделяем имена функций
		.replace(/([a-z\_\$][a-z0-9_]*)\s*?\(/gi, '<span class="func">$1</span>(')
		// *Возвращаем на место
		.replace(/~~~(i_block|strings|comments)(\d+?)~~~/g, function(m, t, i){
			return '<span class="'+t+'">'+storage[t][i]+'</span>'; })
		// Выставляем переводы строк
		.replace(/([\n])+/g, '$1<br>')
		// Табуляцию заменяем неразрывными пробелами
		.replace(/\t/g, '&nbsp;&nbsp;');

		// console.log('storage=',storage);
	}
}

