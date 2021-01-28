<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(7);

/**
 *	This file manages other settings
 */

if($url -> op(0) == URL_SAVE && is_array($_POST)) {
	foreach($_POST as $k => $v) {
		$setup -> $k = $v;
	}
	$main -> alertPrepare(true);
	$url -> redirect();
}

if($url -> op(0) == URL_ADD && isset($_FILES)) {
	$ex = explode('.', $_FILES['file']['name']);
	$new_file = './favicon.ico';
	if($check = getimagesize($_FILES['file']['tmp_name'])) {
		if(!file_exists($new_file)) {
			if($_FILES['file']['size'] < 100000000000) {
				if(in_array($_FILES['file']['type'], array('image/x-icon', 'image/ico', 'image/png'))) {
					if(move_uploaded_file($_FILES['file']['tmp_name'], $new_file)) {
						$main -> alertPrepare(true);
						$url -> redirect();
					} else {
						$main -> alertPrepare(false, FILE_ERR_UPLOAD);
					}
				} else {
					$main -> alertPrepare(false, FILE_ERR_FORMAT);
				}
			} else {
				$main -> alertPrepare(false, FILE_ERR_WEIGHT);
			}
		} else {
			$main -> alertPrepare(false, FILE_ERR_SEND);
		}
	} else {
		$main -> alertPrepare(false, FILE_ERR_NULL);
	}
}

if($url -> op(0) == URL_DEL) {
	if(file_exists('./favicon.ico')) unlink('./favicon.ico');
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
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section class="toggle">
				<h2><span class="ri-flask-line icon"></span>Ustawienia developerskie</h2>
				<section>
					<p>Tryb developerski wyłącza zabezpieczenie Captcha przy autoryzacji użytkownika; przełącza tryb płatności na testowy; wyłącza zapisywanie w pamięci podręcznych plików CSS i JS  - co powoduje dłuższe ładowanie strony.</p>
				</section>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<input type="hidden" name="developer" value="0">
					<label>
						<input type="checkbox" name="developer" value="1"<?php if($setup -> developer == 1) echo(' checked="checked"'); ?>>
						Włącz tryb developerski
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-calendar-line icon"></span>Format daty i czasu</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<div class="cols cols-2">
						<label>
							Format daty
							<input type="text" name="format-date" required="required" placeholder="Wytyczne: http://php.net/date" value="<?php echo($setup -> format_date); ?>">
						</label>
						<label>
							Format czasu
							<input type="text" name="format-time" required="required" placeholder="Wytyczne: http://php.net/date" value="<?php echo($setup -> format_time); ?>">
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-numbers-line icon"></span>Format liczb</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<div class="cols cols-3">
						<label>
							Ilość miejsc po przecinku
							<input type="text" name="format-number" required="required" value="<?php echo($setup -> format_number); ?>">
						</label>
						<label>
							Separator miejca dziesiętnego
							<input type="text" name="format-point" required="required" value="<?php echo($setup -> format_point); ?>">
						</label>
						<label>
							Separator tysięcy
							<input type="text" name="format-sep" value="<?php echo($setup -> format_sep); ?>">
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-money-dollar-circle-line icon"></span>Format ceny</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<div class="cols cols-3">
						<label>
							Ilość miejsc po przecinku
							<input type="text" name="format-price-number" required="required" value="<?php echo($setup -> format_price_number); ?>">
						</label>
						<label>
							Separator miejca dziesiętnego
							<input type="text" name="format-price-point" required="required" value="<?php echo($setup -> format_price_point); ?>">
						</label>
						<label>
							Separator tysięcy
							<input type="text" name="format-price-sep" value="<?php echo($setup -> format_price_sep); ?>">
						</label>
					</div>
					<div class="cols cols-2">
						<label>
							Oznaczenie waluty
							<input type="text" name="format-price-currency" required="required" value="<?php echo($setup -> format_price_currency); ?>">
						</label>
						<label>
							Miejsce oznaczenia waluty
							<select name="format-price-position">
								<option value="0"<?php if($setup -> format_price_position == 0) echo(' selected="selected"'); ?>>Przed kwotą</option>
								<option value="1"<?php if($setup -> format_price_position == 1) echo(' selected="selected"'); ?>>Za kwotą</option>
							</select>
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-code-s-slash-line icon"></span>Kody usług</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Kod Google Analytics
						<textarea name="code-analytics" placeholder="Kod statystyk Google lub innych"><?php echo($setup -> code_analytics); ?></textarea>
					</label>
					<label>
						Kod Facebook SDK
						<textarea name="code-facebook" placeholder="Kod inicjacji Facebooka"><?php echo($setup -> code_facebook); ?></textarea>
					</label>
					<label>
						Znaczniki Metadanych
						<textarea name="code-meta" placeholder="Kod umieszczany w <head>"><?php echo($setup -> code_meta); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-image-line icon"></span>Favicon</h2>
				<?php if(file_exists('./favicon.ico')) echo('<img src="./favicon.ico?rand='.rand(0, 9999).'" alt=""> <a href="'.$url -> getUrl(null, false, '/'.URL_DEL).'" class="underline">Usuń</a><hr>'); ?>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>" enctype="multipart/form-data">
					<label>
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>