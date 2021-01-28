<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	--------------------------
 *	This file manages the list
 *	--------------------------
 */

if($url -> op(0) == URL_CANCEL) {
	unset($_SESSION['city']);
	$url -> redirect();
}

$catList = new Category;

/**
 *	Parse search
 */
if($url -> op(0) == URL_SEARCH) {
	
	$prepareUrl = '';
	
	/**
	 *	Search by category (URL)
	 *
	 *	e.g.: http://adres.pl/motoryzacja/samochody-osobowe
	 */
	if($_POST['category'] > 0) {
		$catList -> getCategoryById($_POST['category']);
		if($catList -> categoryId > 0) {
			
			/**
			 *	Category and city
			 *
			 *	e.g.: http://adres.pl/motoryzacja/samochody-osobowe/bydgoszcz
			 */
			if($_POST['city'] <> '') {
				$prepareUrl = SITE_ADDRESS.'/'.$url -> getUrlByFile('item/index', 'parent_id='.$catList -> parent_id.'&category_id='.$catList -> categoryId.'&city='.rawurlencode($_POST['city']));
				if($prepareUrl <> '') {
					unset($_POST['category']);
					unset($_POST['city']);
					
					/**
					 *	Radius from the city
					 */
					if($_POST['radius'] > 0) {
						$prepareUrl .= '/'.URL_RADIUS.'-'.$_POST['radius'];
					}
				}
			}
			
			/**
			 *	Category only
			 */
			if($prepareUrl == '') {
				$prepareUrl = SITE_ADDRESS.'/'.$url -> getUrlByFile('item/index', 'parent_id='.$catList -> parent_id.'&category_id='.$catList -> categoryId);
				unset($_POST['category']);
			}
		}
	}

	/**
	 *	If is no URL category
	 */
	if($prepareUrl == '') {
		$prepareUrl = $url -> getUrl('item/index');
	}
	
	/**
	 *	Search by category (OPD)
	 *
	 *	e.g.: http://adres.pl/ogloszenia/c-1-motoryzacja
	 */
	if($_POST['category'] > 0) {
		$catList -> getCategoryById($_POST['category']);
		if($catList -> categoryId > 0) {
			$prepareUrl .= '/'.URL_CATEGORY.'-'.$catList -> categoryId.'-'.toUrl($catList -> name);
		}
	}
	
	/**
	 *	Search by city
	 */
	if($_POST['city'] <> '') {
		$prepareUrl .= '/'.URL_CITY.'-'.rawurlencode($_POST['city']);
		
		/**
		 *	Search by city and radius
		 */
		if($_POST['radius'] > 0) {
			$prepareUrl .= '/'.URL_RADIUS.'-'.$_POST['radius'];
		}
	}
	
	/**
	 *	Search by price
	 */
	if($_POST['price-from'] > 0 || $_POST['price-to'] > 0) {
		if($_POST['price-from'] > 0 && $_POST['price-to'] > 0) {
			$prepareUrl .= '/'.URL_PRICE.'-'.((int)$_POST['price-from']).'-'.((int)$_POST['price-to']);
		} else {
			if($_POST['price-from'] > 0) {
				$prepareUrl .= '/'.URL_PRICE.'-'.URL_FROM.'-'.((int)$_POST['price-from']);
			} else {
				$prepareUrl .= '/'.URL_PRICE.'-'.URL_TO.'-'.((int)$_POST['price-to']);
			}
		}
	}
	
	/**
	 *	Search by word
	 */
	if($_POST['query'] <> '') {
		$prepareUrl .= '/'.URL_QUERY.'-'.rawurlencode($_POST['query']);
	}
	
	/**
	 *	Search by features
	 */
	if(is_array($_POST['feature'])) {
		$featureUrl = [];
		
		foreach($_POST['feature'] as $k => $v) {
			if(is_array($v)) {
				$tmp = [];
				foreach($v as $vv) {
					if($vv <> '') {
						$tmp[] = rawurlencode($vv);
					}
				}
				if(count($tmp) > 0) {
					$featureUrl[] = $k.'-'.implode(',', $tmp);
				}
			} else {
				if($v <> '') {
					$featureUrl[] = $k.'-'.rawurlencode($v);
				}
			}
		}
		
		if(count($featureUrl) > 0) {
			$prepareUrl .= '/'.URL_FEATURE.'-'.implode(';', $featureUrl);
		}
	}
	
	/**
	 *	Search by NIP
	 */
	if($_POST['nip'] <> '') {
		$prepareUrl .= '/nip-'.rawurlencode($_POST['nip']);
	}
	
	/**
	 *	Sort
	 */
	if($_POST['sort'] > 0) {
		$prepareUrl .= '/sortuj-'.$_POST['sort'];
	}
	
	/**
	 *	Sub cat
	 */
	if(is_array($_POST['sub-cat'])) {
		$tmp = [];
		foreach($_POST['sub-cat'] as $v) {
			if($v > 0) {
				$tmp[] = $v;
			}
		}
		if(count($tmp) > 0 && $tmp[0] > 0) {
			$prepareUrl .= '/sub-'.implode(',', $tmp);
		}
	}

	/**
	 *	Reload page
	 */
	header('Location: '.$prepareUrl);
	exit;

}

