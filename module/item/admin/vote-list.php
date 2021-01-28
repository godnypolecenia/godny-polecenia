<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(1);

/**
 *	This file manages item
 */
 
if($url -> opd(URL_ACTIVE) > 0) {
	$vote = new Vote;
	$vote -> active($url -> opd(URL_ACTIVE));
	
	$r = $vote -> getVoteById($url -> opd(URL_ACTIVE));
	
	$editItem = new Item;
	$editItem -> getItemById($r['item_id']);
	if($editItem -> itemId > 0) {
	
		$tmp = ($editItem -> star * $editItem -> vote)+$r['vote'];

		$editItem -> vote += 1;
		$editItem -> star = round($tmp / $editItem -> vote);
			
		$sendUser = new User;
		$sendUser -> getUserById($editItem -> user_id);
		if($sendUser -> email <> '') {
			$tmpMail = str_replace(
				['{klient}', '{link}', '{nazwa}'],
				[$r['nick'], $url -> getUrl('item/vote-list'), $setup -> name],
				$setup -> mail_vote
			);
			send_mail($sendUser -> email, $setup -> mail_vote_title, $tmpMail);
		}
	}
	
	$main -> alertPrepare(true);
	$url -> redirect();
}

/**
 *	Search
 */

$sqlSearch = '';
if($url -> opd(URL_BOOKMARK) == 1) $sqlSearch .= ' && `v`.`status` = 1 ';
if($url -> opd(URL_BOOKMARK) == 2) $sqlSearch .= ' && `v`.`status` = 0 ';

$search = [];
$searchCount = 0;

if($url -> opd(URL_SEARCH) == URL_SEND) {
	$tmpUrl = '/'.URL_SEARCH;
	if($_POST['word'] <> '') $tmpUrl .=  '/'.URL_QUERY.'-'.urlencode($_POST['word']);
	$url -> redirect(null, false, $tmpUrl);
} elseif($url -> issetOpd(URL_SEARCH)) {
	if($url -> opd(URL_QUERY) <> '') {
		$search['word'] = urldecode($url -> opd(URL_QUERY));
		$sqlWordTmp = str_replace(' ', '%', $search['word']);
		$sqlSearch .= ' && `v`.`content` LIKE "%'.$sqlWordTmp.'%" ';
		$searchCount++;
	}
}

$voteList = new Vote;
$countVote = $voteList -> countVoteList($sqlSearch);

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
			<ul class="bookmark">
				<?php if($mobile == 1) echo('<li class="bookmark-slide"><span class="ri-arrow-left-right-line"></span></li>'); ?>
				<li><?php echo($url -> getBookmark(0, 'Wszystkie')); ?></li>
				<li><?php echo($url -> getBookmark(1, 'Aktywne')); ?></li>
				<li><?php echo($url -> getBookmark(2, 'Nieaktywne')); ?></li>
			</ul>
			<section>
				<h1><span class="ri-star-line icon"></span><?php echo($meta['title']); ?></h1>
				<a href="#" id="search-open" class="ri-search-line open-section section-top-right" title="Szukaj"></a>
				
<?php

if($countVote > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>Firma</th>');
	echo('<th>Treść</th>');
	echo('<th>Ocena</th>');
	echo('<th>Autor</th>');
	echo('<th>Status</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($voteList -> getVoteList($sqlSearch, '`date` DESC') as $r) {
		echo('<tr>');
		echo('<td><a href="'.$url -> getUrl('item/admin/edit', false, '/'.$r['item_id']).'">'.$r['title'].'</a></td>');
		echo('<td>'.(($r['content'] <> '') ? $r['content'] : '<span class="italic">Bez treści</span>').'</td>');
		echo('<td>'.$r['vote'].'</td>');
		echo('<td>'.$r['nick'].' (<a href="mailto:'.$r['email'].'">'.$r['email'].'</a>)</td>');
		echo('<td>'.(($r['status'] == 1) ? 'Aktywna' : 'Nieaktywny - <a href="'.$url -> getUrl(null, false, '/'.URL_ACTIVE.'-'.$r['vote_id']).'">aktywuj</a>').'</td>');
		echo('<td><a href="'.$url -> getUrl('item/admin/vote', false, '/'.$r['vote_id']).'">Zarządzaj</a></td>');
		echo('</tr>');
	}
	echo('</table>');
	paging($countVote);
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