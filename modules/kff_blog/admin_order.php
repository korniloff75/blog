<?php
/**
 * *Подключается из admin.php BlogKff_adm::RenderPU()
 */
?>

<ul id="categories" class="uk-nav uk-nav-default" uk-sortable="group: cats; handle: .uk-sortable-handle;">

<?php
// self::$log->add(__METHOD__,null,['self::$catsDB'=>self::$catsDB, ]);

// foreach(self::$catsDB->get() as $catInd=>$catId) {
foreach(self::getBlogMap() as $catInd=>$catData) {

	$catData['id'] = $catData['id'] ?? self::$catsDB->get($catInd);
	$catId= &$catData['id'];

	// self::$log->add(__METHOD__,null,['count'=>count(self::$catsDB), '$catInd'=>$catInd, '$catId'=>$catId, '$catData'=>$catData]);
	// !
	// continue;
	?>
	<li>
	<div class="uk-flex uk-flex-middle uk-margin-top">
		<div class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: table; ratio: 1.5"></div>
		<!-- Category name -->
		<h4 class="uk-margin-remove"><?=$catData['name']?> <div class="removeCategory" uk-icon="icon: trash; ratio: 1.5" data-del="<?=$catData['id']?>"></div></h4>
	</div>

	<div class="uk-display-inline-block uk-width-1-2@s">
		<input type="hidden" name="ind" value="<?=$catInd . ',' . (is_array($catData['items'])? count($catData['items']): 0)?>">
		<input type="hidden" name="catId" value="<?=$catData['id']?>">
		<input type="hidden" name="catName" value="<?=$catData['name']?>">
		<input type="text" name="addArticle" class="uk-width-expand" placeholder="Название статьи">
	</div><button class="addArticle" title="Добавить статью">ADD</button>

	<ul data-id=<?=$catData['id']?> class="listArticles uk-nav uk-nav-default uk-width-auto" uk-sortable="group: cat-items; handle: .uk-sortable-handle; cls-custom: uk-box-shadow-small uk-flex uk-flex-expand uk-background">

	<?php
	if(is_array($catData['items'])) foreach($catData['items'] as $ind=>&$artData) {
		$artData['title'] = $artData['title'] ?? $artData['name'];
		$artData['oldCatId']= $catData['id'];

		echo "<li data-artData='"
		. json_encode($artData, JSON_UNESCAPED_UNICODE|JSON_HEX_QUOT|JSON_HEX_APOS)
		. "' uk-tooltip title=\"{$artData['title']}\" class=\"uk-flex uk-flex-wrap uk-flex-middle\">
		<div class=\"uk-sortable-handle uk-margin-small-right\" uk-icon=\"icon: menu\"></div>

		<!-- artName -->
		{$artData['name']}

		<!-- Remove article -->
		<span uk-icon=\"trash\" data-del=\"$catId/{$artData['id']}\" class='delArticle'></span>

		</li>";
		// print_r($artData);
	}

	?>
	</ul>
</li>
<?php
} //foreach
?>

</ul><!-- #categories -->