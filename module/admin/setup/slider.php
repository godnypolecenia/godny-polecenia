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
 *	This file manages slide settings
 */

$slider = explode(';', $setup -> slider);
if(!($slider[$url -> op(0)] <> '')) {
	require_once('./module/tool/404.php');
	exit;
}

$ex = explode('.', $slider[$url -> op(0)]);
$name = $ex[0];
$format = $ex[1];
$bgColor = $ex[2];
$color = $ex[3];

/**
 *
 */

if($url -> op(1) == URL_SAVE) {
	
	$_POST['bgcolor'] = str_replace('#', '', $_POST['bgcolor']);
	$_POST['color'] = str_replace('#', '', $_POST['color']);
	
	$slider[$url -> op(0)] = $name.'.'.$format.'.'.$_POST['bgcolor'].'.'.$_POST['color'];
	$setup -> slider = implode(';', $slider);
	
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_IMAGE && isset($_FILES)) {
	$ex = explode('.', $_FILES['file']['name']);
	$newName = password(time().rand(0, 9999));
	$new_file = './data/upload/'.$newName.'.'.end($ex);
	if($check = getimagesize($_FILES['file']['tmp_name'])) {
		if(!file_exists($new_file)) {
			if($_FILES['file']['size'] < 100000000000) {
				if(in_array($_FILES['file']['type'], array('image/jpg', 'image/jpeg', 'image/png', 'image/gif'))) {
					if(move_uploaded_file($_FILES['file']['tmp_name'], $new_file)) {
						$slider[$url -> op(0)] = $newName.'.'.end($ex).'.'.$bgColor.'.'.$color;
						$setup -> slider = implode(';', $slider);

						$main -> alertPrepare(true);
						$url -> redirect(null, false, '/'.$url -> op(0));
					} else {
						$main -> alertPrepare(false, FILE_ERR_UPLOAD);
					}
				} else {
					$main -> alertPrepare(false, FILE_ERR_FORMAT);
				}
			} else {
				$main -> alertPrepare(false, FILE_ERR_WEIGHT);
			}
		} else {
			$main -> alertPrepare(false, FILE_ERR_SEND);
		}
	} else {
		$main -> alertPrepare(false, FILE_ERR_NULL);
	}
}

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	unset($slider[$url -> op(0)]);
	$setup -> slider = implode(';', $slider);
	
	$main -> alertPrepare(true);
	$url -> redirect('admin/setup/slider-list');
}

/**
 *	Rotate the photo a degree
 */
if($url -> opd(URL_EXEC) == 90 || $url -> opd(URL_EXEC) == 180 || $url -> opd(URL_EXEC) == 270) {
	$editImg = new Image($name.'.'.$format);
	if($editImg -> rotate($url -> opd(URL_EXEC))) {
		$editImg -> removeThumbnails();
		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	$main -> alertPrepare(false);
}

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
$bc -> add($url -> getLink('admin/setup/slider-list'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-slideshow-2-line icon"></span><?php echo($meta['title']); ?></h1>
				<div id="photo-editor"><img src="<?php echo($url -> getUrl('tool/image', false, '/'.$name.'.'.$format)); ?>" alt=""></div>
				<div class="buttons">
					<a href="<?php echo($url -> getUrl('admin/setup/slider-list')); ?>" class="button button-2">Powrót</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-90')); ?>" class="button">Obróć o 90&#186;</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-180')); ?>" class="button">Obróć o 180&#186;</a>
					<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC.'-270')); ?>" class="button">Obróć o 270&#186;</a>
				</div>
			</section>
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Kolory</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SAVE)); ?>">
					<div class="cols cols-2">
						<label>
							Bazowy kolor tła
							<input type="text" name="bgcolor" value="<?php echo('#'.$bgColor); ?>" required="required">
						</label>
						<label>
							Kolor czcionki
							<input type="text" name="color" value="<?php echo('#'.$color); ?>" required="required">
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Zmień">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-image-add-line icon"></span>Zmień zdjęcie</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_IMAGE)); ?>" enctype="multipart/form-data">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Zmień">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-close-circle-line icon"></span>Usuń slajd</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tego slajdu
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