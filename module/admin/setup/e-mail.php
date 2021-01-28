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
 *	This file manages the e-mail settings
 */

if($url -> op(0) == URL_SEND && $_POST['email'] <> '' && $_POST['title'] <> '') {
	$status = send_mail($_POST['email'], $_POST['title'], $_POST['content']);
	if($status === true) {
		$main -> alertPrepare(true);
		$url -> redirect();
	} else {
		$main -> alertPrepare(false, 'Nie udało się wysłać wiadomości, gdyż natrafiono na błąd: '.$status);
	}
}

if($url -> op(0) == URL_SAVE && is_array($_POST)) {
	foreach($_POST as $k => $v) {
		if($k == 'email-password' && $v == '') {
			continue;
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
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section class="toggle">
				<h2><span class="ri-mail-line icon"></span>Wyślij wiadomość</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SEND)); ?>">
					<label>
						Adres e-mail odbiorcy
						<input type="text" name="email" required="required" value="<?php echo($_POST['email']); ?>">
					</label>
					<label>
						Temat
						<input type="text" name="title" required="required" value="<?php echo($_POST['title']); ?>">
					</label>
					<label>
						Treść wiadomości
						<textarea name="content" class="wysiwyg"><?php echo($_POST['content']); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Wyślij">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h1><span class="ri-mail-settings-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<div class="cols cols-3">
						<label>
							Nadawca
							<input type="text" name="email-sender" value="<?php echo($setup -> email_sender); ?>" required="required">
						</label>
						<label>
							Adres e-mail wysyłki
							<input type="email" name="email" value="<?php echo($setup -> email); ?>" required="required">
						</label>
						<label>
							Adres e-mail odpowiedzi
							<input type="email" name="email-reply" value="<?php echo($setup -> email_reply); ?>" required="required">
						</label>
					</div>
					<div class="cols cols-4">
						<label>
							Host
							<input type="text" name="email-host" value="<?php echo($setup -> email_host); ?>" required="required">
						</label>
						<label>
							Port
							<input type="text" name="email-port" value="<?php echo($setup -> email_port); ?>" required="required">
						</label>
						<label>
							Użytkownik
							<input type="text" name="email-username" value="<?php echo($setup -> email_username); ?>" required="required">
						</label>
						<label>
							Hasło
							<input type="password" name="email-password" placeholder="Hasło zapamiętane">
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-mail-settings-line icon"></span>E-maile systemowe</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Stopka wiadomości
						<textarea name="mail-footer" class="wysiwyg" required="required"><?php echo($setup -> mail_footer); ?></textarea>
					</label>
					<hr>
					
					<label>
						Wiadomość po założeniu konta
						<input type="text" name="mail-register-title" value="<?php echo($setup -> mail_register_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-register" class="wysiwyg" required="required"><?php echo($setup -> mail_register); ?></textarea>
					</label>
					
					<hr>
					<label>
						Wiadomość przypominająca hasło
						<input type="text" name="mail-password-1-title" value="<?php echo($setup -> mail_password_1_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-password-1" class="wysiwyg" required="required"><?php echo($setup -> mail_password_1); ?></textarea>
					</label>
					
					<hr>
					<label>
						Wiadomość z nowym hasłem
						<input type="text" name="mail-password-2-title" value="<?php echo($setup -> mail_password_2_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-password-2" class="wysiwyg" required="required"><?php echo($setup -> mail_password_2); ?></textarea>
					</label>
					
					<hr>
					<label>
						Wiadomość do użytkownika po założeniu konta przez administratora
						<input type="text" name="mail-register-admin-title" value="<?php echo($setup -> mail_register_admin_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-register-admin" class="wysiwyg" required="required"><?php echo($setup -> mail_register_admin); ?></textarea>
					</label>
					
					<hr>
					<label>
						Wiadomość do użytkownika po aktywacji firmy przez administratora
						<input type="text" name="mail-active-title" value="<?php echo($setup -> mail_active_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-active" class="wysiwyg" required="required"><?php echo($setup -> mail_active); ?></textarea>
					</label>
					
					<hr>
					<label>
						Wiadomość do użytkownika po aktywacji pakietu
						<input type="text" name="mail-premium-title" value="<?php echo($setup -> mail_premium_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-premium" class="wysiwyg" required="required"><?php echo($setup -> mail_premium); ?></textarea>
					</label>
					
					<hr>
					<label>
						Powiadomienie o nowej wiadomości
						<input type="text" name="mail-message-title" value="<?php echo($setup -> mail_message_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-message" class="wysiwyg" required="required"><?php echo($setup -> mail_message); ?></textarea>
					</label>
					
					<hr>
					<label>
						Powiadomienie o nowej ocenie
						<input type="text" name="mail-vote-title" value="<?php echo($setup -> mail_vote_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-vote" class="wysiwyg" required="required"><?php echo($setup -> mail_vote); ?></textarea>
					</label>
					
					<hr>
					<label>
						Wiadomość z raportem statystycznym
						<input type="text" name="mail-stat-title" value="<?php echo($setup -> mail_stat_title); ?>" required="required">
					</label>
					<label>
						<textarea name="mail-stat" class="wysiwyg" required="required"><?php echo($setup -> mail_stat); ?></textarea>
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

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>