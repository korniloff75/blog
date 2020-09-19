<?php
// return BlogKff::sidebarRender();

global $Page;

class BlogKff_sidebar extends BlogKff
{
	public function Render()
	{
		global $Page;
		echo '<ul id="categories" class="uk-nav" uk-sticky="show-on-up:true; media:@m; ">';

		foreach($this->getCategories() as &$cat) {
			// $catData = (new DbJSON(self::$storagePath."/$cat/cfg.json"))->get();
			$catData = $this->getCategory($cat);
			if(!count($catData['items']))
				continue;
			// print_r ($catData);
		?>

			<li>

				<span><h4><?=$catData['name']?></h4></span>
				<div uk-dropdown="mode: hover; delay-hide: 100; pos: left-top">

					<ul data-cat=<?=$cat?>  class="uk-nav uk-dropdown-nav">

					<?php

					foreach($catData['items'] as &$art) {

						echo "<li data-id={$art['id']} data-cat=$cat>
						<a href=\"/{$Page->id}/$cat/{$art['id']} \">{$art['name']}</a>

						</li>";
					}

					?>
					</ul>
				</div>

			</li>
		<?php
		}
	}
}

if ($Page->module !== 'kff_blog') return;

ob_start();

$BlogSidebar = new BlogKff_sidebar;

$BlogSidebar->Render();

return ob_get_clean();