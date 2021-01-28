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
 *	This file manages the images
 */

if($url -> op(0) == URL_ADD && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload');
				if($name <> '') {
					$main -> alertPrepare(true);
					$url -> redirect();
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
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-image-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

$array = [];
$dir = './data/upload/';
if($handle = opendir($dir)) {
	while(false !== ($file = readdir($handle))) {
		if(is_file($dir.$file) && $file <> 'index.html' && $file <> '.htaccess') {
			$ex = explode('.', $file);
			$array[] = array($ex[0], filesize($dir.$file), end($ex));
		}
	}
}
$n = count($array);

if($n == 0) {
	echo('<p>Niczego nie znaleziono</p>');
} else {
	echo('<div class="gallery">');
	foreach($array as $k => $v) {
		echo('<a href="'.$url -> getUrl('admin/data/image', false, '/'.$v[0].'.'.$v[2]).'"><img src="'.$url -> getUrl('tool/image', false, '/'.$v[0].'.150x150.'.$v[2]).'" alt="" class="image"></a>');
	}
	echo('</div>');
}

?>

			</section>
			<section class="toggle">
				<h2><span class="ri-image-add-line icon"></span>Wgraj zdjęcia</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<br>
				<a href="<?php echo($url -> getUrl()); ?>" class="button">Dodaj</a>
			</section>
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Wymiary zdjęć</h2>
				<p><a href="<?php echo($url -> getUrl('admin/setup/image')); ?>" class="underline">Przejdź do ustawień</a></p>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>