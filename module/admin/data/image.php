<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(7);

/**
 *	This file manages the image
 */

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	$image = new Image($url -> op(0));
	$image -> remove();
	$main -> alertPrepare(true);
	$url -> redirect('admin/data/image-list');
}

/**
 *	Rotate the photo a degree
 */
if($url -> opd(URL_EXEC) == 90 || $url -> opd(URL_EXEC) == 180 || $url -> opd(URL_EXEC) == 270) {
	$editImg = new Image($url -> op(0), 'jpg');
	if($editImg -> rotate($url -> opd(URL_EXEC))) {
		$editImg -> removeThumbnails();
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	$main -> alertPrepare(false);
}

if($setup -> image <> '') {
	$img = explode(';', $setup -> image);
	$n = count($img);
} else {
	$n = 0;
}

$ex = explode('.', $url -> op(0));
$name = $ex[0];
$format = $ex[1];

/**
 *	Add URL to history
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
require_once(INC_ADMIN_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('admin/index'));
$bc -> add($url -> getLink('admin/data/image-list'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-image-line icon"></span><?php echo($meta['title']); ?></h1>
				<div id="photo-editor"><img src="<?php echo($url -> getUrl('tool/image', false, '/'.$url -> op(0))); ?>" alt=""></div>
				<div class="buttons">
					<a href="<?php echo($url -> getUrl('admin/data/image-list')); ?>" class="button button-2">Powrót</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-90')); ?>" class="button">Obróć o 90&#186;</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-180')); ?>" class="button">Obróć o 180&#186;</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-270')); ?>" class="button">Obróć o 270&#186;</a>
				</div>
			</section>
			<section class="toggle">
				<h2><span class="ri-link-m icon"></span>Miniatury & link</h2>
				<table>
					<tr>
						<th>Szerokość</th>
						<th>Wysokość</th>
						<th>Link</th>
					</tr>
					<tr>
						<td>Oryginał</td>
						<td>Oryginał</td>
						<td><a href="<?php echo($url -> getUrl('tool/image', false, '/'.$url -> op(1))); ?>" target="_blank"><?php echo($url -> getUrl('tool/image', false, '/'.$url -> op(0).'/'.$url -> op(1))); ?></a></td>
					</tr>
					<?php foreach($img as $v) { $ex = explode('x', $v); ?>
					<tr>
						<td><?php echo($ex[0]); ?> px</td>
						<td><?php echo($ex[1]); ?> px</td>
						<td><a href="<?php echo($url -> getUrl('tool/image', false, '/'.$name.'.'.$v.'.'.$format)); ?>" target="_blank"><?php echo($url -> getUrl('tool/image', false, '/'.$name.'.'.$v.'.'.$format)); ?></a></td>
						</tr>
					<?php } ?>
				</table>
			</section>
			<section class="toggle">
				<h2><span class="ri-delete-bin-line icon"></span>Usuń zdjęcie</h2>
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

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>