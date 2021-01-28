<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyGuest();

$url -> redirect('user/login');

/**
 *	This file contains the registration form
 */

/**
 *	Create account
 */
if($url -> op(0) == URL_EXEC) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	$vd -> isValue($_POST['password'], 'Hasło');
	$vd -> isValue($_POST['password-confirm'], 'Powtórz hasło');
	$vd -> isValue($_POST['rules'], 'Regulamin');
	$vd -> isCaptcha($_POST['g-recaptcha-response']);
	
	if($_POST['password-new'] <> $_POST['password-new-confirm']) {
		$vd -> putError(USER_ERR_DIFFERENT_PASS);
	}
	
	if($vd -> pass() == true) {	
		
		$newUser = new User;
		$newUserId = $newUser -> add($_POST['email']);
		
		if($newUserId > 0) {
			$newUser -> password = $_POST['password'];
			$newUser -> name = $_POST['name'];
			$newUser -> region = $_POST['region'];
			$newUser -> city = $_POST['city'];
			$newUser -> postcode = $_POST['postcode'];
			$newUser -> address = $_POST['address'];
			$newUser -> nip = $_POST['nip'];
			$newUser -> phone = str_replace('-', '', $_POST['phone']);
			
			$newsletter = new Newsletter;
			$newsletter -> add($_POST['email']);
			
			$tmpMail = str_replace(
				['{login}', '{email}', '{link}', '{nazwa}', '{adres}'],
				[$_POST['email'], $_POST['email'], $url -> getUrl('user/active', false, '/'.$newUserId.'-'.password(password($_POST['password']).$_POST['email'])), $setup -> name, SITE_ADDRESS],
				$setup -> mail_register
			);
			send_mail($_POST['email'], $setup -> mail_register_title, $tmpMail);
			
			send_mail($setup -> email, 'Nowa rejestracja', 'W serwisie zarejestrował się nowy użytkownik. Jego login to <strong>'.$_POST['email'].'</strong>');
			
			$url -> redirect('user/active');
		} else {
			$vd -> putError(false, USER_ERR_BUSY);
		}
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

//$url -> addBackUrl();

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
			<section>
				<h1><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_EXEC)); ?>">
					<div class="cols cols-2">
						<label>
							Adres e-mail
							<input type="email" name="email" placeholder="Adres e-mail jest loginem" required="required" value="<?php echo($_POST['email']); ?>">
						</label>
						<label>
							Numer telefonu
							<input type="text" name="phone" placeholder="+48" value="<?php echo($_POST['phone']); ?>">
						</label>
					</div>
					<div class="cols cols-2">
						<label>
							Hasło
							<input type="password" name="password" placeholder="Minimum 8 znaków" pattern=".{8,}" required="required">
						</label>
						<label>
							Powtórz hasło
							<input type="password" name="password-confirm" placeholder="" pattern=".{8,}" required="required">
						</label>
					</div>
					<h2>Twoje dane</h2>
					<label>
						Nazwa
						<input type="text" name="name" placeholder="Nick lub imię i nazwisko" required="required" value="<?php echo($_POST['name']); ?>">
					</label>
					<div class="cols cols-4">
						<label>
							Województwo
							<select name="region">
								<option value="0">Wybierz</option>
								<?php foreach($regionName as $k => $v) echo('<option value="'.$k.'"'.(($_POST['region'] == $k) ? ' selected="selected"' : '').'>'.$v.'</option>'); ?>
							</select>
						</label>
						<label>
							Miejscowość
							<input type="text" name="city" value="<?php echo($_POST['city']); ?>">
						</label>
						<label>
							Kod pocztowy
							<input type="text" name="postcode" pattern="^[0-9]{2}-[0-9]{3}$" placeholder="00-000" value="<?php echo($_POST['postcode']); ?>">
						</label>
						<label>
							Adres
							<input type="text" name="address" value="<?php echo($_POST['address']); ?>">
						</label>
					</div>
					<label>
						Numer NIP
						<input type="text" name="nip" placeholder="Tylko firmy" value="<?php echo($_POST['nip']); ?>">
					</label>
					<h2>Regulamin</h2>
					<label class="label-checkbox">
						<input type="checkbox" name="rules" value="1" required="required"<?php if($_POST['rules'] == 1) echo(' checked="checked"'); ?>>
						Oświadczam, iż zapozałem się z&#160;<a href="<?php echo($url -> getUrl('page/page?page_id=1')); ?>" target="_blank" class="underline">Regulaminem</a> i&#160;<a href="<?php echo($url -> getUrl('page/page?page_id=2')); ?>" target="_blank" class="underline">Polityką prywatności</a> serwisu <span class="bold"><?php echo($setup -> name); ?></span> i&#160;je akceptuję, a&#160;także wyrażam zgodę na przetwarzanie moich danych osobowych do celów świadczenia usług w&#160;ramach portalu internetowego. 
					</label>
					<div class="buttons">
						<input type="submit" value="Zarejestruj się">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>