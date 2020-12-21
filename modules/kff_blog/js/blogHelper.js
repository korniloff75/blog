'use strict';
var BH = window.BH || {
	inited: false,
	contentSelector: '.content',
	navSelector: 'aside ul.uk-nav',
	navActiveClass: 'active',
	get content$obj(){
		return $(this.contentSelector);
	},
	getSidebar: function(){
		return document.querySelector('aside');
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
	getCategoryList: function(catId, catName, e){
		// UIkit.modal.alert('OPA!');
		e.preventDefault();
		e.stopPropagation();

		kff.request('',{
			name: 'getCategoryList',
			value: catId,
			opts: null,
		})
		.then(response=>{
			var dfr= document.createElement('html');
			dfr.innerHTML= response;

			// console.log({dfr}, U.$('body', dfr));
			UIkit.modal.dialog(
				'<h3 class="uk-text-center">' + catName + '</h3>'
				+ U.$('body', dfr).innerHTML
			);

		});
	},

	// *Список по #тэгу
	getHashList: function(hashtag, e){
		// UIkit.modal.alert('OPA!');
		e.preventDefault();
		e.stopPropagation();

		kff.request('',{
			name: 'getHashList',
			value: hashtag,
			opts: null,
		})
		.then(response=>{
			var dfr= document.createElement('html');
			dfr.innerHTML= response;
			// console.log(response);
			UIkit.modal.dialog(
				'<h3 class="uk-text-center">#' + hashtag + '</h3>'
				+ U.$('body', dfr).innerHTML
			);

		});
	},

	// *Предыдущая / Следующая
	getSiblingArticle: function(_step, e){
		// UIkit.modal.alert('OPA!');
		e.preventDefault();
		e.stopPropagation();

		var items= U.$$('.uk-nav a[data-ind]', this.getSidebar());

		items= items.sort((a,b)=>+U.attr(a, 'data-ind') - +U.attr(b, 'data-ind'));

		var active= items.find(i=>i.classList.contains(this.navActiveClass));

		var siblInd = parseInt(active.blockIndex) + _step,
			sibling= items[siblInd % items.length] || items[items.length-1];

		// console.log('items=', items, items.map(i=>U.attr(i,'data-ind')), 'sibling.href= '+ sibling.href + location.search, active, 'active.href= '+active.href, 'active.blockIndex= '+active.blockIndex, 'siblInd= '+siblInd);

		location.href= sibling.href + location.search;
	},
} //BH


// console.log('BH.inited', BH.inited);

// *Не обновляется при AJAX
// BH.inited || kff.checkLib('UIkit', '/modules/kff_basic/modules/kff_uikit-3.5.5/js/uikit.min.js').then(UIkit=>{
BH.inited || $(()=>{
	window.U = window.U || window.UIkit && UIkit.util;

	// *Copy from CODE
	U.on(BH.contentSelector, 'click', 'code', $e=>{
		var t= $e.current;
		// console.log(U.closest(t, 'pre'), t);
		if(U.closest(t, 'pre')) return;

		U.css(t,{cursor:'pointer'});
		var r = document.createRange();
		r.selectNode(t);
		document.getSelection().addRange(r);
		document.execCommand('copy');
		UIkit.notification('Текст скопирован в буфер обмена','success');
	});

	Object.defineProperty(BH, 'inited', {value: true});
	// console.log('BH.inited', BH.inited);
});



// *Test
// kff.request('',null,'.log');