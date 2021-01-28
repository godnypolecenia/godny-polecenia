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
 *	Save new password
 */
if($url -> op(0) == URL_SAVE) {
	$vd = new Validate;
	$vd -> isValue($_POST['password-current'], 'Obecne hasło');
	$vd -> isString($_POST['password-new'], 'Nowe hasło', 8);
	$vd -> isString($_POST['password-new-confirm'], 'Powtórz hasło', 8);
	
	if($_POST['password-new'] <> $_POST['password-new-confirm']) {
		$vd -> putError(USER_ERR_DIFFERENT_PASS);
	}
	
	if($user -> password <> password($_POST['password-current'])) {
		$vd -> putError(USER_ERR_CURRENT_PASS);
	}
	
	if($vd -> pass() == true) {	
		$user -> password = $_POST['password-new'];
		
		$main -> alertPrepare(true);
		$url -> redirect();
	}
	
	$url -> setOpd(URL_OPEN, URL_PASS);
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
$bc -> add($url -> getLink('user/manage'));
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
				<h1><span class="ri-lock-password-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Obecne hasło
						<input type="password" name="password-current" pattern=".{8,}" required="required">
					</label>
					<div class="cols cols-2">
						<label>
							Nowe hasło
							<input type="password" name="password-new" placeholder="Minimum 8 znaków" pattern=".{8,}" required="required">
						</label>
						<label>
							Powtórz hasło
							<input type="password" name="password-new-confirm" pattern=".{8,}" required="required">
						</label>
					</div>
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