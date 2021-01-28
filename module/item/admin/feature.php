<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(2);

/**
*	-----------------------------
 *	This file manages the feature
 *	-----------------------------
 */

/**
 *	Get the feature data
 */
$editFeature = new Feature;
if(!$editFeature -> getFeatureById($url -> op(0))) {
	require_once('./module/tool/404.php');
	exit;
}

/**
 *	Save change data
 */
if($url -> op(1) == URL_SAVE) {
	$editFeature -> name = $_POST['name'];
	$editFeature -> label = $_POST['label'];
	$editFeature -> type = $_POST['type'];
	$editFeature -> value = str_replace(["\n", "\r"], [';', ''], $_POST['value']);
	$editFeature -> placeholder = $_POST['placeholder'];
	$editFeature -> prefix = $_POST['prefix'];
	$editFeature -> sufix = $_POST['sufix'];
		
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editFeature -> featureId);
}

/**
 *	Delete feature
 */
if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	$editFeature -> delete($editFeature -> featureId);
	$main -> alertPrepare(true);
	$url -> redirect('item/admin/feature-list');
}

/**
 *	Set new icon
 */
if($url -> op(1) == URL_SET) {
	$editFeature -> icon = $_POST['icon'];

	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editFeature -> featureId);
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
$bc -> add($url -> getLink('item/admin/index'));
$bc -> add($url -> getLink('item/admin/feature-list'));
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
							Nazwa cechy
							<input type="text" name="label" placeholder="Np. podejście do pacjenta" value="<?php echo($editFeature -> label); ?>" required="required">
						</label>
						<label>
							Pomocniczna nazwa cechy
							<input type="text" name="name" placeholder="Np. podejście do pacjenta (ginekolog)" value="<?php echo($editFeature -> name); ?>" required="required">
						</label>

					</div>
					<input type="hidden" name="type" value="2">
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
						Potwierdzam chęć usunięcia tej cechy
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