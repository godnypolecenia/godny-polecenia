<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	Layout
 */

if($user -> power <> '') {
	$power = explode(';', $user -> power);
} else {
	$power = [1, 1, 1, 1, 1, 1, 1, 1];
}

?>

<?php if($power[0] == 1 || $power[1] == 1 || $power[2] == 1) {  ?>
<section>
	<h2>Firmy</h2>
	<ul class="ul">
		<?php if($power[0] == 1) {  ?><li><span class="ri-database-2-line icon"></span> <?php echo($url -> getButton('item/admin/index', false, null, ['count' => $itemCount])); ?></li><?php } ?>
		<?php if($power[0] == 1) {  ?><li><span class="ri-chat-new-fill icon"></span> <?php echo($url -> getButton('item/admin/index', false, '/'.URL_BOOKMARK.'-2', ['count' => $itemDeactiveCount], 'Firmy do aktywacji')); ?></li><?php } ?>
		<?php if($power[1] == 1) {  ?><li><span class="ri-star-fill icon"></span> <?php echo($url -> getButton('item/admin/vote-list', false, null, ['count' => $voteCount])); ?></li><?php } ?>
		<?php if($power[1] == 1) {  ?><li><span class="ri-chat-new-fill icon"></span> <?php echo($url -> getButton('item/admin/vote-list', false, '/'.URL_BOOKMARK.'-2', ['count' => $voteDeactiveCount], 'Oceny do aktywacji')); ?></li><?php } ?>
		<?php if($power[2] == 1) {  ?><li><span class="ri-settings-2-line icon"></span> <?php echo($url -> getButton('item/admin/manage')); ?></li><?php } ?>
		<?php if($power[2] == 1) {  ?><li><span class="ri-folders-line icon"></span> <?php echo($url -> getButton('item/admin/category-list')); ?></li><?php } ?>
		<?php if($power[2] == 1) {  ?><li><span class="ri-stack-line icon"></span> <?php echo($url -> getButton('item/admin/feature-list')); ?></li><?php } ?>
		<?php if($power[2] == 1) {  ?><li><span class="ri-stack-line icon"></span> <?php echo($url -> getButton('item/admin/amenitie-list')); ?></li><?php } ?>
	</ul>
</section>
<?php } ?>
<?php if($power[3] == 1 || $power[4] == 1 || $power[5] == 1) {  ?>
<section>
	<h2>Użytkownicy</h2>
	<ul class="ul">
		<?php if($power[3] == 1) {  ?><li><span class="ri-user-line icon"></span> <?php echo($url -> getButton('user/admin/index', false, null, ['count' => $userCount])); ?></li><?php } ?>
		<?php if($power[4] == 1) {  ?><li><span class="ri-money-dollar-circle-line icon"></span> <?php echo($url -> getButton('user/admin/payment-list')); ?></li><?php } ?>
		<?php if($power[4] == 1) {  ?><li><span class="ri-close-circle-line icon"></span> <?php echo($url -> getButton('user/admin/ban-list')); ?></li><?php } ?>
		<?php if($power[5] == 1) {  ?><li><span class="ri-mail-line icon"></span> <?php echo($url -> getButton('user/admin/newsletter')); ?></li><?php } ?>
		<?php if($power[5] == 1) {  ?><li><span class="ri-mail-line icon"></span> <?php echo($url -> getButton('user/admin/newsletter-list')); ?></li><?php } ?>
	</ul>
</section>
<?php } ?>
<?php if($power[6] == 1) {  ?>
<section>
	<h2>Treści</h2>
	<ul class="ul">
		<?php if($power[6] == 1) {  ?><li><span class="ri-file-3-line icon"></span> <?php echo($url -> getButton('page/admin/index')); ?></li><?php } ?>
		<?php if($power[6] == 1) {  ?><li><span class="ri-file-3-line icon"></span> <?php echo($url -> getButton('admin/setup/text')); ?></li><?php } ?>
	</ul>
</section>
<?php } ?>
<?php if($power[7] == 1) { ?>
<section>
	<h2>Ustawienia</h2>
	<ul class="ul">
		<?php if($power[7] == 1) {  ?><li><span class="ri-search-line icon"></span> <?php echo($url -> getButton('admin/setup/seo')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-map-2-line icon"></span> <?php echo($url -> getButton('admin/setup/url-list')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-facebook-circle-line icon"></span> <?php echo($url -> getButton('admin/setup/social-media')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-mail-line icon"></span> <?php echo($url -> getButton('admin/setup/e-mail')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-slideshow-2-line icon"></span> <?php echo($url -> getButton('admin/setup/slider-list')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-tv-line icon"></span> <?php echo($url -> getButton('admin/setup/ad-list')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-image-line icon"></span> <?php echo($url -> getButton('admin/setup/image')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-code-s-slash-line icon"></span> <?php echo($url -> getButton('admin/setup/other')); ?></li><?php } ?>
	</ul>
</section>
<section>
	<h2>Dane</h2>
	<ul class="ul">
		<?php if($power[7] == 1) {  ?><li><span class="ri-image-line icon"></span> <?php echo($url -> getButton('admin/data/image-list')); ?></li><?php } ?>
		<?php if($power[7] == 1) {  ?><li><span class="ri-database-2-line icon"></span> <?php echo($url -> getButton('admin/data/file-list')); ?></li><?php } ?>
	</ul>
</section>
<?php } ?>
<section>
	<h2>Twoje konto</h2>
	<ul class="ul">
		<li><span class="ri-user-line icon"></span> <?php echo($url -> getButton('admin/manage')); ?></li>
		<li><span class="ri-logout-box-r-line icon"></span> <?php echo($url -> getButton('admin/logout')); ?></li>
	</ul>
</section>
