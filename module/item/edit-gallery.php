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

/**
 *	Add photo
 */
if($url -> op(1) == URL_ADD) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload');
				if($name <> '') {
					if($editItem -> gallery <> '') {
						$editItem -> gallery .= ';'.$name;
					} else {
						$editItem -> gallery = $name;
					}
					
					//$main -> alertPrepare(true);
					$url -> redirect(null, false, '/'.$editItem -> itemId);
				} else {
					$main -> alertPrepare(false, FILE_ERR_UPLOAD);
				}
			} else {
				$main -> alertPrepare(false, FILE_ERR_WEIGHT);
			}
		} else {
			$main -> alertPrepare(false, FILE_ERR_FORMAT);
		}
	} else {
		$main -> alertPrepare(false, FILE_ERR_NULL);
	}
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
$bc -> add($url -> getLink('item/edit', true));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<?php if($editItem -> validity < time() && $editItem -> premium < time()) echo('<section class="alert">Pamiętaj, że pełne dane wizytówki wyświetlane są w płatnych pakietach. Darmowa wersja zawiera jedynie okrojonny widok.</section>'); ?>
			
			<?php require_once('./module/item/bookmark.php'); ?>
			<section>
				<h1><span class="ri-image-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

if($gal[0]['file'] <> '') {
	echo('<section>Kliknij na zdjęciu, żeby nim zarządzać</section>');
	echo('<div class="gallery">');
	foreach($gal as $k => $v) {
		echo('<a href="'.$url -> getUrl('item/photo', false, '/'.$url -> op(0).'/'.$k.'/'.$v['file']).'"><img src="'.$url -> getUrl('tool/image', false, '/'.$v['name'].'.150x150.'.$v['format']).'" alt=""></a>'."\n");
	}
	echo('</div>');
} else {
	echo('<p>Galeria jest pusta</p>');
}

?>
				
			</section>
			<section>
				<h2><span class="ri-image-add-line icon"></span>Dodaj zdjęcia</h2>
				<section>
					Dozwolone formaty: JPG, PNG, GIF
				</section>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_ADD)); ?>" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<div class="buttons">
					<a href="<?php echo($url -> getUrl(null, true)); ?>" class="button">Zapisz</a>
				</div>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>