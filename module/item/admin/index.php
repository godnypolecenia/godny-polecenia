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
 *	This file manages item
 */
 
if($url -> op(0) == URL_ADD && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		$nnn = 'import';
		if($file -> save('./data/file', $nnn) <> false) {
			
			$open = file_get_contents('./data/file/'.$nnn.'.csv');
			if($open) {
				
				
				
				$ex = explode("\n", $open);
				foreach($ex as $v) {
					$ex2 = explode(';', $v);
					
					$newItem = new Item;
					$itemId = $newItem -> add($ex2[0], 0);
					$newItem -> category_id = $ex2[1];
					$newItem -> nip = $ex2[2];
					$newItem -> region = $ex2[3];
					$newItem -> city = $ex2[4];
					$newItem -> postcode = $ex2[5];
					$newItem -> address = $ex2[6];
					$newItem -> email = $ex2[7];
					$newItem -> phone = $ex2[8];
					$newItem -> content = $ex2[9];
					$newItem -> www = $ex2[10];
					$newItem -> status = 1;
					
					if($ex2[10] == 1) {
						$newItem -> validity = time()+($ex2[11]*86400);
					}
					if($ex2[10] == 2) {
						$newItem -> validity = time()+($ex2[11]*86400);
						$newItem -> premium = time()+($ex2[11]*86400);
					}
				}
				
				$main -> alertPrepare(true);
				$url -> redirect();
			} else {
				var_dump($open); exit;
			}
			
			
		} else {
			$main -> alertPrepare(false, FILE_ERR_UPLOAD);
		}
	} else {
		$main -> alertPrepare(false, FILE_ERR_NULL);
	}
} 
 
if($url -> opd(URL_ACTIVE) > 0) {
	$editItem = new Item;
	if(!$editItem -> getItemById($url -> opd(URL_ACTIVE))) {
		require_once('./module/tool/404.php');
		exit;
	}
	//$editItem -> validity = (time()+31536000);
	$editItem -> status = 1;
	$main -> alertPrepare(true);
	$url -> redirect();
}

if($url -> opd(URL_REFRESH) > 0) {
	$editItem = new Item;
	if(!$editItem -> getItemById($url -> opd(URL_REFRESH))) {
		require_once('./module/tool/404.php');
		exit;
	}
	$editItem -> validity = (time()+31536000);
	$editItem -> status = 1;
	$main -> alertPrepare(true);
	$url -> redirect();
}

if($url -> op(0) == URL_DELETE) {
	$delItem = new Item;
	foreach((array)$_POST['delete'] as $k => $v) {
		if($v == 1) {
			$delItem -> delete($k);
		}
	}
	$main -> alertPrepare(true);
	$url -> redirect();
}


/**
 *	Search
 */

$sqlSearch = '';
if($url -> opd(URL_BOOKMARK) == 1) $sqlSearch .= ' && `i`.`status` = 1 && `i`.`validity` > UNIX_TIMESTAMP() ';
if($url -> opd(URL_BOOKMARK) == 2) $sqlSearch .= ' && `i`.`status` = 0 ';
if($url -> opd(URL_BOOKMARK) == 3) $sqlSearch .= ' && `i`.`status` = 1 && `i`.`validity` < UNIX_TIMESTAMP() ';
if($url -> opd(URL_BOOKMARK) == 4) $sqlSearch .= ' && `i`.`premium` > UNIX_TIMESTAMP() ';
if($url -> opd(URL_BOOKMARK) == 5) $sqlSearch .= ' && `i`.`premium` < UNIX_TIMESTAMP() ';

$search = [];
$searchCount = 0;

if($url -> opd(URL_SEARCH) == URL_SEND) {
	$tmpUrl = '/'.URL_SEARCH;
	if($_POST['word'] <> '') $tmpUrl .=  '/'.URL_QUERY.'-'.urlencode($_POST['word']);
	if($_POST['category'] <> '') $tmpUrl .=  '/'.URL_CATEGORY.'-'.$_POST['category'];
	$url -> redirect(null, false, $tmpUrl);
} elseif($url -> issetOpd(URL_SEARCH)) {
	if($url -> opd(URL_QUERY) <> '') {
		$search['word'] = urldecode($url -> opd(URL_QUERY));
		$sqlWordTmp = str_replace(' ', '%', $search['word']);
		$sqlSearch .= ' && (`i`.`title` LIKE "%'.$sqlWordTmp.'%" || `i`.`content` LIKE "%'.$sqlWordTmp.'%" || `i`.`nip` LIKE "%'.$sqlWordTmp.'%") ';
		$searchCount++;
	}
	if($url -> opd(URL_CATEGORY) > 0) {
		$search['category'] = $url -> opd(URL_CATEGORY);
		$sqlSearch .= ' && `i`.`category_id` = "'.$search['category'].'" ';
		$searchCount++;
	}
}

