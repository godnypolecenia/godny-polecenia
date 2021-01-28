<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays the content of the content page
 */

$page = new Page($url -> var['page_id']);
if(!($page -> pageId > 0)) {
	require_once('./module/tool/404.php');
	exit;
}

/**
 *	Gallery
 */
$exGal = explode(';', $page -> gallery);
foreach($exGal as $v) {
	$ex = explode('.', $v);
	$gal[] = [
		'file' => $v,
		'name' => $ex[0],
		'format' => end($ex)
	];
}

/**
 *
 */
 
$url -> addBackUrl();

/**
 *	Layout
 */

$url -> setBodyId('index');

$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="slider" class="slider-mini" style="background-image: none;">
	<div class="main">
		<div class="red-line"></div>
		<h1 class="lora"><?php echo($meta['title']); ?></h1>
		<div class="red-line"></div>
	</div>
</div>
<div id="content">
	<div class="main bottom-space">

<?php

$bc = new Breadcrumb();
if($page -> group == 1) $bc -> add($url -> getLink('blog'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			

			<h2><?php echo($meta['title']); ?></h2>
				<?php echo($page -> content); ?>

			<?php if($gal[0]['file'] <> '') { ?>

				<h2>Galeria zdjęć</h2>
				<div class="gallery">

<?php

foreach($gal as $k => $v) {
	echo('<a href="'.$url -> getUrl('tool/image', false, '/'.$v['file']).'" data-lightbox="galeria"><img src="'.$url -> getUrl('tool/image', false, '/'.$v['name'].'.'.(($mobile == 1) ? '320x240' : '150x150').'.'.$v['format']).'" alt="'.$meta['title'].' - zdjęcie nr '.($k+1).'"></a>'."\n");
}

?>

				</div>

			<?php } ?>

	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>