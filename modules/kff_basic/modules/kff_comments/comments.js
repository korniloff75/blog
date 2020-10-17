// comm_vars - settings in comments.php
'use strict';
var U= window.U || UIkit.util;

var commFns = {
	module: 'php/modules/comments/comments.php',
	$comments: $('section#comments'),
	refreshed: 0,


	refresh: function(data, sts) {
		if(this.refreshed) return Promise.resolve();

		sts = Object.assign( {
			handler: '',
			hash: '#',
			cb: null // callback
		}, sts || {});

		data = Object.assign( {
			act: 'comments',
		}, data || {});

		this.refreshed = 1;

		console.log('data= ', data);

		return kff.request(
			sts.handler,
			data,
			['#entries']
		)
		.then(
			out=>{
				console.log('out=', out);
				commFns.refreshed = 0;
				// commFns.$formEdit.hide();
			},
			err=>{
				console.info('err=', err);
			}
		);

		/* commFns.$comments.load(
			sts.handler,
			data,
			function(response) {
				location.replace(sts.hash);
				if(typeof sts.cb === 'function') sts.cb.call(null, response);
				commFns.refreshed = 1;
			}
		); */
	},


	Edit : {
		createForm : function (resp) {
			// commFns.$formEdit && commFns.$formEdit.remove();
			// commFns.$formEdit = $('<div/>', { id: "com_ed" }).appendTo("body");
			commFns.$formEdit= UIkit.modal.dialog(resp);
			console.log('commFns.$formEdit= ', commFns.$formEdit);
			// commFns.$formEdit.append (resp);
		},

		open : function(num) {
			U.ajax('', {
				method: 'POST',
				data: JSON.stringify({
					act: 'comments',
					name:'Edit_Comm',
					num: num
				})
			}).then(xhr=>{
				commFns.Edit.createForm(xhr.response);
			});
			return;
			/* $.post('', {
				act: 'comments',
				name:'Edit_Comm',
				num: num
			}).done(
				commFns.Edit.createForm
			); */
		},

		save : function () {
			var formData= [].reduce.call(document.forms["edit_comm"].querySelectorAll('input,select,textarea'), (a,c)=>{
				if(!c.name) return a;
				a[c.name]= c.value;
				return a;
			}, {});

			// console.log(formData);

			var ajaxData = {
				name: "Save_Edit_Comm",
				value: formData
			};

			console.log('!!! Save !!!\n');
			commFns.refresh(ajaxData)
			.then(()=>{
				console.log('!!! Saved !!!\n');
				commFns.$formEdit.hide();
			}, err=>{console.info(err);});
		},

		del : function(num) {
			if(confirm('Продолжить удаление комментария?'))
				commFns.refresh({
					s_method:'del', num: num
				});
		}

	}, // Edit


	// Считаем символы
	countChars: function(out,e) {
		var maxLen= comm_vars.MAX_LEN,
			count= maxLen - this.value.length;

		if (count < 1) {
			count=0;
			this.blur();
			this.value= this.value.substr(0,maxLen);
		}

		out.textContent= count;
	},


	Send: function ($form) {
		$form = $.check($form || this.form);
		$form = !!$form && $form[0].tagName === 'FORM' && $form;
		console.log("$form = ", $form);

		var ajaxData = $form.ajaxForm(),
			err='',
			TO=10000;

		if ($form.disabled) err += "Вы слишком часто комментируете. \nПодождите <b>" + TO/1000 + "</b> секунд\n";

		err += _H.form.errors($form[0], {breaks: '\n'});

		if(err) {
			return alert(err);
		}

		ajaxData = Object.assign({
			s_method: 'write',
			keyCaptcha: comm_vars.captcha,
			dataCount: comm_vars.dataCount,
			curpage: location.href,
		}, ajaxData);

		console.log("ajaxData= ", ajaxData);

		if($().spam)
			// ajaxData.entry = $f('#comments_form #entry').spam(10).trim();
			ajaxData.entry = $($form[0].elements.entry).spam(10).trim();

		commFns.refresh(ajaxData, {
			cb: function(response) {
				var keystring = $('#keystring')[0];
				$form.disabled = 1;
				$('#entry').val('');
				if(keystring) keystring.value="";

				setTimeout(function() {
					$form.disabled = 0;
					console.log($form);
				}, TO);
			}
		});

	},


	en_com: function (c) {
		//== enaible / disable on page
		commFns.refresh({
			enable_comm: this.checked, p_name : decodeURIComponent(comm_vars.pageName), s_method : 'enable_comm'
		});

		/* $.post(comm_vars.ajaxPath, {
			enable_comm: this.checked, p_name : decodeURIComponent(comm_vars.pageName), s_method : 'enable_comm'
		})
		.done(function(response) {
			commFns.render(response);
		}); */
	},

	paginator : function paginator() {
		var ajax= new kff.menu('.uk-pagination', '#wrapEntries');
		ajax.after= paginator;
	},

	init: function(gl) {
		var $form = $('#comments_form'),
		form = $form[0],
		$entry = $('#entry')[0];

		// console.log('form = ', form, $paginators);

		// Показываем форму при работающем JS
		form.hidden= 0;

		// Навешиваем отправку
		// $form.find("#subm").e.add("click",commFns.Send);
		$('#c_subm').on("click", gl.commFns.Send.bind(null, $form));

		// ajax на пагинатор
		this.paginator();

		if(!window.BB) return;

		BB.panel('#bb_bar', $entry, {
			b: ['fa-bold'],
			i: ['fa-italic'],
			u: ['fa-underline'],
			s: ['fa-strikethrough'],
		});
		BB.smiley('#sm_bar', $entry);

		// $('#CMS').val(comm_vars.cms);
	} // init

} //== /commFns


commFns.init(window);