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
 *	
 */

if(!($amenitie[$url -> op(0)])) {
	$url -> redirect(404);
}

/**
 *	Initialize feature class
 */
$relFeature = new Feature;

/**
 *
 */


if($url -> op(1) == URL_SAVE) {
	$amenitie[$url -> op(0)] = $_POST['name'];
	$setup -> amenitie = implode(';', $amenitie);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	unset($amenitie[$url -> op(0)]);
	$setup -> amenitie = implode(';', $amenitie);
	$main -> alertPrepare(true);
	$url -> redirect('item/admin/amenitie-list');
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
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-settings-2-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SAVE)); ?>">	
					<label>
						Nazwa udogodnienia 
						<input type="text" name="name" required="required" value="<?php echo($amenitie[$url -> op(0)]); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-delete-bin-line icon"></span>Usuń</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tej pozycji
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