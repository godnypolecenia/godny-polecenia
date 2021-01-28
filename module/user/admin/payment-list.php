<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(4);

/**
 *	This file manages payments settings
 */


$sqlSearch = '';
if($url -> opd(URL_BOOKMARK) == 1) $sqlSearch .= ' && `status` = 1 ';
if($url -> opd(URL_BOOKMARK) == 2) $sqlSearch .= ' && `status` = 0 ';
if($url -> opd(URL_BOOKMARK) == 3) $sqlSearch .= ' && `status` = -1 ';

/**
 *
 */
 
$payList = new Payment;
$payCount = $payList -> countPaymentList($sqlSearch);

if($url -> opd(URL_EXPORT) == 'csv') {
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=file.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo('"Użytkownik";"Firma";"Typ płatności";"Data";"Kwota";"Status"'."\n");
	foreach($payList -> getPaymentList($sqlSearch) as $r) {
		echo('"'.$r['user'].'";');
		echo('"'.$r['item'].'";');
		echo('"'.$paymentType[$r['type']].'";');
		echo('"'.dateTimeFormat($r['date']).'";');
		echo('"'.priceFormat($r['amount']).'";');
		echo('"'.$paymentStatus[$r['status']].'"');
		echo("\n");
	}
	exit;
}

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
$bc -> add($url -> getLink('user/admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<ul class="bookmark">
				<?php if($mobile == 1) echo('<li class="bookmark-slide"><span class="ri-arrow-left-right-line"></span></li>'); ?>
				<li><?php echo($url -> getBookmark(0, 'Wszystkie')); ?></li>
				<li><?php echo($url -> getBookmark(1, 'Opłacone')); ?></li>
				<li><?php echo($url -> getBookmark(2, 'Nieopłacone')); ?></li>
				<li><?php echo($url -> getBookmark(3, 'Anulowane')); ?></li>
			</ul>
			<section>
				<h1><span class="ri-money-dollar-circle-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

if($payCount > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>Użytkownik</th>');
	echo('<th>Firma</th>');
	echo('<th>Typ</th>');
	echo('<th>Data</th>');
	echo('<th>Kwota</th>');
	echo('<th>Status</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($payList -> getPaymentList($sqlSearch) as $r) {
		echo('<tr>');
		echo('<td><a href="'.$url -> getUrl('user/admin/edit', false, '/'.$r['user_id']).'">'.$r['user'].'</a></td>');
		echo('<td><a href="'.$url -> getUrl('item/admin/edit', false, '/'.$r['id']).'">'.$r['item'].'</a></td>');
		echo('<td>'.$paymentType[$r['type']].'</td>');
		echo('<td>'.dateTimeFormat($r['date']).'</td>');
		echo('<td>'.priceFormat($r['amount']).'</td>');
		echo('<td>'.$paymentStatus[$r['status']].'</td>');
		echo('<td><a href="'.$url -> getUrl('user/admin/payment', false, '/'.$r['payment_id']).'">Zarządzaj</a></td>');
		echo('</tr>');
	}
	echo('</table>');
	paging($n);
} else {
	echo('<div>Niczego nie znaleziono</div>');
}

?>

			</section>
			<section class="toggle">
				<h2><span class="ri-file-download-line icon"></span>Eksportuj płatności</h2>
				<p>Możesz pobrać listę płatności do pliku CSV. Aktualne filtry (status, wyszukiwanie) zostaną uwzględnione.</p><br>
				<a href="<?php echo($url -> getUrl(null, true, '/'.URL_EXPORT.'-csv')); ?>" class="button" target="_blank">Pobierz plik CSV</a>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>