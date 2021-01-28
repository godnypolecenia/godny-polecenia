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
 *	This file allows you to edit the user account
 */

/**
 *	Save account settings
 */
if($url -> op(0) == URL_SAVE) {
	$vd = new Validate;
	$vd -> isValue($_POST['name'], 'Nazwa firmy lub imię i nazwisko');
	
	if($vd -> pass() == true) {	
		$user -> name = $_POST['name'];
		//$user -> region = $_POST['region'];
		//$user -> city = $_POST['city'];
		//$user -> postcode = $_POST['postcode'];
		//$user -> address = $_POST['address'];
		//$user -> nip = $_POST['nip'];
		
		$main -> alertPrepare(true);
		$url -> redirect();
	}
	
	$url -> setOpd(URL_OPEN, URL_DATA);
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

/**
 *
 */
 
if(!($url -> opd(URL_OPEN) <> '')) $url -> setOpd(URL_OPEN, URL_DATA);
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
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>	
			<?php require_once('./module/user/bookmark.php'); ?>
			<section>
				<h1><span class="ri-settings-2-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Nazwa
						<input type="text" name="name" placeholder="Nick lub imię i nazwisko" required="required" value="<?php echo($user -> name); ?>">
					</label>
					<?php /*<div class="cols cols-4">
						<label>
							Województwo
							<select name="region">
								<option value="0">Wybierz</option>
								<?php foreach($regionName as $k => $v) echo('<option value="'.$k.'"'.(($user -> region == $k) ? ' selected="selected"' : '').'>'.$v.'</option>'); ?>
							</select>
						</label>
						<label>
							Miejscowość
							<input type="text" name="city" value="<?php echo($user -> city); ?>">
						</label>
						<label>
							Kod pocztowy
							<input type="text" name="postcode" placeholder="00-000" value="<?php echo($user -> postcode); ?>">
						</label>
						<label>
							Adres
							<input type="text" name="address"  value="<?php echo($user -> address); ?>">
						</label>
					</div>
					<label>
						Numer NIP
						<input type="text" name="nip" placeholder="Tylko firmy" value="<?php echo($user -> nip); ?>">
					</label>*/ ?>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>