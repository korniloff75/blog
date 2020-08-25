'use strict';

var kff = {
	/**
	 * *Асинхронная подгрузка библиотек
	 * @param {string} name - global name of var
	 * @param {string} src - path to lib
	 */
	checkLib: function (name, src) {
		return new Promise((resolve, reject) => {
			if(typeof window[name] !== 'undefined')
				resolve(window[name]);

			console.info(name + ' отсутствует!');

			var $_= document.createElement('script');
			$_.src= src || 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js';
			// $_.async= false;
			document.head.append($_);
			$_.onload= ()=>resolve(window[name]);
		});
	}
}

