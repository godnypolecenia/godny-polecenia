<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(6);

/**
 *	This file manages content pages
 */

if($url -> op(0) == URL_ADD) {
	$vd = new Validate;
	$vd -> isValue($_POST['title'], 'Tytuł');
	
	if($vd -> pass() == true) {
		$newPage = new Page;
		$newPageId = $newPage -> add($_POST['title']);
		if($newPageId > 0) {
			$newPage -> content = $_POST['content'];
			$newPage -> group = 1;			
			$url -> redirect('page/admin/edit', false, '/'.$newPageId);
		}
	}
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
}

/**
 *	Search
 */

if($url -> opd(URL_SEARCH) == URL_SEND) {
	$tmpUrl = '/'.URL_SEARCH;
	if($_POST['word'] <> '') $tmpUrl .=  '/'.URL_QUERY.'-'.urlencode($_POST['word']);
	$url -> redirect(null, false, $tmpUrl);
}

$sqlSearch = ' && `group` = 1 ';

$search = [];
$searchCount = 0;

if($url -> opd(URL_QUERY) <> '') {
	$search['word'] = urldecode($url -> opd(URL_QUERY));
	$sqlWordTmp = str_replace(' ', '%', $search['word']);
	$sqlSearch .= ' && (`title` LIKE "%'.$sqlWordTmp.'%" || `content` LIKE "%'.$sqlWordTmp.'%") ';
	$searchCount++;
}

/**
 *
 */
 
$pageList = new Page;
$countPage = $pageList -> countPageList($sqlSearch);
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
				<h1><span class="ri-file-3-line icon"></span><?php echo($meta['title']); ?></h1>
				<a href="#" id="search-open" class="ri-search-line open-section section-top-right" title="Szukaj"></a>
				
<?php

if($countPage > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>Tytuł</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($pageList -> getPageList($sqlSearch) as $r) {
		echo('<tr>');
		echo('<td><span class="ri-link-m icon"></span> <a href="'.$url -> getUrl('page/page?page_id='.$r['page_id']).'" target="_blank">'.$r['title'].'</a></td>');
		echo('<td><a href="'.$url -> getUrl('page/admin/edit', false, '/'.$r['page_id']).'">Zarządzaj</a></td>');
		echo('</tr>');
	}
	echo('</table>');
	paging($countPage);
} else {
	echo('<p>Niczego nie znaleziono</p>');
}

?>
				
			</section>
			<section class="toggle">
				<h2><span class="ri-file-add-line icon"></span>Dodaj wpis</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
					<label>
						Tytuł
						<input type="text" name="title" required="required">
					</label>
					<label>
						Treść wpisu
						<textarea name="content" class="wysiwyg"><p></p></textarea>
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