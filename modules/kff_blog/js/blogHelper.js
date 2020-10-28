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
			// console.log(response);
			UIkit.modal.dialog(
				'<h3 class="uk-text-center">' + catName + '</h3>'
				+ response
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
			// console.log(response);
			UIkit.modal.dialog(
				'<h3 class="uk-text-center">#' + hashtag + '</h3>'
				+ response
			);

		});
	}
}

// ?
var U = window.U || window.UIkit && UIkit.util;

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
})

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