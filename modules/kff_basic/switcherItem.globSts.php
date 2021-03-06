<!-- <script src="/modules/ckeditor_plus/ckeditor/ckeditor.js"></script> -->
<!-- <script src="https://cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script> -->
<script src="/modules/ckeditor_4.5.8_standard/ckeditor/ckeditor.js"></script>


<!-- <hr> -->
<div id="kff_sts" class="kff_sts uk-section">

	<h2>Глобальные настройки</h2>

	<div data-group="admin">
		<p><span class="uk-width-1-3@m uk-display-inline-block">Имя админа</span>  <input name="name" type="text" class="uk-width-expand" value="<?=self::$cfg['admin']['name']?>"></p>
		<p><span class="uk-width-1-3@m uk-display-inline-block">Email админа</span>  <input name="email" type="text" class="uk-width-expand" value="<?=self::$cfg['admin']['email']?>"></p>
	</div>


	<p><label>Init Modules <input name="init_mods" type="checkbox" ></label></p>
	<p class="comment">Инициализация всех модулей. Системная.</p>

	<ul id="UIKit" data-group="uk" class="uk-list uk-list-striped uk-list-large">
		<li><label>Подключить UIKIT <input id="include_uikit" name="include_uikit" type="checkbox"></label>
			<p class="comment">Подключение библиотеки UIKIT на все страницы сайта</p>
		</li>
		<li><label>Подключить изображения <input name="include_picts" type="checkbox"></label>
			<h5>Применить стили UIKIT ко всем:</h5>
			<p class="comment">
				Подключённые теги будут динамически обрабатываться модулем.
			</p>

			<ul class="uk-list uk-list-striped">

				<li><label>Тегам <i>input/textarea/select</i> <input name="use_styles_input" type="checkbox"></label></li>
				<li><label>Тегам <i>ul</i> <input name="use_styles_ul" type="checkbox"></label></li>

			</ul>
		</li>

	</ul>

	<button class="uk-button">Сохранить настройки</button>
</div>


<script>
'use strict';
var U= window.U || UIkit.util;
// console.log('U=',U);

