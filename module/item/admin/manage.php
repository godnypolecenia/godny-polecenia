<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(2);

/**
 *	This file manages item settings
 */

if($url -> op(0) == URL_SAVE && is_array($_POST)) {
	foreach($_POST as $k => $v) {
		
		if($k == 'validity-price' || $k == 'validity-day' || $k == 'premium-price' || $k == 'premium-day') {
			$v = convertToNumber($v);
		}
		$setup -> $k = $v;
	}
	$main -> alertPrepare(true);
	$url -> redirect();
}

/**
 *	Add URL to history
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
require_once(INC_ADMIN_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('admin/index'));
$bc -> add($url -> getLink('item/admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-settings-2-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<div class="cols cols-3">
						<div class="col-item">
							<h3>Pakiet Darmowy</h3>
						</div>
						<div class="col-item">
							<h3>Pakiet Standard</h3>
						</div>
						<div class="col-item">
							<h3>Pakiet Premium</h3>
						</div>
					</div>
					<br>
					<div class="cols cols-3">
						<div class="col-item">
							<label>
								Cena [zł]
								<input type="text" name="free-price" disabled="disabled" value="0,00" required="required">
							</label>
						</div>
						<div class="col-item">
							<label>
								Cena [zł]
								<input type="text" name="validity-price" value="<?php echo(numberFormat($setup -> validity_price)); ?>" required="required">
							</label>
						</div>
						<div class="col-item">
							<label>
								Cena [zł]
								<input type="text" name="premium-price" value="<?php echo(numberFormat($setup -> premium_price)); ?>" required="required">
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<label>
								Czas [dni]
								<input type="text" name="free-day" disabled="disabled" value="Bez limitu" required="required">
							</label>
						</div>
						<div class="col-item">
							<label>
								Czas [dni]
								<input type="text" name="validity-day" value="<?php echo($setup -> validity_day); ?>" required="required">
							</label>
						</div>
						<div class="col-item">
							<label>
								Czas [dni]
								<input type="text" name="premium-day" value="<?php echo($setup -> premium_day); ?>" required="required">
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<label>
								Opis
								<textarea name="free-text"><?php echo($setup -> free_text); ?></textarea>
							</label>
						</div>
						<div class="col-item">
							<label>
								Opis
								<textarea name="validity-text"><?php echo($setup -> validity_text); ?></textarea>
							</label>
						</div>
						<div class="col-item">
							<label>
								Opis
								<textarea name="premium-text"><?php echo($setup -> premium_text); ?></textarea>
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-data" value="0">
							<label>
								<input type="checkbox" name="free-data" value="1"<?php if($setup -> free_data == 1) echo(' checked="checked"'); ?>>
								Dane firmy
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-data" value="0">
							<label>
								<input type="checkbox" name="validity-data" value="1"<?php if($setup -> validity_data == 1) echo(' checked="checked"'); ?>>
								Dane firmy
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-data" value="0">
							<label>
								<input type="checkbox" name="premium-data" value="1"<?php if($setup -> premium_data == 1) echo(' checked="checked"'); ?>>
								Dane firmy
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-logo" value="0">
							<label>
								<input type="checkbox" name="free-logo" value="1"<?php if($setup -> free_logo == 1) echo(' checked="checked"'); ?>>
								Logo
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-logo" value="0">
							<label>
								<input type="checkbox" name="validity-logo" value="1"<?php if($setup -> validity_logo == 1) echo(' checked="checked"'); ?>>
								Logo
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-logo" value="0">
							<label>
								<input type="checkbox" name="premium-logo" value="1"<?php if($setup -> premium_logo == 1) echo(' checked="checked"'); ?>>
								Logo
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-amenitie" value="0">
							<label>
								<input type="checkbox" name="free-amenitie" value="1"<?php if($setup -> free_amenitie == 1) echo(' checked="checked"'); ?>>
								Udogodnienia
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-amenitie" value="0">
							<label>
								<input type="checkbox" name="validity-amenitie" value="1"<?php if($setup -> validity_amenitie == 1) echo(' checked="checked"'); ?>>
								Udogodnienia
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-amenitie" value="0">
							<label>
								<input type="checkbox" name="premium-amenitie" value="1"<?php if($setup -> premium_amenitie == 1) echo(' checked="checked"'); ?>>
								Udogodnienia
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-map" value="0">
							<label>
								<input type="checkbox" name="free-map" value="1"<?php if($setup -> free_map == 1) echo(' checked="checked"'); ?>>
								Mapa
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-map" value="0">
							<label>
								<input type="checkbox" name="validity-map" value="1"<?php if($setup -> validity_map == 1) echo(' checked="checked"'); ?>>
								Mapa
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-map" value="0">
							<label>
								<input type="checkbox" name="premium-map" value="1"<?php if($setup -> premium_map == 1) echo(' checked="checked"'); ?>>
								Mapa
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-contact-form" value="0">
							<label>
								<input type="checkbox" name="free-contact-form" value="1"<?php if($setup -> free_contact_form == 1) echo(' checked="checked"'); ?>>
								Formularz kontaktowy
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-contact-form" value="0">
							<label>
								<input type="checkbox" name="validity-contact-form" value="1"<?php if($setup -> validity_contact_form == 1) echo(' checked="checked"'); ?>>
								Formularz kontaktowy
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-contact-form" value="0">
							<label>
								<input type="checkbox" name="premium-contact-form" value="1"<?php if($setup -> premium_contact_form == 1) echo(' checked="checked"'); ?>>
								Formularz kontaktowy
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-gallery" value="0">
							<label>
								<input type="checkbox" name="free-gallery" value="1"<?php if($setup -> free_gallery == 1) echo(' checked="checked"'); ?>>
								Galeria zdjęć
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-gallery" value="0">
							<label>
								<input type="checkbox" name="validity-gallery" value="1"<?php if($setup -> validity_gallery == 1) echo(' checked="checked"'); ?>>
								Galeria zdjęć
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-gallery" value="0">
							<label>
								<input type="checkbox" name="premium-gallery" value="1"<?php if($setup -> premium_gallery == 1) echo(' checked="checked"'); ?>>
								Galeria zdjęć
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-social" value="0">
							<label>
								<input type="checkbox" name="free-social" value="1"<?php if($setup -> free_social == 1) echo(' checked="checked"'); ?>>
								Udostępnienie
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-social" value="0">
							<label>
								<input type="checkbox" name="validity-social" value="1"<?php if($setup -> validity_social == 1) echo(' checked="checked"'); ?>>
								Udostępnienie
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-social" value="0">
							<label>
								<input type="checkbox" name="premium-social" value="1"<?php if($setup -> premium_social == 1) echo(' checked="checked"'); ?>>
								Udostępnienie
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-pricelist" value="0">
							<label>
								<input type="checkbox" name="free-pricelist" value="1"<?php if($setup -> free_pricelist == 1) echo(' checked="checked"'); ?>>
								Cennik
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-pricelist" value="0">
							<label>
								<input type="checkbox" name="validity-pricelist" value="1"<?php if($setup -> validity_pricelist == 1) echo(' checked="checked"'); ?>>
								Cennik
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-pricelist" value="0">
							<label>
								<input type="checkbox" name="premium-pricelist" value="1"<?php if($setup -> premium_pricelist == 1) echo(' checked="checked"'); ?>>
								Cennik
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-services" value="0">
							<label>
								<input type="checkbox" name="free-services" value="1"<?php if($setup -> free_services == 1) echo(' checked="checked"'); ?>>
								Usługi
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-services" value="0">
							<label>
								<input type="checkbox" name="validity-services" value="1"<?php if($setup -> validity_services == 1) echo(' checked="checked"'); ?>>
								Usługi
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-services" value="0">
							<label>
								<input type="checkbox" name="premium-services" value="1"<?php if($setup -> premium_services == 1) echo(' checked="checked"'); ?>>
								Usługi
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-time" value="0">
							<label>
								<input type="checkbox" name="free-time" value="1"<?php if($setup -> free_time == 1) echo(' checked="checked"'); ?>>
								Godziny otwarcia
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-time" value="0">
							<label>
								<input type="checkbox" name="validity-time" value="1"<?php if($setup -> validity_time == 1) echo(' checked="checked"'); ?>>
								Godziny otwarcia
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-time" value="0">
							<label>
								<input type="checkbox" name="premium-time" value="1"<?php if($setup -> premium_time == 1) echo(' checked="checked"'); ?>>
								Godziny otwarcia
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-stat" value="0">
							<label>
								<input type="checkbox" name="free-stat" value="1"<?php if($setup -> free_stat == 1) echo(' checked="checked"'); ?>>
								Raporty
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-stat" value="0">
							<label>
								<input type="checkbox" name="validity-stat" value="1"<?php if($setup -> validity_stat == 1) echo(' checked="checked"'); ?>>
								Raporty
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-stat" value="0">
							<label>
								<input type="checkbox" name="premium-stat" value="1"<?php if($setup -> premium_stat == 1) echo(' checked="checked"'); ?>>
								Raporty
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-mailing" value="0">
							<label>
								<input type="checkbox" name="free-mailing" value="1"<?php if($setup -> free_mailing == 1) echo(' checked="checked"'); ?>>
								Mailing
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-mailing" value="0">
							<label>
								<input type="checkbox" name="validity-mailing" value="1"<?php if($setup -> validity_mailing == 1) echo(' checked="checked"'); ?>>
								Mailing
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-mailing" value="0">
							<label>
								<input type="checkbox" name="premium-mailing" value="1"<?php if($setup -> premium_mailing == 1) echo(' checked="checked"'); ?>>
								Mailing
							</label>
						</div>
					</div>
					<div class="cols cols-3">
						<div class="col-item">
							<input type="hidden" name="free-position" value="0">
							<label>
								<input type="checkbox" name="free-position" value="1"<?php if($setup -> free_position == 1) echo(' checked="checked"'); ?>>
								Promowana pozycja
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="validity-position" value="0">
							<label>
								<input type="checkbox" name="validity-position" value="1"<?php if($setup -> validity_position == 1) echo(' checked="checked"'); ?>>
								Promowana pozycja
							</label>
						</div>
						<div class="col-item">
							<input type="hidden" name="premium-position" value="0">
							<label>
								<input type="checkbox" name="premium-position" value="1"<?php if($setup -> premium_position == 1) echo(' checked="checked"'); ?>>
								Promowana pozycja
							</label>
						</div>
					</div>
					<div class="buttons">
						<input type="submit" value="Zapisz zmiany">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>