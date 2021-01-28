<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file contains a layout header
 */

$newMsg = 0;
if($user -> userId > 0) {
	$newMsg = new Message;
	$countNewMsg = $newMsg -> countNewMessageOfUser($user -> userId);
}

$slider = explode(';', $setup -> slider);
$slider_text = explode(';', $setup -> slider_text);

if(!isset($meta['robots']) || $meta['robots'] == '') $meta['robots'] = (($setup -> robots == 1) ? 'index' : 'noindex');
if(!isset($meta['description']) || $meta['description'] == '') $meta['description'] = $setup -> description;
if(!isset($meta['keywords']) || $meta['keywords'] == '') $meta['keywords'] = $setup -> keywords;

?>


<!DOCTYPE html>

<html lang="pl-PL">
	<head>
		<meta charset="utf-8">
		<title><?php echo(htmlspecialchars($meta['title'])); ?></title>
		<base href="<?php echo(SITE_ADDRESS.'/'); ?>">
		<meta name="robots" content="<?php echo(htmlspecialchars($meta['robots'])); ?>">
		<meta name="keywords" content="<?php echo(htmlspecialchars($meta['keywords'])); ?>">
		<meta name="description" content="<?php echo(htmlspecialchars($meta['description'])); ?>">
		<link href="<?php echo(SITE_ADDRESS); ?>/template/lib/remixicon/remixicon.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/lib/lightslider/src/css/lightslider.css">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/default/main.css<?php if($setup -> developer == 1) echo('?rand='.rand(0, 9999)); ?>" media="all">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/lib/dropzone/dropzone.css" media="all">
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/lib/lightbox/dist/css/lightbox.css" media="all">
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=2.0; user-scalable=0;">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/dropzone/dropzone.js"></script>
		<script src="//www.google.com/recaptcha/api.js?render=<?php echo(RECAPTCHA_KEY); ?>" id="recaptcha-script" data-value="<?php echo(RECAPTCHA_KEY); ?>"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/googlemap/jquery.googlemap.js"></script>
		<script src="//maps.googleapis.com/maps/api/js?sensor=true&amp;libraries=places&amp;key=<?php echo(GOOGLE_MAPS_KEY); ?>"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/lightbox/dist/js/lightbox.js"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/lightslider/src/js/lightslider.js"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/default/main.js<?php if($setup -> developer == 1) echo('?rand='.rand(0, 9999)); ?>"></script>
		<?php if(file_exists('./favicon.ico')) { ?>
		<link type="image/x-icon" rel="shortcut icon" href="./favicon.ico">
		<link type="image/x-icon" rel="icon" href="./favicon.ico">
		<link type="image/x-icon" rel="apple-touch-icon" href="./favicon.ico">
		<?php } ?>
	</head>
	<body id="<?php echo($url -> bodyId()); ?>">
		<div id="wrapper">
			<header id="header">
				<div class="main">
					<div class="logo"><a href="<?php echo(SITE_ADDRESS); ?>" title="<?php echo($setup -> title); ?>"><img src="./template/default/image/logo<?php if($mobile == 0 && $url -> bodyId() == 'login') echo('-2'); ?>.png" alt="<?php echo($setup -> name); ?>"></a></div>
					<ul id="menu">
						<li><?php echo($url -> getButton('item/index')); ?></li>
						<li><?php echo($url -> getButton('tips')); ?></li>
						<li><?php echo($url -> getButton('about')); ?></li>
						<li><?php echo($url -> getButton('user/index', false, null, ['class' => 'button'.(($user -> userId == 0) ? ' login-window' : '')], 'Dla firm')); ?></li>
					</ul>
					<a href="#" id="menu-toggle"><span class="ri-menu-line icon"></span></a>
				</div>
			</header>
			<div id="header-space"></div>
			<div id="lat-lng" data-lat="<?php echo($_SESSION['lat']); ?>" data-lng="<?php echo($_SESSION['lng']); ?>"></div>
			