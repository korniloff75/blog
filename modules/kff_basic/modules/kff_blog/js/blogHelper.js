'use strict';
var BH = {
	contentSelector: '.content',
	get content$obj(){
		return $(this.contentSelector);
	},
	logSelector: '.log',
	get pageInfo(){
		var uri= kff.URI;
		return {
			category: uri[2],
			article: uri[3],
		}
	},

	// *Save edit article
	editRequest: function(artData, e) {
		var opts= {
			cat: BH.pageInfo.category,
			art: BH.pageInfo.article,
			artOpts: artData
		};

		$('#artOpts').find('input,textarea,select').each((ind,i)=>{
			opts.artOpts[i.name]= i.value;
		});

		console.log(kff.URI, opts);

		return kff.request('',{
			act: 'save',
			name: 'saveEdit',
			value: CKEDITOR.instances.editor1.getData().trim(),
			opts: JSON.stringify(opts),
		}, ['.blog_content','.log']
		).then(()=>{
			// UIkit.modal.alert( "Статья успешно отредактирована");
			UIkit.notification("<span uk-icon='icon: check'></span> Статья успешно отредактирована", 'success')
		});
	},

	// *Список статей в категории
	getCategoryList: function(catId, catName){
		// UIkit.modal.alert('OPA!');
		kff.request('',{
			name: 'getCategoryList',
			value: catId,
			opts: null,
		})
		.then(response=>{
			// console.log(response);
			UIkit.modal.dialog(
				'<h3 class="uk-text-center">' + catName + '</h3>'
				+ response
			);

		});
	}
}

// ?
var U = U || window.UIkit && UIkit.util;


// *Events
// *Удаление категории
BH.content$obj.on('click', '.removeCategory', $e=>{
	var $t= $($e.currentTarget);
	$e.stopPropagation();

	console.log($t, $t.data('del'));

	UIkit.modal.confirm("Подтверждаете удаление категории " + $t.data('del') + '?',{bgClose:1})
	.then(success=> kff.request('',{
			name: 'removeCategory',
			value: $t.data('del'),
		},[BH.contentSelector,BH.logSelector])
	).then(()=>{
		UIkit.notification( "Категория "+ $t.data('del') + " успешно удалена",'success');
	});
});


// *.delArticle
BH.content$obj.on('click', '.delArticle', $e=>{
	var $t= $($e.currentTarget);

	$e.stopPropagation();

	console.log($t, $t.data('del'));
	// return;
	UIkit.modal.confirm("Подтверждаете удаление статьи " + $t.data('del') + '?',{bgClose:1})
	.then(success=>{
		kff.request('',{
			name: 'removeArticle',
			value: $t.data('del'),
		},[BH.contentSelector,BH.logSelector]);
	}).then(()=>{
		UIkit.notification( "Статья "+ $t.data('del') + " успешно удалена",'success');
	});

});


// *Создание новых категорий и статей
BH.content$obj.on('click', 'button.addCategory, button.addArticle', $e=>{
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

	if(!data.value.trim()){
		UIkit.notification( "Заполните название элемента!",'warning');
	}
	else kff.request('',data,[BH.contentSelector,BH.logSelector])
	.then(()=>{
		UIkit.notification( "Новый элемент успешно добавлен",'success');
	});
});


// *Сохраняем сортировку страниц
BH.content$obj.on('click', '#save_sts', $e=>{
	var
		out={},
		err=[],
		$t = $($e.target).parent(),
		$list= $('.listArticles');

	if(!$list.length) return;

	$e.stopPropagation();
	$e.preventDefault();

	// console.log('$list=',$list);

	$list.each((catInd,i)=>{
		var $i=$(i);
		out[$i.data('id')]= [];
		$i.find('[data-artData]').each((ind,i)=>{
			var
				data= JSON.parse(i.getAttribute('data-artData'));

			if(out[$i.data('id')].filter(
				function(i){return i.id === data.id}
			).length) {
				err.push('Элемент ' + id + ' не может дублироваться в одной категории!');
			}
			/* out[$i.data('id')].push({
				id: id, ind:[catInd,ind], name: name, oldCatId: oldCatId, tag: i.getAttribute('data-tag'),
			}); */
			data.ind= [catInd,ind];
			out[$i.data('id')].push(data);

			/* if(title.trim()){
				out[$i.data('id')][ind].title= title;
			} */
			// console.log('data= ', data);
		})
	})

	if(err.length) {
		UIkit.modal.alert(err.join("\n\n"))
		.then(location.reload);

		// setTimeout(()=>{location.reload()}, 2000);
	} else {
		kff.request('',{name:'sortCategories', value: out},['.switcher-item.order','.log'])
		.then(()=>{
			UIkit.notification( "<span uk-icon='icon: check; ratio:1.5;'></span> Порядок элементов успешно сохранён",'success');
		});
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