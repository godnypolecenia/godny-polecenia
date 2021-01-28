<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays the RSS feed
 */

if($setup -> rss == 0) exit;

header('Content-Type:text/xml'); 
echo('<rss version="2.0">');
echo('<channel>');
echo('<title>'.$setup -> title.'</title>');
echo('<link>'.SITE_ADDRESS.'</link>');
echo('<description>'.$setup -> description.'</description>');
echo('<language>'.$selectedLang.'</language>');

foreach($url -> getUrlList('feed=1') as $r) {
	echo('<item>');
	echo('<title>'.$r['title'].'</title>');
	echo('<pubDate>'.date('D, d M Y H:i:s T', $r['date']).'</pubDate>');
	echo('<link>'.SITE_ADDRESS.'/'.$r['url'].'</link>');
	echo('<description>'.$r['description'].'</description>');
	echo('</item>');
}

echo('</channel>');
echo('</rss>');

?>