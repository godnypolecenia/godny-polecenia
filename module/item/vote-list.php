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
 
$itemArray = [];
$db -> query(
	'SELECT `item_id` '.
	'FROM `db_item` '.
	'WHERE `user_id` = "'.$user -> user_id.'"'
);
while($r = $db -> fetchArray()) {
	$itemArray[] = $r['item_id'];
} 
 
if($url -> opd(URL_SEND) > 0 && $_POST['reply'] <> '') {
	$db -> query(
		'UPDATE `db_vote` '.
		'SET `reply` = "'.$_POST['reply'].'" '.
		'WHERE `vote_id` = "'.$url -> opd(URL_SEND).'" && `item_id` IN("'.implode('", "', $itemArray).'") && `reply` = "" '
	);
	$main -> alertPrepare(true);
	$url -> redirect();
}	



$sqlSearch = ' && `v`.`status` = 1 && `v`.`item_id` IN("'.implode('", "', $itemArray).'") ';

$voteList = new Vote;
$countVote = $voteList -> countVoteList($sqlSearch);

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
$bc -> add($url -> getLink());
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-list-check icon"></span><?php echo($meta['title']); ?></h1>
				<?php if($countVote == 0) echo('<p>Niczego nie znaleziono</p>'); ?>
			</section>

<?php

if($countVote > 0) {
	foreach($voteList -> getVoteList($sqlSearch) as $r) {
		echo('<section>');
		echo('<p class="small bold"><a href="'.$url -> getUrl('item/item?item_id='.$r['item_id']).'">'.$r['title'].'</a> - '.$r['nick'].' / '.dateTimeFormat($r['date']).'</p>');
		echo('<div class="line-item-star section-top-right">');
		for($i = 1; $i <= 5; $i++) {
			if($r['vote'] >= $i) {
				echo('<span class="ri-star-fill"></span>');
			} else {
				echo('<span class="ri-star-line"></span>');
			}
		}
		echo('</div>');
		echo('<br><p>'.(($r['content'] <> '') ? $r['content'] : '<span class="italic">Bez treści</span>').'</p>');
		echo('<br>');
		if($r['reply'] <> '') {
			echo('<section>');
			echo('<p><span class="bold">Odpowiedź:</span> '.$r['reply'].'</p>');
			echo('</section>');
		} else {
			echo('<div>');
				echo('<a href="#" class="button vote-reply-toggle">Odpowiedz na ocenę</a>');
				echo('<form method="post" action="'.$url -> getUrl(null, false, '/'.URL_SEND.'-'.$r['vote_id']).'" class="vote-reply-form">');
					echo('<label>');
						echo('<textarea name="reply" required="required" placeholder="Treść odpowiedzi" class="short"></textarea>');
					echo('</label>');
					echo('<div class="buttons">');
						echo('<input type="submit" value="Wyślij">');
					echo('</div>');
				echo('</form>');
			echo('</div>');
		}
		echo('</section>');
	}
}

?>	
			
		</div>
		<div class="clear"></div>
		<?php paging($countVote); ?>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>