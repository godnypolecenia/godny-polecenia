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

<?php if($editItem -> itemId > 0 && $editItem -> status == 0) { ?>
<section class="alert alert-success">
	<h2>Jeszcze chwilkę...</h2>
	<p>Twoja firma oczekuje na akceptację przez administratora.</p>
</section>
<?php } ?>

<ul class="bookmark">
	<li><?php echo($url -> getButton('item/edit', false, '/'.$editItem -> itemId)); ?></li>
	<li><?php echo($url -> getButton('item/edit-time', false, '/'.$editItem -> itemId)); ?></li>
	<li><?php echo($url -> getButton('item/edit-gallery', false, '/'.$editItem -> itemId)); ?></li>
	<li><?php echo($url -> getButton('item/edit-amenitie', false, '/'.$editItem -> itemId)); ?></li>
	<li><?php echo($url -> getButton('item/edit-services', false, '/'.$editItem -> itemId)); ?></li>
	<li><?php echo($url -> getButton('item/edit-banner', false, '/'.$editItem -> itemId)); ?></li>
	<li><?php echo($url -> getButton('item/edit-promote', false, '/'.$editItem -> itemId)); ?></li>
</ul>