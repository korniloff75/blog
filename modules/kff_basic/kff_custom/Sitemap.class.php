<?php

class Sitemap extends BlogKff
{
	public
		$path= \DR.'sitemap.xml',
		$RSSpath= \DR.'rss.xml',
		$sitemap = '<?xml version="1.0" encoding="UTF-8"?>
		<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n",
		$rss = '<?xml version="1.0" encoding="UTF-8"?>
		<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
		version="2.0">
		<channel>
		<title>' . \SITENAME . '</title>
		<link>' . \BASE_URL . '</link>
		<description>' . \DESCRIPTION . '</description>
		<language>' . \LANG . '</language>
		<turbo:analytics type="LiveInternet"></turbo:analytics>' . "\n";


	public function __construct()
	{
		// $this->path = \SITEMAP['path'] ?? 'sitemap.xml';
		// $this->RSSpath = \SITEMAP['RSSpath'] ?? 'rss.xml';

		if(
			!\AJAX && (
				!file_exists($this->path)
				|| !file_exists($this->RSSpath)
				|| empty(\SITEMAP['expires'])
				|| (ceil(time() - filemtime($this->path))/3600/24 > \SITEMAP['expires'])
			)
		){
			$this->build();
		}
	} // __construct


	public function build()

	{
		foreach($Nav->map_flat as &$page) {
			// var_dump(filemtime($page));
			$data = \H::json($page . '/data.json');
			// var_dump($page . '/data.json', $data);

			if(!empty($data['hidden'])) continue;

			// $page = \Path::fromRootStat($page);
			$this->sitemap .= "<url>\n"
			. "<loc>" . \BASE_URL . $page . "</loc>\n"
			. "<lastmod>" . date ('Y-m-d', filemtime($page)) . "</lastmod>\n"
			. "<changefreq>weekly</changefreq>\n"
			. "<priority>0.7</priority>\n"
			. "</url>\n";

			# RSS
			# Собираем контент
			$itemContent = php\classes\Render::contentCollect($page);
			# Удаляем скрипты и коды
			// $itemContent = preg_replace("#<(script|pre)[\s\S]+?</\\1>#ui", "\n", $itemContent); // |&[\S]+?;

			# Чистка
			$itemContent = str_replace(['/>'], ['>'], $itemContent);
			// $itemContent = str_replace(['&', '/>'], ['&amp;amp;', '>'], $itemContent);

			$itemContent = preg_replace([
				# Удаляем скрипты и коды
				'#<(script|pre)[\s\S]+?</\1>#ui',
				# Оборачиваем значения атрибутов в кавычки
				"#\s(id|class|rel)=\s*([^\"\'\d][^\s\b>]*)#ui",
				"#\s(value|data-[^\s=]+|title|name|border)=\s*([^\"\'\s\b>]+)#ui",
				"#\s+(hidden|checked|required)\s*(?!=)#ui"
			], [
				"\n",
				" $1=\"$2\"",
				" $1=\"$2\"",
				" $1=\"\" "
			], $itemContent);

			$itemContent = preg_replace_callback_array([
				"#<(=|\s*\d+)#ui" => function($matches) {
				return htmlspecialchars($matches[0]);
				},
				# FIX html in title
				"#(title)=\"(.*?)\"#ui" => function($matches) {
				return "{$matches[1]}=\"" . htmlspecialchars($matches[2]) . '"';
				},
				# FIX html in code
				'#<(code)>(.+?)</\1>#ui' => function($matches) {
				return "<{$matches[1]}>" . htmlspecialchars($matches[2]) . "</{$matches[1]}>";
				},
				# Закрываем одиночные теги
				/* "#(<(?:img|input|br|hr|link|source)[^>]*?)\s*>#ui" => function($matches) {
					// "$1 />",
				return "{$matches[1]} />";
				}, */
			], $itemContent);

			# Закрываем одиночные теги
			$itemContent = preg_replace("#(<(?:img|input|br|hr|link|source|stop|path)[^>]*?)\s*>#ui", "$1 />", $itemContent);


			$this->rss .= '<item turbo="true">'
			. "\n<link>" . \BASE_URL . $page . "</link>\n"
			. "\n<turbo:content>
				<![CDATA[\n"
			. $itemContent
			. "\n]]>
				</turbo:content>\n"
			. "</item>\n\n";

		} // foreach($Nav->map_flat as &$page)

		$this->sitemap .= "\n</urlset> ";
		$this->rss .= "\n</channel>\n</rss>";

		# Compress
		if( \SITEMAP['gzip'])
		{
			file_put_contents($this->path . '.gz', gzencode($this->sitemap,  \SITEMAP['gzip']));
		}

		file_put_contents($this->path, $this->sitemap);
		file_put_contents($this->RSSpath, $this->rss);

		return $this->sitemap;
	} // build

} // SiteMap_RSS


// exit ((new SiteMap_RSS)->build());