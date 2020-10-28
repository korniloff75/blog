<button id="addSetting" title="Добавить настройку" uk-tooltip>ADD</button>

<?php
foreach(self::$blogDB as $n=>$v){
	$iType= is_numeric($v)? 'number': 'text';
	echo "<li>
	<span class='uk-width-1-3@s uk-display-inline-block'>$n</span> <input type='$iType' name='$n' class='stsVal uk-width-expand' value='$v'>
	</li>";
}
?>

<h4>Сохранить изменения</h4>
<button id="saveSettings" title="Сохранить изменения" uk-tooltip>SAVE</button>