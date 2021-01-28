<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */
 
if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	-----------------------
 *	This file displays item
 *	-----------------------
 */

//echo  'W budowie'; exit;

/**
 *	URL mode
 *	1 - the only URL
 *	2 - parameters in OP
 */
$urlMode = 1;

/**
 *	Get item data
 */
$item = new Item(($urlMode == 1) ? $url -> var['item_id'] : $url -> op(0));
if(!($item -> itemId > 0)) {
	require_once('./module/tool/404.php');
	exit;
}

$packName = 'free';
if($item -> validity > time()) $packName = 'validity';
if($item -> premium > time()) $packName = 'premium';

$item -> counter();

/**
 *	Initialize feature class
 */
$featureList = new Feature;
$countFeatureList = $featureList -> countFeatureListOfCategory($item -> category_id);
$featureArray = [];
if($countFeatureList > 0) {
	foreach($featureList -> getFeatureListOfCategory($item -> category_id) as $r) {
		$featureArray[] = $r['label'];
	}
}

/**
 *	URL mode - setup
 */
if($urlMode == 1) {
	$urlModeDir = null; 
	$urlModeExistOp = false; 
	$urlModeAddOp = null;
	$UrlModeFirstOp = 0;
} else {
	$urlModeDir = 'item/item'; 
	$urlModeExistOp = false; 
	$urlModeAddOp = '/'.$url -> op(0).'/'.toUrl($item -> title);
	$UrlModeFirstOp = 2;
}

/**
 *	Gallery
 */
$exGal = explode(';', $item -> gallery);
foreach($exGal as $v) {
	$ex = explode('.', $v);
	$gal[] = [
		'file' => $v,
		'name' => $ex[0],
		'format' => end($ex)
	];
}

/**
 *
 */
 
$url -> addBackUrl();

/**
 *	Initialize feature class
 */
$relFeature = new Feature;

/**
 *
 */
if($url -> opd(URL_EXEC) == 'tel') {
	$item -> counterPhone();
	exit;
}	

/**
 *	Add to favorite
 */ 
if($url -> opd(URL_FAVORITE) == URL_ADD && $user -> userId > 0) {
	$item -> addFavorite($item -> itemId, $user -> userId);
	$main -> alertPrepare(true);
	$url -> redirect($urlModeDir, $urlModeExistOp, $urlModeAddOp);
}

/**
 *	Delete from favorite
 */ 
if($url -> opd(URL_FAVORITE) == URL_DEL && $user -> userId > 0) {
	$item -> deleteFavorite($item -> itemId, $user -> userId);
	$main -> alertPrepare(true);
	$url -> redirect($urlModeDir, $urlModeExistOp, $urlModeAddOp);
}

/**
 *	Send message
 */ 
if($url -> op($UrlModeFirstOp) == URL_SEND && $_POST['content'] <> '') {
	$sendMsg = new Message;
	$sendMsg -> add(0, $item -> user_id, (($_POST['date'] <> '') ? 'Data rezerwacji: '.$_POST['date']."\n\n" : '').'Nadawca: '.$_POST['name']."\n".'Adres e-mail: <a href=\"mailto:'.$_POST['email'].'\">'.$_POST['email'].'</a>'."\n".'Telefon: '.$_POST['phone']."\n\n".$_POST['content'], $_POST['name']);
	
	$item -> counterMsg();
	
	if($item -> email <> '') {
		$tmpMail = str_replace(
			['{nadawca}', '{link}', '{nazwa}'],
			[$_POST['name'], $url -> getUrl('user/message-list'), $setup -> name],
			$setup -> mail_message
		);
		send_mail($item -> email, $setup -> mail_message_title, $tmpMail);
	}
	
	$newsletter = new Newsletter;
	$newsletter -> add($_POST['email']);

	$main -> alertPrepare(true, 'Wiadomość została wysłana');
	$url -> redirect($urlModeDir, $urlModeExistOp, $urlModeAddOp);
}

/**
 *	Get author data
 */
$itemUser = new User;
$itemUser -> getUserById($item -> user_id);

