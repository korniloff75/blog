<?php

class BlogKff_sidebar extends BlogKff
{
	private function _setAJAXMenu()
	{
		?>
		<!-- AJAX menu -->
		<script>
		(function() {
			'use strict';
			var targetSel = '.blog_content',
			$sidebar = $('.aside_content>ul.categories');
			new kff.menu($sidebar, targetSel);

			var stiky= $sidebar.attr('uk-sticky') + 'offset:' + parseInt(getComputedStyle($('.bgheader')[0]).height) + ';';
			console.log('stiky=',stiky);
			// !
			$sidebar.attr('uk-sticky', stiky);
		})();
		</script>
	<?php
	}
	public function Render()
	{
		global $Page;
		// uk-visible@m
		// echo '<ul class="categories uk-nav uk-visible@m" uk-sticky="show-on-up:true; media:@m; " style="background: inherit;">';

		$pageId= $Page->module === 'kff_blog'?
			$Page->id : 'index';

		echo '<ul uk-nav="multiple: false" class="categories uk-nav-parent-icon uk-nav-primary uk-nav-center" uk-sticky="show-on-up:true; media:@m; bottom: .sidebar; " style="background: inherit;">';

		foreach(self::getBlogMap() as $ind=>$catData){

			if(empty($items= $catData['items']))
				continue;
			// print_r ($catData);

			$catId= &$catData['id'];
		?>

			<li class="uk-parent">

				<a href="#" style="text-decoration:underline; font-size: 1.2em;"><?=$catData['name']?></a>

				<ul data-cat=<?=$catId?>  class="">

					<?php

					foreach($items as &$artData) {
						$li= "<li data-id={$artData['id']} data-cat=$catId class=\"\">
						<a href=\"/{$pageId}/$catId/{$artData['id']}\" itemprop=\"url\" title=\"" . ($artData['title'] ?? $artData['name']) . "\" uk-tooltip>{$artData['name']}</a>

						</li>";

						if(!empty(filter_var($artData['not-public'],FILTER_VALIDATE_BOOLEAN))){
							if(self::is_adm())
							echo str_replace('class=""', 'class="not-public"', $li);
						}
						else echo $li;
					}

					?>
				</ul>

			</li>
		<?php
		} //foreach

		$this->_setAJAXMenu();
	}
}

// if ($Page->module !== 'kff_blog') return;

ob_start();

$BlogSidebar = new BlogKff_sidebar;

$BlogSidebar->Render();

return ob_get_clean();