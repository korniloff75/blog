'use strict';
hl_code.styleBox = document.createElement('style');
hl_code.styleBox.textContent = `
pre > code {
	white-space: pre-wrap;
	padding: 1em;
	font: 1em consolas !important;
}
pre > code .S{color:red}/* Строки красные */
pre > code .func{color:blue}/* Юзер-функции синие */
pre > code .C{
	color:orange; /* Комменты оранжевые */
	font-size: 70%;
}
pre > code .kwrd{font-weight:bold}/* Ключевые слова полужирные */
.kwrd_green{ color:green;}
pre > code .R{color:gray} /*Серые регвыражения */
`;
document.querySelector('head')
.appendChild(hl_code.styleBox);

[].forEach.call(document.querySelectorAll('pre>code'), i=>{
	i.innerHTML = hl_code(i.innerHTML);
});

console.info('hl inited');

function hl_code(code){
var comments = [],	// Тут собираем все каменты
	strings = [], // Тут собираем все строки
	res = [], // Тут собираем все RegExp
	all = { 'C': comments, 'S': strings, 'R': res },
	safe		= { '<': '<', '>': '>', '&': '&' };

	return code
	// Маскируем HTML
	.replace(/[<>&]/g, function (m){
		return safe[m];
	})
	// Убираем каменты
	.replace(/([^\\]|^)(?:\/\/|\#)[^\n]*$/mg, function(m, f){
		var l=comments.length; comments.push(m); return f+'~~~C'+l+'~~~';
	})
	.replace(/\/\*[\s\S]*?\*\//g, function(m){
		var l=comments.length; comments.push(m); return '~~~C'+l+'~~~';
	})

	// Убираем строки
	.replace(/([^\\])((?:'(?:\\'|[^'])*')|(?:"(?:\\"|[^"])*"))/g, function(m, f, s){
		var l=strings.length; strings.push(s); return f+'~~~S'+l+'~~~';
	})
	// Убираем regexp
	.replace(/\/([^\/\n])+?\/[gim]{0,3}/g, function(m){
		var l=res.length; res.push(m); return '~~~R'+l+'~~~';   })
	// Выделяем ключевые слова
	.replace(/\b(var|function|typeof|throw|new\s+.+?|return|if|for|in|while|break|do|continue|switch|case)\b([^a-z0-9\$_])/gi, '<span class="kwrd">$1</span>$2')
	// Выделяем ключевые слова 2 тип
	// .replace(/(\|+|\w+?\:\:)/g, '<span class="kwrd_green">$1</span>')
	.replace(/(\w+?\:\:)/g, '<span class="kwrd_green">$1</span>')
	// Выделяем скобки
	.replace(/(\{|\}|\]|\[|\|)/gi, '<span class="gly">$1</span>')
	// Выделяем имена функций
	.replace(/([a-z\_\$][a-z0-9_]*)\s*?\(/gi, '<span class="func">$1</span>(')
	// Возвращаем на место каменты, строки, RegExp
	.replace(/~~~([CSR])(\d+)~~~/g, function(m, t, i){
		return '<span class="'+t+'">'+all[t][i]+'</span>'; })
	// Выставляем переводы строк
	.replace(/\n/g, '<br/>')
	// Табуляцию заменяем неразрывными пробелами
	.replace(/\t/g, '&nbsp;&nbsp;');
}