<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */
 
if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyUser();

/**
 *	Get item data
 */
$editItem = new Item($url -> op(0));
if(!($editItem -> itemId > 0)) {
	$url -> redirect(404);
}

/**
 *	Gallery
 */
$exGal = explode(';', $editItem -> gallery);
foreach($exGal as $v) {
	$ex = explode('.', $v);
	$gal[] = [
		'file' => $v,
		'name' => $ex[0],
		'format' => end($ex)
	];
}
if(!($gal[$url -> op(1)]['file'] == $url -> op(2))) {
	$url -> redirect(404);
}

/**
 *	Set photo as first 
 */
if($url -> op(3) == URL_SET) {
	$newArray = [$url -> op(2)];
	unset($gal[$url -> op(1)]);
	foreach($gal as $v) {
		$newArray[] = $v['file'];
	}
	$editItem -> gallery = implode(';', $newArray);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0).'/0/'.$url -> op(2));
}

/**
 *	Rotate the photo a degree
 */
if($url -> opd(URL_EXEC) == 90 || $url -> opd(URL_EXEC) == 180 || $url -> opd(URL_EXEC) == 270) {
	$editImg = new Image($url -> op(2));
	if($editImg -> rotate($url -> opd(URL_EXEC))) {
		$editImg -> removeThumbnails();
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0).'/'.$url -> op(1).'/'.$url -> op(2));
	}
	$main -> alertPrepare(false);
}

/**
 *	Delete photo
 */
if($url -> op(3) == URL_DEL && $_POST['delete'] == 1) {
	$delImg = new Image($url -> op(2));
	$delImg -> remove();
	unset($gal[$url -> op(1)]);
	$editItem -> gallery = '';
	foreach($gal as $v) {
		if($editItem -> gallery <> '') {
			$editItem -> gallery .= ';';
		}
		$editItem -> gallery .= $v['file'];
	}
	$main -> alertPrepare(true);
	$url -> redirect('item/edit-gallery', false, '/'.$url -> op(0));
}

/**
 *
 */
$url -> addBackUrl();

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
$bc -> add($url -> getLink('item/add-list'));
$bc -> add($url -> getLink('item/edit', false, '/'.$editItem -> itemId));
$bc -> add($url -> getLink('item/edit-gallery', false, '/'.$editItem -> itemId));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<?php require_once('./module/item/bookmark.php'); ?>
			<section>
				<h1><span class="ri-image-edit-line icon"></span><?php echo($meta['title']); ?></h1>
				<div id="photo-editor"><img src="<?php echo($url -> getUrl('tool/image', false, '/'.$url -> op(2))); ?>" alt=""></div>
				<div class="buttons">
					<a href="<?php echo($url -> getUrl('item/edit-gallery', false, '/'.$editItem -> itemId)); ?>" class="button button-2">Powrót</a>
					<?php if($url -> op(1) > 0) echo('<a href="'.$url -> getUrl(null, true, '/'.URL_SET).'" class="button">Ustaw jako główne zdjęcie</a>'); ?>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-90')); ?>" class="button">Obróć o 90&#186;</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-180')); ?>" class="button">Obróć o 180&#186;</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-270')); ?>" class="button">Obróć o 270&#186;</a>
				</div>
			</section>
			<section>
				<h2><span class="ri-close-circle-line icon"></span>Usuń zdjęcie</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tego zdjęcia
					</label>
					<div class="buttons">
						<input type="submit" value="Usuń">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>