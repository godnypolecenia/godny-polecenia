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

$user -> logout();
	
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
			<h1 class="center"><?php echo($meta['title']); ?></h1>
			<p>Pomyślnie wylogowano z konta. Przejdź na <a href="<?php echo(SITE_ADDRESS); ?>" class="underline">stronę główną</a> lub <a href="<?php echo($url -> getUrl('admin/login')); ?>" class="underline">zaloguj się</a> ponownie.</p>
		</section>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>