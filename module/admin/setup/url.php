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
 *	This file manages the URL
 */

$editUrl = new Url;
if(!$editUrl -> getUrlById($url -> op(0))) {
	require_once('./module/tool/404.php');
	exit;
}

/**
 *
 */

if($url -> op(1) == URL_SAVE) {
	$vd = new Validate;
	
	$vd -> isValue($_POST['button'], 'Przycisk');
	$vd -> isValue($_POST['url'], 'Adres URL');
	
	if($vd -> pass() == true) {
	
		$editUrl -> button = $_POST['button'];
		$editUrl -> title = (($_POST['title'] <> '') ? $_POST['title'] : $_POST['button']);
		$editUrl -> url = toUrl($_POST['url']);
		$editUrl -> description = $_POST['description'];
		$editUrl -> keywords = $_POST['keywords'];
		$editUrl -> content = $_POST['content'];
		$editUrl -> index = $_POST['index'];
		$editUrl -> feed = $_POST['feed'];

		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	$editUrl -> delete();
	$main -> alertPrepare(true);
	$url -> redirect('admin/setup/url-list');
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
$bc -> add($url -> getLink('admin/url-list'));
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
					<div class="cols cols-3">
						<label>
							Adres URL
							<input type="text" name="url" value="<?php echo($editUrl -> url); ?>" required="required" maxlength="100">
						</label>
						<label>
							Przycisk
							<input type="text" name="button" value="<?php echo($editUrl -> button); ?>" required="required" maxlength="100" placeholder="Treść na przyciskach i linkach">
						</label>
						<label>
							Tytuł
							<input type="text" name="title" value="<?php echo($editUrl -> title); ?>" required="required" maxlength="100" placeholder="Tytuł do pozycjonowania">
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