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
	editRequest: function(opts, e) {
		console.log(location, kff.URI, BH.pageInfo);
		// return;

		opts= Object.assign({
			cat: BH.pageInfo.category,
			art: BH.pageInfo.article,
			artOpts: {}
		}, opts || {});

		$('#artOpts').find('input,textarea,select').each((ind,i)=>{
			opts.artOpts[i.name]= i.value;
		});

		return kff.request('',{
			act: 'save',
			name: 'saveEdit',
			value: CKEDITOR.instances.editor1.getData(),
			opts: JSON.stringify(opts),
		}, ['.blog_content','.log']);
	}
}

// ?
var U = U || window.UIkit&&UIkit.util;


// *Events
// *Удаление категории
$('.content').on('click', '.delCat', $e=>{
	var $t= $($e.target);
	UIkit.modal.confirm("Подтверждаете удаление категории " + $t.data('del') + '?')
	.then(success=>{
		kff.request('',{
			name: 'removeCategory',
			value: $t.data('del'),
		},['.content','.log']);
	});
});


// *.delArticle
// todo ...
$('.content').on('click', '.delArticle', $e=>{
	var $t= $($e.target);

	$e.stopPropagation();

	UIkit.modal.confirm("Подтверждаете удаление статьи " + $t.data('del') + '?')
	.then(success=>{
		kff.request('',{
			name: 'removeArticle',
			value: $t.data('del'),
		},['.content','.log']);
	});

	console.log($t);
});


// *Создание новых категорий и статей
$('.content').on('click', 'button.addNew', $e=>{
	var $t = $($e.target).prev(),
		$inners = $t.find('input'),
		data = {opts:{}};

	console.log($t);

	if(!$t[0]) return;

	$e.stopPropagation();
	$e.preventDefault();

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


// *Сохраняем сортировку страниц
$('.content').on('click', '#save_sts', $e=>{
	var
		out={},
		err=[],
		$t = $($e.target).parent(),
		$list= $('.listArticles');

	if(!$list.length) return;

	$e.stopPropagation();
	$e.preventDefault();

	$list.each((ind,i)=>{
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
		UIkit.modal.alert(err.join("\n\n"));
		location.reload();
		// setTimeout(()=>{location.reload()}, 2000);
	} else {
		kff.request('',{name:'sortCategories', value: JSON.stringify(out)},['.content','.log']);
	}

	console.log(out);
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