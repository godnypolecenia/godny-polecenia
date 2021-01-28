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

if($url -> op(1) == URL_EXEC) {
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

$exArr = explode(';', $editItem -> amenitie);

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
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$url -> op(0).'/'.URL_EXEC)); ?>">
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
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>