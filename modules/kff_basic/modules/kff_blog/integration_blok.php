<?php
if(realpath('.') === __DIR__) die('Access denied');

class BlogKff_sidebar extends BlogKff
{
	private function _setAJAXMenu()
	{
		?>
		<script>
		'use strict';
		// <?=__FILE__?>

		kff.getSidebar().hidden=1;

		// console.log('kff.getSidebar().hidden=', kff.getSidebar().hidden);

		// *Active in nav
		kff.checkLib('UIkit', '/modules/kff_basic/modules/kff_uikit-3.5.5/js/uikit.min.js').then(UIkit=>{
			window.U = window.U || window.UIkit && UIkit.util;
			var uri= kff.getURI(),
				targetSel = '.blog_content',
				$sidebar = U.$('ul.categories', kff.getSidebar()),
				items= U.$$('a[data-ind]', $sidebar);

			// items.some((item,ind)=>{
			items.forEach((item,ind)=>{
				var iUri= kff.getURI(item.href),
					cond= uri[uri.length-1] === iUri[iUri.length-1];

				item.blockIndex= ind;

				if(cond){
					item.closest('.uk-parent').classList.add('uk-open');
					// ?
					var hidden= item.closest('[hidden]');
					hidden&&(hidden.hidden=0);
					// item.classList.add(BH.navActiveClass);
				}

				return cond;
			});

			kff.getSidebar().hidden=0;

			// *AJAX menu
			new kff.menu($sidebar, targetSel);
			// note worked
			// new kff.menu($sidebar, targetSel, [BH.navSelector]);

			var stiky= U.attr($sidebar, 'uk-sticky') + 'offset:' + parseInt(getComputedStyle(U.$('.bgheader')).height) + ';';
			// console.log('stiky=',stiky);
			// !
			U.attr($sidebar, 'uk-sticky', stiky);
		});

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

		foreach(self::getBlogMap() as $catInd=>$catData){

			if(empty($items= &$catData['items']))
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
						<a href=\"/{$pageId}/$catId/{$artData['id']}\" data-ind=\"".implode('', $artData['ind'])."\" class=\"". ($artData['id'] === self::getArtData()['id']? 'active': '') ."\" itemprop=\"url\" title=\"" . ($artData['title'] ?? $artData['name']) . "\" uk-tooltip>{$artData['name']}</a>

						</li>";

						if(filter_var($artData['not-public'],FILTER_VALIDATE_BOOLEAN)){
							if(self::is_adm())
							echo str_replace('class=""', 'class="not-public"', $li);
						}
						else echo $li;
					}

					?>
				</ul>

			</li><!-- .uk-parent -->
		<?php
		} //foreach
		echo "</ul><!-- .uk-nav -->";

		$this->_setAJAXMenu();
	}
}

// if ($Page->module !== 'kff_blog') return;

ob_start();

$BlogSidebar = new BlogKff_sidebar;

$BlogSidebar->Render();

return ob_get_clean();