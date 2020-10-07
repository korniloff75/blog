// comm_vars - settings in comments.php
'use strict';
var commFns = {
	module: 'php/modules/comments/comments.php',
	$comments: $('section#comments'),
	refreshed: 1,


	refresh: function(data, sts) {
		if(!commFns.refreshed) return;

		sts = Object.assign( {
			handler: '/',
			hash: '#',
			cb: null // callback
		}, sts || {});

		data = Object.assign( {
			page: sv.DIR,
			module: commFns.module
		}, data || {});

		commFns.refreshed = 0;

		commFns.$comments.load(
			sts.handler,
			data,
			function(response) {
				location.replace(sts.hash);
				if(typeof sts.cb === 'function') sts.cb.call(null, response);
				commFns.refreshed = 1;
			}
		);
	},


	Edit : {
		createForm : function (resp) {
			commFns.$formEdit && commFns.$formEdit.remove();
			commFns.$formEdit = $('body').cr("div", { id: "com_ed" });
			commFns.$formEdit.append (resp);
		},

		open : function(num) {
			$.post(comm_vars.ajaxPath, {
				page: sv.DIR,
				module: commFns.module,
				s_method:'edit_comm',
				num: num })
				.done(commFns.Edit.createForm);
		},

		save : function () {
			var ajaxData = $(document.forms["edit_comm"]).ajaxForm();

			ajaxData.s_method= "save_edit";

			commFns.refresh(ajaxData, {
				cb: function() {
					$('#com_ed').remove();
				}
			});

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

	paginator : function(e) {
		e = $().e.fix(e);
		var t = e.target;
		// console.log('t.tagName = ' + t.tagName);
		if(t.tagName !== 'A') return;
		e.stopPropagation();
		e.preventDefault();

		commFns.refresh({
			p_comm: _H.qs.parse(t.href)['p_comm'],
		}, {
			// handler: t.href,
			hash: '#comments_header',
		});

		console.log('p_comm = ', _H.qs.parse(t.href)['p_comm']);

		history.pushState(null, null, '/' + _H.getPath(t.href));
	},

	init: function(gl) {
		var $paginators = $("#comments .paginator"),
		$form = $('#comments_form'),
		form = $form[0],
		$entry = $('#entry')[0];

		// console.log('form = ', form, $paginators);

		// Показываем форму при работающем JS
		form.hidden= 0;

		// Навешиваем отправку
		// $form.find("#subm").e.add("click",commFns.Send);
		$('#c_subm').on("click", gl.commFns.Send.bind(null, $form));

		// ajax на пагинатор
		$paginators.on("click",commFns.paginator);

		$(document).on("keyup", function (e) {
			if (commFns.$formEdit && $().e.fix(e).defKeyCode('esc')) commFns.$formEdit.remove();
		});

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