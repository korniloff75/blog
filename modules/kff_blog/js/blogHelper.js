'use strict';
var BH = {

}

// ?
var U = U || window.UIkit&&UIkit.util;

// *Отсылаем на сервер имя и значение элемента перед каждой кнопкой
$('.content').on('click', 'button:not([id])', $e=>{
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

// *.delArticle
$('.content').on('click', '.delArticle', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var
		out={},
		$t = $($e.target).parent();

	console.log($t);
});


$('.content').on('click', '#save_sts', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var
		out={},
		err=[],
		$t = $($e.target).parent();

	$('.listArticles').each((ind,i)=>{
		var $i=$(i);
		out[$i.data('cat')]= [];
		$i.find('[data-id]').each((ind,i)=>{
			var id = i.getAttribute('data-id');
			if(out[$i.data('cat')].includes(id)) {
				err.push('Элемент ' + id + ' не может дублироваться в одной категории!');
			}
			out[$i.data('cat')].push(id);
		})
	})

	if(err.length) {
		alert(err.join("\n\n"));
		location.reload();
		// setTimeout(()=>{location.reload()}, 2000);
	} else {
		kff.request('',{name:'setCategories', value: JSON.stringify(out)},['.content','.log']);
	}

	console.log(out);
});

/* $(()=>{

}); */

// *Test
// kff.request('',null,'.log');