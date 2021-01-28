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
 *	This file manages categories
 */

if($url -> op(0) == URL_ADD) {
	$newCat = new Category;
	$newCatId = $newCat -> add($_POST['parent'], $_POST['name']);
	if($newCatId > 0) {
		$newCat -> content = $_POST['content'];
		$newCat -> icon = '';
		
		$main -> alertPrepare(true);
		$url -> redirect('item/admin/category', false, '/'.$newCatId);
	} else {
		$vd -> putError(FORM_ERR_UNKNOWN);
	}
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

$catList = new Category;
$countCat = $catList -> countCategoryList();

if($url -> op(0) == URL_UP && $url -> op(1) > 0) {
	if($catList -> yPositionUp($url -> op(1))) {
		$main -> alertPrepare(true);
	} else {
		$main -> alertPrepare(false);
	}
	$url -> redirect();
}

if($url -> op(0) == URL_DOWN && $url -> op(1) > 0) {
	if($catList -> yPositionDown($url -> op(1))) {
		$main -> alertPrepare(true);
	} else {
		$main -> alertPrepare(false);
	}
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
			<section class="toggle">
				<h2><span class="ri-folder-add-line icon"></span>Dodaj kategorię</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
					<div class="cols cols-2">
						<label>
							Drzewo kategorii
							<select name="parent">
								<option value="0"<?php if($_POST['parent_id'] == 0) echo(' selected="selected"'); ?>>Kategoria główna</option>
								<?php foreach($catList -> getCategoryList(0) as $r) echo('<option value="'.$r['category_id'].'"'.(($_POST['parent'] == $r['category_id']) ? ' selected="selected"' : '').'>'.$r['name'].'</a>'); ?>
							</select>
						</label>

						
						<label>
							Nazwa
							<input type="text" name="name" value="<?php echo($_POST['name']); ?>">
						</label>
					</div>
					<a href="#" class="show-container underline" id="s1">Pokaż więcej opcji</a>
					<div class="hide-container" id="s1-container">
						<div class="cols cols-3">
							<label>
								Tytuł
								<input type="text" name="title" value="<?php echo($_POST['title']); ?>" placeholder="Znacznik <title>" maxlength="255">
							</label>
							<label>
								Opis
								<input type="text" name="description" value="<?php echo($_POST['description']); ?>" placeholder="Znacznik <meta description>" maxlength="255">
							</label>
							<label>
								Słowa kluczwe
								<input type="text" name="keywords" value="<?php echo($_POST['keywords']); ?>" placeholder="Znacznik <meta keywords>" maxlength="255">
							</label>
						</div>
						<label>
							Treść do pozycjonowania
							<textarea name="content" class="wysiwyg"><?php echo($_POST['content']); ?></textarea>
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
			<section>
				<h1><span class="ri-folders-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

if($countCat > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>ID</th>');
	echo('<th>Kategoria</th>');
	echo('<th>Pozycja</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<tr>');
		echo('<td>'.$r['category_id'].'</td>');
		echo('<td>'.$r['name'].'</td>');
		echo('<td>'.(($r['y'] > 1) ? '<a href="'.$url -> getUrl(null, false, '/'.URL_UP.'/'.$r['category_id']).'" class="ri-arrow-up-line"></a>' : '<span class="ri-arrow-up-line"></span>').' '.(($r['y'] < $catList -> getHighestY($r['parent_id'])) ? '<a href="'.$url -> getUrl(null, false, '/'.URL_DOWN.'/'.$r['category_id']).'" class="ri-arrow-down-line"></a>' : '<span class="ri-arrow-down-line"></span>').'</td>');
		echo('<td><a href="'.$url -> getUrl('item/admin/category', false, '/'.$r['category_id']).'">Zarządzaj</a></td>');
		echo('</tr>');
		foreach($catList -> getCategoryList($r['category_id']) as $rr) {
			echo('<tr>');
			echo('<td>'.$rr['category_id'].'</td>');
			echo('<td>'.$r['name'].' <span class="ri-arrow-right-line"></span> '.$rr['name'].'</td>');
			echo('<td>'.(($rr['y'] > 1) ? '<a href="'.$url -> getUrl(null, false, '/'.URL_UP.'/'.$rr['category_id']).'" class="ri-arrow-up-line"></a>' : '<span class="ri-arrow-up-line"></span>').' '.(($rr['y'] < $catList -> getHighestY($rr['parent_id'])) ? '<a href="'.$url -> getUrl(null, false, '/'.URL_DOWN.'/'.$rr['category_id']).'" class="ri-arrow-down-line"></a>' : '<span class="ri-arrow-down-line"></span>').'</td>');
			echo('<td><a href="'.$url -> getUrl('item/admin/category', false, '/'.$rr['category_id']).'">Zarządzaj</a></td>');
			echo('</tr>');
		}
	}
	echo('</table>');
} else {
	echo('<p>Niczego nie znaleziono</p>');
}

?>
				
			</section>
			
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>