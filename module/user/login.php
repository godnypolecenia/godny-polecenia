<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyGuest();

/**
 *	This file contains the login form
 */


/**
 *	Login
 */
if($url -> op(0) == URL_LOGIN) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	$vd -> isValue($_POST['password'], 'Hasło');
	$vd -> isCaptcha($_POST['g-recaptcha-response']);
	
	if($vd -> pass() == true) {	
		if($user -> login($_POST['email'], $_POST['password'])) {
			if($url -> op(1) == URL_BACK) {
				if($url -> getBackUrl() <> null) {
					$url -> goBackUrl();
				}
			}
			
			if($user -> type >= 8) {
				$url -> redirect('admin/index');
			}
			
			$it = new Item;
			$countIt = $it -> countItemListOfUser($user -> userId);
			if($countIt > 0) {
				$url -> redirect('item/add-list');
			} else {
				$url -> redirect('item/add');
			}
			$url -> redirect('user/index');
			
		} else {
			$vd -> putError(USER_ERR_BAD);
		}
	}
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

/**
 *	Create account
 */
if($url -> op(0) == URL_EXEC) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	$vd -> isValue($_POST['password'], 'Hasło');
	$vd -> isValue($_POST['password-confirm'], 'Powtórz hasło');
	$vd -> isValue($_POST['rules'], 'Regulamin');
	//$vd -> isCaptcha($_POST['g-recaptcha-response']);
	
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
			
			$tmpMail = str_replace(
				['{login}', '{email}', '{link}', '{nazwa}', '{adres}'],
				[$_POST['email'], $_POST['email'], $url -> getUrl('user/active', false, '/'.$newUserId.'-'.password(password($_POST['password']).$_POST['email'])), $setup -> name, SITE_ADDRESS],
				$setup -> mail_register
			);
			send_mail($_POST['email'], $setup -> mail_register_title, $tmpMail);
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

$url -> setBodyId('login');

$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="top-content">
	<div class="main">
		<?php $main -> alert(); ?>
		<div id="left" class="left-2">
			<h2 style="margin-top: 0;">Zarejestruj się</h2>
			<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_EXEC)); ?>">
				<label>
					<input type="text" name="name" placeholder="Nazwa użytkownika *" required="required" value="<?php echo($_POST['name']); ?>">
				</label>
				<div class="cols cols-2">
					<label>
						<input type="email" name="email" placeholder="Adres e-mail *" required="required" value="<?php echo($_POST['email']); ?>">
					</label>
					<label>
						<input type="text" name="nip" placeholder="NIP *" value="<?php echo($_POST['nip']); ?>">
					</label>
				</div>
				<div class="cols cols-2">
					<label>
						<input type="password" name="password" placeholder="Hasło (min. 8 znaków) *" pattern=".{8,}" required="required">
					</label>
					<label>
						<input type="password" name="password-confirm" placeholder="Powtórz hasło *" pattern=".{8,}" placeholder="Powtórz hasło" required="required">
					</label>
				</div>
				<label class="label-checkbox">
					<input type="checkbox" name="rules" value="1" required="required"<?php if($_POST['rules'] == 1) echo(' checked="checked"'); ?>>
					Znam <a href="<?php echo($url -> getUrl('page/page?page_id=1')); ?>" target="_blank">Regulamin</a> i&#160;<a href="<?php echo($url -> getUrl('page/page?page_id=2')); ?>" target="_blank">Politykę prywatności</a> i je akceptuję
				</label>
				<div class="buttons">
					<input type="submit" value="Zarejestruj się">
				</div>
			</form>
		</div>
		<div id="right" class="right-2">
			<h2>Zaloguj się</h2>
			<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_LOGIN)); ?>" class="recaptcha">
				<label>
					<input type="email" name="email" required="required" placeholder="Adres e-mail *" value="<?php echo($_POST['email']); ?>" class="input-login">
				</label>
				<label>
					<input type="password" name="password" required="required" class="input-password" placeholder="Hasło *">
				</label>
				<div class="buttons">
					<input type="submit" value="Zaloguj się">
					<a href="<?php echo($url -> getUrl('user/password')); ?>">Przypomnij hasło</a>
				</div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
</div>
<div id="slider" class="slider-mini">
	<div class="main">
		<div class="red-line"></div>
		<h1 class="lora"><?php echo($meta['title']); ?></h1>
		<div class="red-line"></div>
	</div>
