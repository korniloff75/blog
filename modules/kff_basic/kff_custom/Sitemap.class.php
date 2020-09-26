<?php

class Sitemap extends BlogKff
{
	private
		$path= \DR.'/sitemap.xml',
		$RSSpath= \DR.'/rss.xml',

		$sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n",

		$rss = '<?xml version="1.0" encoding="UTF-8"?>
		<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
		version="2.0">
		<channel>' . "\n";


	public function __construct(DbJSON &$map)
	{
		parent::__construct();
		// $this->path = \SITEMAP['path'] ?? 'sitemap.xml';
		// $this->RSSpath = \SITEMAP['RSSpath'] ?? 'rss.xml';

		$this->rss.= '<title>' . '{{SITENAME}}' . '</title>
		<link>' . (self::is('https')?'https':'http') . '://' . \HOST . '</link>
		<description>' . '{{DESCRIPTION}}' . '</description>
		<language>' . 'ru' . '</language>
		<turbo:analytics type="LiveInternet"></turbo:analytics>';

		if(
			self::is_adm() && (
				!file_exists($this->path)
				|| !file_exists($this->RSSpath)
				// || (ceil(time() - filemtime($this->path))/3600/24 > \SITEMAP['expires'])
			)
		){
			$this->build($map);
		}
	} // __construct


	public function build(DbJSON &$map)
	{

		echo '<pre>';

		foreach($map->get() as $num=>&$catDB) {
		// foreach($map as $num=>&$catDB) {
			$catId= $catDB['id'];
			$catName= $catDB['name'];

			echo "<h3>[$num] - $catId - $catName</h3>";
			// echo "<p>".\HOST."</p>";
			// var_dump($catDB);

			// *Перебор статей в категории
			if(count($catDB['items'])) foreach($catDB['items'] as $artDB){
				$artPath= self::getPathFromRoot(self::$storagePath."/$catId/{$artDB['id']}");
				echo "$artPath<br>";

				// *Удаляем черновики
				if(!empty($artDB['not-public'])) continue;

				$this->sitemap .= "<url>\n"
				. "<loc>" . (self::is('https')?'https':'http') . '://' . \HOST . "/$artPath</loc>\n"
				. "<lastmod>" . date ('Y-m-d', filemtime(\DR."/$artPath" . self::$l_cfg['ext'])) . "</lastmod>\n"
				. "<changefreq>weekly</changefreq>\n"
				. "<priority>0.7</priority>\n"
				. "</url>\n";

				// *RSS
				$itemContent = ($this->_addToRss(\DR."/$artPath" . self::$l_cfg['ext']));

				// echo "<hr>". htmlspecialchars($itemContent);

				$itemContent = '<item turbo="true">'
				. "\n<link>" . (self::is('https')?'https':'http') . '://' . \HOST . "/$artPath</link>\n"
				. "\n<turbo:content>\n<![CDATA[\n"
				. $itemContent
				. "\n]]>\n</turbo:content>\n"
				. "</item>\n\n";

				// echo "<hr>". htmlspecialchars($itemContent);

				$this->rss .= $itemContent;
			}

		} // foreach


		$this->sitemap .= "\n</urlset> ";
		$this->rss .= "\n</channel>\n</rss>";


		echo "<hr><h3>Sitemap</h3>". htmlspecialchars($this->sitemap) . "<hr>";
		echo "<hr><h3>RSS</h3>". htmlspecialchars($this->rss);
		echo '</pre>';

		file_put_contents($this->path, $this->sitemap);
		// !
		return;

		# Compress
		if( \SITEMAP['gzip'])
		{
			file_put_contents($this->path . '.gz', gzencode($this->sitemap,  \SITEMAP['gzip']));
		}


		file_put_contents($this->RSSpath, $this->rss);

		return $this->sitemap;
	} // build


	/**
	 * *Добавляем элемент в RSS
	 */
	private function _addToRss($artPathname)
	{
		$doc = new DOMDocument('1.0','utf-8');
		@$doc->loadHTMLFile($artPathname);

		$doc->normalizeDocument();

		$xpath= new \DOMXPath($doc);

		// $body= $xpath->query('//body/descendant::*');
		$scripts= $xpath->query('//body/descendant::script');

		foreach($scripts as $s){
			$s->parentNode->removeChild($s);
		}

		$body= $xpath->query('//body')->item(0);

		$xml= utf8_decode($doc->saveXML($body));

		return preg_replace('~<body>([\s\S]+)</body>~u', '$1', $xml, 1);
		// return utf8_decode($xml);
	}

} // SiteMap_RSS


// exit ((new SiteMap_RSS)->build());