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
 *	------------------------------
 *	This file manages the category
 *	------------------------------
 */

/**
 *	Get the category data
 */
$editCat = new Category;
if(!$editCat -> getCategoryById($url -> op(0))) {
	require_once('./module/tool/404.php');
	exit;
}

/**
 *	Initialize feature class
 */
$relFeature = new Feature;

/**
 *	Save change data
 */
if($url -> op(1) == URL_SAVE) {
	$editCat -> name = $_POST['name'];
	$editCat -> content = $_POST['content'];
	
	if($_POST['parent'] <> $editCat -> parent_id) {
		$editCat -> changeParent($_POST['parent']);
	}
		
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editCat -> categoryId);
}

/**
 *	Set new icon
 */
if($url -> op(1) == URL_SET && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload');
				if($name <> '') {
					$editCat -> icon = $name;
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

/**
 *	Delete category
 */
if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	$editCat -> delete();

	$main -> alertPrepare(true);
	$url -> redirect('item/admin/category-list');
}

/**
 *	Add a feature to the category
 */
if($url -> op(1) == URL_ADD) {
	$relFeature = new Feature;
	$relFeature -> addCategoryFeatureRel($editCat -> categoryId, $_POST['feature'], $_POST['required']);

	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editCat -> categoryId);
}

/**
 *	Delete feature from category
 */
if($url -> opd(URL_CLEAR) > 0) {
	$relFeature = new Feature;
	$relFeature -> deleteCategoryFeatureRel($editCat -> categoryId, $url -> opd(URL_CLEAR));

	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editCat -> categoryId);
}

/**
 *	Move the feature up
 */
if($url -> op(1) == URL_UP && $url -> op(2) > 0) {
	if($relFeature -> yPositionUp($editCat -> categoryId, $url -> op(2))) {
		$main -> alertPrepare(true);
	} else {
		$main -> alertPrepare(false);
	}
	$url -> redirect(null, false, '/'.$editCat -> categoryId);
}

/**
 *	Move the feature down
 */
if($url -> op(1) == URL_DOWN && $url -> op(2) > 0) {
	if($relFeature -> yPositionDown($editCat -> categoryId, $url -> op(2))) {
		$main -> alertPrepare(true);
	} else {
		$main -> alertPrepare(false);
	}
	$url -> redirect(null, false, '/'.$editCat -> categoryId);
}

/**
 *	Change the required feature
 */
if($url -> op(1) == URL_CHANGE && $url -> op(2) > 0) {
	$relFeature -> changeCategoryFeatureRelRequired($editCat -> categoryId, $url -> op(2));
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editCat -> categoryId);
}

/**
 *	Get the category tree
 */
$editCatList = new Category;
$countCat = $editCatList -> countCategoryList();

/**
 *	Take the features for the category
 */
$featureList = new Feature;
$countFeature = $featureList -> countFeatureList();
$countFeatureListOfCategory = $featureList -> countFeatureListOfCategory($editCat -> categoryId);
$featureUsed = [];

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
$bc -> add($url -> getLink('item/admin/category-list'));
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
							Drzewo kategorii
							<select name="parent">
								<option value="0"<?php if($editCat -> parent_id == 0) echo(' selected="selected"'); ?>>Kategoria główna</option>
								<?php foreach($editCatList -> getCategoryList(0) as $r) echo('<option value="'.$r['category_id'].'"'.(($editCat -> parent_id == $r['category_id']) ? ' selected="selected"' : '').'>'.$r['name'].'</a>'); ?>
							</select>
						</label>
						
						
						<label>
							Nazwa
							<input type="text" name="name" value="<?php echo($editCat -> name); ?>">
						</label>
					</div>
					<label>
						Treść do pozycjonowania
						<textarea name="content" class="wysiwyg"><?php echo($editCat -> content); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz zmiany">
					</div>
				</form>
			</section>
			
			<?php if($countFeatureListOfCategory > 0) { ?>
			<section class="toggle">
				<h2><span class="ri-stack-line icon"></span>Cechy</h2>
				<table>
					<tr>
						<th>Cecha</th>
					
						<th>Pozycja</th>

						<th>Opcje</th>
					</tr>

<?php

foreach($featureList -> getFeatureListOfCategory($editCat -> categoryId) as $r) {
	echo('<tr>');
	echo('<td><a href="'.$url -> getUrl('item/admin/feature', false, '/'.$r['feature_id']).'">'.$r['name'].'</a></td>');

	echo('<td>'.(($r['y'] > 1) ? '<a href="'.$url -> getUrl(null, false, '/'.$editCat -> category_id.'/'.URL_UP.'/'.$r['feature_id']).'" class="ri-arrow-up-line"></a>' : '<span class="ri-arrow-up-line"></span>').' '.(($r['y'] < $relFeature -> getCategoryFeatureRelHighestY($editCat -> categoryId)) ? '<a href="'.$url -> getUrl(null, false, '/'.$editCat -> category_id.'/'.URL_DOWN.'/'.$r['feature_id']).'" class="ri-arrow-down-line"></a>' : '<span class="ri-arrow-down-line"></span>').'</td>');
	echo('<td><a href="'.$url -> getUrl(null, true, '/'.URL_CLEAR.'-'.$r['feature_id']).'">Usuń</a></td>');
	echo('</tr>');
	$featureUsed[] = $r['feature_id'];
}
	
?>

				</table>
			</section>
			<?php } ?>
			<section class="toggle">
				<h2><span class="ri-stack-line icon"></span>Dodaj cechę</h2>
				<?php if($countFeature > 0) { ?>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_ADD)); ?>">
					
						<label>
							Cecha
							<select name="feature" required="required">
								<option value="">Wybierz</option>
								<?php foreach($featureList -> getFeatureList() as $r) echo('<option value="'.$r['feature_id'].'"'.((in_array($r['feature_id'], $featureUsed)) ? ' disabled="disabled"' : '').'>'.$r['name'].((in_array($r['feature_id'], $featureUsed)) ? ' (już dodano)' : '').'</option>'); ?>
							</select>
						</label>
						
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<?php } else { ?>
				<section>
					<p>Nie utworzono jeszcze żadnych cech. <a href="<?php echo($url -> getUrl('item/admin/features')); ?>" class="underline">Zarządzaj cechami</a></p>
				</section>
				<?php } ?>
			</section>
		
			<section class="toggle">
				<h2><span class="ri-image-line icon"></span>Ikona</h2>
				<?php if($editCat -> icon <> '') echo('<section><img src="'.$url -> getUrl('tool/image', false, '/'.$editCat -> icon).'" alt=""></section>'); ?>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SET)); ?>" enctype="multipart/form-data">
					<label>
						<input type="file" name="file" required="required">
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
						Potwierdzam chęć usunięcia tej kategorii
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