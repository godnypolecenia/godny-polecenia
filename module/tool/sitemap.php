<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays a site map
 */

header('Content-Type:text/xml'); 
echo('<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

foreach($url -> getUrlList('index=1') as $r) {
	echo('<url><loc>'.SITE_ADDRESS.'/'.$r['url'].'</loc></url>');
}

echo('</urlset>');

?>