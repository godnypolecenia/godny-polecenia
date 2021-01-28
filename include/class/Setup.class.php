<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages the portal settings
 */

class Setup {

	private $setupArray = [];

	/**
	 *	This function takes the settings from the database
	 *
	 *	@return  void
	 */
	public function __construct() {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_setup`'
		);
		while($r = $db -> fetchArray()) {
			$this -> setupArray[$r['setup_id']] = $r['value'];
		}
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {

	}

	/**
	 *	This function retrieves a value from the array
	 *
	 *	@param   string   $setupId   Setting ID
	 *	@return  string
	 */
	public function __get($setupId) {
		
		$setupId = str_replace('_', '-', $setupId);
		
		if(isset($this -> setupArray[$setupId])) {
			return($this -> setupArray[$setupId]);
		}
		
		return(false);
	}

	/**
	 *	This function saves the settings in the database (updates or creates)
	 *
	 *	@param   string   $setupId   Setting ID
	 *	@param   string   $value      Value
	 *	@return  boolean
	 */
	public function __set($setupId, $value) {
		global $db;
		
		$setupId = str_replace('_', '-', $setupId);
		
		/**
		 *	It must be (without $saveArray)
		 */
		if(isset($this -> setupArray[$setupId])) {
			$db -> query(
				'UPDATE `db_setup` '.
				'SET `value` = "'.$value.'" '.
				'WHERE `setup_id` = "'.$setupId.'"'
			);
		} else {
			$db -> query(
				'INSERT INTO `db_setup` (`setup_id`, `value`) '.
				'VALUES("'.$setupId.'", "'.$value.'")'
			);
		}
		
		$this -> setupArray[$setupId] = $value;
		return(true);
	}

	/**
	 *	This function removes the setting from the database
	 *
	 *	@param   string   $setupId   Setting ID
	 *	@return  boolean
	 */
	public function remove($setupId) {
		global $db;
		
		$setupId = str_replace('_', '-', $setupId);
		if(isset($this -> setupArray[$setupId])) {
			$db -> query(
				'DELETE '.
				'FROM `db_setup` '.
				'WHERE `setup_id` = "'.$setupId.'"'
			);
			return(true);
		}
		return(false);
	}
}

?>