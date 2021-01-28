<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This function clears variables from illegal characters
 */

function good_var_html(&$arr) {
	foreach($arr as &$v) {
		if(is_array($v)) {
			good_var_html($v);
		} else {
			$v = addslashes(strip_tags($v));
		}
	}
	unset($v);
}

/**
 *	This function sends emails
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail($to, $title, $content, $reply = null, $attachment = null) {
	global $setup;
	
	require './include/mail/src/Exception.php';
	require './include/mail/src/PHPMailer.php';
	require './include/mail/src/SMTP.php';
	
	if(!($sender <> '')) $sender = $setup -> sender;
	if(!($reply <> '')) $reply = $setup -> email;
	
	date_default_timezone_set('Europe/Warsaw');
	
	$mail = new PHPMailer();
	$mail -> IsSMTP();
	$mail -> SMTPOptions = array(
		'ssl' => array(
		'verify_peer' => false,
		'verify_peer_name' => false,
		'allow_self_signed' => true
		)
	);
	$mail -> CharSet = 'UTF-8';
	$mail -> Host = $setup -> email_host;
	$mail -> SMTPDebug = 0;
	$mail -> SMTPAuth = true;
	$mail -> Port = $setup -> email_port;
	$mail -> Username = $setup -> email_username;
	$mail -> Password = $setup -> email_password;
	$mail -> isHTML(true);
	$mail -> setFrom($setup -> email, $setup -> email_sender);
	$mail -> addReplyTo((($reply <> '') ? $reply : $setup -> email_reply), (($sender <> '') ? $sender : $setup -> email_sender));
	$mail -> addAddress($to);
	$mail -> Subject = $title;
	
	$mailTpl = file_get_contents(SITE_ADDRESS.'/mail-template');
	$mailTpl = str_replace(['{title}', '{content}', '{footer}'], [$title, $content, $setup -> mail_footer], $mailTpl);
	
	$mail -> Body = $mailTpl;
	$mail -> AltBody = $content;
	//$mail -> AddAttachment($attachment, 'plik.pdf');
	if($mail -> send()) {
		return(true);
	} else {
		return($mail -> ErrorInfo);
	}
}

/**
 *	This function formats the number
 */
function numberFormat($value) {
	global $setup;
	
	return(number_format($value, $setup -> format_number, $setup -> format_point, $setup -> format_sep));
}
function convertToNumber($value) {
	
	return((float)floatval(str_replace(',', '.', str_replace('.', '', $value))));
}

/**
 *	This function formats the price
 */
function priceFormat($value) {
	global $setup;
	
	return((($setup -> format_price_position == 0 && $setup -> format_price_currency <> '') ? $setup -> format_price_currency.' ' : '').number_format($value, $setup -> format_price_number, $setup -> format_price_point, $setup -> format_price_sep).(($setup -> format_price_position == 1 && $setup -> format_price_currency <> '') ? ' '.$setup -> format_price_currency : ''));
}

/**
 *	This function formats the date
 */
function dateFormat($value) {
	global $setup;
	
	return(date($setup -> format_date, $value));
}

/**
 *	This function formats the time
 */
function timeFormat($value) {
	global $setup;
	
	return(date($setup -> format_time, $value));
}

/**
 *	This function formats the date and time
 */
function dateTimeFormat($value) {
	global $setup;
	
	return(date($setup -> format_date.' '.$setup -> format_time, $value));
}

/**
 *
 */
function textFormat($text) {
	
	return(str_replace("\n", '<br>', $text));
}

/**
 *	This function creates page numbering
 */
