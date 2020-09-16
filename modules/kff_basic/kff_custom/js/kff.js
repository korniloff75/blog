'use strict';
// *polifills
Object.assign || Object.defineProperty(Object, 'assign', {
	enumerable: false,
	configurable: true,
	writable: true,
	value: function (target, firstSource) {
		if (target === undefined || target === null) { throw new TypeError('Cannot convert first argument to object'); }
		var to = Object(target);
		for (var i = 1, L = arguments.length; i < L; i++) {
			Object.keys(arguments[i]).forEach(function(p) {
				to[p] = arguments[i][p];
			})
		}
		return to;
	}
});

if (![].includes) {
	Array.prototype.includes = String.prototype.includes = function (searchElement, fromIndex) {
		fromIndex = fromIndex || 0;
		return this.indexOf(searchElement, fromIndex) > -1;
	}
}

if (!NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

// closest && fix matches
;(function(EL) {
	if(EL.closest) return;

	EL.matches = EL.matches || EL.mozMatchesSelector || EL.msMatchesSelector || EL.oMatchesSelector || EL.webkitMatchesSelector;

	EL.closest = function closest(selector) {
		if (!this) return null;
		if (this.matches(selector)) return this;
		if (!this.parentElement) {return null}
		else return this.parentElement.closest(selector)
	};

	EL.getBoundingClientRect || Object.defineProperty(EL, 'getBoundingClientRect', {
		value: function () {
			var top = 0,
				left = 0,
				elem = this;
			while (elem) {
				top += parseInt(elem.offsetTop);
				left += parseInt(elem.offsetLeft);
				elem = elem.offsetParent;
			}
			return { top: top, left: left }
		},
		writable: 1
	});
}(Element.prototype));

Object.setPrototypeOf = Object.setPrototypeOf || function (obj, proto) {
	!/MSIE [6-9]/.test(navigator.appVersion) ? (obj.__proto__ = proto) : _K.clonePpts(obj, proto, { enum: 1 });
	return obj;
};

Object.getPrototypeOf = Object.getPrototypeOf || function (obj) { return obj.__proto__ };


Object.values = Object.values || function(obj) {
	var
		allowedTypes = ["[object String]", "[object Object]", "[object Array]", "[object Function]"],
		objType = Object.prototype.toString.call(obj);

	if(obj === null || typeof obj === "undefined") {
		throw new TypeError("Cannot convert undefined or null to object");
	} else if(allowedTypes.includes(objType)) { // allowedTypes.indexOf(objType) >= 0
		return [];
	} else {
		return Object.keys(obj).map(function (key) {
			return obj[key];
		});
	}
};

Function.prototype.bind = Function.prototype.bind || function (oThis) {
	if (typeof this !== 'function') {
		// ближайший аналог внутренней функции
		// IsCallable в ECMAScript 5
		throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
	}
	var aArgs = [].slice.call(arguments, 1),
		fToBind = this,
		fNOP = function () { },
		fBound = function () {
			return fToBind.apply(this instanceof fNOP && oThis ? this : oThis, aArgs.concat([].slice.call(arguments)));
		};
	fNOP.prototype = this.prototype;
	fBound.prototype = new fNOP();
	return fBound;
};

document.documentElement.hidden !== undefined || Object.assign(HTMLElement.prototype, {
	get hidden() { return this.style && this.style.display === 'none' },
	set hidden(a) {
		this.style && (this.style.display = !!a ? 'none' : '');
	}
});

String.prototype.trim = String.prototype.trim || function () { return this.replace(/^\s+|\s+$/gm, '') };
String.prototype.ltrim = String.prototype.ltrim || function () { return this.replace(/^\s+/gm, '') };
String.prototype.tabTrim = function () { return this.replace(/\t/gm, '  ') };
String.prototype.rtrim = String.prototype.rtrim || function () { return this.replace(/\s+$/gm, '') };
String.prototype.fulltrim = String.prototype.fulltrim || function () { return this.replace(/((^|\n)\s+|\s+($|\n))/gm, '').replace(/\s+/gm, ' '); };

Math.sign = Math.sign || function (x) {
	x = +x;
	return (x === 0 || isNaN(x)) ? x : x > 0 ? 1 : -1;
}


// *kff ===
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
		.replace(/([^\\\w])((?:'(?:\\'|[^'])*?')|(?:"(?:\\"|[^"])*?"))/g, function(m, f, s){
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


	get URI(){
		return location.pathname.split('/');
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
			});
			sel.includes('.log') && this.highlight('.log');
		})
	}
}

