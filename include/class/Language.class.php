<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages the language settings
 */

class Language {

	public $selected;
	public $default;
	public $list = [];

	/**
	 *
	 */
	public function __construct($selected = null) {
		global $setup;

		$this -> default = $setup -> default_language;
		$this -> selected = $selected;
		
		$exLangs = explode(';', $setup -> languages);
		foreach($exLangs as $v) {
			$ex = explode('-', $v);
			$this -> list[$ex[0]] = $ex[1];
		}
	}
}

?>