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
				<div class="main">
					<div id="footer-box">
						<div class="cols cols-3">
							<div class="col-item">
								<h3><?php echo($setup -> star_h_1); ?></h3>
								<p><?php echo($setup -> star_1); ?></p>
							</div>
							<div class="col-item">
								<h3><?php echo($setup -> star_h_2); ?></h3>
								<p><?php echo($setup -> star_2); ?></p>
							</div>
							<div class="col-item">
								<h3><?php echo($setup -> star_h_3); ?></h3>
								<p><?php echo($setup -> star_3); ?></p>
							</div>
						</div>
					</div>
					<div class="cols cols-4">
						<div class="col-item">
							<h3>Serwis</h3>
							<ul>
								<li><?php echo($url -> getButton('index')); ?></li>
								<li><?php echo($url -> getButton('page/page?page_id=1')); ?></li>
								<li><?php echo($url -> getButton('page/page?page_id=2')); ?></li>
								<li><?php echo($url -> getButton('contact')); ?></li>
							</ul>
						</div>
						<div class="col-item">
							<h3>Dla użytkownika</h3>
							<ul>
								<li><?php echo($url -> getButton('tips')); ?></li>
								<li><?php echo($url -> getButton('about')); ?></li>
								<li><?php echo($url -> getButton('item/index')); ?></li>
								<li><?php echo($url -> getButton('contact')); ?></li>
							</ul>
						</div>
						<div class="col-item">
							<h3>Dla biznesu</h3>
							<ul>
								<li><?php echo($url -> getButton('tips-company')); ?></li>
								<li><?php echo($url -> getButton('pricelist')); ?></li>
								<li><?php echo($url -> getButton('about')); ?></li>
								<li><?php echo($url -> getButton('item/add')); ?></li>
							</ul>
						</div>
						<div class="col-item">
							<h3>Godny polecenia</h3>
							<p>godny-polecenia.pl to serwis, który pomógł i pomaga firmom i klientom końcowym znależć się nawzajem na podstawie poleceń</p>
							<div id="social-media">
								<?php if($setup -> facebook <> '') echo('<a href="'.$setup -> facebook.'" target="_blank" class="icon"><span class="ri-facebook-line"></span></a>'); ?>
								<?php if($setup -> google <> '') echo('<a href="'.$setup -> google.'" target="_blank" class="icon"><span class="ri-google-line"></span></a>'); ?>
								<?php if($setup -> twitter <> '') echo('<a href="'.$setup -> twitter.'" target="_blank" class="icon"><span class="ri-twitter-line"></span></a>'); ?>
								<?php if($setup -> youtube <> '') echo('<a href="'.$setup -> youtube.'" target="_blank" class="icon"><span class="ri-youtube-line"></span></a>'); ?>
								<?php if($setup -> instagram <> '') echo('<a href="'.$setup -> instagram.'" target="_blank" class="icon"><span class="ri-instagram-line"></span></a>'); ?>
							</div>
						</div>
					</div>
				</div>
			</footer>
			<?php if($mobile == 1) { ?>
			<div id="footer-panel">
				<ul>
					<li><?php echo($url -> getButton('item/add', false, null, [], '<span class="ri-add-circle-line"></span> Dodaj')); ?></li>
					<li><?php echo($url -> getButton('item/index', false, null, [], '<span class="ri-search-line"></span> Szukaj')); ?></li>
					<li><?php echo($url -> getButton('index', false, null, [], '<span class="ri-home-4-line"></span> Start')); ?></li>
					<li><?php echo($url -> getButton('user/index', false, null, [], '<span class="ri-user-line"></span> Konto'.(($countNewMsg > 0) ? '<div class="count">'.$countNewMsg.'</div>' : ''))); ?></li>
					<li><?php echo($url -> getButton('item/favorite', false, null, [], '<span class="ri-heart-3-line"></span> Obserwuje')); ?></li>
				</ul>
				<div class="clear"></div>
			</div>
			<?php } ?>
		</div>
		<?php if($user -> userId == 0 && $mobile == 0 && $url -> getUrl() <> SITE_ADDRESS.'/'.$urlList['user/login']) echo('<div id="login-window-container" data-url="'.$url -> getUrl('tool/login-window').'"></div>'."\n"); ?>
		<?php /*if($cookie == 1) echo('<div id="cookie-window-container">aaa</div>'."\n");*/ ?>
		<?php echo($setup -> coode_facebook."\n"); ?>
		<?php echo($setup -> code_analytics."\n"); ?>	
		<?php if($setup -> developer == 1) $main -> showDeveloperBar(); ?>
	</body>
</html>