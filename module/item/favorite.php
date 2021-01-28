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

$itemList = new Item;
$countItem = $itemList -> countFavoriteItemListOfUser($user -> userId);

if($url -> opd(URL_DEL) > 0) {
	$itemList -> deleteFavorite($url -> opd(URL_DEL), $user -> userId);
	$main -> alertPrepare(true);
	$url -> redirect();
}

/**
 *
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
				<h1><span class="ri-heart-3-line icon"></span><?php echo($meta['title']); ?></h1>
				<?php if($countItem == 0) echo('<p>Niczego nie znaleziono</p>'); ?>
			</section>

<?php

if($countItem > 0) {
	foreach($itemList -> getFavoriteItemListOfUser($user -> userId) as $r) {
		$itemUrl = $url -> getUrl('item/item?item_id='.$r['item_id']);
		$gal = explode(';', $r['gallery']);
		if($gal[0] <> '') {
			$ex = explode('.', $gal[0]);
			$img = $ex[0].'.400x300.'.end($ex);
		} else {
			$img = 'default.400x300.jpg';
		}
		
		echo('<div class="line-item">');
			echo('<div class="line-item-img">');
				echo('<a href="'.$itemUrl.'"><img src="'.$url -> getUrl('tool/image', false, '/'.$img).'" alt="'.$r['title'].'"></a>');
			echo('</div>');
			echo('<div class="line-item-content'.(($r['premium'] > time()) ? ' line-item-premium' : '').'">');
				echo('<h3><a href="'.$itemUrl.'">'.$r['title'].'</a></h3>');
				echo('<div>'.$r['category'].'</div>');
				echo('<div class="line-item-star">');
				for($i = 1; $i <= 5; $i++) {
					if($r['star'] >= $i) {
						echo('<span class="ri-star-fill"></span>');
					} else {
						echo('<span class="ri-star-line"></span>');
					}
				}
				echo('<strong>'.$r['precent'].' ('.$r['vote'].' '.inflect($r['vote'], ['głos', 'głosy', 'głosów']).')</strong>');
				echo('</div>');
				echo('<div class="line-item-phone"><a href="tel:'.$r['phone'].'">'.$r['phone'].'</a></div>');
				echo('<div class="line-item-email"><a href="mailto:'.$r['email'].'">'.$r['email'].'</a></div>');
				echo('<a href="'.$itemUrl.'" class="button">'.ITEM_BUTTON.'</a> ');
				echo('<a href="'.$url -> getUrl(null, false, '/'.URL_DEL.'-'.$r['item_id']).'" class="button button-2">Usuń</a>');
			echo('</div>');
		echo('</div>');
	}
}

?>	
			
		</div>
		<div class="clear"></div>
		<?php paging($countItem); ?>		
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>