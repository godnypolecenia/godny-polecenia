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
 *	This file displays the payment history
 */
 
if($url -> opd(URL_CANCEL) > 0) {
	$delPay = new Payment($url -> opd(URL_CANCEL));
		if(!($delPay -> paymentId > 0) || $delPay -> status <> 0) {
		$url -> redirect(404);
	}
	$delPay -> status = -1;
	$main -> alertPrepare(true);
	$url -> redirect();
}
 
$payList = new Payment;
$payCount = $payList -> countPaymentListOfUser($user -> userId);

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
				<h1><span class="ri-money-dollar-circle-line icon"></span><?php echo($meta['title']); ?></h1>
				
<?php

if($payCount > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>Typ</th>');
	echo('<th>Data</th>');
	echo('<th>Kwota</th>');
	echo('<th>Status</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($payList -> getPaymentListOfUser($user -> userId) as $r) {
		echo('<tr>');
		echo('<td>'.$paymentType[$r['type']].'</td>');
		echo('<td>'.dateTimeFormat($r['date']).'</td>');
		echo('<td>'.priceFormat($r['amount']).'</td>');
		echo('<td>'.$paymentStatus[$r['status']].'</td>');
		echo('<td>');
			if($r['status'] == 0) echo('<a href="'.$url -> getUrl('user/payment', false, '/'.$r['payment_id']).'">Opłać teraz</a> lub <a href="'.$url -> getUrl(null, false, '/'.URL_CANCEL.'-'.$r['payment_id']).'">Anuluj</a>');
			if($r['status'] == 1 && $r['bill'] <> '') echo('<a href="'.$url -> getUrl('bill', false, $r['payment_id']).'" target="_blank">Pobierz fakturę</a>');
		echo('</td>');
		echo('</tr>');
	}
	echo('</table>');
	paging($n);
} else {
	echo('<div>Niczego nie znaleziono</div>');
}

?>
				
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>