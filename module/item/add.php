<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */
 
if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyUser();


$catList = new Category;

/**
 *	Add item
 */
if($url -> op(0) == URL_EXEC) {
	$vd = new Validate;
	$vd -> isValue($_POST['title'], 'Nazwa firmy');
	$vd -> isInt($_POST['category'], 'Kategoria');
	$vd -> isValue($_POST['content'], 'Treść');
	$vd -> isValue($_POST['nip'], 'NIP');
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	$vd -> isValue($_POST['city'], 'Miejscowość');
	$vd -> isValue($_POST['address'], 'Adres');
	$vd -> isValue($_POST['postcode'], 'Kod pocztowy');
	$vd -> isValue($_POST['region'], 'Województwo');
	$vd -> isValue($_POST['phone'], 'Telefon');
	
	/**
	 *	Check the features
	 */
	if($_POST['category'] > 0) {
		$featureList = new Feature;
		foreach($featureList -> getFeatureListOfCategory($_POST['category']) as $r) {
			if($r['required'] == 1) {
				$vd -> isValue($_POST['feature'][$r['feature_id']], $r['name']);
			}
			if($_POST['feature'][$r['feature_id']] <> '') {
				if($r['type'] == 2) $vd -> isInt($_POST['feature'][$r['feature_id']], $r['name']);
				if($r['type'] == 3) $vd -> isPossible($_POST['feature'][$r['feature_id']], explode(';', $r['value']), $r['name']);
			}
		}
	}
	
	/**
	 *	Validation passed - add to database
	 */
	if($vd -> pass() == true) {
		
		$newItem = new Item;
		$itemId = $newItem -> add($_POST['title'], $user -> userId);
		
		if($itemId > 0) {
			//$newItem -> validity = time()+($setup -> validity_day*86400);
			$newItem -> category_id = $_POST['category'];
			$newItem -> category_id_2 = $_POST['category-2'];
			$newItem -> category_id_3 = $_POST['category-3'];
			$newItem -> category_id_4 = $_POST['category-4'];
			$newItem -> category_id_5 = $_POST['category-5'];
			$newItem -> content = $_POST['content'];
			$newItem -> nip = $_POST['nip'];
			if($_POST['region'] <> '') $newItem -> region = $_POST['region'];
			if($_POST['city'] <> '') $newItem -> city = $_POST['city'];
			if($_POST['postcode'] <> '') $newItem -> postcode = $_POST['postcode'];
			if($_POST['address'] <> '') $newItem -> address = $_POST['address'];
			if($_POST['email'] <> '') $newItem -> email = $_POST['email'];
			if($_POST['phone'] <> '') $newItem -> phone = $_POST['phone'];
			if($_POST['www'] <> '') $newItem -> www = ((!preg_match('(http|https)', $_POST['www'])) ? 'http://' : '').$_POST['www'];
			
			
			$categories = '';
			if(is_array($_POST['categories'])) {
				foreach($_POST['categories'] as $k => $v) {
					$categories .= '('.$k.')';
				}
				$newItem -> categories = $categories;
			}
			
			$services = [];
			if($catList -> countCategoryList($_POST['category']) > 0) {
				foreach($catList -> getCategoryList($_POST['category']) as $r) {
					$services[] = $r['name'].'{0}';
				}
				$newItem -> services = implode(';', $services);
			}
			
			if($_POST['category'] > 0) {
				foreach($featureList -> getFeatureListOfCategory($_POST['category']) as $r) {
					$featureList -> addItemFeatureRel($itemId, $r['feature_id'], $r['type'], $_POST['feature'][$r['feature_id']]);
				}
			}
			
			$newsletter = new Newsletter;
			$newsletter -> add($_POST['email']);
			
			send_mail($setup -> email, 'Nowa firma', 'W serwisie dodano nową firmę. Jej nazwa to <strong>'.$_POST['name'].'</strong>. Dodana przez użytkownika <strong>'.$user -> email.'</strong>');
			
			$url -> redirect('item/edit-time', false, '/'.$itemId);
		} else {
			$vd -> putError(false, FORM_ERR_UNKNOWN);
		}
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

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
$bc -> add($url -> getLink());
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-add-circle-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_EXEC)); ?>">
					<div class="cols cols-2">
						<label>
							Nazwa firmy
							<input type="text" name="title" required="required" value="<?php echo($_POST['title']); ?>">
						</label>
						<label>
							NIP
							<input type="text" name="nip" required="required" value="<?php echo($_POST['nip']); ?>">
						</label>
					</div>
					
						<label>
							Kategoria główna
							<select name="category" required="required" id="category-select">
								<option value="">Wybierz</option>
								
<?php

if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<option value="'.$r['category_id'].'"'.(($_POST['category'] == $r['category_id']) ? ' selected="selected"' : '').'>'.$r['name'].'</option>');
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
		echo('<input type="checkbox" class="cat-head" id="cat-head-'.$r['category_id'].'" name="categories['.$r['category_id'].']" value="1"'.(($_POST['categories'][$r['category_id']] == 1) ? ' checked="checked"' : '').'>');
		echo($r['name']);
		echo('</label>');
		if($catList -> countCategoryList($r['category_id']) > 0) {
			foreach($catList -> getCategoryList($r['category_id']) as $r2) {
				echo('<label class="cat-hide cat-head-'.$r['category_id'].'" style="padding-left: 20px;">');
				echo('<input type="checkbox" name="categories['.$r2['category_id'].']" value="1"'.(($_POST['categories'][$r2['category_id']] == 1) ? ' checked="checked"' : '').'>');
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
						<textarea name="content" required="required" placeholder="Opisz swoją firmę i świadczone usługi"><?php echo($_POST['content']); ?></textarea>
					</label>
					<h2>Lokalizacja</h2>
					<div class="cols cols-4">
						<label>
							Województwo
							<select name="region" required="required">
								<option value="0">Wybierz</option>
								<?php foreach($regionName as $k => $v) echo('<option value="'.$k.'"'.(($_POST['region'] == $k || (!isset($_POST['region']) && $user -> region == $k)) ? ' selected="selected"' : '').'>'.$v.'</option>'); ?>
							</select>
						</label>
						<label>
							Miejscowość
							<input type="text" name="city" required="required" value="<?php echo(($_POST['city'] <> '') ? $_POST['city'] : $user -> city); ?>">
						</label>
						<label>
							Kod pocztowy
							<input type="text" name="postcode" required="required" placeholder="00-000" value="<?php echo(($_POST['postcode'] <> '') ? $_POST['postcode'] : $user -> postcode); ?>">
						</label>
						<label>
							Adres
							<input type="text" name="address" required="required" value="<?php echo(($_POST['address'] <> '') ? $_POST['address'] : $user -> address); ?>">
						</label>
					</div>
					<h2>Kontakt</h2>
					<div class="cols cols-3">
						<label>
							Adres e-mail
							<input type="text" name="email" required="required" value="<?php echo(($_POST['email'] <> '') ? $_POST['email'] : $user -> email); ?>">
						</label>
						<label>
							Numer telefonu
							<input type="text" name="phone" required="required" placeholder="+48" value="<?php echo(($_POST['phone'] <> '') ? $_POST['phone'] : $user -> phone); ?>">
						</label>
						<label>
							Strona www
							<input type="text" name="www" placeholder="http://" value="<?php echo(($_POST['www'] <> '') ? $_POST['www'] : $user -> www); ?>">
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="<?php echo(ITEM_ADD_TITLE); ?>">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>