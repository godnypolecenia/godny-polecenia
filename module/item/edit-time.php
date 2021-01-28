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

$time = [];
$ex = explode(';', $editItem -> time);
foreach($ex as $k => $v) {
	$time[($k+1)] = (($v <> '') ? explode('-', $v) : ['', '']);
}

/**
 *
 */
 
if($url -> op(1) == URL_SAVE) {
	
	$arr = [];
	
	for($i = 1; $i <= 7; $i++) {
		if($_POST['from-'.$i] <> '' && $_POST['to-'.$i] <> '') {
			$arr[$i] = $_POST['from-'.$i].'-'.$_POST['to-'.$i];
		} else {
			$arr[$i] = '';
		}
	}

	$editItem -> time = implode(';', $arr);
	$main -> alertPrepare(true);
	$url -> redirect(null, false, '/'.$url -> op(0));
}

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
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_SAVE)); ?>">
					
					
<?php

foreach($dayName as $k => $v) {
	if($k == 0) {
		continue;
	}

	echo('<div class="cols cols-3">');
	echo('<div class="col-item"><label>'.$v.':</label></div>');
	echo('<div class="col-item"><label>');
	echo('<select name="from-'.$k.'">');
	echo('<option value="">Nieczynne</option>');
	for($i = 0; $i <= 23; $i++) {
		echo('<option'.(($time[$k][0] == (($i < 10) ? '0' : '').$i.':00') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':00</option>');
		echo('<option'.(($time[$k][0] == (($i < 10) ? '0' : '').$i.':30') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':30</option>');
	}
	echo('</select>');
	echo('</label></div>');
	echo('<div class="col-item"><label>');
	echo('<select name="to-'.$k.'">');
	echo('<option value="">Nieczynne</option>');
	for($i = 0; $i <= 23; $i++) {
		echo('<option'.(($time[$k][1] == (($i < 10) ? '0' : '').$i.':00') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':00</option>');
		echo('<option'.(($time[$k][1] == (($i < 10) ? '0' : '').$i.':30') ? ' selected="selected"' : '').'>'.(($i < 10) ? '0' : '').$i.':30</option>');
	}
	echo('</select>');
	echo('</label></div>');
	echo('</div>');
}

?>

					<div class="buttons center">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>