$itemList = new Item;
$countItem = $itemList -> countItemList($sqlSearch);

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
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section id="search-container" class="open-section-container<?php if($searchCount > 0) echo(' open-section-container-show'); ?>">
				<h2><span class="ri-search-line icon"></span>Szukaj</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SEARCH.'-'.URL_SEND)); ?>">
					<div class="cols cols-2">
						<label>
							Szukana fraza
							<input type="text" name="word" value="<?php echo($search['word']); ?>">
						</label>
						<label>
							Branża
							<select name="category">
								<option value="">Wybierz</option>

<?php

$catList = new Category;
if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<option value="'.$r['category_id'].'"'.(($search['category'] == $r['category_id']) ? ' selected="selected"' : '').'>'.$r['name'].'</option>');
	}
}

?>

							</select>
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Szukaj">
						<?php if($searchCount > 0) echo('<a href="'.$url -> getUrl().'" class="underline">Anuluj wyszukiwanie</a>'); ?>
					</div>
				</form>
			</section>
			<ul class="bookmark">
				<?php if($mobile == 1) echo('<li class="bookmark-slide"><span class="ri-arrow-left-right-line"></span></li>'); ?>
				<li><?php echo($url -> getBookmark(0, 'Wszystkie')); ?></li>
				<li><?php echo($url -> getBookmark(1, 'Aktywne')); ?></li>
				<li><?php echo($url -> getBookmark(2, 'Nieaktywne')); ?></li>
				<li><?php echo($url -> getBookmark(3, 'Wygasłe')); ?></li>
				<li><?php echo($url -> getBookmark(5, 'Pakiet Standard')); ?></li>
				<li><?php echo($url -> getBookmark(4, 'Pakiet Premium')); ?></li>
			</ul>
			<section>
				<h1><span class="ri-file-3-line icon"></span><?php echo($meta['title']); ?></h1>
				<a href="#" id="search-open" class="ri-search-line open-section section-top-right" title="Szukaj"></a>
				
<?php

if($countItem > 0) {
	echo('<form method="post" action="'.$url -> getUrl(null, false, '/'.URL_DELETE).'">');
	echo('<table>');
	echo('<tr>');
	echo('<th></th>');
	echo('<th>Tytuł</th>');
	echo('<th>Dodano</th>');
	echo('<th>Wygasa</th>');
	echo('<th>Status</th>');
	echo('<th>Pakiet</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($itemList -> getItemList($sqlSearch, '`premium` DESC, `validity` DESC, `vote` DESC') as $r) {
		echo('<tr>');
		echo('<td><input type="checkbox" name="delete['.$r['item_id'].']" value="1"></td>');
		echo('<td><span class="ri-link-m icon"></span> <a href="'.$url -> getUrl('item/item?item_id='.$r['item_id']).'" target="_blank">'.$r['title'].'</a></td>');
		echo('<td>'.(($r['date'] > 0) ? dateTimeFormat($r['date']) : '-').'</td>');
		echo('<td>'.(($r['validity'] > 0) ? dateTimeFormat($r['validity']) : '-').'</td>');
		echo('<td>');
			if($r['status'] == 0) echo('Nieaktywne - <a href="'.$url -> getUrl(null, false, '/'.URL_ACTIVE.'-'.$r['item_id']).'">aktywuj</a>');
			if($r['status'] == 1 && $r['validity'] > time()) echo('Aktywne');
			if($r['status'] == 1 && $r['validity'] < time()) echo('Wygasłe - <a href="'.$url -> getUrl(null, false, '/'.URL_REFRESH.'-'.$r['item_id']).'">odśwież</a>');
		echo('</td>');
		echo('<td>'.(($r['premium'] > time()) ? 'Premium' : 'Standard').'</td>');
		echo('<td><a href="'.$url -> getUrl('item/admin/edit', false, '/'.$r['item_id']).'">Zarządzaj</a></td>');
		echo('</tr>');
	}
	echo('</table>');
	echo('<div class="buttons">');
	echo('<input type="submit" value="Usuń zaznaczone">');
	echo('</div>');
	echo('</form>');
	paging($countItem);
} else {
	echo('<p>Niczego nie znaleziono</p>');
}

?>
				
			</section>
			<section class="toggle">
				<h2>Importuj firmy</h2>
				<section>
					<p>Struktura pliku: Nazwa;ID branży;NIP;Wojdwództwo (1 dolnośląskie, 2 - kujawsko pomorskie, ...);Miejscowość;Kod pocztowy;Adres;Adres e-mail;Numer telefonu;Opis;Pakiet (1 - darmowy / 2 - standard / 3 - premium);Liczba dni pakietu;Strona www</p>
				</section>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>" enctype="multipart/form-data">
					<label>
						Plik CSV
						<input type="file" name="file" required="required">
					</label>
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