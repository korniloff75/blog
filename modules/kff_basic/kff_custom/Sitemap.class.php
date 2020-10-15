<?php

class Sitemap extends BlogKff
{
	public static
		$test = 0,
		$createGzip = false,
		$path= \DR.'/sitemap.xml',
		$RSSpath= \DR.'/turbo.rss.xml';

	private static
	// *Защита от повторных вызовов
		$mapCreated= 0;

	private
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
		if(!self::$modDir)
			parent::__construct();

		$this->rss.= '<title>' . $GLOBALS['Config']->header . '</title>
		<link>' . (self::is('https')?'https':'http') . '://' . \HOST . '</link>
		<description>' . $GLOBALS['Config']->slogan . '</description>
		<language>' . 'ru' . '</language>
		<turbo:analytics type="LiveInternet"></turbo:analytics>'."\n";

		$cond= self::is_adm() && (
			!self::$mapCreated
			|| !file_exists(self::$path)
			|| !file_exists(self::$RSSpath)
			// || (ceil(time() - filemtime(self::$path))/3600/24 > \SITEMAP['expires'])
		);

		self::$log->add(__METHOD__,null,[
			'$cond'=>$cond,
			// '$GLOBALS[\'Config\']'=>$GLOBALS['Config'],
		]);

		if($cond){
			$this->build($map);
			self::$mapCreated= 1;
		}
	} // __construct

	public static function test()
	{
		self::$test= 1;
		self::$log->add(__METHOD__. 'run as test', Logger::BACKTRACE);
		unlink(self::$path);
		ob_start();
		echo '<pre>';
		$map= self::_createBlogMap(1);
		echo '</pre>';
		ob_end_clean();
		return $map;
	}


	public function build(DbJSON &$map)
	{
		foreach($map as $ind=>$catData) {
		// foreach($map as $num=>&$catData) {
			$catId= &$catData['id'];
			$catName= &$catData['name'];

			echo "<h3>[$ind] - $catId - $catName</h3>";
			// echo "<p>".\HOST."</p>";
			// var_dump($catData);

			// *Перебор статей в категории
			if(count($catData['items'])) foreach($catData['items'] as $artData)
			{
				$artPath= self::getPathFromRoot(self::$storagePath."/$catId/{$artData['id']}");
				echo "$artPath<br>";

				// *Удаляем черновики
				if(filter_var($artData['not-public'],FILTER_VALIDATE_BOOLEAN)) continue;

				$artData['ts'] = filemtime(\DR."/$artPath" . self::$l_cfg['ext']);
				$artData['date'] = date (self::DATE_FORMAT, $artData['ts']);

				$this->sitemap .= "<url>\n"
				. "<loc>" . (self::is('https')?'https':'http') . '://' . \HOST . "/$artPath</loc>\n"
				. "<lastmod>{$artData['date']}</lastmod>\n"
				. "<changefreq>weekly</changefreq>\n"
				. "<priority>0.7</priority>\n"
				. "</url>\n";

				// *RSS
				ob_start();
				include \DR."/$artPath" . self::$l_cfg['ext'];
				// $itemContent = ($this->_addToRss(\DR."/$artPath" . self::$l_cfg['ext']));
				$itemContent = $this->_addToRss(ob_get_clean());

				// echo "<hr>". htmlspecialchars($itemContent);
				// echo "<h4>\$artData</h4>";
				// var_dump($artData);

				$this->rss .= "\n".'<item turbo="true">'
				. "\n<link>" . (self::is('https')?'https':'http') . '://' . \HOST . "/$artPath</link>\n"
				. "<pubDate>". date ('r', $artData['ts']) ."</pubDate>"
				. "\n<turbo:content>\n<![CDATA[\n"
				. "<header>\n<h1>".($artData['title'] ?? $artData['name'])."</h1>\n</header>\n"
				. $itemContent
				. "\n]]>\n</turbo:content>\n"
				. "</item>\n";

				// echo "<hr>". htmlspecialchars($itemContent);
			} //foreach

		} // foreach


		$this->sitemap .= "\n</urlset> ";
		$this->rss .= "\n</channel>\n</rss>";


		echo "<hr><h3>Sitemap</h3>". htmlspecialchars($this->sitemap) . "<hr>";
		echo "<hr><h3>RSS</h3>". htmlspecialchars($this->rss);

		file_put_contents(self::$path, $this->sitemap);

		//* Compress
		if( self::$createGzip )
		{
			file_put_contents(self::$path . '.gz', gzencode($this->sitemap));
		}


		file_put_contents(self::$RSSpath, $this->rss);

		return $this->sitemap;
	} // build


	/**
	 * *Добавляем элемент в RSS
	 */
	private function _addToRss($artContent)
	{
		$doc = new DOMDocument('1.0','utf-8');
		@$doc->loadHTML($artContent);

		$doc->normalizeDocument();

		$xpath= new \DOMXPath($doc);

		// $body= $xpath->query('//body/descendant::*');

		foreach($xpath->query('//script') as $s){
			$s->parentNode->removeChild($s);
		}

		foreach($xpath->query('//img[@data-src]') as $i){
			$i->setAttribute('src', $i->getAttribute('data-src'));
			$i->removeAttribute('data-src');
			if($i->hasAttribute('uk-img')) $i->removeAttribute('uk-img');
		}

		$body= $xpath->query('//body')->item(0);

		$xml= utf8_decode($doc->saveXML($body));
		$xml= str_replace(']]','',$xml);

		return preg_replace('~<body>([\s\S]+)</body>~u', '$1', $xml, 1);
		// return utf8_decode($xml);
	}

} // SiteMap_RSS


// exit ((new SiteMap_RSS)->build());