/**
 *	Search init
 */
$sqlSearch = ' && `status` = 1 ';
$searchArray = [];
$moreCount = 0;
$sqlSort = '`premium` DESC, `star` DESC, `vote` DESC ';

/**
 *	Search by category (URL)
 */
 
if($url -> var['category_id'] > 0) {
	$searchArray['category'] = $url -> var['category_id'];
	$catList -> getCategoryById($searchArray['category']);
	if($url -> var['parent_id'] > 0) {
		$sqlSearch .= ' && (`i`.`category_id` = "'.$searchArray['category'].'" || `i`.`category_id` = "'.$url -> var['parent_id'].'" || `i`.`category_id_2` = "'.$searchArray['category'].'" || `i`.`category_id_2` = "'.$url -> var['parent_id'].'" || `i`.`category_id_3` = "'.$searchArray['category'].'" || `i`.`category_id_3` = "'.$url -> var['parent_id'].'" || `i`.`category_id_4` = "'.$searchArray['category'].'" || `i`.`category_id_4` = "'.$url -> var['parent_id'].'" || `i`.`category_id_5` = "'.$searchArray['category'].'" || `i`.`category_id_5` = "'.$url -> var['parent_id'].'") ';
	} else {
		$sqlSearch .= ' && (`i`.`category_id` = "'.$searchArray['category'].'"  || `i`.`category_id_2` = "'.$searchArray['category'].'" || `i`.`category_id_3` = "'.$searchArray['category'].'" || `i`.`category_id_4` = "'.$searchArray['category'].'" || `i`.`category_id_5` = "'.$searchArray['category'].'") ';
	}
}

/**
 *	Search by category (OPD)
 */
if($url -> opd(URL_CATEGORY) <> '') {
	$ex = explode('-', $url -> opd(URL_CATEGORY));
	$searchArray['category'] = $ex[0];
	$catList -> getCategoryById($searchArray['category']);
	$sqlSearch .= ' && (`i`.`category_id` = "'.$searchArray['category'].'" || `i`.`category_id_2` = "'.$searchArray['category'].'" || `i`.`category_id_3` = "'.$searchArray['category'].'" || `i`.`category_id_4` = "'.$searchArray['category'].'" || `i`.`category_id_5` = "'.$searchArray['category'].'") ';
}

/**
 *	Search by word
 */
if($url -> opd(URL_QUERY) <> '') {
	$searchArray['query'] = rawurldecode($url -> opd(URL_QUERY));
	$sqlSearchArray = [];
	$ex = explode(' ', $searchArray['query']);
	foreach($ex as $v) {
		
		/**
		 *	Variations of words
		 */
		$v = substr($v, 0, -1);
		
		$sqlSearchArray[] = '(`i`.`title` LIKE "%'.$v.'%" || `i`.`content` LIKE "%'.$v.'%")';
	}
	$sqlSearch .= ' && ('.implode(' || ', $sqlSearchArray).') ';
}

/**
 *	Search by price
 */
