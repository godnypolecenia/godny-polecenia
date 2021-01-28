<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(0);

/**
 *	This file manages the item
 */

$editItem = new Item;
if(!$editItem -> getItemById($url -> op(0))) {
	require_once('./module/tool/404.php');
	exit;
}

$gal = explode(';', $editItem -> gallery);

$time = [];
$ex = explode(';', $editItem -> time);
foreach($ex as $k => $v) {
	$time[($k+1)] = (($v <> '') ? explode('-', $v) : ['', '']);
}

$exArr = explode(';', $editItem -> amenitie);

$exGal = explode(';', $editItem -> banner);
foreach($exGal as $v) {
	$ex = explode('.', $v);
	$banner = [
		'file' => $v,
		'name' => $ex[0],
		'format' => end($ex)
	];
}


/**
 *	Initialize feature class
 */
$relFeature = new Feature;

/**
 *
 */

if($url -> op(1) == URL_ACTIVE) {
	$editItem -> status = 1;
	if($editItem -> validity < time()) $editItem -> validity = time()+($setup -> validity_day*86400);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_REFRESH) {
	$editItem -> status = 1;	
	$editItem -> validity = time()+($setup -> validity_day*86400);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> op(1) == URL_SET) {
	$editItem -> user_id = $_POST['user'];	
	$editItem -> status = $_POST['status'];	
	$editItem -> validity = strtotime($_POST['validity']);	
	$editItem -> premium = strtotime($_POST['premium']);	
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

/**
 *	Save changes to the item
 */
if($url -> op(1) == URL_EXEC) {
	$vd = new Validate;
	$vd -> isValue($_POST['title'], 'Nazwa firmy');
	$vd -> isInt($_POST['category'], 'Branżą');
	//$vd -> isValue($_POST['content'], 'Treść');
	//$vd -> isEmail($_POST['email'], 'Adres e-mail');



	/**
	 *	Validation passed - save to database
	 */
	if($vd -> pass() == true) {	
		$editItem -> title = $_POST['title'];
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
		$editItem -> email = $_POST['email'];
		$editItem -> phone = $_POST['phone'];
		$editItem -> nip = $_POST['nip'];
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

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	$editItem -> delete();
	$main -> alertPrepare(true);
	$url -> redirect('item/admin/index', false, '/'.$url -> op(0));
}

if($url -> opd('dodaj') == 'zdjecie' && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload');
				if($name <> '') {
					$editItem -> gallery = (($editItem -> gallery <> '') ? $editItem -> gallery.';' : '').$name;
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
	$editItem -> gallery = implode(';', $gal);

	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

if($url -> opd(URL_SET) == URL_IMAGE) {
	
	$tmp = [0 => $gal[$url -> op(2)]];
	unset($gal[$url -> op(2)]);
	$editItem -> gallery = implode(';', array_merge($tmp, $gal));
	
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

$categories = explode(')', str_replace('(', '', $editItem -> categories));


if($url -> op(1) == 'czas') {
	
	$arr = [];
	
	for($i = 1; $i <= 7; $i++) {
		if($_POST['from-'.$i] <> '' && $_POST['to-'.$i] <> '') {
			$arr[$i] = $_POST['from-'.$i].'-'.$_POST['to-'.$i];
		} else {
			$arr[$i] = '';
		}
	}

	$editItem -> time = implode(';', $arr);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}


if($url -> op(1) == 'dodajusluge' && $_POST['name'] <> '') {
	if($editItem -> services <> '') {
		$editItem -> services .= ';';
	}
	$editItem -> services .= str_replace(';', ',', $_POST['name']).'{'.convertToNumber($_POST['price']).'}';
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
}

if($url -> opd('usunusluge') <> '') {
	$ex = explode(';', $editItem -> services);
	unset($ex[$url -> opd('usunusluge')]);
	$editItem -> services = implode(';', $ex);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
}

if($url -> opd('zapiszusluge') <> '') {
	$ex = explode(';', $editItem -> services);
	$ex[$url -> opd('zapiszusluge')] = str_replace(';', ',', $_POST['name']).'{'.convertToNumber($_POST['price']).'}';
	$editItem -> services = implode(';', $ex);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
}



if($url -> op(1) == 'dodajbanner') {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> allowedType(['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
			if($file -> maxSize(50000000)) {
				$name = $file -> save('./data/upload');
				if($name <> '') {
					
						$editItem -> banner = $name;
				
					
					$main -> alertPrepare(true);
					$url -> redirect(null, false, '/'.$editItem -> itemId);
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

if($url -> op(1) == 'usunbanner') {
	$editItem -> banner = '';
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
}


if($url -> op(1) == 'udogodnienia') {
	$tmp = [];
	foreach($amenitie as $k => $v) {
		if($_POST['amenitie'][$k] == 1) {
			$tmp[] = 1;
		} else {
			$tmp[] = 0;
		}
	}
	$editItem -> amenitie = implode(';', $tmp);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
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
				<h1><span class="ri-user-line icon"></span><?php echo($meta['title']); ?></h1>
				<ul class="ul">
					<li>Tytuł: <a href="<?php echo($url -> getUrl('item/item?item_id='.$editItem -> itemId)); ?>" target="_blank" class="bold"><?php echo($editItem -> title); ?></a></li>
					<li>Status: <span class="bold"><?php echo(($editItem -> status == 0) ? 'Niekatywne - <a href="'.$url -> getUrl(null, true, '/'.URL_ACTIVE).'">aktywuj</a>' : (($editItem -> status == 1 && $editItem -> validity > time()) ? 'Aktywne do '.dateTimeFormat($editItem -> validity) : 'Wygasłe - <a href="'.$url -> getUrl(null, true, '/'.URL_REFRESH).'">odśwież</a>')); ?></span></li>
					<li>Promowanie: <span class="bold"><?php echo(($editItem -> premium > time()) ? 'Opłacone do '.dateTimeFormat($editItem -> premium) : 'Nie'); ?></span></li>
				</ul>
			</section>
			<section class="toggle">
				<h2><span class="ri-line-chart-line icon"></span>Statystyki</h2>

<?php

$sum = 0;
$sumPhone = 0;
$sumMsg = 0;

$db -> query(
	'SELECT * '.
	'FROM `db_stat` '.
	'WHERE `item_id` = "'.$editItem -> itemId.'" '.
	'ORDER BY `date` DESC'
);
if($db -> numRows() == 0) {
	echo('<p>Brak statystyk</p>');
} else {
	echo('<table>');
	echo('<tr>');
	echo('<th>Dzień</th>');
	echo('<th>Wyświetleń</th>');
	echo('<th>Odsłon nr telefonu</th>');
	echo('<th>Wiadomości</th>');
	echo('</tr>');
	while($r = $db -> fetchArray()) {
		echo('<tr>');
		echo('<td>'.$r['date'].'</td>');
		echo('<td>'.$r['counter'].'</td>');
		echo('<td>'.$r['phone'].'</td>');
		echo('<td>'.$r['message'].'</td>');
		echo('</tr>');
		$sum += $r['counter'];
		$sumPhone += $r['phone'];
		$sumMsg += $r['message'];
	}
	echo('<tr>');
		echo('<td class="bold">Razem</td>');
		echo('<td class="bold">'.$sum.'</td>');
		echo('<td class="bold">'.$sumPhone.'</td>');
		echo('<td class="bold">'.$sumMsg.'</td>');
		echo('</tr>');
	echo('</table>');
}

?>
			
			</section>
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Ustawienia</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SET)); ?>">	
					<label>
						Status
						<select name="status">
							<option value="1"<?php if($editItem -> status == 1) echo(' selected="selected"'); ?>>Aktywne</option>
							<option value="0"<?php if($editItem -> status == 0) echo(' selected="selected"'); ?>>Nieaktywne</option>
						</select>
					</label>
					<label>
						Pakiet Standard
						<input type="text" name="validity" class="datepicker" value="<?php if($editItem -> validity > 0) echo(dateFormat($editItem -> validity)); ?>">
					</label>
					<label>
						Pakiet Premium
						<input type="text" name="premium" class="datepicker" value="<?php if($editItem -> premium > 0) echo(dateFormat($editItem -> premium)); ?>">
					</label>
					<label>
						Przypisz użytkownikowi
						<select name="user">

<?php

$db -> query(
	'SELECT `user_id`, `name`, `email` '.
	'FROM `db_user` '.
	'ORDER BY `name` ASC'
);
while($us = $db -> fetchArray()) {
	echo('<option value="'.$us['user_id'].'"'.(($us['user_id'] == $editItem -> user_id) ? ' selected="selected"' : '').'>'.$us['name'].' - '.$us['email'].'</option>');
}

?>
						
						</select>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-file-edit-line icon"></span><?php echo(ITEM_EDIT_TITLE); ?></h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC)); ?>" id="edit-item">	
					<div class="cols cols-2">
						<label>
							Nazwa firmy
							<input type="text" name="title" value="<?php echo($editItem -> title); ?>">
						</label>
						
						</label>
							NIP
							<input type="text" name="nip" value="<?php echo($editItem -> nip); ?>">
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
						<textarea name="content" placeholder="Opisz swoją firmę i świadczone usługi"><?php echo($editItem -> content); ?></textarea>
					</label>
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
					<div class="cols cols-3">
						<label>
							Adres e-mail
							<input type="text" name="email" value="<?php echo($editItem -> email); ?>">
						</label>
						<label>
							Numer telefonu
							<input type="text" name="phone" placeholder="+48" value="<?php echo($editItem -> phone); ?>">
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
					</div>
				</form>
			</section>
			
			
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Godziny otwarcia</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/czas')); ?>">
					
					
<?php

foreach($dayName as $k => $v) {
	if($k == 0) {
		continue;
	}

	echo('<div class="cols cols-3">');
	echo('<div class="col-item"><label>'.$v.':</label></div>');
	echo('<div class="col-item"><label>');
	echo('<select name="from-'.$k.'">');
	echo('<option value="">Nieczynne</option>');
	for($i = 0; $i <= 23; $i++) {
		echo('<option'.(($time[$k][0] == (($i < 10) ? '0' : '').$i.':00') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':00</option>');
		echo('<option'.(($time[$k][0] == (($i < 10) ? '0' : '').$i.':30') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':30</option>');
	}
	echo('</select>');
	echo('</label></div>');
	echo('<div class="col-item"><label>');
	echo('<select name="to-'.$k.'">');
	echo('<option value="">Nieczynne</option>');
	for($i = 0; $i <= 23; $i++) {
		echo('<option'.(($time[$k][1] == (($i < 10) ? '0' : '').$i.':00') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':00</option>');
		echo('<option'.(($time[$k][1] == (($i < 10) ? '0' : '').$i.':30') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':30</option>');
	}
	echo('</select>');
	echo('</label></div>');
	echo('</div>');
}

?>

					<div class="buttons center">
						<input type="submit" value="Zapisz">
					</div>
				</form>			
			</section>
			
			
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Udogodnienia</h2>

				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/udogodnienia')); ?>">
					<?php foreach($amenitie as $k => $v) { ?>
					<label>
						<input type="checkbox" name="amenitie[<?php echo($k); ?>]" value="1"<?php if($exArr[$k] == 1) echo(' checked="checked"'); ?>>
						<?php echo($v); ?>
					</label>
					<?php } ?>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>				
				
			</section>
			
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Usługi</h2>
<?php

if($editItem -> services <> '') {
	$ex = explode(';', $editItem -> services);
	echo('<ul class="ul-2" style="max-width: 550px;">');
	foreach($ex as $k => $v) {
		$ex2 = explode('{', $v);
		echo('<li>'.$ex2[0].' - <a href="#" class="edit-service-toggle underline" data-value="'.$k.'">zarządzaj</a> <span class="service-price">'.((str_replace('}', '', $ex2[1]) <> '0') ? priceFormat(str_replace('}', '', $ex2[1])) : '').'</span>');
		echo('<div class="service-edit" id="edit-service-'.$k.'">');
		echo('<form method="post" action="'.$url -> getUrl(null, false, '/'.$editItem -> itemId.'/zapiszusluge-'.$k).'"><br>');
		echo('<div class="cols cols-2">');
		echo('<label>');
		echo('<input type="text" name="name" placeholder="Nazwa usługi *" value="'.$ex2[0].'" required="required">');
		echo('</label>');
		echo('<label>');
		echo('<input type="text" name="price" placeholder="Cena za usługę [zł]" value="'.((str_replace('}', '', $ex2[1]) <> '0') ? priceFormat(str_replace('}', '', $ex2[1])) : '').'">');
		echo('</label>');
		echo('</div>');
		echo('<div class="buttons">');
		echo('<input type="submit" value="Zapisz"> <a href="'.$url -> getUrl(null, false, '/'.$editItem -> itemId.'/usunusluge-'.$k).'" class="underline">usuń</a>');
		echo('</div><br>');
		echo('</form>');
		echo('</div>');
		echo('</li>');
	}
	echo('</ul>');
} else {
	echo('<p>Niczego nie dodano</p>');
}

?>				
			</section>
			
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Dodaj usługę</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$editItem -> itemId.'/dodajusluge')); ?>">
					<div class="cols cols-2">
						<label>
							<input type="text" name="name" required="required" placeholder="Nazwa usługi *">
						</label>
						<label>
							<input type="text" name="price" placeholder="Cena za usługę [zł]">
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
			
			<section class="toggle">
				<h2><span class="ri-settings-2-line icon"></span>Banner</h2>
				<section>
					Uwaga! Banner widoczny jest jedynie w pakiecie Premium
				</section>

<?php

if($banner['file'] <> '') {
	echo('<div><img src="'.$url -> getUrl('tool/image', false, '/'.$banner['name'].'.320x240.'.$banner['format']).'" alt=""></div>');
	echo('<br><a href="'.$url -> getUrl(null, false, '/'.$url -> op(0).'/usunbanner').'" class="button">Usuń banner</a>');
} else {	
	echo('<p>Brak bannera</p>');
}

?>			
			</section>
			
				<section class="toggle">
				<h2><span class="ri-image-add-line icon"></span>Dodaj banner</h2>
				<section>
					Dozwolone formaty: JPG, PNG, GIF
				</section>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/dodajbanner')); ?>" enctype="multipart/form-data">
					<label>
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
			
			<?php if($editItem -> gallery <> '' && count($gal) > 0) { ?>
			<section class="toggle">
				<h2><span class="ri-image-line icon"></span>Galeria zdjęć</h2>
				<section><p>Kliknij na zdjęciu aby wyświetlić dodatkowe opcje.</p></section>
				<div class="gallery">

<?php

foreach($gal as $k => $v) {
	$ex = explode('.', $v);
	$name = $ex[0];
	$format = $ex[1];
	echo('<a href="'.$url -> getUrl('item/admin/photo', false, '/'.$editItem -> itemId.'/'.$k.'/'.$v).'"><img src="'.$url -> getUrl('tool/image', false, '/'.$name.'.150x150.'.$format).'" alt=""></a>');
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
						Potwierdzam chęć usunięcia tej oferty
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