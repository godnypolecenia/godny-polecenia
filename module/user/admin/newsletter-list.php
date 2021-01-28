<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(5);

/**
 *	This file manages content newsletter
 */

$emailList = new Newsletter;

if($url -> op(0) == URL_ADD) {
	$vd = new Validate;
	$vd -> isEmail($_POST['email'], 'Adres e-mail');
	
	if($vd -> pass() == true) {	
		$newsletter = new Newsletter;
		$newsletter -> add($_POST['email'], $_POST['city'], implode(';', $_POST['cat']));
		$main -> alertPrepare(true);
		$url -> redirect();
	}
	
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

if($url -> opd(URL_DELETE) > 0) {
	$email = $emailList -> getEmailById($url -> opd(URL_DELETE));
	$emailList -> delete($email);
	$main -> alertPrepare(true);
	$url -> redirect();
}

/**
 *	Search
 */

if($url -> opd(URL_SEARCH) == URL_SEND) {
	$tmpUrl = '/'.URL_SEARCH;
	if($_POST['word'] <> '') $tmpUrl .=  '/'.URL_QUERY.'-'.urlencode($_POST['word']);
	$url -> redirect(null, false, $tmpUrl);
}

$sqlSearch = '';

$search = [];
$searchCount = 0;

if($url -> opd(URL_QUERY) <> '') {
	$search['word'] = urldecode($url -> opd(URL_QUERY));
	$sqlWordTmp = str_replace(' ', '%', $search['word']);
	$sqlSearch .= ' && (`email` LIKE "%'.$sqlWordTmp.'%" || `city` LIKE "%'.$sqlWordTmp.'%") ';
	$searchCount++;
}

$catList = new Category;
$catArr = [];
if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		$catArr[$r['category_id']] = $r['name'];
	}
}

/**
 *
 */
 
$countEmail = $emailList -> countNewsletterList($sqlSearch);

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
					<label>
						Szukana fraza
						<input type="text" name="word" required="required" value="<?php echo($search['word']); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Szukaj">
						<?php if($searchCount > 0) echo('<a href="'.$url -> getUrl().'" class="underline">Anuluj wyszukiwanie</a>'); ?>
					</div>
				</form>
			</section>
			<section>
				<h1><span class="ri-mail-line icon"></span><?php echo($meta['title']); ?></h1>
				<a href="#" id="search-open" class="ri-search-line open-section section-top-right" title="Szukaj"></a>
				
<?php

if($countEmail > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>E-mail</th>');
	echo('<th>Miejscowość</th>');
	echo('<th>Kategorie</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($emailList -> getNewsletterList($sqlSearch) as $r) {
		if($r['category'] == '') {
			$cat = 'Wszystkie kategorie';
		} else {
			$ex = explode(';', $r['category']);
			foreach($ex as $k => $v) {
				$ex[$k] = $catArr[$v];
			}
			$cat = implode(', ', $ex);
		}
		
		echo('<tr>');
		echo('<td>'.$r['email'].'</td>');
		echo('<td>'.$r['city'].'</td>');
		echo('<td>'.$cat.'</td>');
		echo('<td><a href="'.$url -> getUrl(null, false, '/'.URL_DELETE.'-'.$r['newsletter_id']).'">Usuń</a></td>');
		echo('</tr>');
	}
	echo('</table>');
	paging($countEmail);
} else {
	echo('<p>Niczego nie znaleziono</p>');
}

?>
				
			</section>
			<section class="toggle">
				<h2><span class="ri-file-add-line icon"></span>Dodaj adres</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
					<label>
						<input type="text" name="email" required="required" placeholder="Adres e-mail *">
					</label>
					<label>
						<input type="text" name="city" placeholder="Miejscowość">
					</label>
					<h3>Branże</h3>
					<div class="cols cols-2">

<?php

if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<label class="col-item"><input type="checkbox" name="cat[]" value="'.$r['category_id'].'">'.$r['name'].'</label>');
	}
}
	
?>
					
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

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>