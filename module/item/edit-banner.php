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
$exGal = explode(';', $editItem -> banner);
foreach($exGal as $v) {
	$ex = explode('.', $v);
	$banner = [
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
					
						$editItem -> banner = $name;
				
					
					$main -> alertPrepare(true);
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

if($url -> op(1) == URL_DEL) {
	$editItem -> banner = '';
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
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
				<section>
					Uwaga! Banner widoczny jest jedynie w pakiecie Premium
				</section>

<?php

if($banner['file'] <> '') {
	echo('<div><img src="'.$url -> getUrl('tool/image', false, '/'.$banner['name'].'.320x240.'.$banner['format']).'" alt=""></div>');
	echo('<br><a href="'.$url -> getUrl(null, true, '/'.URL_DEL).'" class="button">Usuń banner</a>');
} else {	
	echo('<p>Brak bannera</p>');
}

?>
				
			</section>
			<section>
				<h2><span class="ri-image-add-line icon"></span>Dodaj banner</h2>
				<section>
					Dozwolone formaty: JPG, PNG, GIF
				</section>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_ADD)); ?>" enctype="multipart/form-data">
					<label>
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>