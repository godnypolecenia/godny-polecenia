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
 *	
 */

$editItem = new Item($url -> op(0));
if(!($editItem -> itemId > 0)) {
	$url -> redirect(404);
}

if($url -> op(1) == URL_ADD && $_POST['name'] <> '') {
	if($editItem -> services <> '') {
		$editItem -> services .= ';';
	}
	$editItem -> services .= str_replace(';', ',', $_POST['name']).'{'.convertToNumber($_POST['price']).'}';
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
}

if($url -> opd(URL_DEL) <> '') {
	$ex = explode(';', $editItem -> services);
	unset($ex[$url -> opd(URL_DEL)]);
	$editItem -> services = implode(';', $ex);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
}

if($url -> opd(URL_SAVE) <> '') {
	$ex = explode(';', $editItem -> services);
	$ex[$url -> opd(URL_SAVE)] = str_replace(';', ',', $_POST['name']).'{'.convertToNumber($_POST['price']).'}';
	$editItem -> services = implode(';', $ex);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editItem -> itemId);
}

/**
 *
 */
 
//$url -> addBackUrl();

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
$bc -> add($url -> getLink('item/edit', true));
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

<?php

if($editItem -> services <> '') {
	$ex = explode(';', $editItem -> services);
	echo('<ul class="ul-2" style="max-width: 550px;">');
	foreach($ex as $k => $v) {
		$ex2 = explode('{', $v);
		echo('<li>'.$ex2[0].' - <a href="#" class="edit-service-toggle underline" data-value="'.$k.'">zarządzaj</a> <span class="service-price">'.((str_replace('}', '', $ex2[1]) <> '0') ? priceFormat(str_replace('}', '', $ex2[1])) : '').'</span>');
		echo('<div class="service-edit" id="edit-service-'.$k.'">');
		echo('<form method="post" action="'.$url -> getUrl(null, false, '/'.$editItem -> itemId.'/'.URL_SAVE.'-'.$k).'"><br>');
		echo('<div class="cols cols-2">');
		echo('<label>');
		echo('<input type="text" name="name" placeholder="Nazwa usługi *" value="'.$ex2[0].'" required="required">');
		echo('</label>');
		echo('<label>');
		echo('<input type="text" name="price" placeholder="Cena za usługę [zł]" value="'.((str_replace('}', '', $ex2[1]) <> '0') ? priceFormat(str_replace('}', '', $ex2[1])) : '').'">');
		echo('</label>');
		echo('</div>');
		echo('<div class="buttons">');
		echo('<input type="submit" value="Zapisz"> <a href="'.$url -> getUrl(null, false, '/'.$editItem -> itemId.'/'.URL_DEL.'-'.$k).'" class="underline">usuń</a>');
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
			<section>
				<h2>Dodaj usługę</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$editItem -> itemId.'/'.URL_ADD)); ?>">
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
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>