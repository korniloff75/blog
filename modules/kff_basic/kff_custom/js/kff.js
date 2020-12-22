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

if (!Array.prototype.find) {
	Object.defineProperty(Array.prototype, 'find', {
		value: function(predicate) {
			var o = Object(this);
			var len = o.length >>> 0;
			if (typeof predicate !== 'function') {
				throw new TypeError('predicate must be a function');
			}

			var thisArg = arguments[1];
			var k = 0;

			while (k < len) {
				var kValue = o[k];
				if (predicate.call(thisArg, kValue, k, o)) {
					return kValue;
				}
				k++;
			}

			return undefined;
		},
		configurable: true,
		writable: true
	});
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
String.prototype.tabTrim = function () { return this.replace(/\t/gm, '  ') };

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
			// console.info('name= ' + name, `script[src*=${name}]`, document.querySelector(`script[src*=${name.toLowerCase()}]`));

			if(window[name] !== undefined) {
				return resolve(window[name]);
			}

			var $_= document.querySelector(`script[src*=${name.toLowerCase()}]`);

			if(!$_){
				$_= document.createElement('script');
				$_.src= src || 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js';

				console.info(name + ' отсутствует! Загружаем из ' + $_.src);
				// $_.async= false;
				document.head.append($_);
			}

			$_.onload= ()=>resolve(window[name]);
			$_.onerror= ()=>reject(window[name]);

		});
	},

	/* getSidebar: function(){
		return document.querySelector('aside');
	}, */

	/**
	 * Базовая подсветка кодов
	 * @param {string} selector
	 */
	highlight: function highlight(selector) {
		if(!1) return;
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
		${selector} .strings{color:#f99 !important}
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

		if(!highlight.inited) document.querySelector('head').appendChild(styleBox);
		highlight.inited= 1;

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

	getURI: function(uri){
		return uri? uri.split('/'): this.URI;
	},


	/**
	 * Конструктор AJAX меню
	 * @param {jQ || NodeElement} $nav - навигация
	 * @param {string} mainSelector - селектор блока с динамическим контентом
	 * @param {Array} sels - optional селекторы с обновляемым контентом
	 */
	menu: function($nav, mainSelector, sels) {
		this.$nav = ($nav instanceof jQuery)? $nav: $($nav);

		if(this.$nav.menuInited){
			console.info('this.$nav.menuInited!');
		}

		var self = this,
			$loader = $('#loading');

		if(!$loader.length) {
			$loader = $('<div id="loading" uk-spinner class="uk-position-center uk-position-medium uk-position-fixed" style="z-index:1000; display:none;"></div>').appendTo(document.body);
		}

		this.mainSelector= mainSelector= this.getContentSelector(mainSelector);
		this.sels= sels || [];
		this.$loader= $loader;

		this.$nav.on('click', this.clickHahdler.bind(this));
		/* this.$nav.on({
			click: this.clickHahdler,
			// touch: self.clickHahdler,
		}); */

		// *AJAX history
		$(window).on('popstate', function($e) {
			var e= $e.originalEvent,
				state= e.state && e.state[mainSelector];

			if(!state) return false;

			// ?
			/* kff.request(state.href,null,state.sels)
			.then(r=>{
				document.title= state.title;
				self.setActive(state.href);
			}); */

			var html,
				$dfr= $(state.html);
			if(!$dfr.find('h1#title').length){
				// *Добавляем заголовки
				html= `<h1 id="title" hidden></h1><h1>${state.title}</h1><div>${state.html}</div>`;
			}
			else{
				html= state.html;
			}

			console.log('state.sels', state.sels, {$dfr});

			// !
			// console.log('render=', kff.render(state.sels, html));
			// kff.render(state.sels, html)
			kff.render([mainSelector, '#title'], html)
			.then(out=>{
				document.title= state.title;
				self.setActive(state.href);
				self.$loader.hide();
			}, err=>console.error(err));

		});

		this.$nav.menuInited= 1;
	},


	/**
	 * Обёртка для аякс-запроса
	 * @param {String} uri
	 * @param {Object} data
	 * @param {Array} sels - массив из селекторов
	 * @returns Promise
	 */
	request: function(uri, data, sels) {
		// sels = sels || ['.content','.log'];
		if(!sels){
			return $.post(uri, data)
		}
		else return $.post(uri, data)
		.then(response=>{
			return kff.render(sels,response);
		})
	},

	/**
	 *
	 * @param {Array} sels - заменяем контент узлов из sels
	 * @param {string HTML} response
	 * @returns Promise
	 */
	render: function(sels,response) {
		var stop= 0;

		return kff.checkLib('UIkit', '/modules/kff_basic/modules/kff_uikit-3.5.5/js/uikit.min.js')
		.then(UIkit=>{

			// var $dfr= $(document.createDocumentFragment()),
			var $dfr= $('<div/>'),
				// out = {$dfr: $dfr};
				out = {};

			// console.log({response});

			$dfr.html(response);

			sels.forEach(i=>{
				if(stop) return;
				var targetNode= document.querySelector(i);

				if(!targetNode){
					i= 'main';
					targetNode= document.querySelector(i);
					stop= 1;
				};

				var $sourceNode = $dfr.find(i);

				if(!$sourceNode.length)
					$sourceNode = $dfr;
				else{
					targetNode.className= $sourceNode[0].className;
				}

				// console.log({i, targetNode, $sourceNode}, $sourceNode.html(), '\n');

				var newContent= $sourceNode.html();

				// if($sourceNode[0].classList && $sourceNode[0].classList.length > targetNode.classList.length) targetNode.classList= $sourceNode[0].classList;

				out[i]= $(targetNode).html(newContent).html();
				// $(targetNode).html(newContent).html();
				// out[i]= targetNode.innerHTML;
				// out[i]= targetNode.innerHTML= newContent;

			}); //forEach

			window.scroll(0,0);

			// *Подсвечиваем лог
			sels.includes('.log') && this.highlight('.log');
			// *title
			// sels.includes('h1#title') && (document.title= $sourceNode);
			return out;
		});


		// return response;
	}
}

// *Расширяем конструкторы

// *Клик по меню
kff.menu.prototype.clickHahdler = function ($e) {
	var t= $e.target.closest('a'),
		self= this;

	// console.log({t});

	if(!t || !t.href || location.search.includes('?edit')) return;

	$e.preventDefault();
	$e.stopPropagation();

	if(t.href === location.href+'#') return;

	console.log({t});

	// console.log('t.href=', t.href, location.href+'#', t.href === location.href+'#');
	var mainSelector= this.mainSelector;
	this.currentItem= t;
	// $= this.mainSelector;
	console.log('this.mainSelector=', this.mainSelector);
	this.$loader.show();
	this.setActive(t.href);

	// Собираем селекторы для обновления
	if(!this.sels.includes(mainSelector)) this.sels.push(mainSelector);
	var sels= this.sels.concat(['h1#title','.core.info','.log','#wrapEntries']);

	var req= kff.request(t.href,null,sels)
	.then(pr=>{
		if(pr instanceof Promise){
			pr.then(r=>{
				console.info('pr is Promise', {r});
				return r;
			});
			pr.then(this.handleResponse);
		}
		else{
			// console.info({pr});
			this.handleResponse(pr);
		}

		console.info({pr});
	}, err=>console.log(err));

	console.log({req});
	// req.always(r=>console.log({r}));

	return false;
}

// *Active btn
kff.menu.prototype.setActive = function setActive (href) {
// kff.menu.setActive = function setActive (href) {
	if(!this.$nav.length) return;

	this.$nav.find('.active').removeClass();
	this.$nav.find('a').filter((ind,i)=> i.href === href).addClass('active');
	// console.log($nav.css('height'));

}


/**
 * Обработка ответа
 * @param {Object} r
 */
kff.menu.prototype.handleResponse= function (r) {
	var t= this.currentItem,
		state={},
		self= this,
		mainSelector= this.mainSelector;

	state[mainSelector]= {
		href: t.href,
		title: $('h1#title, h1').html(),
		sels: mainSelector,
		html: r[mainSelector]
	};
	console.info({state});

	history.pushState(state, '', t.href);
	this.$loader.hide();

	if(state[mainSelector].title)
		document.title= state[mainSelector].title;

	// *Close uk-dropdown
	if(t.closest('.uk-dropdown')){
		console.log('uk-dropdown',t.closest('.uk-open') );
		var open= $e.target.closest('.uk-open');
		open && UIkit.dropdown(open).hide();
	}

	// *After click
	if(typeof this.after === 'function'){
		this.after();
	}
}

Object.defineProperties(kff.menu.prototype, {
	getContentSelector: {
		value: function(mainSelector){
			return U.$(mainSelector)? mainSelector: 'main'
		}
	}
});