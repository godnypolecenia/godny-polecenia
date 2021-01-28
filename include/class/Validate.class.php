<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class validates the submitted data
 */

class Validate {

	private $counter = 0;
	private $fail = 0;
	private $failStatus = [];

	/**
	 *	This function checks if the variable has any value
	 *
	 *	@param   string   $value   Checked variable
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isValue($value, $label = null) {
		
		if(!isset($value) || empty($value)) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_IS_VALUE;
			return(false);
		}
		
		$this -> counter++;
		return(true);
	}
	
	/**
	 *	This function checks if the variable is string
	 *	It can also check if the variable has a number of characters $min/$max
	 *
	 *	@param   string   $value   Checked variable
	 *	@param   int      $min     The minimum number of characters
	 *	@param   int      $max     Maximum number of characters
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isString($value, $min = null, $max = null, $label = null) {
		
		if(!is_string($value)) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_IS_STRING;
			return(false);
		}
		if($min <> null && strlen($value) < $min) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_IS_STRING_MIN;
			return(false);
		}
		if($max <> null && strlen($value) > $max) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_IS_STRING_MAX;
			return(false);
		}
		
		$this -> counter++;
		return(true);
	}
	
	/**
	 *	This function checks if the variable is e-mail address
	 *
	 *	@param   string   $value   Checked variable
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isEmail($value, $label = null) {
		
		if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_EMAIL;
			return(false);
		}

		$this -> counter++;
		return(true);
	}
	
	/**
	 *	This function checks if the variable is URL address
	 *
	 *	@param   string   $value   Checked variable
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isUrl($value, $label = null) {
		
		if(!filter_var($value, FILTER_VALIDATE_URL)) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_URL;
			return(false);
		}
		
		$this -> counter++;
		return(true);
	}

	/**
	 *	This function checks if the variable is integer
	 *
	 *	@param   int      $value   Checked variable
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isInt($value, $label = null) {

		/*if(!filter_input(INPUT_POST, $value, FILTER_VALIDATE_INT)) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_INT;
			return(false);
		}*/

		$this -> counter++;
		return(true);
	}
	
	/**
	 *	This function checks if the variable is float
	 *
	 *	@param   float    $value   Checked variable
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isFloat($value, $label = null) {

		/*if(!filter_input(INPUT_POST, $value, FILTER_VALIDATE_FLOAT)) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_FLOAT;
			return(false);
		}*/

		$this -> counter++;
		return(true);
	}
	
	/**
	 *	This function checks if the variable is in array
	 *
	 *	@param   string   $value   Checked variable
	 *	@param   array    $array   An array of possibilities
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isPossible($value, $array, $label = null) {
		
		if(!in_array($value, (array)$array)) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_IS_POSSIBLE;
			return(false);
		}
		
		$this -> counter++;
		return(true);
	}
	
	/**
	 *	This function checks if the captcha is correct
	 *
	 *	@param   string   $value   Checked variable
	 *	@param   string   $label   Field name returned in error communications
	 *	@return  boolean
	 */
	public function isCaptcha($value, $label = null) {
		global $_SERVER, $setup;
		
		if($setup -> developer == 1) {
			return(true);
		}
		
		$verf = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.RECAPTCHA_SECRET_KEY.'&response='.$value.'&remoteip='.$_SERVER['REMOTE_ADDR']);
		$resp = json_decode($verf);
		if(!$resp -> success) {
			$this -> fail++;
			$this -> failStatus[$label] = VALIDATE_IS_CAPTCHA;
			return(false);
		}
		
		$this -> counter++;
		return(true);
	}
	
	/**
	 *	This function allows you to add an error from external validation
	 *
	 *	@param   string   $content   Error content
	 *	@param   string   $label     Field name returned in error communications
	 *	@return  void
	 */
	public function putError($content, $label = null) {
		
		$this -> fail++;
		$this -> failStatus[$label] = $content;
	}
	
	/**
	 *	This function returns the result true/false
	 *
	 *	@return   boolean
	 */
	public function result() {
		
		if($this -> fail == 0) {
			return(true);
		}
		
		return(false);
	}
	
	/**
	 *	This function returns the result and values
	 *
	 *	@return   boolean
	 */
	public function pass() {
		
		if($this -> fail > 0) {
			return(false);
		}
		
		return(true);
	}
	
	/**
	 *	This function returns an array with error statuses
	 *
	 *	@return   array
	 */
	public function resultArray() {
		
		return($this -> failStatus);
	}
}

?>