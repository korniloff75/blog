<button id="addSetting" title="Добавить настройку" uk-tooltip>ADD</button>

<?php
foreach(self::$blogDB->get() as $n=>&$v){
	$iType= is_numeric($v)? 'number': 'text';
	echo "<li>
	<span class='uk-width-1-3@s uk-display-inline-block'>$n</span> <input type='$iType' name='$n' class='stsVal uk-width-expand' value='$v'>
	</li>";
}
?>

<h4>Сохранить изменения</h4>
<button id="saveSettings" title="Сохранить изменения" uk-tooltip>SAVE</button>

<script>
'use strict';
var U= window.U || UIkit.util;
kff.curSel= '.switcher-item.sts';

// *Добавляем настройку
U.on('#addSetting', 'click', e=>{
	var node= U.$(kff.curSel+' li'),
		newNode= node.cloneNode(true);

	U.remove(U.find('span', newNode));
	var name= U.prepend(newNode, '<input type="text" class="uk-width-1-3@s uk-display-inline-block" placeholder="name">'),
		stsVal= name.nextElementSibling;

	stsVal.name= stsVal.value= '';

	console.log(name);

	U.on(name, 'blur', e=>{
		e.target.nextElementSibling.name= name.value;
		// console.log(e.target.nextElementSibling.name, e.target.nextElementSibling);
	})

	U.after(node, newNode);
});


// *Сохраняем настройки
U.on('#saveSettings', 'click', e=>{
	var data= {},
		err=[];

	U.$$(kff.curSel+' li').forEach(i=>{
		var el= i.querySelector('input.stsVal');
		// console.log(el.name, el);
		if(!el.name) err.push('Не заполнены обязательные поля');
		data[el.name]= el.value;
	});

	if(err.length)
		err.forEach(i=>UIkit.notification(i,'warning'));
	else kff.request('',{
		name: 'saveSts',
		value: data,
	},[kff.curSel]);
});
</script>