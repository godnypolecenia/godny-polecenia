<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file contains the login form (admin)
 */

$user -> onlyGuest();

/**
 *	Login
 */
if($url -> op(0) == URL_EXEC) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	$vd -> isValue($_POST['password'], 'Hasło');
	$vd -> isCaptcha($_POST['g-recaptcha-response']);
	
	if($vd -> pass() == true) {	
		if($user -> login($_POST['email'], $_POST['password'])) {
			$url -> redirect('admin/index');
		} else {
			$vd -> putError(USER_ERR_BAD);
		}
	}
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}
	
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
	<div class="main main-logout">	
		<section>
			<h1><?php echo($meta['title']); ?></h1>
			<?php $main -> alert(); ?>
			<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_EXEC)); ?>" class="recaptcha">
				<label>
					<input type="email" name="email" required="required" placeholder="Login" value="<?php echo($_POST['email']); ?>">
				</label>
				<label>
					<input type="password" name="password" required="required" placeholder="Hasło">
				</label>
				<div class="buttons">
					<input type="submit" value="Zaloguj się">
				</div>
				<br><a href="<?php echo(SITE_ADDRESS); ?>" class="underline">Powrót do serwisu</a>
			</form>
		</section>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>