/**
 *	Get category data
 */
$itemCat = new Category;
$itemCat -> getCategoryById($item -> category_id);

/**
 *	Check favorite
 */
$isFavorite = false;
if($user -> userId > 0) {
	$isFavorite = $item -> isFavorite($item -> itemId, $user -> userId);
}

/**
 *	Add Vote
 */
if($url -> op($UrlModeFirstOp) == URL_EXEC && $_POST['email'] <> '' && ($_POST['vote'] > 0 && $_POST['vote'] <= 5)) {
	$vote = new Vote;
	
	$tmp = [];
		
	$countFeatureList = $featureList -> countFeatureListOfCategory($item -> category_id);
	if($countFeatureList > 0) {
		foreach($featureList -> getFeatureListOfCategory($item -> category_id) as $r) {
			if($_POST['vote-'.$r['feature_id']] > 0) {
				$tmp[] = $_POST['vote-'.$r['feature_id']];
			} else {
				$tmp[] = 0;
			}
		}
	}
	
	if($vote -> add($item -> itemId, $_POST['vote'], $_POST['email'], $_POST['name'], $_POST['content'], implode(';', $tmp)) <> false) {

		$main -> alertPrepare(true, 'Ocena została dodana. Zaczekaj na aktywację swojej opinii przez administratora.');
		$newsletter = new Newsletter;
		$newsletter -> add($_POST['email']);
	} else {
		$main -> alertPrepare(false, 'Już oceniłeś wcześniej tę firmę');
	}
	$url -> redirect($urlModeDir, $urlModeExistOp, $urlModeAddOp);
}

/**
 *	Layout
 */

$url -> setBodyId('index');

