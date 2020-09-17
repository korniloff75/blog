'use strict';
var BH = {
	get pageInfo(){
		var uri= kff.URI;
		return {
			category: uri[2],
			article: uri[3],
		}
	},
	// *Save edit article
	editRequest: function(selector, e) {
		console.log(location, kff.URI, BH.pageInfo);
		// return;
		return kff.request('',{
			act: 'save',
			name: 'saveEdit',
			value: CKEDITOR.instances.editor1.getData(),
			opts: JSON.stringify({
				cat: BH.pageInfo.category,
				art: BH.pageInfo.article,
			}),
		}, ['.blog_content','.log']);
	}
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
// todo ...
$('.content').on('click', '.delArticle', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var
		out={},
		$t = $($e.target).parent();

	console.log($t);
});


// *Сохраняем сортировку страниц
$('.content').on('click', '#save_sts', $e=>{
	$e.stopPropagation();
	$e.preventDefault();
	var
		out={},
		err=[],
		$t = $($e.target).parent();

	$('.listArticles').each((ind,i)=>{
		var $i=$(i);
		out[$i.data('id')]= [];
		$i.find('[data-id]').each((ind,i)=>{
			var
				id = i.getAttribute('data-id'),
				name = i.getAttribute('data-name'),
				oldCatId = i.getAttribute('data-oldCatId');

			if(out[$i.data('id')].filter(function(i){return i.id === id}).length) {
				err.push('Элемент ' + id + ' не может дублироваться в одной категории!');
			}
			out[$i.data('id')].push({
				id: id, name: name, oldCatId: oldCatId
			});
			// out[$i.data('id')].push(id);
		})
	})

	if(err.length) {
		alert(err.join("\n\n"));
		location.reload();
		// setTimeout(()=>{location.reload()}, 2000);
	} else {
		kff.request('',{name:'sortCategories', value: JSON.stringify(out)},['.content','.log']);
	}

	console.log(out);
});


// *AJAX nav
$(()=>{
	var targetSel = '.blog_content',
		bm = new kff.menu($('article #categories'), targetSel);

	console.log(bm);

	// *AJAX history
	window.onpopstate = function(e) {
		if(!e.state || !e.state[targetSel]) return false;

		// console.log('e=',e);
		kff.render([targetSel], e.state[targetSel].html);
		bm.setActive(e.state[targetSel].href);
	}
});





// *AJAX nav old
/* $(()=>{
	$('article #categories').on('click', 'a', $e=>{
		var link = $e.target,
			href = link.href;

		if(!href || href === '#') return;

			$e.stopPropagation();
			$e.preventDefault();

			console.log(link);
			kff.request(href,null,['.blog_content','.log'])
			.then((response)=>{
				// console.log(response);
				UIkit.dropdown($e.target.closest('.uk-open')).hide();
			});
	})
}) */



// *Test
// kff.request('',null,'.log');