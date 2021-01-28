<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	TThis file initiates CRON tasks
 */

echo(file_get_contents(SITE_ADDRESS.'/cron-exec.php'));

?>