'use strict';
var BH = {

}

var U = U || UIkit.util;

$('.content').on('click', 'button', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var $t = $($e.target).prev();
	console.log($t);
	console.log(kff.request('',{
		name: $t.prop('name'),
		value: $t.prop('value')
	},['.content','.log']));
});

$(()=>{

});

// *Test
// kff.request('',null,'.log');