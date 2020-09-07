<?php
// *Get pages list
echo '<pre>';
var_dump(
	// System::listPages(),
	// get_class_methods('Page'),
	// get_class_methods('System'),
	// get_class_vars('System'),
	$Page
);

// DbJSON::$convertPath = false;
// $Storage = new DbJSON(__DIR__.'/cfg.json');
// $cfg = $Storage->get();

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

	require_once DR.'/'.$kff::$dir.'/Uploads.class.php';
	Uploads::$pathname = $kff::fixSlashes($_REQUEST['imgpath'] ?? $Imgpath);
	Uploads::$allow = ['jpg','jpeg','png','gif'];

	new Uploads;

	die;

}

echo '</pre>';

echo '<ul id="sts" uk-accordion>';
// echo '<ul id="sts" class="uk-form-horizontal">';

foreach(System::listPages() as $pname)
{
	// echo System::validPath($pname);
	// var_dump (htmlspecialchars(Page::contentDat($pname)));
	// var_dump (Page::get_content($pname));
	?>
	<!-- <li class='uk-flex uk-flex-wrap uk-padding-small uk-width-1 uk-flex-between uk-flex-middle uk-flex-1'> -->
	<li class="uk-margin-bottom">
			<!-- <button class="more">+</button> -->
			<span class="uk-accordion-title"><?=$pname?></span>
			<!-- <p class="uk-form-custom uk-flex-1"> -->
			<form action='' method='post' class="uk-accordion-content" enctype="multipart/form-data">
				<input type="hidden" name="act" value="upload">
				<input type="text" name="imgpath" placeholder="путь к папке и изображениями" value="<?=$cfg[$pname] ?? "$Imgpath/$pname" ?>">
				<input type="file" name="file[]" class="" value="<?=$cfg[$pname] ?? $Imgpath?>" multiple>
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