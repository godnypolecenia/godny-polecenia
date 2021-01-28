<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file is the main page of the administration panel
 */

$user -> onlyAdmin();

/**
 *	Save new email & name
 */
if($url -> op(0) == URL_SAVE) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email']);
	
	if($vd -> pass() == true) {	
		if($user -> availableLogin($_POST['email'])) {
			$user -> email = $_POST['email'];
			$user -> name = $_POST['name'];
			
			$main -> alertPrepare(true);
			$url -> redirect();
		} else {
			$vd -> putError(USER_ERR_BUSY);
		}
	}
	
	$url -> setOpd(URL_OPEN, URL_EMAIL);
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

/**
 *	Save new password
 */
if($url -> op(0) == URL_PASS) {
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
			<section>
				<h1><span class="ri-user-line icon"></span><?php echo($meta['title']); ?></h1>
				<p>Pełna edycja konta dostępna jest w zakładce <a href="<?php echo($url -> getUrl('user/manage')); ?>" class="underline">twoje konto</a> lub <a href="<?php echo($url -> getUrl('admin/user/user', false, '/'.$user -> userId)); ?>" class="underline">zarządzaniu użytkownikiem</a></p>
			</section>
			<section class="toggle">
				<h2><span class="ri-edit-box-line icon"></span>Dane ogólne</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Adres e-mail (login)
						<input type="email" name="email" placeholder="Adres e-mail jest loginem" required="required" value="<?php echo(($_POST['email'] <> '') ? $_POST['email'] : $user -> email); ?>">
					</label>
					<label>
						Nazwa
						<input type="text" name="name" placeholder="Nick lub imię i nazwisko" required="required" value="<?php echo(($_POST['name'] <> '') ? $_POST['name'] : $user -> name); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-lock-password-line icon"></span>Zmień hasło</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_PASS)); ?>">
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

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>