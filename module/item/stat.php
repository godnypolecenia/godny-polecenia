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
 *	Get item data
 */
$editItem = new Item($url -> op(0));
if(!($editItem -> itemId > 0)) {
	$url -> redirect(404);
}

$d1 = date('Y-m-d', $editItem -> date);
$d2 = date('Y-m-d');

if($_POST['d1'] <> '') {
	$d1 = $_POST['d1'];
}

if($_POST['d2'] <> '') {
	$d2 = $_POST['d2'];
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
$bc -> add($url -> getLink('item/add-list'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>

			<section>
				<h1><span class="ri-line-chart-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

$sum = 0;
$sumPhone = 0;
$sumMsg = 0;

$db -> query(
	'SELECT * '.
	'FROM `db_stat` '.
	'WHERE `item_id` = "'.$editItem -> itemId.'" && `date` BETWEEN "'.$d1.'" AND "'.$d2.'" '.
	'ORDER BY `date` DESC'
);
if($db -> numRows() == 0) {
	echo('<p>Brak statystyk</p>');
} else {
	echo('<table>');
	echo('<tr>');
	echo('<th>Dzień</th>');
	echo('<th>Wyświetleń</th>');
	echo('<th>Odsłon nr telefonu</th>');
	echo('<th>Wiadomości</th>');
	echo('</tr>');
	while($r = $db -> fetchArray()) {
		echo('<tr>');
		echo('<td>'.$r['date'].'</td>');
		echo('<td>'.$r['counter'].'</td>');
		echo('<td>'.$r['phone'].'</td>');
		echo('<td>'.$r['message'].'</td>');
		echo('</tr>');
		$sum += $r['counter'];
		$sumPhone += $r['phone'];
		$sumMsg += $r['message'];
	}
	echo('<tr>');
		echo('<td class="bold">Razem</td>');
		echo('<td class="bold">'.$sum.'</td>');
		echo('<td class="bold">'.$sumPhone.'</td>');
		echo('<td class="bold">'.$sumMsg.'</td>');
		echo('</tr>');
	echo('</table>');
}

?>
				
			</section>
			<section>
				<h2>Filtruj statystyki</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.$editItem -> item_id)); ?>">
					<div class="cols cols-2">
						<label>
							<input type="text" name="d1" placeholder="Od kiedy" value="<?php echo($d1); ?>">
						</label>
						<label>
							<input type="text" name="d2" placeholder="Do kiedy" value="<?php echo($d2); ?>">
						</label>
					</div>
					<div class="buttons">
						<input type="submit" value="Filtruj">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>