if($url -> opd(URL_PRICE) <> '') {
	$ex = explode('-', $url -> opd(URL_PRICE));
	
	if($ex[0] == URL_FROM) {
		/**
		 *	Search by price from
		 */
		if($ex[1] > 0) {
			$searchArray['price-from'] = $ex[1];
			$sqlSearch .= ' && `price` >= "'.$searchArray['price-from'].'" ';
		}
		
	} elseif($ex[0] == URL_TO) {
		/**
		 *	Search by price to
		 */
		 
		if($ex[1] > 0) {
			$searchArray['price-to'] = $ex[1];
			$sqlSearch .= ' && `price` <= "'.$searchArray['price-to'].'" ';
		}

	} else {
		/**
		 *	Search by price from to
		 */
		if($ex[0] > 0) $searchArray['price-from'] = $ex[0];
		if($ex[1] > 0) $searchArray['price-to'] = $ex[1];
		$sqlSearch .= ' && `price` BETWEEN "'.$ex[0].'" AND "'.$ex[1].'" ';
	}
}

/**
 *	Search by region
 */
if($url -> opd(URL_REGION) <> '') {
	$ex = explode('-', $url -> opd(URL_REGION));
	$searchArray['region'] = $ex[0];
	$sqlSearch .= ' && `region` = "'.$searchArray['region'].'" ';
}

/**
 *	Search by city
 */
$lat = 0;
$lng = 0;

if($url -> opd(URL_CITY) <> '') {
	if($url -> opd(URL_RADIUS) > 0) {
		$searchArray['radius'] = $url -> opd(URL_RADIUS);
	}
	$ex = explode('-', $url -> opd(URL_CITY));
	$searchArray['city'] = $ex[0];
	/*if($searchArray['radius'] > 0) {
		$file = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.rawurlencode($searchArray['city']).'&sensor=true&key='.GOOGLE_MAPS_KEY);
		$json  = json_decode($file);
		$lat = $json -> {'results'}[0] -> {'geometry'} -> {'location'} -> {'lat'};
		$lng = $json -> {'results'}[0] -> {'geometry'} -> {'location'} -> {'lng'};
	} else {*/
		$sqlSearch .= ' && `city` = "'.$searchArray['city'].'" ';
	//}
} else {
	if($_SESSION['city'] <> '') {
		$searchArray['city'] = $_SESSION['city'];
		/*if($searchArray['radius'] > 0) {
			$file = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.rawurlencode($searchArray['city']).'&sensor=true&key='.GOOGLE_MAPS_KEY);
			$json  = json_decode($file);
			$lat = $json -> {'results'}[0] -> {'geometry'} -> {'location'} -> {'lat'};
			$lng = $json -> {'results'}[0] -> {'geometry'} -> {'location'} -> {'lng'};
		} else {*/
			$sqlSearch .= ' && `city` = "'.$searchArray['city'].'" ';
		//}
	}
}

/**
 *	Search by features
 */
if($url -> opd(URL_FEATURE) <> '') {
	$searchFeature = new Feature;
	
	$ex = explode(';', $url -> opd(URL_FEATURE));
	foreach($ex as $k => $v) {
		$exF = explode('-', $v);
		$searchFeature -> getFeatureById($v[0]);
		
		$featureFieldName = '';
		if($searchFeature -> type == 1) $featureFieldName = 'value_text';
		if($searchFeature -> type == 2) $featureFieldName = 'value_number';
		if($searchFeature -> type == 3) $featureFieldName = 'value_select';
		if($searchFeature -> type == 4) $featureFieldName = 'value_checkbox';
		
		if($featureFieldName <> '') {
			$searchArray['feature'][$exF[0]] = $exF[1];
			
			$exList = explode(',', $exF[1]);
			$sqlSearchArray = [];
			$sqlSearch .= ' && `f'.$k.'`.`'.$featureFieldName.'` IN ("'.implode('", "', $exList).'") ';
		}
	}
}

/**
 *	Search by NIP
 */
if($url -> opd('nip') <> '') {
	$searchArray['nip'] = $url -> opd('nip');
	$sqlSearch .= ' && `nip` = "'.$searchArray['nip'].'" ';
	$moreCount++;
}

/**
 *	Sort
 */
if($url -> opd('sortuj') > 0) {
	if($url -> opd('sortuj') == 1) $sqlSort = ' `star` DESC ';
	if($url -> opd('sortuj') == 2) $sqlSort = ' `vote` DESC ';
	$moreCount++;
}

/**
 *	Sub cat
 */
