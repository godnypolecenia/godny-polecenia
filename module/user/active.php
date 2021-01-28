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
 *	This file activates the user account
 */

//$url -> addBackUrl();

/**
 *	Active account
 */
if($url -> op(0) <> '') {
	$ex = explode('-', $url -> op(0));
	
	$editUser = new User;
	if(!$editUser -> getUserById($url -> op(0))) {
		require_once('./module/tool/404.php');
		exit;
	}
	
	if(password($editUser -> password.$editUser -> email)  == $ex[1]) {
		$editUser -> status = 1;
		$main -> alertPrepare(true, 'Twoje konto zostało aktywowane. Możesz się teraz na nie zalogować.');
		$url -> redirect('user/login');
	}
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
			<section class="alert alert-success">
				<h3>Gotowe</h3>
				<p>Na podany adres e-mail została wysłana wiadomość z kluczem aktywacyjnym - kliknij w niego by aktywować swoje konto. Jeżeli wiadomość nie dotrze w ciągu kilku minut <span class="bold">sprawdź zakładkę SPAM</span> w swoim kliencie pocztowym.</p>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>