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
 *	Get item data
 */
$editItem = new Item($url -> op(0));
if(!($editItem -> itemId > 0)) {
	$url -> redirect(404);
}

/**
 *	Initialize feature class
 */
$relFeature = new Feature;

/**
 *	Save changes to the item
 */
if($url -> op(1) == URL_EXEC) {
	$vd = new Validate;
	//$vd -> isValue($_POST['title'], 'Nazwa firmy');
	$vd -> isInt($_POST['category'], 'Kategoria');
	$vd -> isValue($_POST['content'], 'Treść');
	//$vd -> isValue($_POST['nip'], 'NIP');
	//$vd -> isEmail($_POST['email'], 'Adres e-mail');
	$vd -> isValue($_POST['city'], 'Miejscowość');
	$vd -> isValue($_POST['address'], 'Adres');
	$vd -> isValue($_POST['postcode'], 'Kod pocztowy');
	$vd -> isValue($_POST['region'], 'Województwo');
	$vd -> isValue($_POST['phone'], 'Telefon');



	/**
	 *	Validation passed - save to database
	 */
	if($vd -> pass() == true) {	
		//$editItem -> title = $_POST['title'];
		$editItem -> category_id = $_POST['category'];
		$editItem -> category_id_2 = $_POST['category-2'];
		$editItem -> category_id_3 = $_POST['category-3'];
		$editItem -> category_id_4 = $_POST['category-4'];
		$editItem -> category_id_5 = $_POST['category-5'];
		$editItem -> content = $_POST['content'];
		$editItem -> region = $_POST['region'];
		$editItem -> city = $_POST['city'];
		$editItem -> postcode = $_POST['postcode'];
		$editItem -> address = $_POST['address'];
		//$editItem -> email = $_POST['email'];
		$editItem -> phone = $_POST['phone'];
		//$editItem -> nip = $_POST['nip'];
		if($_POST['www'] <> '') $editItem -> www = ((!preg_match('(http|https)', $_POST['www'])) ? 'http://' : '').$_POST['www'];

		$categories = '';
		if(is_array($_POST['categories'])) {
			foreach($_POST['categories'] as $k => $v) {
				$categories .= '('.$k.')';
			}
		}
		$editItem -> categories = $categories;

		$main -> alertPrepare(true);
		$url -> redirect(null, false, '/'.$url -> op(0));
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

$categories = explode(')', str_replace('(', '', $editItem -> categories));

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
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('user/index'));
$bc -> add($url -> getLink('item/add-list'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<?php if($editItem -> validity < time() && $editItem -> premium < time()) echo('<section class="alert">Pamiętaj, że pełne dane wizytówki wyświetlane są w płatnych pakietach. Darmowa wersja zawiera jedynie okrojonny widok.</section>'); ?>
			
			<?php require_once('./module/item/bookmark.php'); ?>
			<section>
				<h1><span class="ri-file-edit-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/'.URL_EXEC)); ?>" id="edit-item">
					<div class="cols cols-2">
						<label>
							Nazwa firmy
							<input type="text" name="title" disabled="disabled" value="<?php echo($editItem -> title); ?>">
						</label>
						
						</label>
							NIP
							<input type="text" name="nip" disabled="disabled" value="<?php echo($editItem -> nip); ?>">
						</label>
					</div>
					
					<label>
							Kategoria główna
							<select name="category" required="required" id="category-select">
								
<?php
	
$catList = new Category;
if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<option value="'.$r['category_id'].'"'.(($editItem -> category_id == $r['category_id']) ? ' selected="selected"' : '').'>'.$r['name'].'</option>');
	}
}
	
?>

							</select>
						</label>
					<label>Kategorie dodatkowe</label>
					<div id="category-tree">

<?php

if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<label>');
		echo('<input type="checkbox" class="cat-head" id="cat-head-'.$r['category_id'].'" name="categories['.$r['category_id'].']" value="1"'.((in_array($r['category_id'], $categories)) ? ' checked="checked"' : '').'>');
		echo($r['name']);
		echo('</label>');
		if($catList -> countCategoryList($r['category_id']) > 0) {
			foreach($catList -> getCategoryList($r['category_id']) as $r2) {
				echo('<label class="cat-hide cat-head-'.$r['category_id'].'" style="padding-left: 20px;">');
				echo('<input type="checkbox" name="categories['.$r2['category_id'].']" value="1"'.((in_array($r2['category_id'], $categories)) ? ' checked="checked"' : '').'>');
				echo($r2['name']);
				echo('</label>');
			}
		}
	}
}
	
?>
					
					</div>
					<label>
						Opis firmy
						<textarea name="content" required="required" placeholder="Opisz swoją firmę i świadczone usługi"><?php echo($editItem -> content); ?></textarea>
					</label>
					<h2>Lokalizacja</h2>
					<div class="cols cols-4">
						<label>
							Województwo
							<select name="region" required="required">
								<option value="">Wybierz</option>
								<?php foreach($regionName as $k => $v) echo('<option value="'.$k.'"'.(($editItem -> region == $k) ? ' selected="selected"' : '').'>'.$v.'</option>'); ?>
							</select>
						</label>
						<label>
							Miejscowość
							<input type="text" name="city" required="required" value="<?php echo($editItem -> city); ?>">
						</label>
						<label>
							Kod pocztowy
							<input type="text" name="postcode" required="required" placeholder="00-000" value="<?php echo($editItem -> postcode); ?>">
						</label>
						<label>
							Adres
							<input type="text" name="address" required="required" value="<?php echo($editItem -> address); ?>">
						</label>
					</div>
					<h2>Kontakt</h2>
					<div class="cols cols-3">
						<label>
							Adres e-mail
							<input type="text" name="email" disabled="disabled" value="<?php echo($editItem -> email); ?>">
						</label>
						<label>
							Numer telefonu
							<input type="text" name="phone" required="required" placeholder="+48" value="<?php echo($editItem -> phone); ?>">
						</label>
						<label>
							Strona www
							<input type="text" name="www" placeholder="http://" value="<?php echo($editItem -> www); ?>">
						</label>
					</div>

<?php

if($relFeature -> countItemFeatureRel($editItem -> itemId) > 0) {
	foreach($relFeature -> getItemFeatureRel($editItem -> itemId, false) as $f) {
		echo('<input type="hidden" name="feature['.$f['feature_id'].']" value="'.$f['value'].'">'."\n");
	}
}

?>
					
					<div class="buttons">
						<input type="submit" value="Zapisz">
						<a href="<?php echo($url -> getUrl('item/edit-delete', false, '/'.$editItem -> itemId)); ?>" class="underline section-bottom-right">Usuń firmę</a>
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>