if($url -> opd('sub') <> '') {
	if($searchArray['category'] > 0) {
		
		$ex = explode(',', $url -> opd('sub'));
		foreach($ex as $v) {
			$searchArray['sub'][$v] = 1;
		}
		
		$tmpSqlSearchArray = [];
		
		if($catList -> countCategoryList($searchArray['category']) > 0) {
			foreach($catList -> getCategoryList($searchArray['category']) as $r) {
				if($searchArray['sub'][$r['category_id']] == 1) {
					$tmpSqlSearchArray[] = ' `i`.`categories` LIKE "%('.$r['category_id'].')%" ';
				}
			}	

			if(count($tmpSqlSearchArray) > 0 && $tmpSqlSearchArray[0] <> '') {
				$sqlSearch .= ' && (';
				$sqlSearch .= implode(' || ', $tmpSqlSearchArray);
				$sqlSearch .= ') ';
			}
		}
	}
}

/**
 *	Count searches
 */
$countSearch = count($searchArray);

/**
 *	Get items
 */
$itemList = new Item;
$countItem = $itemList -> countItemList($sqlSearch, $searchArray['radius'], $lat, $lng);

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
$bc -> add($url -> getLink());
$bc -> output();

?>
			

		<h1><?php echo($meta['title']); ?></h1>
		<div id="left" class="left-3">
			<?php $main -> alert(); ?>

			<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SEARCH)); ?>" id="search-item">
				<div class="cols cols-3">
					<label>
						<input type="text" name="query" placeholder="Czego szukasz?" value="<?php echo($searchArray['query']); ?>">
					</label>
					<label>
						<input type="text" name="city" placeholder="Lokalizacja" value="<?php echo($searchArray['city']); ?>">
					</label>
					<label>
						<select name="category" id="feature-search">
							<option value="">Branża</option>
								
<?php

if($catList -> countCategoryList() > 0) {
	foreach($catList -> getCategoryList(0) as $r) {
		
			echo('<option value="'.$r['category_id'].'"'.(($searchArray['category'] == $r['category_id']) ? ' selected="selected"' : '').'>'.$r['name'].'</option>');
		
	}
}
	
?>

						</select>
					</label>
				</div>
				<div<?php if($moreCount == 0) echo(' id="search-more"'); ?>>
					<div class="cols cols-3">
						<label>
							<input type="text" name="nip" placeholder="NIP" value="<?php echo($searchArray['nip']); ?>">
						</label>
						<label>
							<select name="radius">
								<option value="10"<?php if($url -> opd(URL_RADIUS) == 1) echo(' selected="selected"'); ?>>W promieniu 10 km</option>
								<option value="25"<?php if($url -> opd(URL_RADIUS) == 25) echo(' selected="selected"'); ?>>W promieniu 25 km</option>
								<option value="50"<?php if($url -> opd(URL_RADIUS) == 50) echo(' selected="selected"'); ?>>W promieniu 50 km</option>
								<option value="100"<?php if($url -> opd(URL_RADIUS) == 100) echo(' selected="selected"'); ?>>W promieniu 100 km</option>
							</select>
						</label>
						<label>
							<select name="sort">
								<option value="0">Godne polecenia</option>
								<option value="1"<?php if($url -> opd('sortuj') == 1) echo(' selected="selected"'); ?>>Najlepiej oceniane</option>
								<option value="2"<?php if($url -> opd('sortuj') == 2) echo(' selected="selected"'); ?>>Najczęściej oceniane</option>
							</select>
						</label>
					</div>
				</div>
				<div id="sub-category-container" data-url="<?php echo($url -> getUrl('tool/feature', false, '/sub')); ?>">

<?php

if($searchArray['category'] > 0) {
	if($catList -> countCategoryList($searchArray['category']) > 0) {
		foreach($catList -> getCategoryList($searchArray['category']) as $r) {
			echo('<label>');
			echo('<input type="checkbox" name="sub-cat[]" value="'.$r['category_id'].'"'.((!$searchArray['sub'] || $searchArray['sub'][$r['category_id']] == 1) ? ' checked="checked"' : '').'>');
			echo($r['name']);
			echo('</label>');
		}
	}	
}

?>
				
				</div>
				<div>
					<input type="submit" value="Szukaj">
					<?php if($moreCount == 0) echo('<a href="#" id="search-more-toggle" style="margin-left: 10px;">zaawansowane filtry</a>'); ?>
					<?php if($moreCount == 0 && count($searchArray) > 0) echo(' <span style="margin: 0 10px;">/</span> '); ?>
					<?php if(count($searchArray) > 0) echo('<a href="'.$url -> getUrl('item/index', false, '/'.URL_CANCEL).'">wyczyść filtry</a>'); ?>
				</div>
			</form>
			<hr>
			<?php if($setup -> block_3 <> '') echo('<div id="block-3">'.$setup -> block_3.'</div>'); ?>
				
				<?php if($countItem == 0) echo('<p>Niczego nie znaleziono</p>'); ?>
	
	
			
