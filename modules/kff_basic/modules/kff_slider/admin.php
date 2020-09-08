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

require_once __DIR__.'/fns.php';

$Storage = getStorage();
// *pathname to imgs folder
// $Folder = $Storage->get($Page->id) ?? __DIR__;
$cfg = $Storage->get();

$log->add('Storage=',null,[$Storage, ]);

$Imgpath = DR."/files/slider";


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
	Uploads::$pathname = $kff::fixSlashes($Imgpath) . "/{$Page_id}";
	Uploads::$allow = ['jpg','jpeg','png','gif'];

	$Upload = new Uploads;

	if(count($Upload->getSuccess()))
	{
		// *Add to $cfg
		$Storage->set([
			$Page_id=> Uploads::$pathname
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
			showImgs($Storage->get($id));
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
	.each((ind