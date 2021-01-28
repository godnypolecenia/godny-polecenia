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
 
$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_item`'
);
$r = $db -> fetchArray();
$itemCount = $r['count'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_item` '.
	'WHERE `status` = 0'
);
$r = $db -> fetchArray();
$itemDeactiveCount = $r['count'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_user`'
);
$r = $db -> fetchArray();
$userCount = $r['count'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_vote`'
);
$r = $db -> fetchArray();
$voteCount = $r['count'];

$db -> query(
	'SELECT COUNT(*) AS `count` '.
	'FROM `db_vote` '.
	'WHERE `status` = 0'
);
$r = $db -> fetchArray();
$voteDeactiveCount = $r['count'];

if(!isset($meta['robots']) || $meta['robots'] == '') $meta['robots'] = (($setup -> robots == 1) ? 'index' : 'noindex');
if(!isset($meta['description']) || $meta['description'] == '') $meta['description'] = $setup -> description;
if(!isset($meta['keywords']) || $meta['keywords'] == '') $meta['keywords'] = $setup -> keywords;

?>


<!DOCTYPE html>

<html lang="pl-PL">
	<head>
		<meta charset="utf-8">
		<title><?php echo(htmlspecialchars($meta['title'])); ?></title>
		<meta name="robots" content="<?php echo(htmlspecialchars($meta['robots'])); ?>">
		<meta name="keywords" content="<?php echo(htmlspecialchars($meta['keywords'])); ?>">
		<meta name="description" content="<?php echo(htmlspecialchars($meta['description'])); ?>">
		<base href="<?php echo(SITE_ADDRESS.'/'); ?>">
		<link href="<?php echo(SITE_ADDRESS); ?>/template/lib/remixicon/remixicon.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/admin/main.css<?php if($setup -> developer == 1) echo('?rand='.rand(0, 9999)); ?>" media="all">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/lib/dropzone/dropzone.css" media="all">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/lib/trumbowyg/dist/ui/trumbowyg.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/lib/trumbowyg/dist/plugins/colors/ui/trumbowyg.colors.css">
		<link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="<?php echo(SITE_ADDRESS); ?>/template/lib/lightbox/dist/css/lightbox.css" media="all">
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=2.0; user-scalable=0;">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/dropzone/dropzone.js"></script>
		<script src="//www.google.com/recaptcha/api.js?render=<?php echo(RECAPTCHA_KEY); ?>" id="recaptcha-script" data-value="<?php echo(RECAPTCHA_KEY); ?>"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/trumbowyg/dist/trumbowyg.min.js"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/trumbowyg/dist/langs/pl.min.js"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/lib/trumbowyg/plugins/colors/trumbowyg.colors.js"></script>
		<script src="<?php echo(SITE_ADDRESS); ?>/template/admin/main.js<?php if($setup -> developer == 1) echo('?rand='.rand(0, 9999)); ?>"></script>
		<?php if(file_exists('./favicon.ico')) { ?>
		<link type="image/x-icon" rel="shortcut icon" href="./favicon.ico">
		<link type="image/x-icon" rel="icon" href="./favicon.ico">
		<link type="image/x-icon" rel="apple-touch-icon" href="./favicon.ico">
		<?php } ?>
	</head>
	<body>
		<div id="wrapper">
			<?php if($user -> userId > 0) { ?>
			<header id="header">
				<div class="main">
					<div class="logo"><a href="<?php echo($url -> getUrl('admin/index')); ?>">Panel administracyjny</a></div>
					<ul id="menu">
						<li><a href="<?php echo(SITE_ADDRESS); ?>">Powrót do serwisu</a></li>
						<li><a href="<?php echo($url -> getUrl('admin/index')); ?>">Strona główna</a></li>
						<li>Jesteś zalogowany jako <a href="<?php echo($url -> getUrl('admin/manage')); ?>" class="bold"><?php echo($user -> name); ?></a></li>
						<li><a href="<?php echo($url -> getUrl('admin/logout')); ?>"<?php if($mobile == 0) echo(' class="button"'); ?>>Wyloguj się</a></li>
					</ul>
					<a href="#" id="menu-toggle"><span class="ri-menu-line icon"></span></a>
				</div>
			</header>
			<div id="header-space"></div>
			<?php } ?>
				
			