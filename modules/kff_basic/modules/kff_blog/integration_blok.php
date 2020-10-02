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

		$pageId= $Page->module === 'kff_blog'?
			$Page->id : 'index';

		echo '<ul uk-nav="multiple: false" class="categories uk-nav-parent-icon uk-nav-primary uk-nav-center" uk-sticky="show-on-up:true; media:@m; bottom: .sidebar; " style="background: inherit;">';

		foreach(self::getBlogMap() as $ind=>$catData){

			if(empty($catData['items']))
				continue;
			// print_r ($catData);

			$catId= &$catData['id'];
		?>

			<li class="uk-parent">

				<a href="#" style="text-decoration:underline; font-size: 1.2em;"><?=$catData['name']?></a>
				<!-- <div uk-dropdown="mode: hover; delay-hide: 100; pos: left-top@m;">

					<ul data-cat=<?=$catId?>  class="uk-nav uk-dropdown-nav">

					<?php

					foreach($catData['items'] as &$art) {

						echo "<li data-id={$art['id']} data-cat=$catId>
						<a href=\"/{$Page->id}/$catId/{$art['id']} \">{$art['name']}</a>

						</li>";
					}

					?>
					</ul>
				</div> -->

				<!-- <ul data-cat=<?=$catId?>  class="uk-nav uk-dropdown-nav"> -->
				<ul data-cat=<?=$catId?>  class="">

					<?php

					foreach($catData['items'] as &$art) {
						// self::$log->add("/$catId/{$art['id']}");
						// todo Оптимизировать
						// $artData= self::getArtData(self::$storagePath . "/$catId/{$art['id']}" . self::$l_cfg['ext']);

						$li= "<li data-id={$art['id']} data-cat=$catId class=\"\">
						<a href=\"/{$pageId}/$catId/{$art['id']}\" title=\"" . ($art['title'] ?? $art['name']) . "\" uk-tooltip>{$art['name']}</a>

						</li>";

						if(!empty($art['not-public'])){
							if(self::is_adm())
							echo str_replace('class=""', 'class="not-public"', $li);
						}
						else echo $li;
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