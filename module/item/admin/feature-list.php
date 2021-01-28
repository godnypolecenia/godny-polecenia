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
 *	This file manages features
 */

$featureList = new Feature;
$countFeature = $featureList -> countFeatureList();

if($url -> op(0) == URL_ADD && $_POST['label'] <> '' && isset($featureType[$_POST['type']])) {
	$newFeatureId = $featureList -> add($_POST['type'], $_POST['label']);
	$featureList -> getFeatureById($newFeatureId);
	$featureList -> name = (($_POST['name'] <> '') ? $_POST['name'] : $_POST['label']);
	$featureList -> value = str_replace(["\n", "\r"], [';', ''], $_POST['value']);
	$featureList -> placeholder = $_POST['placeholder'];
	$featureList -> prefix = $_POST['prefix'];
	$featureList -> sufix = $_POST['sufix'];
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
$bc -> add($url -> getLink('item/admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-folders-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

if($countFeature > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>Nazwa</th>');

	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($featureList -> getFeatureList() as $r) {
		echo('<tr>');
		echo('<td>'.$r['name'].'</td>');

		echo('<td><a href="'.$url -> getUrl('item/admin/feature', false, '/'.$r['feature_id']).'">Zarządzaj</a></td>');
		echo('</tr>');
	}
	echo('</table>');
} else {
	echo('<p>Niczego nie znaleziono</p>');
}

?>
				
			</section>
			<section class="toggle">
				<h2><span class="ri-folder-add-line icon"></span>Dodaj cechę</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
					<div class="cols cols-2">
						<label>
							Nazwa cechy 
							<input type="text" name="label" placeholder="Np. podejście do pacjenta" required="required" value="<?php echo($_POST['label']); ?>">
						</label>
						<label>
							Pomocnicza nazwa cechy
							<input type="text" name="name" placeholder="Np. podejście do pacjenta (ginekolog)" value="<?php echo($_POST['name']); ?>">
						</label>
					</div>
					<input type="hidden" name="type" value="2">
					
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>