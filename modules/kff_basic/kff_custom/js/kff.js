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
	highlight: function highlight(selector) {
		if(!`1`) return;
		selector= selector||'.log';
		if(selector instanceof NodeList) {
			// todo
		}
		var nodes= document.querySelectorAll(selector);

		if(!nodes.length) return;

		var styleBox = document.createElement('style');

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

		[].forEach.call(nodes, node=>{
			var storage = {
				'i_block': [],
				'strings': [],
				'comments': [],
			},
			safe = { '<': '<', '>': '>', '&': '&' };

		document.querySelector('head').appendChild(styleBox);

		// node.innerHTML = node.textContent
		node.innerHTML = node.innerHTML
		// *Маскируем HTML
		.replace(/[<>&]/g, function (m){
			return safe[m];
		})
		// *Убираем блоки [...]
		.replace(/^\s*\[.+?\]/gm, function(m){
			m= m.replace(/:(\d+)/, ':<span class="kwrd kwrd_2">$1</span>')
			.replace(/(INFO|WARNING|ERROR)/, '<span class="$1">$1</span>');

			storage.i_block.push(m); return '~~~i_block'+(storage.i_block.length-1)+'~~~';
		})
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
		});

		// console.log('storage=',storage);
	},


	/**
	 * Обёртка для аякс-запроса
	 * @param uri
	 * @param {Object} data
	 * @param {Array} sel - заменяем контент узлов из sel
	 * @returns Promise
	 */
	request: function(uri, data, sel) {
		sel = sel || ['.content','.log'];
		return $.post(uri, data)
		.then(response=>{
			sel.forEach(i=>{
				var node= document.querySelector(i);
				node.innerHTML= $(response).find(i).html();
			})
		})
	}
}

