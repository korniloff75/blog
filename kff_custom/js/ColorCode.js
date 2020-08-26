'use strict';

var CC= CC || {
	__proto__: null,
	sts:{ //
		lineNumbers: 1
	},

	ajax: null,

	parseCode: function(ind, cA) { //== Parser
		var tags= /(<|&lt;)([^!>]+?)(>|&gt;|[\s!])/g, ops=/\b(document|return|forEach|for|if|else|null|var)\b/g,
		funcs= /\b(function|querySelector|querySelectorAll|use strict|console\..+?|onreadystatechange)\b/g; // /(<)([^>\s]+)([^>]*?>[\s\S]+)(<\/\2>)/g

		cA.innerHTML= cA.textContent
			.replace(tags,"$1<span class='tag'>$2</span>$3") // <span class='attrs'>$3</span>
		//	.replace(/([^\s]+\=)([^\s>]+)/g, "<span class='attrs'>$1$2</span>")
			.replace(ops, "<span class='ops'>$1</span>")
			.replace(funcs, "<span class='funcs'>$1</span>")
			.replace(/(([\"])[^\2>]*?\2)/g, "<span class='str'>$1</span>")
		//	.replace(/=\s*?(\d+)/g, "= <span class='num'>$1</span>")
			.replace(/(\d+(?:%|px)?)/g, "<span class='num'>$1</span>")
			.replace(/([^:\"\'])(\/\/.+$|\/\*[\s\S]+?\*\/|<!--[\s\S]+?-->)/mg, "$1<span class='comments'>$2</span>").replace(/<(!--)/g,'&lt;$1')
			// tab -> 1 space
			.replace(/\t/g, '    ')
			.replace(/ {4}/g, ' ');

			// Убираем подсветку в комментах
			$(cA).find('span.comments span').each(function(ind,i) {
				i.removeAttribute('class');
			});

		if(CC.sts.lineNumbers) CC.addLineNumbers(cA);
	},


	exterCode: function() {
		function addClick (i) {
			var saldom = i.hasAttribute('saldom'),
				lib = i.getAttribute('data-lib'),
				$i = $(i),
				noLib = $i.attr('saldom') === 'noLib' || i.hasAttribute('noLib');

			if(i.$pre && (!saldom || lib)) {
				i.$pre.css({position : 'relative'});
				// console.log('css = ', $(i.pre).css({position : 'relative'}));
				// console.log(lib);
				$('<span />', {
					style: "position:absolute; right:5px; top:-7px; font-weight:600; color:#159; background: #eee; padding: 3px; border-radius: 3px;"
				}).appendTo(i.$pre)
				.text('use ' + (lib || 'native ES-5'));

			}

			i.title='выделить код';
			$i.on ('click',  $i.select );
		} // addClick


		var $codes = $f('code' );
		if (!$codes) return console.warn('$codes = ', $codes);


		$codes.each(function(ind, cA) {
			var $cA = $(cA),
				pre = cA.closest('pre');

			if(!$cA.parent()) return; //== nE
			cA.classList.add('http');

			if(!pre) {
				$cA.attr({saldom:'noLib'});
			} else {
				pre.style.overflowX = 'auto';
				// cA.$pre = $(cA.parentNode).cr('div',{class:'code'},'before');
				cA.$pre = $(cA).wrap('<div class=code />');
				// cA.$pre.append(cA.parentNode);
			}

			var $tmpDiv= $(document.createElement('div')),
				sourse = $cA.attr('for'); //

			if(!!sourse) {
				var $bl = $( sourse);

				// console.log(sourse, $bl);
				if(!$bl) console.warn('Узла с селектором ' + sourse + ' не существует!');
				else
					$tmpDiv.html($cA.html() + $bl.html());

				// console.log($tmpDiv, $tmpDiv.html(), $cA.html(), );

				$tmpDiv.find('div').each(function(ind, i) {
					if (i.style.display==='none')
						$(i).remove();
				});
			} else {
				$tmpDiv.html($cA.html());
			}

			$cA.text($tmpDiv.html());


			addClick(cA);

		});

		$f('.helpLib' ).each(function(ind, i) {
			var b = $(i).siblings('blockquote')[0];
			i.onclick= function() {
				b.hidden = !b.hidden;
			}
		});

	}, //== /exterCode


	addLineNumbers: function (cA) {
		var $lN= $(cA).parent().find('span.line-numbers-rows');

		if($lN.length) {
			$lN.html('');
		} else $lN= $(cA).cr('span',{class:'line-numbers-rows'},'before');

		// console.log('$lN = ', $lN);

		if(!$lN.length) return;

		for (var i=1, L=cA.textContent.split('\n').length; i <= L; i++) {
			$lN.cr('span').text(i);
		};
	},


	init : function() {
		//== nE
		(!window.hljs || !hljs.inited || /js-master/.test(location.href)) && CC.exterCode();

		$('body pre>code').each(CC.parseCode);
	}
}; // CC


//== Inited ColorCode

if(!CC.inited) {
	CC.inited= $('head').cr('link',{href:'/assets/css/ColorCode.css',rel:"stylesheet",type:"text/css",charset:"utf-8"});
	// CC.init();
	_H.defer.add(CC.init);
}

// CC.init();