// comm_vars - settings in comments.php
'use strict';

var commFns = {
	module: 'php/modules/comments/comments.php',
	$comments: $('section#comments'),
	refreshed: 0,
	err: [],


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

		console.log({data});

		return kff.request(
			sts.handler,
			data,
			// ['#entries']
			['#wrapEntries']
		)
		.then(
			out=>{
				console.log({out});
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
		open : function(num) {
			U.ajax('', {
				method: 'POST',
				data: JSON.stringify({
					act: 'comments',
					name:'Edit_Comm',
					num: num
				})
			}).then(xhr=>{
				this.$formEdit= UIkit.modal.dialog(xhr.response);
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
			.then(ok=>{
				console.log('!!! Saved !!!\n');
				this.$formEdit.hide();
			}, err=>{console.info(err);});
		},

		del : function(num) {
			UIkit.modal.confirm('Продолжить удаление комментария?')
			.then(ok=>{
				commFns.refresh({
					name:'Del_Comm', value: num
				});
			}, err=>{console.info(err);})

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


	Send: function (e) {
		var $form = $(this.form);

		console.log("$form = ", $form);

		var formData = [].reduce.call($form.find('input,select,textarea'), (a,c)=>{
			if(!c.name) return a;
			a[c.name]= c.value;
			return a;
		}, {keyCaptcha: comm_vars.captcha});

		var btn= this,
			TO=10000;

		if ($form.disabled) commFns.err.push ("Вы слишком часто комментируете. \nПодождите <b>" + TO/1000 + "</b> секунд\n");

		if(commFns.err.length) {
			return UIkit.modal.alert(commFns.err.split('\n\n'));
		}

		var ajaxData = {
			name: "Write_Comm",
			value: formData,
		}

		// ?
		if($().spam)
			// ajaxData.entry = $f('#comments_form #entry').spam(10).trim();
			formData.entry = $($form[0].elements.entry).spam(10).trim();

		commFns.refresh(ajaxData)
		.then(function(response) {
			var keystring = $('#keystring')[0];
			$form.disabled = 1;
			$('#entry').val('');
			if(keystring) keystring.value="";

			setTimeout(function() {
				$form.disabled = 0;
				console.log($form, btn);
			}, TO);
		});

	},


	en_com: function (c) {
		//== enaible / disable on page
		commFns.refresh({
			name: 'Enabled_Comm',
			value: this.checked,
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
		entry = $('#entry')[0];

		// console.log('form = ', form, $paginators);

		// Показываем форму при работающем JS
		form.hidden= 0;

		// Навешиваем отправку
		// $form.find("#subm").e.add("click",commFns.Send);
		var $subm_btn= $('#c_subm');
		$subm_btn.on("click", gl.commFns.Send);

		// ajax на пагинатор
		this.paginator();

		if(!window.BB) return;

		BB.panel('#bb_bar', entry, {
			b: ['fa-bold'],
			i: ['fa-italic'],
			u: ['fa-underline'],
			s: ['fa-strikethrough'],
		});
		BB.smiley('#sm_bar', entry);

		// $('#CMS').val(comm_vars.cms);
	} // init

} //== /commFns


kff.checkLib('UIkit', '/modules/kff_basic/modules/kff_uikit-3.5.5/js/uikit.min.js').then(UIkit=>{
	window.U= window.U || UIkit.util;
	commFns.init(window);
});