function paging($n) {
	global $url;
	
	if($n > PAGE_N) {
		$count = ceil($n/PAGE_N);
		
		$start = 1;
		if($url -> opd(URL_PAGE) > 1) $start = $url -> opd(URL_PAGE)-5;
		if($start < 1) $start = 1;
		
		$end = $count;
		if($url -> opd(URL_PAGE) < $end-5) $end = $url -> opd(URL_PAGE)+5;
		if($end > $count) $end = $count;
		
		$current = $url -> opd(URL_PAGE);
		$adr = str_replace('/'.URL_PAGE.'-'.$current, '', $url -> getUrl(null, true));

		echo('<div class="paging">');
			echo('<ul>');
				echo('<li><a href="'.$adr.'/'.URL_PAGE.'-'.(($current > 1) ? $current-1 : 1).'"><span class="ri-arrow-left-line"></span></a></li>');
				if($start > 1) echo('<li><a href="'.$adr.'/'.URL_PAGE.'-1">...</a></li>');
				for($i = $start; $i <= $end; $i++) {
					echo('<li><a href="'.$adr.'/'.URL_PAGE.'-'.$i.'"'.(($current == $i) ? ' class="selected"' : '').'>'.$i.'</a></li>');
				}
				if($end < $count) echo('<li><a href="'.$adr.'/'.URL_PAGE.'-'.$end.'">...</a></li>');
				echo('<li><a href="'.$adr.'/'.URL_PAGE.'-'.(($current < $end) ? $current+1 : $end).'"><span class="ri-arrow-right-line"></span></a></li>');
			echo('</ul>');
		echo('</div>');
	}
}

/**
 *
 */
$amenitie = explode(';', $setup -> amenitie);
	
/**
 *
 */
function wfirma($title, $netto, $name, $address, $postcode, $city, $nip, $email) {

	$xmlRequest = '<api>
		<contractors>
			<parameters>
				<conditions>
					<condition>
						<field>nip</field>
						<operator>eq</operator>
						<value>'.$nip.'</value>
					</condition>
				</conditions>
			</parameters>
		</contractors>
	</api>';

	$ch = curl_init('');
	curl_setopt($ch, CURLOPT_URL, 'http://api2.wfirma.pl/contractors/find');
	curl_setopt($ch, CURLOPT_POST,   1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
	curl_setopt($ch, CURLOPT_USERPWD, WFIRMA_LOGIN.':'.WFIRMA_PASSWORD);
	$response = curl_exec($ch); 
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	curl_close($ch);

	preg_match('@\<id\>([0-9]+)\<\/id\>@is', $response, $o);
	$id = $o[1];

	if(!($id > 0)) {
		$xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
		<api>
			<contractors>
				<contractor>
					<name>'.$name.'</name>
					<street>'.$address.'</street>
					<zip>'.$postcode.'</zip>
					<city>'.$city.'</city>
					<nip>'.$nip.'</nip>
					<email>'.$email.'</email>
				</contractor>
			</contractors>
		</api>';

		$ch = curl_init('');
		curl_setopt($ch, CURLOPT_URL, 'http://api2.wfirma.pl/contractors/add');
		curl_setopt($ch, CURLOPT_POST,   1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
		curl_setopt($ch, CURLOPT_USERPWD, WFIRMA_LOGIN.':'.WFIRMA_PASSWORD);
		$response = curl_exec($ch); 
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		curl_close($ch);
		preg_match('@\<id\>([0-9]+)\<\/id\>@is', $response, $o);
		$id = $o[1];
	}

	$xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
	<api> 
		<invoices>
			<invoice>
				<contractor>
					<id>'.$id.'</id>
				</contractor>
				<auto_send_postivo>1</auto_send_postivo>
				<auto_send>1</auto_send>
				<paymentmethod>transfer</paymentmethod>
				<paid>1</paid>
				<date>'.date('Y-m-d').'</date>
				<disposaldate>'.date('Y-m-d').'</disposaldate>
				<paymentdate>'.date('Y-m-d', strtotime('+1 week')).'</paymentdate>
			   <invoicecontents>
					<invoicecontent>
						<price_type>brutto</price_type>
						<name>'.$title.'</name>
						<unit>szt.</unit>
						<count>1</count>
						<price>'.$netto.'</price>
						<alreadypaid>'.$netto.'</alreadypaid>
						<vat>23</vat>
					</invoicecontent>
				</invoicecontents> 
			 </invoice>
			</invoices>
		</api>';
						
	$ch = curl_init('');
	curl_setopt($ch, CURLOPT_URL, 'http://api2.wfirma.pl/invoices/add');
	curl_setopt($ch, CURLOPT_POST,   1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
	curl_setopt($ch, CURLOPT_USERPWD, WFIRMA_LOGIN.':'.WFIRMA_PASSWORD);
	$response = curl_exec($ch); 
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	curl_close($ch);

}
	
?>