U.ready(()=>{

	var cfg= <?=json_encode(Basic::$cfg)?>,
	$content= $('.content');

	ajaxRender();

	// *Set group 4 checkboxes
	$content.find('input')
	.each((ind,i)=>{
		if(i.type === 'checkbox')
			i.classList.add('uk-checkbox');

		var group= i.closest('[data-group]');
		i.group= group && group.getAttribute('data-group');
		i.checked= i.group? cfg[i.group]&&cfg[i.group][i.name]: cfg[i.name];
		// *fix 4 force-unchecked
		if(i.hasAttribute('data-unchecked')) i.checked= false;
		// console.log(i, i.checked);
	});


	// *Сохр глоб. настроек kff_basic/cfg.json
	$('.kff_sts, #installModules').on('change', 'input,select', $e=>{
		var $t= $($e.target);
		// console.log($t.prop('checked'));
		if(!$t.length) return;

		$t.is_checkbox= ($t.val() === 'on');

		var val= $t.is_checkbox ? $t.prop('checked') : $t.val();

		var $include_uikit = $('#include_uikit'),
			send_data = {
				type: 'global',
				group: $t.prop('group'),
				name: $t.prop('name'),
				val: val,
			};

		// *Если UIkit глобально отключён
		if(
			send_data.group === 'uk'
			&& !$include_uikit.prop('checked')
		) {
			$include_uikit.closest('ul').find('input[type=checkbox]').prop({checked:0});
			send_data.disable = 1;
		}

		// todo
		// *installAll
		var $installAll= $('input[name=installAll]');

		console.log($installAll.prop('checked'));

		if($installAll.prop('checked')) {
			send_data.val= {installAll:1};
			// $installAll.closest('ul').find('input[type=checkbox]').prop({checked:1});
			$installAll.closest('ul').find('input[type=checkbox]')
			.each((ind,i)=>{
				if(i === $installAll[0]) return;

				i.checked = 1;
				send_data.val[i.name] = 1;
			});

		}

		UIkit.modal.confirm('Подтверждаете действие?')
		.then(
			success=>{saveSTS(send_data)},
			fail=>{
				$e.preventDefault();
				$e.stopPropagation();
				if($t.is_checkbox) $t.prop('checked', !$t.prop('checked'));
			}
		);

	});


	function saveSTS (send_data) {
		$.post('', send_data)
		.then((response, status)=>{
			// console.log(status, typeof status, status === 'success');
			if(status === 'success')
				UIkit.notification("<span uk-icon='icon: check'></span> Настройки успешно сохранены", 'success');
			else
			if(status === 'success')
				UIkit.notification("<span uk-icon='icon: check'></span> Настройки не были сохранены", 'danger');
			/* UIkit.modal.alert( "Настройки" +
				(status === 'success'?
				" успешно сохранены"
				:" не были сохранены")
			); */

			ajaxRender(response);
		});
	}


	/**
	 * @param {jq} $t form element
	 * @param {string} method 'html' || 'val'
	 */
	function saveINI ($t, method) {
		$.post('',{
			ini_path: $t.closest('li.uk-open').find('h4').data('ini-path'),
			name: $t.prev().text().trim(),
			val: $t[method](),
		}).then((response, status)=>{
			// console.log();
			/* $('<div/>').insertAfter($e.currentTarget)
			.html($(response).find('pre.log')); */

			var lis = U.$$('#mds_sts>li');

			localStorage.setItem('open', lis.indexOf(U.$('#mds_sts>li.uk-open')));

			// console.log(U.$('.uk-open'));
			// document.documentElement.innerHTML = response;

			ajaxRender(response);
		});
	}

	function ajaxRender (response, $currentTab) {
		$currentTab = $currentTab || $('div.uk-active');
		var tabIndex = $currentTab.index();

		// *Переписываем активный таб и лог
		if(response) {
			$currentTab.html($(response).find('.uk-switcher')[tabIndex].innerHTML);
			$('.log').html($(response).find('.log').html());
		}

		// *Открываем аккордион
		var open = localStorage.getItem('open'),
			openEl = open && U.$$('#mds_sts>li')[open]
			|| U.$('#mds_sts>li.uk-open');
		console.log(open);
		// *Открытый элемент
		openEl && openEl.classList.add('uk-open');

		// *Украшения
		U.$$('.content input[type=checkbox]').forEach(i=>i.classList.add('uk-checkbox'));

		// *Сохр настроек модулей
		// console.log($('#mds_sts'));
		var $mds_sts = $('#mds_sts'),
			$descriptions = $('#mds_sts').find('div[contentEditable]');

		$descriptions.each((ind,i)=>{
			i.$saveBtn = $('<button>Save</button>');
			i.initialHtml = i.innerHTML;

			i.$saveBtn.hide();

			i.$saveBtn.insertAfter(i)
			.on('click', $e=>{
				var $t= $($e.target),
				$contDiv= $t.prev();

				console.log($contDiv);

				if($contDiv.prop('initialHtml') === $contDiv.prop('innerHTML'))
					return;

				saveINI($contDiv, 'html');

				console.info('Data saved from', $t);
				i.$saveBtn.hide();
			});
		});

		$mds_sts.on('focus', 'div', $e=>{
			var $t= $($e.target);

			console.log($t.prop('$saveBtn'));

			$t.prop('$saveBtn') && $t.prop('$saveBtn').show();
		});

		$mds_sts.on('blur', 'div', $e=>{
			var $t= $($e.target);
			// $t.prop('$saveBtn').hide();
		});

		$mds_sts.on('change', 'input,textarea', $e=>{
			var $t= $($e.target);
			if(!$t.length) return;

			saveINI($t, 'val');
		});
	}
});

// *Запускаем редактор с файловым браузером
Object.assign(CKEDITOR.config, {
	filebrowserBrowseUrl: '/index?name=createCKEditorBrowser',
	disallowedContent : 'img{width,height}',
	image_removeLinkByEmptyURL: true,
});

CKEDITOR.inlineAll();
</script>