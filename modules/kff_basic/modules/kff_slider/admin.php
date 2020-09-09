<div class="header">
	<h2>Настройки <b><?=$MODULE?></b></h2>
</div>
<?php
// *Get pages list

echo '<pre>';
/* var_dump(
	// System::listPages(),
	// get_class_methods('Page'),
	// get_class_methods('System'),
	// get_class_vars('System'),
	// $Page
); */

$Imgpath = DR."/files/slider";

require_once __DIR__.'/fns.php';

// getStorage("$Imgpath/cfg.json");

DbJSON::$convertPath = false;
$Storage = new DbJSON(__DIR__."/cfg.json");
// *pathname to imgs folder
// $Folder = $Storage->get($Page->id) ?? __DIR__;
$cfg = $Storage->get();

$log->add('Storage=',null,[$Storage, ]);

$log->add('$_FILES',null,[
	$_FILES, $act, $_REQUEST
]);

// *inputs handler
if(
	$act === 'upload'
	&& count($_FILES)
)
{
	if(!$kff::is_adm())
		die('Access denied!');

	$Page_id = filter_var($_REQUEST['id']);

	require_once DR.'/'.$kff::$dir.'/Uploads.class.php';
	Uploads::$pathname = $Imgpath . "/{$Page_id}";
	Uploads::$allow = ['jpg','jpeg','png','gif'];

	$Upload = new Uploads;

	if(count($Upload->getSuccess()))
	{
		// *Add to $cfg
		$Storage->set([
			$Page_id=> $kff::getPathFromRoot(Uploads::$pathname)
		]);
	}

	// die;

} // inputs handler

echo '</pre>';

?>
<style>
	.uk-accordion-title {cursor:pointer;}
	.uk-accordion-title:hover {background:#eee;}
</style>

<h2>Слайдеры на страницах</h2>
<div class="comment">
	<ul>
		<li>Для добавления слайдера кликнуть по названию страницы и выбрать файлы для загрузки.</li>
		<li>Добавленные слайдеры выводятся миниатюрами под названием страницы</li>
		<li><b>Поддерживается мультизагрузка файлов</b>. Во время выбора изображений можно пользоваться клавишами Ctrl или Shift</li>
		<li>Дополнительно подключать модуль к странице <u>не нужно</u>.</li>
		<li>Все файлы сохраняются в директориях внутри <i>/files/slider</i>. При удалении оттуда любой директории - соответствующий слайдер будет удалён. При удалении файла - будет изъят из слайдера.</li>
	</ul>
</div>
<?php

echo '<ul id="sts" uk-accordion class="uk-margin-left">';
// echo '<ul id="sts" class="uk-form-horizontal">';

// *Перебираем все страницы
foreach(System::listPages() as $n=>$id)
{
	$pageInfo = $kff::getPageInfo($id);
	?>

	<!-- <li class='uk-flex uk-flex-wrap uk-padding-small uk-width-1 uk-flex-between uk-flex-middle uk-flex-1'> -->
	<li class="uk-margin-bottom">
		<!-- <button class="more">+</button> -->
		<div class="uk-accordion-title"><?=$pageInfo['name']?>
			<div class="existsImgs">
			<?php
			// *Выводим изображения из Uploads::$pathname
			// showImgs($Storage->get($id));
			showImgs("$Imgpath/{$pageInfo['id']}");
			?>
			</div>
		</div>

		<!-- <p class="uk-form-custom uk-flex-1"> -->
		<form action='' method='post' class="uk-accordion-content" enctype="multipart/form-data">
			<input type="hidden" name="act" value="upload">
			<input type="hidden" name="id" value="<?=$id ?>">
			<!-- <input type="text" name="imgpath" placeholder="путь к папке и изображениями" value="<?=$cfg[$id] ?? "$Imgpath/$id" ?>"> -->
			<input type="file" name="file[]" class="" value="<?=$cfg[$id] ?? $Imgpath?>" multiple>
			<button class="save_sts">Сохранить</button>
		</form>
	</li>

	<?php
}

echo '</ul>';

?>

<script>
$('#sts').on('click', '.more', $e=>{
	var t = $e.target;

	var el = t.closest('li'),
		dupl = el.cloneNode(1),
		parent = $e.delegateTarget;

	// console.log($e, el, dupl, parent);

	parent.insertBefore(dupl, el);

});


// *Upload files
$('#sts').on('submit', '.save_sts', $e=>{
	var t = $e.target,
		$form = t.closest('form');



	/* $parent.find('input[type=file]')
	.each((ind,i)=>{
		console.log(i.files);
	}); */

	console.log($form);

});
</script>