$meta = [
	'title' => $item -> title.' - '.$item -> city.' '.$item -> address,
	'description' => mb_substr(str_replace(';', ', ', preg_replace('@\{([0-9]+)\}@is', '', $item -> services)).'. '.$item -> content, 0, 255, 'UTF-8'),
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="slider" class="slider-mini"<?php echo(($packName == 'premium' && $item -> banner <> '') ? ' style="background: #243244 url('.$url -> getUrl('tool/image', false, '/'.str_replace('.jpg', '.800x600.jpg', $item -> banner)).') no-repeat left +50vw center;"' : ' style="background: #243244 url('.$url -> getUrl('tool/image', false, '/cat-'.$item -> category_id.'.jpg').') no-repeat left +50vw center"'); ?>>
	<div class="main">
		<div class="red-line"></div>

<?php

if($setup -> {$packName.'_logo'} == 1) {
	if($gal[0]['name'] <> '') {
		echo('<img src="'.$url -> getUrl('tool/image', false, '/'.$gal[0]['name'].'.100x100.'.$gal[0]['format']).'" alt="'.$item -> name.'" id="item-logo">');
	}
}
	
?>
		
		<h1 class="lora" style="text-shadow: 0 0 3px rgba(0, 0, 0, 0.7);"><?php echo($item -> title); ?></h1>
		<p style="text-shadow: 0 0 3px rgba(0, 0, 0, 0.7);"><?php if($item -> category_id > 0) echo((($itemCat -> parent <> '') ? $itemCat -> parent.' - ' : '').$itemCat -> name); ?></p>
		<?php if($mobile == 1) echo('<br>'); ?>
		<div class="red-line"></div>

<?php

echo('<div id="item-star">');
for($i = 1; $i <= 5; $i++) {
	if($item -> star >= $i) {
		echo('<span class="ri-star-fill"></span> ');
	} else {
		echo('<span class="ri-star-line"></span> ');
	}
}
echo('<strong>'.$item -> precent.' '.$item -> vote.' '.inflect($item -> vote, ['głos', 'głosy', 'głosów']).'</strong>');
echo('</div>');
			
?>
		
		<div id="counter"><?php echo($item -> counter); ?></div>
	</div>
</div>
<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('item/index'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

$main -> alert();

if($item -> status == 0) {
	echo('<section class="alert alert-error">');
	echo('<p>Uwaga! Ta wizytówka jeszcze nie jest aktywna.</p>');
	echo('</section>');
}

?>
		
		<div id="left" class="left-2" style="border-right: none;">
			<h3 class="red" style="margin-top: 0;">O firmie</h3>
			<p><?php echo(textFormat($item -> content)); ?></p>
			
<?php

if($setup -> {$packName.'_services'} == 1) {
	$i = 0;
	if($item -> services <> '') {
		echo('<h3 class="red">Usługi</h3>');
		$ex = explode(';', $item -> services);
		echo('<ul class="ul-2" style="max-width: 100%;">');
		foreach($ex as $v) {
			$i++;
			$ex2 = explode('{', $v);
			echo('<li'.(($i > 5) ? ' class="service-hide"' : '').'>'.$ex2[0].' <span class="service-price">'.((str_replace('}', '', $ex2[1]) <> '0') ? priceFormat(str_replace('}', '', $ex2[1])) : '').'</span></li>');
		}
		echo('</ul>');
	}
	if($i > 5) echo('<a href="#" id="service-hide-toggle">więcej...</a>');
}

if($setup -> {$packName.'_amenitie'} == 1) {
	$i = 0;
	$ex = explode(';', $item -> amenitie);
	if(in_array(1, $ex)) {
		echo('<h3 class="red">Udogodnienia</h3>');
		echo('<ul class="ul-2 amenitie-li">');
		foreach($amenitie as $k => $v) {
			if($ex[$k] == 1) {
				$i++;
				echo('<li'.(($i > 5) ? ' class="li-hide"' : '').'>'.$v.'</li>');
			}
		}
		echo('</ul>');
		if($i > 5) echo('<a href="#" id="li-hide-toggle">więcej...</a>');
	}
}

?>	
			
				
			<?php if($setup -> block_4 <> '') echo('<div id="block-4">'.$setup -> block_4.'</div>'); ?>
			
			<h3 class="red">Polecenia</h3>

<?php

$j = 0;
$vote = new Vote;
if($vote -> countActiveVoteListOfItem($item -> itemId) > 0) {
	foreach($vote -> getActiveVoteListOfItem($item -> itemId) as $r) {
		$i++;
		echo('<section class="vote-box-2'.(($j > 5) ? ' vote-hide' : '').'">');
		echo('<p class="small bold">'.$r['nick'].' / '.dateTimeFormat($r['date']).'</p>');
		if($r['content'] <> '') echo('<br><p>'.$r['content'].'</p>');
		
		echo('<br>');
		echo('<div>');
		for($i = 1; $i <= 5; $i++) {
			if($r['vote'] >= $i) {
				echo('<span class="ri-star-fill star"></span>'."\n");
			} else {
				echo('<span class="ri-star-line star"></span>'."\n");
			}
		}
		echo('<span style="padding: 0 5px;"></span>Ocena');
		echo('</div>');
		
		if($r['vote_feature'] <> '') {
			$ex = explode(';', $r['vote_feature']);
			
			if(is_array($featureArray)) {
				foreach($featureArray as $k => $v) {
					echo('<div>');
					for($i = 1; $i <= 5; $i++) {
						if($ex[$k] > 0) {
							if($ex[$k] >= $i) {
								echo('<span class="ri-star-fill star"></span>'."\n");
							} else {
								echo('<span class="ri-star-line star"></span>'."\n");
							}
						}
					}
					echo('<span style="padding: 0 5px;"></span>'.$v);
					echo('</div>');
				}
			}
		}
		
		if($r['reply'] <> '') {
			echo('<br><section style="padding: 10px;">');
			echo('<p class="small"><span class="bold">Odpowiedź:</span> '.$r['reply'].'</p>');
			echo('</section>');
		}	
		echo('</section>');
	}
	if($j > 5) echo('<a href="#" id="vote-hide-toggle">więcej...</a>');
} else {
	echo('<p>Jeszcze nikt nie ocenił tej firmy</p>');
}

?>			
			
			
			<h3 class="red">Oceń firmę</h3>
			<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_EXEC)); ?>">
				<div>
					<a href="#" class="vote-click" data-id="vote-input" data-value="1"><span class="ri-star-line"></span></a>
					<a href="#" class="vote-click" data-id="vote-input" data-value="2"><span class="ri-star-line"></span></a>
					<a href="#" class="vote-click" data-id="vote-input" data-value="3"><span class="ri-star-line"></span></a>
					<a href="#" class="vote-click" data-id="vote-input" data-value="4"><span class="ri-star-line"></span></a>
					<a href="#" class="vote-click" data-id="vote-input" data-value="5"><span class="ri-star-line"></span></a>
					<span style="padding: 0 10px;"></span>Twoja ocena
				</div>
				<input type="hidden" name="vote" value="5" id="vote-input">

