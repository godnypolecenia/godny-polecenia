<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file contains a layout footer
 */

?>


			<footer id="footer">
				<div class="main center">
					<p>
						<strong>Wsparcie techniczne:</strong>
						<a href="mailto:kontakt@webhome.pl" class="h-icon h-icon-mail">kontakt@webhome.pl</a>
						<a href="tel:0048887667447" class="h-icon h-icon-phone">+48 887-667-447</a>
						<a href="https://webhome.pl" target="_blank" class="h-icon h-icon-link">webhome.pl</a>
					</p>
				</div>
			</footer>
			<?php if($mobile == 1 && $user -> userId > 0 && $user -> type == 9) { ?>
			<div id="footer-panel">
				<ul>
					<li><?php echo($url -> getButton('item/admin/index', false, (($itemDeactiveCount > 0) ? '/'.URL_BOOKMARK.'-2' : null), [], '<span class="ri-database-2-line"></span> Ogłoszenia'.(($itemDeactiveCount > 0) ? '<div class="count">'.$itemDeactiveCount.'</div>' : ''))); ?></li>
					<li><?php echo($url -> getButton('user/admin/payment-list', false, null, [], '<span class="ri-money-dollar-circle-line"></span> Płatności')); ?></li>
					<li><?php echo($url -> getButton('index', false, null, [], '<span class="ri-home-4-line"></span> Powrót')); ?></li>
					<li><?php echo($url -> getButton('user/admin/index', false, null, [], '<span class="ri-user-line"></span> Użytkownicy')); ?></li>
					<li><?php echo($url -> getButton('admin/logout', false, null, [], '<span class="ri-logout-box-r-line"></span> Wyloguj się')); ?></li>
				</ul>
				<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
		<?php if($setup -> developer == 1) $main -> showDeveloperBar(); ?>
	</body>
</html>