<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file allows you to download a file
 */

$file = $url -> op(0);

header('Content-Type: '.mime_content_type('./data/file/'.$file));
header('Content-Disposition:attachment;filename="'.$file.'"');
readfile('./data/file/'.$file);

?>