<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	
 */


?>

<div id="newsletter">
	<div class="main">
		<div class="title">
			<h2>Zapisz się do newslettera</h2>
			<p>bądź zawsze na bieżąco z firmami godnymi polecenia</p>
		</div>
		<form method="post" action="<?php echo($url -> getUrl('newsletter', false, '/'.URL_ADD)); ?>">
			<label>
				<input type="text" name="email" placeholder="podaj swój adres e-mail" required="required">
			</label>
			<div class="buttons">
				<input type="submit" value="wyślij">
			</div>
		</form>
	</div>
</div>