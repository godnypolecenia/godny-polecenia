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
 *	Save new email
 */
if($url -> op(0) == URL_SAVE) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email']);
	
	if($vd -> pass() == true) {	
		if($user -> availableLogin($_POST['email'])) {
			$user -> email = $_POST['email'];
			$user -> phone = str_replace('-', '', $_POST['phone']);
			
			$main -> alertPrepare(true);
			$url -> redirect();
		} else {
			$vd -> putError(USER_ERR_BUSY);
		}
	}
	
	$url -> setOpd(URL_OPEN, URL_EMAIL);
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

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
				<h1><span class="ri-mail-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Adres e-mail
						<input type="email" name="email" placeholder="Adres e-mail jest loginem" required="required" value="<?php echo(($_POST['email'] <> '') ? $_POST['email'] : $user -> email); ?>">
					</label>
					<label>
						Numer telefonu
						<input type="text" name="phone" required="required" placeholder="+48" value="<?php echo(($_POST['phone'] <> '') ? $_POST['phone'] : $user -> phone); ?>">
					</label>
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