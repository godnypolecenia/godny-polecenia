<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	
 */



$sqlSearch = ' && `status` = 1 && `validity` > UNIX_TIMESTAMP() ';
$premiumItems = new Item;
if($premiumItems -> countItemList($sqlSearch) > 0) {

?>		

		<hr>
		<div id="new-items">
			<div class="main">
				<div class="title">
					<h2>Ostatnio dodane w Twojej okolicy</h2>
				</div>
				
				<div class="item-slider-container">
				<div class="item-slider" data-move="3">

<?php

			foreach($premiumItems -> getItemListToSlider($sqlSearch, 'RAND()', (($mobile == 1) ? 5 : 12)) as $r) { 
				$itemUrl = $url -> getUrl('item/item?item_id='.$r['item_id']);
				$gal = explode(';', $r['gallery']);
				if($gal[0] <> '') {
					$ex = explode('.', $gal[0]);
					$img = $ex[0].'.400x300.'.end($ex);
				} else {
					$img = 'cat-1'.$r['category_id'].'.400x300.jpg';
				}
				
				echo('<section class="item item-click" data-url="'.$itemUrl.'">');
					echo('<div class="item-img">');
						echo('<a href="'.$itemUrl.'"><img src="'.$url -> getUrl('tool/image', false, '/'.$img).'" alt="'.$r['title'].'"></a>');
					echo('</div>');
					//if($r['premium'] > time()) echo('<div class="item-premium">Polecamy</div>');
					echo('<div class="item-content">');
						echo('<div class="item-star">');
							for($i = 1; $i <= 5; $i++) {
								if($r['star'] >= $i) {
									echo('<span class="ri-star-fill"></span> ');
								} else {
									echo('<span class="ri-star-line"></span> ');
								}
							}
						echo('</div>');
						echo('<h3><a href="'.$itemUrl.'">'.$r['title'].'</a></h3>');
						echo('<div>'.$r['category'].'</div>');
						echo('<p>'.$r['address'].'<br>'.$r['postcode'].' '.$r['city'].'</p>');
						echo('<a href="'.$itemUrl.'" class="bold">'.ITEM_BUTTON.'</a>');
					echo('</div>');
				echo('</section>');
			}
			echo('</div>');
		echo('</div>');
		echo('</div>');
	echo('</div>');
}


?>

