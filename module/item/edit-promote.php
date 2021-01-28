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
 *	
 */

$editItem = new Item($url -> op(0));
if(!($editItem -> itemId > 0)) {
	$url -> redirect(404);
}

if($url -> op(1) == URL_SEND) {
	$price = $setup -> premium_price;
	if($_POST['pack'] == 1) $price = $setup -> validity_price;
	if($_POST['pack'] == 2) $price = $setup -> premium_price;
	
	$newPay = new Payment;
	$newPayId = $newPay -> add($user -> userId, $price, $_POST['pack'], $editItem -> itemId, $_POST['firstname'], $_POST['lastname'], $_POST['city'], $_POST['postcode'], $_POST['address'], $_POST['email'], str_replace(['-', ' '], '', $_POST['nip']));
	if($newPayId > 0) {
		$newPay -> type = 1;
		$newPay -> item_id = $editItem -> item_id;
		
		if($_POST['nip'] <> '') {
			$user -> nip = $_POST['nip'];
		}
		
		$url -> redirect('user/payment', false, '/'.$newPayId);
	} else {
		$vd -> putError(false, FORM_ERR_UNKNOWN);
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

/**
 *
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
$bc -> add($url -> getLink('user/index'));
$bc -> add($url -> getLink('item/add-list'));
$bc -> add($url -> getLink('item/edit', true));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<?php if($editItem -> validity < time() && $editItem -> premium < time()) echo('<section class="alert">Pamiętaj, że pełne dane wizytówki wyświetlane są w płatnych pakietach. Darmowa wersja zawiera jedynie okrojonny widok.</section>'); ?>
			
			<?php require_once('./module/item/bookmark.php'); ?>
			<section>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SEND)); ?>">
					<h1><span class="ri-thumb-up-line icon"></span><?php echo($meta['title']); ?></h1>
					<?php if($editItem -> premium > time()) { ?>
					<section class="alert alert-success">
						<p>Pakiet Premium jest aktywny do <?php echo(dateTimeFormat($editItem -> premium)); ?></p>
					</section>
					<?php } elseif($editItem -> validity > time()) { ?>
					<section class="alert alert-success">
						<p>Pakiet Standard jest aktywny do <?php echo(dateTimeFormat($editItem -> validity)); ?></p>
					</section>
					<?php } else { ?>
					<section class="alert alert-success">
						<p>Twoja wizytówka posiada darmowy pakiet.</p>
					</section>
					<?php } ?>
					<section>
						<label class="bold"><input type="radio" name="pack" value="2" checked="checked"> Pakiet Premium</label>
						<?php if($setup -> premium_text <> '') echo('<p>'.textFormat($setup -> premium_text).'</p><br>'); ?>
						<p>Okres: <span class="bold"><?php echo($setup -> premium_day.' '.inflect($setup -> premium_day, ['dzień', 'dni', 'dni'])); ?></span><br>Cena: <span class="bold"><?php echo(priceFormat($setup -> premium_price)); ?></span></p>
					</section>
					<section>
						<label class="bold"><input type="radio" name="pack" value="1"> Pakiet Standard</label>
						<?php if($setup -> validity_text <> '') echo('<p>'.textFormat($setup -> validity_text).'</p><br>'); ?>
						<p>Okres: <span class="bold"><?php echo($setup -> validity_day.' '.inflect($setup -> validity_day, ['dzień', 'dni', 'dni'])); ?></span><br>Cena: <span class="bold"><?php echo(priceFormat($setup -> validity_price)); ?></span></p>
					</section>
					<section>
						<label class="bold"><input type="radio" name="pack" value="0" disabled="disabled"> Pakiet Darmowy</label>
						<?php if($setup -> free_text <> '') echo('<p>'.textFormat($setup -> free_text).'</p><br>'); ?>
						<p>Okres: <span class="bold">bez limitu</span><br>Cena: <span class="bold">gratis</span></p>
					</section>
					<h3><span class="ri-user-line icon"></span>Dane płatnika</h3>
				
					<div class="cols cols-3">
						<label>
							Imię
							<input type="text" name="firstname" value="<?php echo($user -> firstname); ?>" required="required">
						</label>
						<label>
							Nazwisko
							<input type="text" name="lastname" value="<?php echo($user -> lastname); ?>" required="required">
						</label>
						<label>
							Adres e-mail
							<input type="text" name="email" value="<?php echo($user -> email); ?>" required="required">
						</label>
					</div>
					<div class="cols cols-3">
						<label>
							Miejscowość
							<input type="text" name="city" value="<?php echo($user -> city); ?>" required="required">
						</label>
						<label>
							Kod pocztowy
							<input type="text" name="postcode" value="<?php echo($user -> postcode); ?>" required="required">
						</label>
						<label>
							Adres
							<input type="text" name="address" value="<?php echo($user -> address); ?>" required="required">
						</label>
					</div>
					<label>
						NIP
						<input type="text" name="nip" value="<?php echo($user -> nip); ?>" placeholder="Numer NIP jest wymagany jeżeli chcesz otrzymać fakturę VAT">
					</label>
					<div class="buttons">
						<input type="submit" value="Kupuję i płacę">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>