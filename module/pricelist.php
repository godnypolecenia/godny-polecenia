<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays the price list
 */

$url -> addBackUrl();

/**
 *	Layout
 */
$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getUrl(), $meta['title']);
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php echo($setup -> pricelist); ?>
			<?php /*<div class="cols cols-3">
				<section class="col-item pricelist">
					<h2>Darmowy</h2>
					<ul>
						<li><span class="<?php echo(($setup -> free_data == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Dane firmy</li>
						<li><span class="<?php echo(($setup -> free_logo == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Logo</li>
						<li><span class="<?php echo(($setup -> free_amenitie == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Udogodnienia</li>
						<li><span class="<?php echo(($setup -> free_map == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Mapa</li>
						<li><span class="<?php echo(($setup -> free_contact_form == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Formularz kontaktowy</li>
						<li><span class="<?php echo(($setup -> free_gallery == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Galeria zdjęć</li>
						<li><span class="<?php echo(($setup -> free_social == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Udostępnianie</li>
						<li><span class="<?php echo(($setup -> free_pricelist == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Cennik</li>
						<li><span class="<?php echo(($setup -> free_services == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Usługi</li>
						<li><span class="<?php echo(($setup -> free_time == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Godziny otwarcia</li>
						<li><span class="<?php echo(($setup -> free_stat == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Raporty</li>
						<li><span class="<?php echo(($setup -> free_mailing == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Mailing</li>
						<li><span class="<?php echo(($setup -> free_position == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Promowana pozycja</li>
					</ul>
					<?php if($user -> user_id > 0) { ?>
					<strong>Gratis</strong>
					<a href="<?php echo($url -> getUrl('item/add')); ?>" class="underline">Dodaj firmę</a>
					<?php } else { ?>
					<a href="<?php echo($url -> getUrl('item/add')); ?>" class="button">Dodaj firmę</a>
					<?php } ?>
				</section>
				<section class="col-item pricelist">
					<h2>Standard</h2>
					<ul>
						<li><span class="<?php echo(($setup -> validity_data == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Dane firmy</li>
						<li><span class="<?php echo(($setup -> validity_logo == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Logo</li>
						<li><span class="<?php echo(($setup -> validity_amenitie == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Udogodnienia</li>
						<li><span class="<?php echo(($setup -> validity_map == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Mapa</li>
						<li><span class="<?php echo(($setup -> validity_contact_form == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Formularz kontaktowy</li>
						<li><span class="<?php echo(($setup -> validity_gallery == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Galeria zdjęć</li>
						<li><span class="<?php echo(($setup -> validity_social == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Udostępnianie</li>
						<li><span class="<?php echo(($setup -> validity_pricelist == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Cennik</li>
						<li><span class="<?php echo(($setup -> validity_services == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Usługi</li>
						<li><span class="<?php echo(($setup -> validity_time == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Godziny otwarcia</li>
						<li><span class="<?php echo(($setup -> validity_stat == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Raporty</li>
						<li><span class="<?php echo(($setup -> validity_mailing == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Mailing</li>
						<li><span class="<?php echo(($setup -> validity_position == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Promowana pozycja</li>
					</ul>
					<?php if($user -> user_id > 0) { ?>
					<strong><?php echo(priceFormat($setup -> validity_price)); ?></strong>
					<a href="<?php echo($url -> getUrl('item/add')); ?>" class="underline">Dodaj firmę</a>
					<?php } else { ?>
					<a href="<?php echo($url -> getUrl('item/add')); ?>" class="button">Dodaj firmę</a>
					<?php } ?>
				</section>
				<section class="col-item pricelist">
					<h2>Premium</h2>
					<ul>
						<li><span class="<?php echo(($setup -> premium_data == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Dane firmy</li>
						<li><span class="<?php echo(($setup -> premium_logo == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Logo</li>
						<li><span class="<?php echo(($setup -> premium_amenitie == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Udogodnienia</li>
						<li><span class="<?php echo(($setup -> premium_map == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Mapa</li>
						<li><span class="<?php echo(($setup -> premium_contact_form == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Formularz kontaktowy</li>
						<li><span class="<?php echo(($setup -> premium_gallery == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Galeria zdjęć</li>
						<li><span class="<?php echo(($setup -> premium_social == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Udostępnianie</li>
						<li><span class="<?php echo(($setup -> premium_pricelist == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Cennik</li>
						<li><span class="<?php echo(($setup -> premium_services == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Usługi</li>
						<li><span class="<?php echo(($setup -> premium_time == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Godziny otwarcia</li>
						<li><span class="<?php echo(($setup -> premium_stat == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Raporty</li>
						<li><span class="<?php echo(($setup -> premium_mailing == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Mailing</li>
						<li><span class="<?php echo(($setup -> premium_position == 1)? 'ri-checkbox-circle-line green' : 'ri-checkbox-blank-circle-line'); ?>"></span> Promowana pozycja</li>
					</ul>
					<?php if($user -> user_id > 0) { ?>
					<strong><?php echo(priceFormat($setup -> premium_price)); ?></strong>
					<a href="<?php echo($url -> getUrl('item/add')); ?>" class="underline">Dodaj firmę</a>
					<?php } else { ?>
					<a href="<?php echo($url -> getUrl('item/add')); ?>" class="button">Dodaj firmę</a>
					<?php } ?>
				</section>
			</div>*/ ?>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>