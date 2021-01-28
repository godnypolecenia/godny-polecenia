<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages payments
 */

class Payment {
	
	public $paymentId;
	private $paymentArray = [];
	private $saveArray = [];
	private $paymentType;
	
	/**
	 *	This function specifies the payment ID
	 *
	 *	@param   int   $paymentId   Payment ID
	 *	@return  void
	 */
	public function __construct($paymentId = null) {
		
		$this -> paymentType = PAYMENT_TYPE;
		
		if($paymentId <> null) {
			$this -> getPaymentById($paymentId);
		}
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {
		global $db;
		
		if($this -> paymentId > 0 && count($this -> saveArray) > 0) {
			$db -> query(
				'UPDATE `db_payment` '.
				'SET '.implode(', ', array_map(function($v, $k) { return('`'.$k.'` = "'.$v.'"'); }, $this -> saveArray, array_keys($this -> saveArray))).' '.
				'WHERE `payment_id` = "'.$this -> paymentId.'"'
			);
		}
	}
	
	/**
	 *	This function retrieves the payment by ID
	 *
	 *	@param   int   $paymentId   Payment ID
	 *	@return  boolean
	 */
	public function getPaymentById($paymentId) {
		global $db;
		
		if(!($paymentId > 0)) {
			return(false);
		}

		$db -> query(
			'SELECT * '.
			'FROM `db_payment` '.
			'WHERE `payment_id` = "'.$paymentId.'"'
		);
		if($db -> numRows() <> 1) {
			return(false);
		}
	
		$this -> paymentArray = $db -> fetchArray();
		$this -> paymentId = $paymentId;
		return(true);
	}
	
	/**
	 *	This function retrieves a value from the array
	 *
	 *	@param   string   $field   Value
	 *	@return  string
	 */
	public function __get($field) {
		
		if(!($this -> paymentId > 0)) {
			return(false);
		}
		
		if(!isset($this -> paymentArray[$field])) {
			return(false);
		}
		
		return($this -> paymentArray[$field]);
	}
	
	/**
	 *	This function saves the payment in the database
	 *
	 *	@param   string   $field   Field
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function __set($field, $value) {
		global $db;
		
		if(!($this -> paymentId > 0)) {
			return(false);
		}
		
		if($this -> paymentArray[$field] <> $value) {
			$this -> paymentArray[$field] = $value;
			$this -> saveArray[$field] = $value;
		}
		
		return(true);
	}
	
	/**
	 *	This function creates a new payment
	 *
	 *	@param   int      $userId     User ID
	 *	@param   float    $amount     Amount
	 *	@param   int      $type       Type of array $paymentType
	 *	@param   int      $id         e.g. $itemId
	 *	@param   string   $firstname  Client firstname
	 *	@param   string   $lastname   Client lastname
	 *	@param   string   $city       Client city
	 *	@param   string   $postcode   Client postcode
	 *	@param   string   $address    Client address
	 *	@param   string   $email      Client email
	 *	@return  boolean
	 */
	public function add($userId, $amount, $type, $id, $firstname, $lastname, $city, $postcode, $address, $email, $nip = null) {
		global $db, $user;
		
		$db -> query(
			'INSERT INTO `db_payment` (`user_id`, `date`, `amount`, `type`, `id`, `firstname`, `lastname`, `city`, `postcode`, `address`, `email`, `ip`, `nip`) '.
			'VALUES("'.$userId.'", UNIX_TIMESTAMP(), "'.$amount.'", "'.$type.'", "'.$id.'", "'.$firstname.'", "'.$lastname.'", "'.$city.'", "'.$postcode.'", "'.$address.'", "'.$email.'", "'.$user -> ip.'", "'.$nip.'")'
		);
		$this -> paymentId = $db -> insertId();
		
		return($this -> paymentId);
	}
	
	/**
	 *	This function removes the payment from the database
	 *
	 *	@param   string   $paymentId   Payment ID
	 *	@return  boolean
	 */
	public function delete($paymentId = null) {
		global $db;
		
		if($paymentId == null) {
			if(!($this -> paymentId > 0)) {
				return(false);
			}
			
			$paymentId = $this -> paymentId;
		}
		
		$db -> query(
			'DELETE '.
			'FROM `db_payment` '.
			'WHERE `payment_id` = "'.$paymentId.'"'
		);
		
		return(true);
	}
	
	/**
	 *	This function counts payments
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countPaymentList($sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_payment` '.
			'WHERE 1 '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get payments
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@return  int
	 */
	public function getPaymentList($sqlSearch = null, $sqlOrder = '`date` DESC') {
		global $db, $url;
		
		$array = [];
		
		$db -> query(
			'SELECT `p`.*, `u`.`email` AS `user`, `i`.`title` AS `item` '.
			'FROM `db_payment` AS `p` '.
			'LEFT JOIN `db_user` AS `u` ON(`u`.`user_id` = `p`.`user_id`) '.
			'LEFT JOIN `db_item` AS `i` ON(`i`.`item_id` = `p`.`id`) '.
			'WHERE 1 '.$sqlSearch.' '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['payment_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *	This function counts payments of user
	 *
	 *	@param   int      $userId      User Id
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countPaymentListOfUser($userId, $sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_payment` '.
			'WHERE `user_id` = "'.$userId.'" '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get payments of user
	 *
	 *	@param   int      $userId      User Id
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@return  int
	 */
	public function getPaymentListOfUser($userId, $sqlSearch = null, $sqlOrder = '`date` DESC') {
		global $db, $url;
		
		$array = [];
		
		$db -> query(
			'SELECT * '.
			'FROM `db_payment` '.
			'WHERE `user_id` = "'.$userId.'" '.$sqlSearch.' '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['payment_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *	This function displays the payment form
	 *
	 *	@return  string 
	 */
	public function payForm() {
		
		if(!($this -> paymentId > 0)) {
			return(false);
		}

		if($this -> paymentType == 'dotpay') {
			$this -> payFormDotpay();
		}
		
		if($this -> paymentType == 'przelewy24') {
			$this -> payFormPrzelewy24();
		}
	}
	
	/**
	 *	This function displays the payment form Dotpay
	 *
	 *	@return  string 
	 */
	private function payFormDotpay() {
		global $setup, $url, $paymentType;
		
		if($setup -> developer == 1) {
			$dotpayId = '707244';
			$dotpayPin = '77LHkzwnti6YTAj5hjqkRwFEpBiAV3bn';
			$dotpayMode = 'test';
		} else {
			$dotpayId = DOTPAY_ID;
			$dotpayPin = DOTPAY_PIN;
			$dotpayMode = 'production';
		}
		
		$parametersArray = [			
			'amount' => $this -> amount,
			'firstname' => $this -> firstname,
			'lastname' => $this -> lastname,
			'street' => $this -> address,
			'city' => $this -> city,
			'postcode' => $this -> postcode,
			'id' => $dotpayId,
			'description' => $paymentType[$this -> type],
			'currency' => strtolower(PAYMENT_CURRENCY),
			'control' => $this -> paymentId,
			'type' => 0,
			'lang' => 'pl',
			'url' => $url -> getUrl('user/payment-done'),
			'urlc' => $url -> getUrl('tool/payment-exec', false, '/'.$this -> paymentId),
			'email' => $this -> email,
			'buttontext' => 'Powrót'
		];
				
		echo($this -> GenerateChkDotpayRedirection($dotpayId, $dotpayPin, $dotpayMode, 'POST', $parametersArray, [], null));

	}
	
	/**
	 *	This function is part of the Dotpay code
	 */
	private function GenerateChkDotpayRedirection($DotpayId, $DotpayPin, $Environment, $RedirectionMethod, $ParametersArray, $MultiMerchantList, $customer_base64) {
		
		$ParametersArray['id'] = $DotpayId;
		$ChkParametersChain = $this -> GenerateChk($DotpayId, $DotpayPin, $Environment, $RedirectionMethod, $ParametersArray, $MultiMerchantList, $customer_base64);

		$ChkValue = hash('sha256', $ChkParametersChain);

		if ($Environment == 'production') {
			$EnvironmentAddress = 'https://ssl.dotpay.pl/t2/';
		} elseif ($Environment == 'test') {
			$EnvironmentAddress = 'https://ssl.dotpay.pl/test_payment/';
		}

		if ($RedirectionMethod == 'POST') {
			$RedirectionCode = '<form action="'.$EnvironmentAddress.'" method="POST" id="dotpay_redirection_form" accept-charset="UTF-8">'.PHP_EOL;

			foreach ($ParametersArray as $key => $value) {
				$RedirectionCode .= "\t".'<input name="'.$key.'" value="'.$value.'" type="hidden"/>'.PHP_EOL;
			}

			if(isset($customer_base64)) {
				$RedirectionCode .= "\t".'<input name="customer" value="'.$customer_base64.'" type="hidden"/>'.PHP_EOL;
			}

			foreach ($MultiMerchantList as $item) {
				foreach ($item as $key => $value) {
					$RedirectionCode .= "\t".'<input name="'.$key.'" value="'.$value.'" type="hidden"/>'.PHP_EOL;
				}
			}

			$RedirectionCode .= "\t".'<input name="chk" value="'.$ChkValue.'" type="hidden"/>'.PHP_EOL;
			$RedirectionCode .= '</form>'.PHP_EOL.'<button id="dotpay_redirection_button" type="submit" form="dotpay_redirection_form" value="Submit">Przejdź do płatności</button>'.PHP_EOL;

			return '<div id="pay-form">'.$RedirectionCode.'</div>';

		} elseif ($RedirectionMethod == 'GET') {
			$RedirectionCode = $EnvironmentAddress.'?';

			foreach ($ParametersArray as $key => $value) {
				$RedirectionCode .= $key.'='.rawurlencode($value).'&';
			}

			if(isset($customer_base64)) {
				$RedirectionCode .= 'customer='.$customer_base64.'&';
			}

			foreach ($MultiMerchantList as $item) {
				foreach ($item as $key => $value) {
					$RedirectionCode .= $key.'='.rawurlencode($value).'&';
				}
			}

			$RedirectionCode .= 'chk='.$ChkValue;

			return '<a href="'.$RedirectionCode.'">Go to Pay</a><br>link:<br>'.$RedirectionCode;
		}
	}
	
	/**
	 *	This function is part of the Dotpay code
	 */
	private function GenerateChk($DotpayId, $DotpayPin, $Environment, $RedirectionMethod, $ParametersArray, $MultiMerchantList, $customer_base64) {
		
		$ParametersArray['id'] = $DotpayId;
		$ParametersArray['customer'] = $customer_base64;

		$chk =   $DotpayPin.
		(isset($ParametersArray['api_version']) ? $ParametersArray['api_version'] : null).
		(isset($ParametersArray['lang']) ? $ParametersArray['lang'] : null).
		(isset($ParametersArray['id']) ? $ParametersArray['id'] : null).
		(isset($ParametersArray['pid']) ? $ParametersArray['pid'] : null).
		(isset($ParametersArray['amount']) ? $ParametersArray['amount'] : null).
		(isset($ParametersArray['currency']) ? $ParametersArray['currency'] : null).
		(isset($ParametersArray['description']) ? $ParametersArray['description'] : null).
		(isset($ParametersArray['control']) ? $ParametersArray['control'] : null).
		(isset($ParametersArray['channel']) ? $ParametersArray['channel'] : null).
		(isset($ParametersArray['credit_card_brand']) ? $ParametersArray['credit_card_brand'] : null).
		(isset($ParametersArray['ch_lock']) ? $ParametersArray['ch_lock'] : null).
		(isset($ParametersArray['channel_groups']) ? $ParametersArray['channel_groups'] : null).
		(isset($ParametersArray['onlinetransfer']) ? $ParametersArray['onlinetransfer'] : null).
		(isset($ParametersArray['url']) ? $ParametersArray['url'] : null).
		(isset($ParametersArray['type']) ? $ParametersArray['type'] : null).
		(isset($ParametersArray['buttontext']) ? $ParametersArray['buttontext'] : null).
		(isset($ParametersArray['urlc']) ? $ParametersArray['urlc'] : null).
		(isset($ParametersArray['firstname']) ? $ParametersArray['firstname'] : null).
		(isset($ParametersArray['lastname']) ? $ParametersArray['lastname'] : null).
		(isset($ParametersArray['email']) ? $ParametersArray['email'] : null).
		(isset($ParametersArray['street']) ? $ParametersArray['street'] : null).
		(isset($ParametersArray['street_n1']) ? $ParametersArray['street_n1'] : null).
		(isset($ParametersArray['street_n2']) ? $ParametersArray['street_n2'] : null).
		(isset($ParametersArray['state']) ? $ParametersArray['state'] : null).
		(isset($ParametersArray['addr3']) ? $ParametersArray['addr3'] : null).
		(isset($ParametersArray['city']) ? $ParametersArray['city'] : null).
		(isset($ParametersArray['postcode']) ? $ParametersArray['postcode'] : null).
		(isset($ParametersArray['phone']) ? $ParametersArray['phone'] : null).
		(isset($ParametersArray['country']) ? $ParametersArray['country'] : null).
		(isset($ParametersArray['code']) ? $ParametersArray['code'] : null).
		(isset($ParametersArray['p_info']) ? $ParametersArray['p_info'] : null).
		(isset($ParametersArray['p_email']) ? $ParametersArray['p_email'] : null).
		(isset($ParametersArray['n_email']) ? $ParametersArray['n_email'] : null).
		(isset($ParametersArray['expiration_date']) ? $ParametersArray['expiration_date'] : null).
		(isset($ParametersArray['deladdr']) ? $ParametersArray['deladdr'] : null).
		(isset($ParametersArray['recipient_account_number']) ? $ParametersArray['recipient_account_number'] : null).
		(isset($ParametersArray['recipient_company']) ? $ParametersArray['recipient_company'] : null).
		(isset($ParametersArray['recipient_first_name']) ? $ParametersArray['recipient_first_name'] : null).
		(isset($ParametersArray['recipient_last_name']) ? $ParametersArray['recipient_last_name'] : null).
		(isset($ParametersArray['recipient_address_street']) ? $ParametersArray['recipient_address_street'] : null).
		(isset($ParametersArray['recipient_address_building']) ? $ParametersArray['recipient_address_building'] : null).
		(isset($ParametersArray['recipient_address_apartment']) ? $ParametersArray['recipient_address_apartment'] : null).
		(isset($ParametersArray['recipient_address_postcode']) ? $ParametersArray['recipient_address_postcode'] : null).
		(isset($ParametersArray['recipient_address_city']) ? $ParametersArray['recipient_address_city'] : null).
		(isset($ParametersArray['application']) ? $ParametersArray['application'] : null).
		(isset($ParametersArray['application_version']) ? $ParametersArray['application_version'] : null).
		(isset($ParametersArray['warranty']) ? $ParametersArray['warranty'] : null).
		(isset($ParametersArray['bylaw']) ? $ParametersArray['bylaw'] : null).
		(isset($ParametersArray['personal_data']) ? $ParametersArray['personal_data'] : null).
		(isset($ParametersArray['credit_card_number']) ? $ParametersArray['credit_card_number'] : null).
		(isset($ParametersArray['credit_card_expiration_date_year']) ? $ParametersArray['credit_card_expiration_date_year'] : null).
		(isset($ParametersArray['credit_card_expiration_date_month']) ? $ParametersArray['credit_card_expiration_date_month'] : null).
		(isset($ParametersArray['credit_card_security_code']) ? $ParametersArray['credit_card_security_code'] : null).
		(isset($ParametersArray['credit_card_store']) ? $ParametersArray['credit_card_store'] : null).
		(isset($ParametersArray['credit_card_store_security_code']) ? $ParametersArray['credit_card_store_security_code'] : null).
		(isset($ParametersArray['credit_card_customer_id']) ? $ParametersArray['credit_card_customer_id'] : null).
		(isset($ParametersArray['credit_card_id']) ? $ParametersArray['credit_card_id'] : null).
		(isset($ParametersArray['blik_code']) ? $ParametersArray['blik_code'] : null).
		(isset($ParametersArray['credit_card_registration']) ? $ParametersArray['credit_card_registration'] : null).
		(isset($ParametersArray['surcharge_amount']) ? $ParametersArray['surcharge_amount'] : null).
		(isset($ParametersArray['surcharge']) ? $ParametersArray['surcharge'] : null).
		(isset($ParametersArray['ignore_last_payment_channel']) ? $ParametersArray['ignore_last_payment_channel'] : null).
		(isset($ParametersArray['vco_call_id']) ? $ParametersArray['vco_call_id'] : null).
		(isset($ParametersArray['vco_update_order_info']) ? $ParametersArray['vco_update_order_info'] : null).
		(isset($ParametersArray['vco_subtotal']) ? $ParametersArray['vco_subtotal'] : null).
		(isset($ParametersArray['vco_shipping_handling']) ? $ParametersArray['vco_shipping_handling'] : null).
		(isset($ParametersArray['vco_tax']) ? $ParametersArray['vco_tax'] : null).
		(isset($ParametersArray['vco_discount']) ? $ParametersArray['vco_discount'] : null).
		(isset($ParametersArray['vco_gift_wrap']) ? $ParametersArray['vco_gift_wrap'] : null).
		(isset($ParametersArray['vco_misc']) ? $ParametersArray['vco_misc'] : null).
		(isset($ParametersArray['vco_promo_code']) ? $ParametersArray['vco_promo_code'] : null).
		(isset($ParametersArray['credit_card_security_code_required']) ? $ParametersArray['credit_card_security_code_required'] : null).
		(isset($ParametersArray['credit_card_operation_type']) ? $ParametersArray['credit_card_operation_type'] : null).
		(isset($ParametersArray['credit_card_avs']) ? $ParametersArray['credit_card_avs'] : null).
		(isset($ParametersArray['credit_card_threeds']) ? $ParametersArray['credit_card_threeds'] : null).
		(isset($ParametersArray['customer']) ? $ParametersArray['customer'] : null).
		(isset($ParametersArray['gp_token']) ? $ParametersArray['gp_token'] : null);

		foreach ($MultiMerchantList as $item) {
			foreach ($item as $key => $value) {
				$chk =   $chk.
				(isset($value) ? $value : null);
			}
		}
		return $chk;
	}
	
	/**
	 *	This function displays the payment form Przelewy24
	 *
	 *	@return  string 
	 */
	private function payFormPrzelewy24() {
		global $setup, $url;
		
		if($setup -> developer == 1) {
			$p24Id = '117543';
			$p24Key = '10be9857';
			$p24Crc = '92a7f1c3a7db055f';
			$p24Mode = 1;
		} else {
			$p24Id = P24_ID;
			$p24Key = P24_KEY;
			$p24Crc = P24_CRC;
			$p24Mode = 0;
		}
		
		$p24_session_id = password(time().rand(0, 999));
		$p24_sign = (md5($p24_session_id.'|'.$p24Id.'|'.($this -> amount*100).'|'.strtoupper(PAYMENT_CURRENCY).'|'.$p24Crc));
		
		require_once('./include/przelewy24/class_przelewy24.php');

		$p24 = new Przelewy24($p24Id, $p24Id, $p24Crc, $p24Mode);
		$p24 -> addValue('p24_session_id', $p24_session_id);                            
		$p24 -> addValue('p24_merchant_id', $p24Id);                            
		$p24 -> addValue('p24_pos_id', $p24Id);                            
		$p24 -> addValue('p24_currency', strtoupper(PAYMENT_CURRENCY));                            
		$p24 -> addValue('p24_description', $paymentType[$this -> type]);                            
		$p24 -> addValue('p24_language', $lang);                            
		$p24 -> addValue('p24_url_return', $url -> getUrl('user/payment-done'));                            
		$p24 -> addValue('p24_url_status', $url -> getUrl('tool/payment-exec', false, '/'.$this -> paymentId));                            
		$p24 -> addValue('p24_api_version', '3.2');                            
		$p24 -> addValue('p24_country', 'PL');                            
		$p24 -> addValue('p24_email', $this -> email);                            
		$p24 -> addValue('p24_client', $this -> firstname.' '.$this -> lastname);                            
		$p24 -> addValue('p24_address', $this -> address);                            
		$p24 -> addValue('p24_zip', $this -> postcode);                            
		$p24 -> addValue('p24_city', $this -> city);                            
		$p24 -> addValue('p24_amount', ($this -> amount*100));                            
		
		$res = $p24 -> trnRegister(false);
		if($res['error'] == '0') {
			$this -> data = 'p24_crc='.$p24Crc.'&p24_amount='.($this -> amount*100).'&p24_currency='.strtoupper(PAYMENT_CURRENCY).'&env='.$setup -> developer;
			echo('<div id="pay-form">');
			echo('<form method="post" action="'.$p24 -> getHost().'trnRequest/'.$res['token'].'">');
			echo('<div class="buttons">');
			echo('<input type="submit" value="Przejdź do płatności">');
			echo('</div>');
			echo('</form>');
			echo('</div>');
		} else {
			echo '<pre>RESPONSE: '.print_r($res, true).'</pre>';
		}
	}
	
	/**
	 *	This function records errors in the log
	 *
	 *	@param   string   $text   Error content
	 *	@return  string
	 */
	public function payErrorLog($text) {
		global $user;
		
		if(!($this -> paymentId > 0)) {
			return(false);
		}
		
		$this -> log = $this -> log.date('Y-m-d H:i:s').' - '.$text."\n";
		
		return($text);
	}
	
	/**
	 *	This function checks if the answer comes from an authorized source
	 *
	 *	@return  boolean
	 */
	public function authorization() {
		
		if(!($this -> paymentId > 0)) {
			return(false);
		}

		if($this -> paymentType == 'dotpay') {
			if(!$this -> authorizationDotpay()) {
				return(false);
			}
		}
		
		if($this -> paymentType == 'przelewy24') {
			if(!$this -> authorizationPrzelewy24()) {
				return(false);
			}
		}
		
		return(true);
	}
	
	/**
	 *	This function checks if the answer comes from an authorized source (dotpay)
	 *
	 *	@return  boolean
	 */
	public function authorizationDotpay() {
		global $user;
		
		if(!in_array($user -> ip, ['217.17.41.5', '195.150.9.37'])) {
			$this -> payErrorLog('Błędny adres IP ['.$user -> ip.']');
			return(false);
		}
		
		return(true);
	}
	
	/**
	 *	This function checks if the answer comes from an authorized source (przelewy24)
	 *
	 *	@return  boolean
	 */
	public function authorizationPrzelewy24() {

		return(true);
	}
	
	/**
	 *	This function checks if the response data is correct
	 *
	 *	@return  boolean
	 */
	public function checkData() {
		
		if(!($this -> paymentId > 0)) {
			return(false);
		}

		if($this -> paymentType == 'dotpay') {
			if(!$this -> checkDataDotpay()) {
				return(false);
			}
		}
		
		if($this -> paymentType == 'przelewy24') {
			if(!$this -> checkDataPrzelewy24()) {
				return(false);
			}
		}
		
		return(true);
	}
	
	/**
	 *	This function checks if the response data is correct (dotpay)
	 *
	 *	@return  boolean
	 */
	public function checkDataDotpay() {
		global $_POST;
		
		if($_POST['operation_status'] <> 'completed') {
			$this -> payErrorLog('Błędny status ['.$_POST['operation_status'].']');
			return(false);
		}
		
		if($_POST['operation_original_amount'] <> $this -> amount) {
			$this -> payErrorLog('Błędna kwota ['.$_POST['operation_original_amount'].']');
			return(false);
		}
		
		if($_POST['control'] <> $this -> paymentId) {
			$this -> payErrorLog('Błędna wartość control ['.$_POST['control'].']');
			return(false);
		}
		
		return(true);
	}
	
	/**
	 *	This function checks if the response data is correct (przelewy24)
	 *
	 *	@return  boolean
	 */
	public function checkDataPrzelewy24() {
		global $_POST;
		
		require_once('./include/przelewy24/class_przelewy24.php');
		
		$ex = explode('&', $this -> data);
		foreach($ex as $val) {
			$y = explode('=', $val);
			$fil[trim($y[0])] = urldecode(trim($y[1]));
		}
		$p24 = new Przelewy24($_POST['p24_merchant_id'], $_POST['p24_pos_id'], $fil['p24_crc'], $fil['env']);
		foreach($_POST as $k => $v) {
			$p24 -> addValue($k, $v);  
		}
		$p24 -> addValue('p24_currency', $fil['p24_currency']);
		$p24 -> addValue('p24_amount', $fil['p24_amount']);
		$res = $p24 -> trnVerify();

		if($res['error'] > 0) {
			return(false);
		}
		
		return(true);
	}
	
	/**
	 *	This function closes the payment
	 *
	 *	@return  string or boolean
	 */
	public function done() {
		global $db;
		
		if(!($this -> paymentId > 0)) {
			return(false);
		}
		
		$this -> status = 1;
		$this -> date_pay = time();
		
		$this -> log = $this -> log.date('Y-m-d H:i:s').' - sukces'."\n";

		if($this -> paymentType == 'dotpay') {
			$this -> doneDotpay();
		}
		
		if($this -> paymentType == 'przelewy24') {
			$this -> donePrzelewy24();
		}
	}
	
	/**
	 *	This function closes the payment (dotpay)
	 *
	 *	@return  string
	 */
	public function doneDotpay() {

		echo('OK');
	}
	
	/**
	 *	This function closes the payment (przelewy24)
	 *
	 *	@return  boolean
	 */
	public function donePrzelewy24() {

		return(true);
	}
}

?>