<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays login window
 */

?>

<div id="window-container">
	<section id="window" class="slim">
		<h3>Logowanie</h3>
		<a href="#" id="window-close" class="window-close ri-close-line"></a>
		<form method="post" action="<?php echo($url -> getUrl('user/login', false, '/'.URL_EXEC.'/'.URL_BACK)); ?>" class="recaptcha">
			<label>
				<input type="email" name="email" required="required" placeholder="Adres e-mail" class="input-login">
			</label>
			<label>
				<input type="password" name="password" required="required" placeholder="Hasło" class="input-password">
			</label>
			<div class="right"><a href="<?php echo($url -> getUrl('user/password')); ?>" class="underline">Zapomniałem hasła</a></div>
			<div class="buttons">
				<input type="submit" value="Zaloguj się">
			</div>
			<hr>
			<p>Nie masz jeszcze konta?<br><a href="<?php echo($url -> getUrl('user/register')); ?>" class="underline">Załóż konto</a></p>
		</form>
	</section>
</div>