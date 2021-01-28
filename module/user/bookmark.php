<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */
 
if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyUser();

/**
 *	Layout
 */

?>

<ul class="bookmark">
	<?php if($mobile == 1) echo('<li class="bookmark-slide"><span class="ri-arrow-left-right-line"></span></li>'); ?>
	<li><?php echo($url -> getButton('user/manage')); ?></li>
	<li><?php echo($url -> getButton('user/manage-email')); ?></li>
	<li><?php echo($url -> getButton('user/manage-password')); ?></li>
	<li><?php echo($url -> getButton('user/manage-avatar')); ?></li>
</ul>