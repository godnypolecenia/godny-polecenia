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
 *	This file manages slider settings
 */

$slider = explode(';', $setup -> slider);
$slider_text = explode(';', $setup -> slider_text);

/**
 *
 */

if($url -> op(0) == URL_ADD && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload');
				if($name <> '') {
					if($setup -> slider <> '') {
						$setup -> slider .= ';';
					}
					$setup -> slider .= $name.'.000000.ffffff';

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

if($url -> op(0) == URL_SAVE && is_array($_POST)) {
	foreach($_POST as $k => $v) {
		$setup -> $k = $v;
	}
	$main -> alertPrepare(true);
	$url -> redirect();
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
				<h1><span class="ri-slideshow-2-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

if($slider[0] <> '') {
	echo('<table>');
	echo('<tr>');
	echo('<th>Zdjęcie</th>');
	echo('<th>Kolor bazowy</th>');
	echo('<th>Kolor czcionki</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($slider as $k => $v) {
		$ex = explode('.', $v);
		$name = $ex[0];
		$format = $ex[1];
		$bgColor = $ex[2];
		$color = $ex[3];
		
		echo('<tr>');
		echo('<td><a href="'.$url -> getUrl('tool/image', false, '/'.$v).'" data-lightbox="galeria"><img src="'.$url -> getUrl('tool/image', false, '/'.$name.'.300x150.'.$format).'" alt="" class="image"></a></td>');
		echo('<td>#'.$bgColor.'</td>');
		echo('<td>#'.$color.'</td>');
		echo('<td><a href="'.$url -> getUrl('admin/setup/slider', false, '/'.$k).'">Zarządzaj</a></td>');
		echo('</tr>');
		echo("\n");
	}
	echo('</table>');
} else {
	echo('<p>Niczego nie znaleziono</p>');
}

?>

			</section>
			<section class="toggle">
				<h2><span class="ri-image-add-line icon"></span>Wgraj slajdy</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<br>
				<a href="<?php echo($url -> getUrl()); ?>" class="button">Zapisz</a>
			</section>
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Ustawienia slidera</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Czas [s] wyświetlania slajdu
						<input type="text" name="slider-time" value="<?php echo($setup -> slider_time); ?>" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>	
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>