<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays cookie window
 */

?>

<div id="window-container">
	<section id="window">
		<h3>Polityka prywatności</h3>
		<a href="#" id="window-close" class="window-close ri-close-line"></a>
		<div id="window-scroll">

<?php

$privacy = new Page(2);
echo($privacy -> content);

?>

		</div>
		<div class="buttons">
			<a href="<?php echo($url -> getUrl(null, true, '/'.URL_COOKIE.'-'.URL_SAVE)); ?>" class="window-close button">Akceptuję i przechodzę do serwisu</a>
			<a href="https://google.com" class="underline">Nie akceptuję</a>
		</div>
	</section>
</div>