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
 *	This file manages the vote
 */

$vote = new Vote;
$editVote = $vote -> getVoteById($url -> op(0));

if(!($editVote['item_id'] > 0)) {
	require_once('./module/tool/404.php');
	exit;
}

if($url -> op(1) == URL_SAVE) {
	$db -> query(
		'UPDATE `db_vote` '.
		'SET `nick` = "'.$_POST['nick'].'", `vote` = "'.$_POST['vote'].'", `content` = "'.$_POST['content'].'", `reply` = "'.$_POST['reply'].'" '.
		'WHERE `vote_id` = "'.$editVote['vote_id'].'"'
	);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$editVote['vote_id']);
}

if($url -> op(1) == URL_DEL && $_POST['delete'] == 1) {
	$vote -> delete($editVote['vote_id']);
	$main -> alertPrepare(true);
	$url -> redirect('item/admin/vote-list');
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
$bc -> add($url -> getLink('item/admin/vote-list'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h2><span class="ri-file-edit-line icon"></span><?php echo($meta['title']); ?></h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SAVE)); ?>" id="edit-item">	
					<label>
						Autor
						<input type="text" name="nick" value="<?php echo($editVote['nick']); ?>" required="required">
					</label>
					<label>
						Ocena
						<select name="vote" required="required">
							<option<?php if($editVote['vote'] == 5) echo(' selected="selected"'); ?>>5</option>
							<option<?php if($editVote['vote'] == 4) echo(' selected="selected"'); ?>>4</option>
							<option<?php if($editVote['vote'] == 3) echo(' selected="selected"'); ?>>3</option>
							<option<?php if($editVote['vote'] == 2) echo(' selected="selected"'); ?>>2</option>
							<option<?php if($editVote['vote'] == 1) echo(' selected="selected"'); ?>>1</option>
						</select>
					</label>
					<label>
						Komentarz
						<textarea name="content" class="short"><?php echo($editVote['content']); ?></textarea>
					</label>
					<label>
						Odpowiedź firmy
						<textarea name="reply" class="short"><?php echo($editVote['reply']); ?></textarea>
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
			<section class="toggle">
				<h2><span class="ri-delete-bin-line icon"></span>Usuń</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tej oceny
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