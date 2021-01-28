<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file accepts an answer from online payment
 */
 
$forcedPay = false;
if($url -> op(1) == password(date('Y-m-d').TOKEN)) {
	$forcedPay = true;
}

$pay = new Payment($url -> op(0));
if(!($pay -> paymentId > 0)) {
	echo('Brak płatności');
	exit;
}

if($pay -> status <> 0) {
	echo('Płatność została już opłacona');
	exit;
}

if($forcedPay == false && !$pay -> authorization()) {
	echo('Brak autoryzacji');
	exit;
}

if($forcedPay == false && !$pay -> checkData()) {
	echo('Zła kwota transakcj');
	exit;
}

/**
 *	Pakiet Standard
 */
if($pay -> type == 1) {
	$payItem = new Item($pay -> id);
	if(!($payItem -> itemId > 0)) {
		echo($pay -> payErrorLog('Oferta nie istnieje'));
		exit;
	}
	$payItem -> validity = time()+(86400*$setup -> validity_day);
	
	$sendUser = new User($payItem -> user_id);
	if($sendUser -> userId > 0) {
		$tmpEmail = str_replace(
			['{nazwa}', '{tytul}', '{link}'],
			[$setup -> name, $payItem -> title, $url -> getUrl('item', false, '/'.toUrl($payItem -> title).'-'.$payItem -> itemId)],
			$setup -> mail_premium
		);
		send_mail($_POST['email'], $setup -> mail_premium_title, $tmpEmail);
	}
	
	wfirma($paymentType[$pay -> type], ($pay -> amount/1.23), $pay -> firstname.' '.$pay -> lastname, $pay -> address, $pay -> postcode, $pay -> city, (($pay -> nip <> '') ? $pay -> nip : '0'), $pay -> email);
	//file_put_contents('test.txt', 'wfirma('.$paymentType[$pay -> type].', '.($pay -> amount/1.23).', '.$pay -> firstname.' '.$pay -> lastname.', '.$pay -> address.', '.$pay -> postcode.', '.$pay -> city.', '.(($pay -> nip <> '') ? $pay -> nip : '0').', '.$pay -> email.');');

	$pay -> done();
}	

/**
 *	Pakiet Premium
 */
if($pay -> type == 2) {
	$payItem = new Item($pay -> id);
	if(!($payItem -> itemId > 0)) {
		echo($pay -> payErrorLog('Oferta nie istnieje'));
		exit;
	}
	$payItem -> premium = time()+(86400*$setup -> premium_day);
	$payItem -> validity = time()+(86400*$setup -> premium_day);
	
	$sendUser = new User($payItem -> user_id);
	if($sendUser -> userId > 0) {
		$tmpEmail = str_replace(
			['{nazwa}', '{tytul}', '{link}'],
			[$setup -> name, $payItem -> title, $url -> getUrl('item', false, '/'.toUrl($payItem -> title).'-'.$payItem -> itemId)],
			$setup -> mail_premium
		);
		send_mail($_POST['email'], $setup -> mail_premium_title, $tmpEmail);
	}
	
	wfirma($paymentType[$pay -> type], ($pay -> amount/1.23), $pay -> firstname.' '.$pay -> lastname, $pay -> address, $pay -> postcode, $pay -> city, (($pay -> nip <> '') ? $pay -> nip : '0'), $pay -> email);
	//file_put_contents('test.txt', 'wfirma('.$paymentType[$pay -> type].', '.($pay -> amount/1.23).', '.$pay -> firstname.' '.$pay -> lastname.', '.$pay -> address.', '.$pay -> postcode.', '.$pay -> city.', '.(($pay -> nip <> '') ? $pay -> nip : '0').', '.$pay -> email.');');

	$pay -> done();
}

?>