<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file contains a side menu
 */



/**
 *	Layout
 */

?>

<a href="#" id="left-toggle" class="button">Pokaż dodatkowe opcje</a>
<div id="left-container">
	<section>
		<h3>Twoje konto</h3>
		<ul class="ul">
			<?php if($user -> user_id > 0) { ?>
			<li><span class="ri-message-2-line icon"></span><a href="<?php echo($url -> getUrl('user/message-list')); ?>">Wiadomości<?php if($countNewMsg > 0) echo(' <span class="count">'.$countNewMsg.'</span>'); ?></a></li>
			<li><span class="ri-settings-2-line icon"></span> <?php echo($url -> getButton('user/manage')); ?></li>
			<li><span class="ri-money-dollar-circle-line icon"></span> <?php echo($url -> getButton('user/payment-list')); ?></li>
			<?php if($user -> type == 9) { ?><li><span class="ri-tools-fill icon"></span> <?php echo($url -> getButton('admin/index')); ?></li><?php } ?>
			<li><span class="ri-logout-box-r-line icon"></span> <?php echo($url -> getButton('user/logout')); ?></li>
			<?php } else { ?>
			<li><span class="ri-login-box-line icon"></span> <?php echo($url -> getButton('user/login')); ?></li>
			<li><span class="ri-user-add-line icon"></span> <?php echo($url -> getButton('user/register')); ?></li>
			<li><span class="ri-question-line icon"></span> <?php echo($url -> getButton('user/password')); ?></li>
			<?php } ?>
		</ul>
	</section>
	<?php if($user -> userId > 0) { ?>
	<section>
		<h3><?php echo(ITEM_LIST_TITLE); ?></h3>
		<ul class="ul">
			<li><span class="ri-add-circle-line icon"></span> <?php echo($url -> getButton('item/add')); ?></li>
			<li><span class="ri-money-dollar-circle-line icon"></span> <?php echo($url -> getButton('pricelist')); ?></li>
			<li><span class="ri-list-check icon"></span> <?php echo($url -> getButton('item/add-list')); ?></li>
			<li><span class="ri-message-2-line icon"></span> <?php echo($url -> getButton('item/vote-list')); ?></li>
		</ul>
	</section>
	<?php } ?>
	<?php if($setup -> block_3 <> '') echo('<div id="block-3">'.$setup -> block_3.'</div>'); ?>
</div>