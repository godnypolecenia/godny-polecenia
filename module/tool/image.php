<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays images
 */
 
if(empty($url -> op(0))) {
	exit;
}

$ex = explode('.', $url -> op(0));

$image = new Image($url -> op(0));
if(count($ex) == 3) {
	preg_match('@([0-9]+)x([0-9]+)@', $ex[1], $out);
	$image -> size($out[1], $out[2]);
}

$image -> output();

?>