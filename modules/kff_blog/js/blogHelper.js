'use strict';
var BH = {

}

// ?
var U = U || UIkit.util;

// *Отсылаем на сервер имя и значение элемента перед каждой кнопкой
$('.content').on('click', 'button', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var $t = $($e.target).prev(),
		$inners = $t.find('input'),
		data = {opts:{}};

	// *Если несколько input
	if($inners.length) {
		$inners.each((ind,i)=>{
			i.type === 'hidden'?
				data.opts[i.name] = i.value
				: (
					data.name = i.name,
					data.value = i.value
				)
		})
	} else {
		data.name= $t.prop('name');
		data.value= $t.prop('value');
	}
	console.log($t);

	kff.request('',data,['.content','.log']);
});


$('.content').on('click', 'input[type=button]', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var $t = $($e.target).prev();

	console.log($t);
})

/* $(()=>{

}); */

// *Test
// kff.request('',null,'.log');