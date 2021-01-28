<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays the home page
 */

if($url -> op(0) == 'geo') {
	
	$_SESSION['lat'] = str_replace('-', '.', $url -> op(1));
	$_SESSION['lng'] = str_replace('-', '.', $url -> op(2));

	$file = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$_SESSION['lat'].','.$_SESSION['lng'].'&sensor=true&key='.GOOGLE_MAPS_KEY);
	preg_match_all('@\"long_name\" \: \"([A-Za-z\- ]+)\",@', $file, $out);
	file_put_contents('geo.txt', print_r($out, true), FILE_APPEND);
	
	$_SESSION['city'] = $out[1][8];

	exit;
}

$url -> addBackUrl();

/**
 *	Layout
 */
$meta = [
	'title' => $setup -> title,
	'description' => $setup -> description,
	'keywords' => $setup -> keywords,
	'robots' => (($setup -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="slider">
	<div class="main">
		<div class="red-line"></div>
		<h1 class="lora"><?php echo($setup -> abc_h_3); ?></h1>
		<div class="red-line"></div>
		<p><?php echo($setup -> abc_3); ?></p>
		<form method="post" action="<?php echo($url -> getUrl('item/index', false, '/'.URL_SEARCH)); ?>" id="slider-search">
			
			<div class="cols cols-3">
				<label>
					<input type="text" name="query" placeholder="Czego szukasz?">
				</label>
				<label>
					<input type="text" name="city" placeholder="Lokalizacja" value="<?php echo($_SESSION['city']); ?>">
				</label>
				<label>
					<select name="category">
						<option value="">Branża</option>
<?php

$catList = new Category;
if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<option value="'.$r['category_id'].'">'.$r['name'].'</option>'."\n");
	}
}

?>
					</select>
				</label>
			</div>
			<input type="submit" value="" class="ri-search-line icon">
		</form>
	</div>
</div>
<div id="area">
	<div class="main">
		<div class="title">
			<h2>Wybieraj spośród dziesiątek kategorii</h2>
		</div>
		<div class="item-slider-container">
			<ul class="category item-slider" data-move="6" data-mobile="2">

<?php

if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		echo('<li><a href="'.$url -> getUrlByFile('item/index', 'parent_id='.$r['parent_id'].'&category_id='.$r['category_id']).'"><img src="'.$url -> getUrl('tool/image', false, '/'.$r['icon']).'" alt="'.$r['name'].'">'.$r['name'].'</a></li>'."\n");
	}
}

?>

			</ul>
			<div class="clear"></div>
		</div>
	</div>
</div>
<div id="content">
	<div class="main">
		<div id="tip">
			<div class="title">
				<h2>Jak to działa?</h2>
			</div>
			<div class="cols cols-4">
				<div class="col-item center">
					<img src="./template/default/image/step-1.png" alt="Weryfikacja firmy"><br>
					<h3><?php echo($setup -> step_h_1); ?></h3>
					<p><?php echo($setup -> step_1); ?></p>
				</div>
				<div class="col-item center">
					<img src="./template/default/image/step-2.png" alt="Znajdź firmę w Twojej okolicy"><br>
					<h3><?php echo($setup -> step_h_2); ?></h3>
					<p><?php echo($setup -> step_2); ?></p>
				</div>
				<div class="col-item center">
					<img src="./template/default/image/step-3.png" alt="Przeglądaj usługi i polecenia"><br>
					<h3><?php echo($setup -> step_h_3); ?></h3>
					<p><?php echo($setup -> step_3); ?></p>
				</div>
				<div class="col-item center">
					<img src="./template/default/image/step-4.png" alt="Wybierz firmę i umów się"><br>
					<h3><?php echo($setup -> step_h_4); ?></h3>
					<p><?php echo($setup -> step_4); ?></p>
				</div>
			</div>
			<br>
			<div class="center"><a href="<?php echo($url -> getUrl('item/add')); ?>" class="button">Dodaj firmę</a></div>	
			<?php if($setup -> block_1 <> '') echo('<div id="block-1">'.$setup -> block_1.'</div>'); ?>
		</div>
		
	</div>

<?php

/**
 *	Ptomote items - begin
 *	------------------------------
 */
$sqlSearch = ' && `status` = 1 && `premium` > UNIX_TIMESTAMP() && `validity` > UNIX_TIMESTAMP() ';
$premiumItems = new Item;
if($premiumItems -> countItemList($sqlSearch) > 0) {

?>		

		<hr>
		<div id="promote">
			<div class="main">
				<div class="title">
					<h2>Polecane firmy w Twojej okolicy</h2>
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
					$img = 'cat-'.$r['category_id'].'.400x300.jpg';
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

/**
 *	------------------------------
 *	Promote items - end
 */

?>
		
	<div class="main">
		<?php if($setup -> block_2 <> '') echo('<div id="block-2">'.$setup -> block_2.'</div>'); ?>
	</div>

	<?php require_once('./template/default/newsletter-box.php'); ?>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>