<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(6);

/**
 *	This file manages the content page
 */

$page = new Page;
if(!$page -> getPageById($url -> op(0))) {
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

echo $getEditUrl = $url -> getUrlByVar('page_id='.$page -> pageId);
if(!$getEditUrl) {
	require_once('./module/tool/404.php');
	exit;
}
$editUrl = new Url($getEditUrl);

/**
 *	
 */
if($url -> op(1) == URL_SAVE) {
	$vd = new Validate;
	$vd -> isValue($_POST['title'], 'Tytuł');

	if($vd -> pass() == true) {
		$page -> title = $_POST['title'];
		$page -> content = $_POST['content'];
		
		$editUrl -> url = $_POST['url'];
		$editUrl -> title = $_POST['title'];
		$editUrl -> button = $_POST['title'];
		$editUrl -> description = $_POST['description'];
		$editUrl -> keywords = $_POST['keywords'];
		$editUrl -> index = $_POST['index'];
		$editUrl -> feed = $_POST['feed'];

		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	$page -> delete();
	$main -> alertPrepare(true);
	$url -> redirect('page/admin/'.(($page -> group == 1) ? 'blog' : 'index'));
}

if($url -> opd(URL_ADD) == URL_IMAGE && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload');
				if($name <> '') {
					$page -> gallery = (($page -> gallery <> '') ? $page -> gallery.';' : '').$name;
					$main -> alertPrepare(true);
					$url -> redirect(null, false, '/'.$url -> op(0));
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

if($url -> opd(URL_DEL) == URL_IMAGE) {
	
	unset($gal[$url -> op(2)]);
	$page -> gallery = implode(';', $gal);

	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> opd(URL_SET) == URL_IMAGE) {
	
	$tmp = [0 => $gal[$url -> op(2)]];
	unset($gal[$url -> op(2)]);
	$page -> gallery = implode(';', array_merge($tmp, $gal));
	
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
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
require_once(INC_ADMIN_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('admin/index'));
if($page -> group == 0) $bc -> add($url -> getLink('page/admin/index'));
if($page -> group == 1) $bc -> add($url -> getLink('page/admin/blog'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-file-edit-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SAVE)); ?>">
					<div class="cols cols-2">
						<label>
							Tytuł
							<input type="text" name="title" value="<?php echo($page -> title); ?>" required="required" maxlength="50" id="page-title">
						</label>
						<label>
							Adres URL
							<input type="text" name="url" value="<?php echo($editUrl -> url); ?>" required="required" maxlength="50">
						</label>
					</div>
					<div class="cols cols-2">
						<label>
							Opis
							<input type="text" name="description" value="<?php echo($editUrl ->  description); ?>" placeholder="Znacznik <meta description>" maxlength="255">
						</label>
						<label>
							Słowa kluczwe
							<input type="text" name="keywords" value="<?php echo($editUrl -> keywords); ?>" placeholder="Znacznik <meta keywords>" maxlength="255">
						</label>
					</div>
					<label>
						Treść
						<textarea name="content" class="wysiwyg"><?php echo($page -> content); ?></textarea>
					</label>
					<input type="hidden" name="index" value="0">
					<label>
						<input type="checkbox" name="index" value="1"<?php if($editUrl -> index == 1) echo(' checked="checked"'); ?>>
						Indeksuj w przeglądarkach
					</label>
					<input type="hidden" name="feed" value="0">
					<label>
						<input type="checkbox" name="feed" value="1"<?php if($editUrl -> feed == 1) echo(' checked="checked"'); ?>>
						Opublikuj w kanale RSS
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz zmiany">
					</div>
				</form>
			</section>
			<?php if($page -> gallery <> '' && count($gal) > 0) { ?>
			<section class="toggle">
				<h2><span class="ri-image-line icon"></span>Galeria zdjęć</h2>
				<section><p>Kliknij na zdjęciu aby wyświetlić dodatkowe opcje.</p></section>
				<div class="gallery">

<?php

foreach($gal as $k => $v) {
	echo('<a href="'.$url -> getUrl('page/admin/photo', false, '/'.$page -> pageId.'/'.$k.'/'.$v['file']).'"><img src="'.$url -> getUrl('tool/image', false, '/'.$v['name'].'.150x150.'.$v['format']).'" alt=""></a>');
}

?>
				
				</div>
			</section>
			<?php } ?>
			<section class="toggle">
				<h2><span class="ri-image-add-line icon"></span>Dodaj zdjęcie</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_ADD.'-'.URL_IMAGE)); ?>" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<br>
				<a href="<?php echo($url -> getUrl(null, true)); ?>" class="button">Dodaj</a>
			</section>
			<section class="toggle">
				<h2><span class="ri-delete-bin-line icon"></span>Usuń</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tej strony
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