<?php


if($countFeatureList > 0) {
	foreach($featureList -> getFeatureListOfCategory($item -> category_id) as $r) {
		echo('<div>');
		echo('<a href="#" class="vote-click" data-id="vote-'.$r['feature_id'].'" data-value="1"><span class="ri-star-line"></span></a>'."\n");
		echo('<a href="#" class="vote-click" data-id="vote-'.$r['feature_id'].'" data-value="2"><span class="ri-star-line"></span></a>'."\n");
		echo('<a href="#" class="vote-click" data-id="vote-'.$r['feature_id'].'" data-value="3"><span class="ri-star-line"></span></a>'."\n");
		echo('<a href="#" class="vote-click" data-id="vote-'.$r['feature_id'].'" data-value="4"><span class="ri-star-line"></span></a>'."\n");
		echo('<a href="#" class="vote-click" data-id="vote-'.$r['feature_id'].'" data-value="5"><span class="ri-star-line"></span></a>'."\n");
		echo('<span style="padding: 0 10px;"></span>'.$r['label']);
		echo('</div>');
		echo('<input type="hidden" name="vote-'.$r['feature_id'].'" value="5" id="vote-'.$r['feature_id'].'">');
	}
}

?>

				
				<br>
				<div class="cols cols-2">
					<label>
						<input type="text" name="email" placeholder="Adres e-mail" required="required">
					</label>
					<label>
						<input type="text" name="name" placeholder="Twoje imię" required="required">
					</label>
				</div>
				<label>
					<textarea name="content" class="short" placeholder="Treść komentarza (nieobowiązkowe)"></textarea>
				</label>
				<div class="buttons">
					<input type="submit" value="Wyślij">
				</div>
			</form>
		</div>
		<div id="right" class="right-2">
				<?php if($setup -> {$packName.'_time'} == 1 && $item -> time <> '') echo('<div class="cols cols-2"><div class="col-item">'); ?>
				<?php if($setup -> {$packName.'_data'} == 1) { ?>
				<h3 class="red" style="margin-top: 0;">Adres</h3>
				<ul class="ul-2 ">
					<?php if($item -> city <> '') echo('<li class="li-address">'.$item -> city.(($item -> address <> '') ? ', '.$item -> address : '').'</li>'); ?>
					<?php if($item -> phone <> '') echo('<li class="li-phone"><a href="tel:'.$item -> phone.'" class="hide-phone" data-counter="'.$url -> getUrl(null, true, '/'.URL_EXEC.'-tel').'">Pokaż numer telefonu</a></li>'); ?>
					<?php if($item -> email <> '') echo('<li class="li-email"><a href="mailto:'.$item -> email.'">'.$item -> email.'</a></li>'); ?>
					<?php if($item -> www <> '') echo('<li class="li-www"><a href="'.$item -> www.'" target="_blank">'.str_replace(['http://', 'https://', 'www.'], '', $item -> www).'</a></li>'); ?>
				</ul>
				<?php } ?>
			
			
<?php

