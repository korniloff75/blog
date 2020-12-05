'use strict';

window.U = window.U || window.UIkit && UIkit.util;

// *Создание новых категорий и статей
BH.content$obj.on('click', 'button.addCategory, button.addArticle', $e => {
	var $t = $($e.target).prev(),
		$inners = $t.find('input'),
		data = { opts: {} };

	console.log($t);

	if (!$t[0]) return;

	$e.stopPropagation();
	$e.preventDefault();

	// *Если несколько input
	if ($inners.length) {
		$inners.each((ind, i) => {
			i.type === 'hidden'
			? data.opts[i.name] = i.value
			: (
					data.name = i.name,
					data.value = i.value
				)
		})
	} else {
		data.name = $t.prop('name');
		data.value = $t.prop('value');
	}
	console.log($t);

	if (!data.value.trim()) {
		UIkit.notification("Заполните название элемента!", 'warning');
	}
	else kff.request('', data, [BH.contentSelector, BH.logSelector])
		.then(r => {
			console.log(r);
			UIkit.notification("Новый элемент успешно добавлен", 'success');
		});
});


// *Events
// *Сохраняем сортировку страниц
U.on(BH.content$obj, 'click', '#save_sts', $e => {
	var
		out = {},
		err = [],
		list = U.$$('.listArticles');

	if (!list.length) return;

	$e.stopPropagation();
	$e.preventDefault();

	// console.log('$list=',$list);

	list.forEach((i, catInd) => {
		var catId = U.data(i, 'id');

		out[catId] = [];

		U.$$('[data-artData]', i).forEach((i, ind) => {
			var
				data = JSON.parse(U.data(i, 'artData'));

			if (out[catId].some(
				function (i) { return i.id === data.id }
			)) {
				err.push('Элемент не может дублироваться в одной категории!');
			}

			data.ind = [catInd, ind];
			out[catId].push(data);

			/* if(title.trim()){
				out[catId][ind].title= title;
			} */
			// console.log('data= ', data);
		})
	});

	if (err.length) {
		UIkit.modal.alert(err.join("\n\n"))
			.then(location.reload.bind(location));

		// setTimeout(()=>{location.reload()}, 2000);
	} else {
		var request = kff.request('', { name: 'sortCategories', value: out }, ['.switcher-item.order', '.log'])
			.then(r => {
				U.$('.switcher-item.order').classList.add('uk-active');
				// console.log('\nresponse= ', r);
				UIkit.notification("<span uk-icon='icon: check; ratio:1.5;'></span> Порядок элементов успешно сохранён", 'success');
			}, err => console.error(err));
	}

	console.log(out, '\nrequest= ', request);
});


// *Удаление категории
U.on(BH.content$obj, 'click', '.removeCategory', $e => {
	var id = U.data($e.current, 'del');

	$e.stopPropagation();

	console.log($e, id, $e.current);

	UIkit.modal.confirm("Подтверждаете удаление категории " + id + '?', { bgClose: 1 })
		.then(success => kff.request('', {
			name: 'removeCategory',
			value: id,
		}, [BH.contentSelector, BH.logSelector])
		).then(() => {
			UIkit.notification("Категория " + id + " успешно удалена", 'success');
		});
});


// *.delArticle
U.on(BH.content$obj, 'click', '.delArticle', $e => {
	var id = U.data($e.current, 'del');

	$e.stopPropagation();

	console.log($e, id, $e.current);
	// return;
	UIkit.modal.confirm("Подтверждаете удаление статьи " + id + '?', { bgClose: 1 })
		.then(success => {
			kff.request('', {
				name: 'removeArticle',
				value: id,
			}, [BH.contentSelector, BH.logSelector]);
		}).then(() => {
			UIkit.notification("Статья " + id + " успешно удалена", 'success');
		});

});


kff.curSel = '.switcher-item.sts';

// *Добавляем настройку
U.on('#addSetting', 'click', e => {
	var node = U.$(kff.curSel + ' li'),
		newNode = node.cloneNode(true);

	U.remove(U.find('span', newNode));
	var name = U.prepend(newNode, '<input type="text" class="uk-width-1-3@s uk-display-inline-block" placeholder="name">'),
		stsVal = name.nextElementSibling;

	stsVal.name = stsVal.value = '';

	console.log(name);

	U.on(name, 'blur', e => {
		e.target.nextElementSibling.name = name.value;
		// console.log(e.target.nextElementSibling.name, e.target.nextElementSibling);
	})

	U.after(node, newNode);
});


// *Сохраняем настройки
U.on('#saveSettings', 'click', e => {
	var data = {},
		err = [];

	U.$$(kff.curSel + ' li').forEach(i => {
		var el = i.querySelector('input.stsVal');
		// console.log(el.name, el);
		if (!el.name) err.push('Не заполнены обязательные поля');
		data[el.name] = el.value;
	});

	if (err.length)
		err.forEach(i => UIkit.notification(i, 'warning'));
	else kff.request('', {
		name: 'saveSts',
		value: data,
	}, [kff.curSel]);
});