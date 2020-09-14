'use strict';
var BH = {

}

// ?
var U = U || UIkit.util;

// *Отсылаем на сервер имя и значение элемента перед каждой кнопкой
$('.content').on('click', 'button', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var $t = $($e.target).prev();
	console.log($t);
	kff.request('',{
		name: $t.prop('name'),
		value: $t.prop('value')
	},['.content','.log']);
});

/* $(()=>{

}); */

// *Test
// kff.request('',null,'.log');