<?php

$mapArray = [];

if($countItem > 0) {
	foreach($itemList -> getItemList($sqlSearch, $sqlSort, $searchArray['radius'], $lat, $lng) as $r) { 
		$itemUrl = $url -> getUrl('item/item?item_id='.$r['item_id']);
		$gal = explode(';', $r['gallery']);
		if($gal[0] <> '') {
			$ex = explode('.', $gal[0]);
			$img = $ex[0].'.400x300.'.end($ex);
			$logo = $ex[0].'.100x100.'.end($ex);
		} else {
			$img = 'cat-'.$r['category_id'].'.400x300.jpg';
			$logo = '';
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
				echo('<div class="line-item-phone"><a href="'.$itemUrl.'">pokaż numer telefonu</a></div>');
				echo('<div class="line-item-email"><a href="mailto:'.$r['email'].'">'.$r['email'].'</a></div>');
				echo('<a href="'.$itemUrl.'" class="button">'.ITEM_BUTTON.'</a>');
			echo('</div>');
		echo('</div>');
		
		/*if(($r['lat'] <> 0 && $r['lng'] <> 0)) {
			$mapArray[] = "$('#big-map').addMarker({ zoom: 6, coords: [".$r['lat'].", ".$r['lng']."] });";
		} else {
			if(!($r['lat'] <> 0 && $r['lng'] <> 0) && $r['city'] <> '') {
				$mapArray[] = "$('#big-map').addMarker({ address: '".$r['city'].(($r['address'] <> '') ? ', '.$r['address'] : '')."', zoom: 6, icon: './template/default/image/point.png', title: '".$r['title']."', text: '".(($logo <> '') ? '<img src="'.$url -> getUrl('tool/image', false, '/'.$logo).'" alt="'.$r['name'].'" style="border-radius: 100%; margin-right: 10px;"> ' : '')."<a href=\"".$itemUrl."\" class=\"button\">Zobacz wizytówkę</a>' });";
			}
		}*/
	}
	
	foreach($itemList -> getItemListAll($sqlSearch, $sqlSort, $searchArray['radius'], $lat, $lng) as $r) { 
		
		if(($r['lat'] <> 0 && $r['lng'] <> 0)) {
			$mapArray[] = "$('#big-map').addMarker({ zoom: 6, coords: [".$r['lat'].", ".$r['lng']."] });";
		} else {
			if(!($r['lat'] <> 0 && $r['lng'] <> 0) && $r['city'] <> '') {
				$mapArray[] = "$('#big-map').addMarker({ address: '".$r['city'].(($r['address'] <> '') ? ', '.$r['address'] : '')."', zoom: 6, icon: './template/default/image/point.png', title: '".$r['title']."', text: '".(($logo <> '') ? '<img src="'.$url -> getUrl('tool/image', false, '/'.$logo).'" alt="'.$r['name'].'" style="border-radius: 100%; margin-right: 10px;"> ' : '')."<a href=\"".$itemUrl."\" class=\"button\">Zobacz wizytówkę</a>' });";
			}
		}
	}
}

?>

	
			<?php paging($countItem); ?>	
			<hr>
			<div class="center"><a href="<?php echo($url -> getUrl('item/add')); ?>" class="button">Dodaj firmę</a></div>
		</div>
		<div id="right" class="right-3">
			<?php if($countItem > 0) { ?>
			<div id="big-map"></div>
			<script>
				<!--
				$(function() {
					$('#big-map').googleMap({zoom: 6});
					<?php foreach($mapArray as $v) echo($v."\n"); ?>
				});
				//-->
			</script>
			<?php } ?> 
		</div>
		<div class="clear"></div>
		<hr>
	
		
	</div>
	<div style="background: #fff; margin-bottom: -50px; ">
		<?php require_once('./template/default/newsletter-box.php'); ?>
		<div style="position: relative; top: -10px; z-index: 8888; background: #fff;"><?php require_once('./template/default/new-box.php'); ?></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>