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
 *	This file allows you to edit the user account
 */

/**
 *	
 */
if($url -> op(0) == URL_ADD) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload', $name);
				if($name <> '') {
					
					$user -> avatar = $name;
					//$main -> alertPrepare(true);
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

if($url -> op(0) == URL_DEL && $_POST['delete'] == 1) {
	$delImage = new Image($user -> avatar, 'jpg');
	$delImage -> remove();
	$user -> avatar = '';
	$main -> alertPrepare(true);
	$url -> redirect();
}

$url -> addBackUrl();

$ex = explode('.', $user -> avatar);
$name = $ex[0];
$format = end($ex);

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
$bc -> add($url -> getLink('user/manage'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>	
			<?php require_once('./module/user/bookmark.php'); ?>
			<section>
				<h1><span class="ri-image-line icon"></span><?php echo($meta['title']); ?></h1>
				<?php if($user -> avatar <> '') { ?>
				<img src="<?php echo($url -> getUrl('tool/image', false, '/'.$name.'.150x150.'.$format)); ?>" alt="<?php echo($user -> name); ?>" class="avatar">
				<?php } else { ?>
				<p>Nie dodano avatara</p>
				<?php } ?>
			</section>
			<section class="toggle">
				<h2><span class="ri-image-edit-line icon"></span>Zmień avatar</h2>
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
			<?php if($user -> avatar <> '') { ?>
			<section class="toggle">
				<h2><span class="ri-close-circle-line icon"></span>Usuń avatar</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia avataru
					</label>
					<div class="buttons">
						<input type="submit" value="Usuń">
					</div>
				</form>
			</section>
			<?php } ?>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>