if($setup -> {$packName.'_time'} == 1) {
	if($item -> time <> '') {
		echo('</div>');
		echo('<div class="col-item">');
		echo('<h3 class="red">Godziny otwarcia</h3>');
		echo('<ul class="ul-2">');
		$time = explode(';', $item -> time);
		foreach($dayName as $k => $v) {
			if($k == 0) {
				continue;
			}
			echo('<li class="li-time">'.$v.': <span class="service-price">'.(($time[$k] <> '') ? $time[$k] : 'nieczynne').'</span></li>');
		}
		echo('</ul>');
		echo('</div>');
		echo('</div>');
	}
}

?>			

			<?php if($setup -> {$packName.'_map'} == 1) { ?>
			<?php if(($item -> lat <> 0 && $item -> lng <> 0) || $item -> city <> '') { ?>
			<h3 class="red">Lokalizacja</h3>
			<div id="map"></div><br>
			<div class="center"><a href="https://www.google.pl/maps/place/<?php echo(urlencode($item -> address).','.urlencode($item -> city)); ?>/?hl=pl" target="_blank" class="button">Wyznacz trasę</a></div>
			<script>
				<!--
				$(function() {
					$('#map').googleMap();
					$('#map').addMarker({
						<?php if(($item -> lat <> 0 && $item -> lng <> 0)) echo("coords: [".$item -> lat.", ".$item -> lng."],"); ?>
						<?php if(!($item -> lat <> 0 && $item -> lng <> 0) && $item -> city <> '') echo("address: '".$item -> city.(($item -> address <> '') ? ','.$item -> address : '')."',"); ?>
						zoom: 12,
						icon: './template/default/image/point.png'
					});
				});
				//-->
			</script>
			<?php } ?>
			<?php } ?>
			
			<?php if($setup -> {$packName.'_contact_form'} == 1) { ?>
			<br>
			<h3 class="red">Formularz kontaktowy</h3>
			<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SEND)); ?>">
				<label>
					<textarea name="content" placeholder="Treść wiadomości" required="required"></textarea>
				</label>
				<div class="cols cols-2">
					<label>
						<input type="text" name="name" placeholder="Imię i nazwisko" required="required">
					</label>
					<label>
						<input type="text" name="date" placeholder="Termin (nieobowiązkowe)" class="datepicker">
					</label>
				</div>
				<div class="cols cols-2">
					<label>
						<input type="text" name="email" placeholder="Adres e-mail" required="required">
					</label>
					<label>
						<input type="text" name="phone" placeholder="Telefon" required="required">
					</label>
				</div>
				<div class="buttons center">
					<input type="submit" value="Wyślij">
				</div>
			</form>
			<?php } ?>
			
			<?php if($setup -> {$packName.'_social'} == 1) { ?>
			<br>
			<h3 class="red">Udostępnij</h3>
			<a href="<?php echo($url -> getUrl('contact', false, '/zglos-'.$item -> itemId)); ?>" class="float-right underline" style="position: relative; top: 10px;">Zgłoś firmę</a>
			<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo($url -> getUrl()); ?>" target="_blank" title="Facebook"><span class="ri-facebook-line button"></span></a>
			<a href="https://twitter.com/home?status=<?php echo($url -> getUrl()); ?>" target="_blank" title="Twitter"><span class="ri-twitter-line button"></span></a>
			<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo($url -> getUrl()); ?>" target="_blank" title="LinkedIn"><span class="ri-linkedin-line button"></span></a>
			<?php } ?>
		</div>
		<div class="clear"></div>

<?php

if($setup -> {$packName.'_gallery'} == 1) {
	if($gal[0]['name'] <> '') {
		echo('<h3 class="red">Galeria</h3>');
		echo('<div class="item-slider" data-move="4">');
		foreach($gal as $k => $v) {
			echo('<a href="'.$url -> getUrl('tool/image', false, '/'.$v['file']).'" data-lightbox="gallery"><img src="'.$url -> getUrl('tool/image', false, '/'.$v['name'].'.'.(($mobile == 1) ? '480x320' : '320x240').'.'.$v['format']).'" alt="'.$item -> title.'" class="item-gallery"></a>'."\n");
		}
		echo('</div><br>');
	}
}

?>

		
	</div>
</div>
<div class="bottom-space"></div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>