</div>
<div id="info" class="info-padding">
	<div class="main">
		<h2 class="center">Korzyści z dołączenia do Godny Polecenia</h2><br>
		<div class="cols cols-4">
			<div class="col-item">
				<img src="./template/default/image/star-red.png" alt="<?php echo($setup -> benefit_h_1); ?>">
				<h3><?php echo($setup -> benefit_h_1); ?></h3>
				<p><?php echo($setup -> benefit_1); ?></p>
			</div>
			<div class="col-item">
				<img src="./template/default/image/star-yellow.png" alt="<?php echo($setup -> benefit_h_2); ?>">
				<h3><?php echo($setup -> benefit_h_2); ?></h3>
				<p><?php echo($setup -> benefit_2); ?></p>
			</div>
			<div class="col-item">
				<img src="./template/default/image/star-blue.png" alt="<?php echo($setup -> benefit_h_3); ?>">
				<h3><?php echo($setup -> benefit_h_3); ?></h3>
				<p><?php echo($setup -> benefit_3); ?></p>
			</div>
			<div class="col-item">
				<img src="./template/default/image/star-black.png" alt="<?php echo($setup -> benefit_h_4); ?>">
				<h3><?php echo($setup -> benefit_h_4); ?></h3>
				<p><?php echo($setup -> benefit_4); ?></p>
			</div>
		</div>
		<div class="cols cols-4">
			<div class="col-item">
				<img src="./template/default/image/star-black.png" alt="<?php echo($setup -> benefit_h_5); ?>">
				<h3><?php echo($setup -> benefit_h_5); ?></h3>
				<p><?php echo($setup -> benefit_5); ?></p>
			</div>
			<div class="col-item">
				<img src="./template/default/image/star-blue.png" alt="<?php echo($setup -> benefit_h_6); ?>">
				<h3><?php echo($setup -> benefit_h_6); ?></h3>
				<p><?php echo($setup -> benefit_6); ?></p>
			</div>
			<div class="col-item">
				<img src="./template/default/image/star-red.png" alt="<?php echo($setup -> benefit_h_7); ?>">
				<h3><?php echo($setup -> benefit_h_7); ?></h3>
				<p><?php echo($setup -> benefit_7); ?></p>
			</div>
			<div class="col-item">
				<img src="./template/default/image/star-yellow.png" alt="<?php echo($setup -> benefit_h_8); ?>">
				<h3><?php echo($setup -> benefit_h_8); ?></h3>
				<p><?php echo($setup -> benefit_8); ?></p>
			</div>
		</div>
	</div>
</div>
<div id="info-2">
	<div class="main center">
		<div id="red-box">
			<div class="cols cols-3">
				<div class="col-item">
					<h3><?php echo($setup -> precent_h_1); ?></h3>
					<p><?php echo($setup -> precent_1); ?></p>
				</div>
				<div class="col-item">
					<h3><?php echo($setup -> precent_h_2); ?></h3>
					<p><?php echo($setup -> precent_2); ?></p>
				</div>
				<div class="col-item">
					<h3><?php echo($setup -> precent_h_3); ?></h3>
					<p><?php echo($setup -> precent_3); ?></p>
				</div>
			</div>
		</div>
		<h2><?php echo($setup -> abc_h_2); ?></h2>
		<h3><?php echo($setup -> abc_2); ?></h3>
		<div class="center"><a href="<?php echo($url -> getUrl('item/add')); ?>" class="button">Dołącz</a></div>
	</div>
</div>
<div id="content">
	<div class="main bottom-space">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('user/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>

		<h2 class="center">Rekomendacje</h2>
		<div class="cols cols-3 gap-zero">
			<div class="col-item vote-box">
				<h3><?php echo($setup -> rec_h_1); ?></h3>
				<p><?php echo($setup -> rec_1); ?></p>
				<?php echo($setup -> rec_n_1); ?>
			</div>
			<div class="col-item vote-box">
				<h3><?php echo($setup -> rec_h_2); ?></h3>
				<p><?php echo($setup -> rec_2); ?></p>
				<?php echo($setup -> rec_n_2); ?>
			</div>
			<div class="col-item vote-box">
				<h3><?php echo($setup -> rec_h_3); ?></h3>
				<p><?php echo($setup -> rec_3); ?></p>
				<?php echo($setup -> rec_n_3); ?>
			</div>
		</div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>