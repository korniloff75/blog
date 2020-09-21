<?php
// return BlogKff::sidebarRender();

global $Page;

class BlogKff_sidebar extends BlogKff
{
	public function Render()
	{
		global $Page;
		// uk-visible@m
		// echo '<ul class="categories uk-nav uk-visible@m" uk-sticky="show-on-up:true; media:@m; " style="background: inherit;">';

		$pageName= $Page->module === 'kff_blog'?
			$Page->id : 'index';

		echo '<ul uk-nav="multiple: false" class="categories uk-nav-parent-icon " uk-sticky="show-on-up:true; media:@m; top: .sidebar; bottom: .sidebar; " style="background: inherit;">';

		foreach($this->getCategories() as &$cat) {

			$catData = $this->getCategory($cat);
			if(!count($catData['items']))
				continue;
			// print_r ($catData);
		?>

			<li class="uk-parent">

				<a href="#" style="text-decoration:underline; font-size: 1.2em;"><?=$catData['name']?></a>
				<!-- <div uk-dropdown="mode: hover; delay-hide: 100; pos: left-top@m;">

					<ul data-cat=<?=$cat?>  class="uk-nav uk-dropdown-nav">

					<?php

					foreach($catData['items'] as &$art) {

						echo "<li data-id={$art['id']} data-cat=$cat>
						<a href=\"/{$Page->id}/$cat/{$art['id']} \">{$art['name']}</a>

						</li>";
					}

					?>
					</ul>
				</div> -->

					<!-- <ul data-cat=<?=$cat?>  class="uk-nav uk-dropdown-nav"> -->
					<ul data-cat=<?=$cat?>  class="">

					<?php

					foreach($catData['items'] as &$art) {

						echo "<li data-id={$art['id']} data-cat=$cat>
						<a href=\"/{$pageName}/$cat/{$art['id']} \">{$art['name']}</a>

						</li>";
					}

					?>
					</ul>

			</li>
		<?php
		}
	}
}

// if ($Page->module !== 'kff_blog') return;

ob_start();

$BlogSidebar = new BlogKff_sidebar;

$BlogSidebar->Render();

return ob_get_clean();