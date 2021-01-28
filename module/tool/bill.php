<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file allows you to download a bill
 */
 
$billPay = new Payment;
if(!$billPay -> getPaymentById($url -> op(0))) {
	require_once('./module/tool/404.php');
	exit;
}

if(!($user -> userId == $billPay -> user_id || $user -> type == 9)) {
	$url -> redirect(403);
}

header('Content-Type: '.mime_content_type('./data/bill/'.$billPay -> bill));
header('Content-Disposition:attachment;filename="'.$billPay -> bill.'"');
readfile('./data/bill/'.$billPay -> bill);

?>