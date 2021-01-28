<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages content features
 */

class Feature {
	
	public $featureId;
	private $featureArray = [];
	private $saveArray = [];
	
	/**
	 *	This function specifies the feature ID
	 *
	 *	@param   int   $featureId   Feature ID
	 *	@return  void
	 */
	public function __construct($featureId = null) {
		
		if($featureId <> null) {
			$this -> getFeatureById($this -> featureId);
		}
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {
		global $db;
		
		if($this -> featureId > 0 && count($this -> saveArray) > 0) {
			$db -> query(
				'UPDATE `db_feature` '.
				'SET '.implode(', ', array_map(function($v, $k) { return('`'.$k.'` = "'.$v.'"'); }, $this -> saveArray, array_keys($this -> saveArray))).' '.
				'WHERE `feature_id` = "'.$this -> featureId.'"'
			);
		}
	}
	
	/**
	 *	This function retrieves the feature by ID
	 *
	 *	@param   int   $featureId   Feature ID
	 *	@return  boolean
	 */
	public function getFeatureById($featureId) {
		global $db;
		
		if(!($featureId > 0)) {
			return(false);
		}
		
		$db -> query(
			'SELECT * '.
			'FROM `db_feature` '.
			'WHERE `feature_id` = "'.$featureId.'"'
		);
		if($db -> numRows() <> 1) {
			return(false);
		}
	
		$this -> featureArray = $db -> fetchArray();
		$this -> featureId = $featureId;
		
		return(true);
	}
	
	/**
	 *	This function retrieves a value from the array
	 *
	 *	@param   string   $field   Value
	 *	@return  string
	 */
	public function __get($field) {
		
		if(!($this -> featureId > 0)) {
			return(false);
		}
		
		if(!isset($this -> featureArray[$field])) {
			return(false);
		}
		
		return($this -> featureArray[$field]);
	}
	
	/**
	 *	This function saves the feature in the database
	 *
	 *	@param   string   $field   Field
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function __set($field, $value) {
		global $db;
		
		if(!($this -> featureId > 0)) {
			return(false);
		}
		
		if($this -> featureArray[$field] <> $value) {
			$this -> featureArray[$field] = $value;
			$this -> saveArray[$field] = $value;
		}
		
		return(true);
	}
	
	/**
	 *	This function creates a new feature
	 *
	 *	@param   int      $type    Type
	 *	@param   string   $label   Label name
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function add($type, $label) {
		global $db;

		$db -> query(
			'INSERT INTO `db_feature` (`type`, `label`, `name`) '.
			'VALUES("'.$type.'", "'.$label.'", "'.$label.'")'
		);

		$this -> featureId = $db -> insertId();
		
		return($this -> featureId);
	}
	
	/**
	 *	This function removes the feature from the database
	 *
	 *	@param   string   $featureId   Feature ID
	 *	@return  boolean
	 */
	public function delete($featureId = null) {
		global $db;
		
		if($featureId == null) {
			if(!($this -> featureId > 0)) {
				return(false);
			}
			
			$featureId = $this -> featureId;
		}
		
		$db -> query(
			'DELETE '.
			'FROM `db_feature` '.
			'WHERE `feature_id` = "'.$featureId.'"'
		);
		
		return(true);
	}
	
	/**
	 *
	 */
	public function clearFeatureItemRel($itemId) {
		global $db;
		
		$db -> query(
			'DELETE '.
			'FROM `db_item_feature_rel` '.
			'WHERE `item_id` = "'.$itemId.'"'
		);
		
		return(true);
	}
	
	/**
	 *	This function counts features
	 *
	 *	@return  int
	 */
	public function countFeatureList() {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_feature`'
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get all features
	 *
	 *	@return  array
	 */
	public function getFeatureList() {
		global $db;
		
		$array = [];
		
		$db -> query(
			'SELECT * '.
			'FROM `db_feature` '.
			'ORDER BY `name` ASC'
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['feature_id']] = $r;
				$this -> featureArray[$r['feature_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *	This function counts features of category
	 *
	 *	@return  int
	 */
	public function countFeatureListOfCategory($categoryId) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" '
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get all features of category
	 *
	 *	@return  array
	 */
	public function getFeatureListOfCategory($categoryId) {
		global $db;
		
		$array = [];
		
		$db -> query(
			'SELECT `f`.*, `r`.* '.
			'FROM `db_feature` AS `f` '.
			'LEFT JOIN `db_category_feature_rel` AS `r` USING(`feature_id`) '.
			'WHERE `r`.`category_id` = "'.$categoryId.'" '.
			'ORDER BY `r`.`y` ASC'
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['feature_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *
	 */
	public function addCategoryFeatureRel($categoryId, $featureId, $required = 0) {
		global $db;
		
		$y = ($this -> getCategoryFeatureRelHighestY($categoryId)+1);
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);
		$r = $db -> fetchArray();
		if($r['count'] > 0) {
			return(false);
		}
		
		$db -> query(
			'INSERT INTO `db_category_feature_rel` (`category_id`, `feature_id`, `y`, `required`) '.
			'VALUES("'.$categoryId.'", "'.$featureId.'", "'.$y.'", "'.$required.'")'
		);
		
		return(true);
	}
	
	/**
	 *
	 */
	public function deleteCategoryFeatureRel($categoryId, $featureId) {
		global $db;
		
		$db -> query(
			'SELECT `y` '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);
		$r = $db -> fetchArray();
		
		$db -> query(
			'DELETE '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);
		
		$db -> query(
			'UPDATE `db_category_feature_rel` '.
			'SET `y` = `y`-1 '.
			'WHERE `y` > "'.$r['y'].'" && `category_id` = "'.$categoryId.'"'
		);
		
		return(true);
	}
	
	/**
	 *
	 */
	public function getCategoryFeatureRelHighestY($categoryId) {
		global $db;
		
		$db -> query(
			'SELECT `y` '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" '.
			'ORDER BY `y` DESC '.
			'LIMIT 1'
		);
		$r = $db -> fetchArray();
		
		if(!($r['y'] > 0)) {
			return(0);
		}
		
		return($r['y']);
	}
	
	/**
	 *	This function moves the feature up
	 *
	 *	@param   int   $categoryId   Category ID
	 *	@param   int   $featureIf    Feature ID
	 *	@return  boolean
	 */
	public function yPositionUp($categoryId, $featureId) {
		global $db;
		
		$db -> query(
			'SELECT `y` '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);
		$r = $db -> fetchArray();
		
		if($r['y'] <= 1) {
			return(false);
		}
			
		$db -> query(
			'UPDATE `db_category_feature_rel` '.
			'SET `y` = `y`+1 '.
			'WHERE `category_id` = "'.$categoryId.'" && `y` = "'.($r['y']-1).'"'
		);
		
		$db -> query(
			'UPDATE `db_category_feature_rel` '.
			'SET `y` = `y`-1 '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);

		return(true);
	}
	
	/**
	 *	This function moves the feature down
	 *
	 *	@param   int   $categoryId   Category ID
	 *	@param   int   $featureIf    Feature ID
	 *	@return  boolean
	 */
	public function yPositionDown($categoryId, $featureId) {
		global $db;
		
		$db -> query(
			'SELECT `y` '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);
		$r = $db -> fetchArray();
		
		if($r['y'] >= $this -> getCategoryFeatureRelHighestY($categoryId)) {
			return(false);
		}
		
		$db -> query(
			'UPDATE `db_category_feature_rel` '.
			'SET `y` = `y`-1 '.
			'WHERE `category_id` = "'.$categoryId.'" && `y` = "'.($r['y']+1).'"'
		);
		
		$db -> query(
			'UPDATE `db_category_feature_rel` '.
			'SET `y` = `y`+1 '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);

		return(true);
	}
	
	/**
	 *
	 */
	public function changeCategoryFeatureRelRequired($categoryId, $featureId) {
		global $db;
		
		$db -> query(
			'SELECT `required` '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);
		$r = $db -> fetchArray();
		
		$db -> query(
			'UPDATE `db_category_feature_rel` '.
			'SET `required` = "'.(($r['required'] == 1) ? 0 : 1).'" '.
			'WHERE `category_id` = "'.$categoryId.'" && `feature_id` = "'.$featureId.'"'
		);
		
		return(true);
	}
	
	/**
	 *
	 */
	public function addItemFeatureRel($itemId, $featureId, $type, $value) {
		global $db;
		
		$fieldName = '';
		if($type == 1) $fieldName = 'value_text';
		if($type == 2) $fieldName = 'value_number';
		if($type == 3) $fieldName = 'value_select';
		if($type == 4) $fieldName = 'value_checkbox';
		
		if($fieldName == '') {
			return(false);
		}
		
		$db -> query(
			'INSERT INTO `db_item_feature_rel` (`item_id`, `feature_id`, `'.$fieldName.'`) '.
			'VALUES("'.$itemId.'", "'.$featureId.'", "'.$value.'")'
		);
		
		return(true);
	}
	
	/**
	 *
	 */
	public function countItemFeatureRel($itemId) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_item_feature_rel` '.
			'WHERE `item_id` = "'.$itemId.'" '
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *
	 */
	public function getItemFeatureRel($itemId, $format = true) {
		global $db;
		
		$array = [];

		$db -> query(
			'SELECT `i`.*, `f`.`type`, `f`.`name`, `f`.`icon`, `f`.`prefix`, `f`.`sufix` '.
			'FROM `db_item_feature_rel` AS `i` '.
			'LEFT JOIN `db_feature` AS `f` ON(`f`.`feature_id` = `i`.`feature_id`) '.
			'LEFT JOIN `db_category_feature_rel` AS `c` ON(`c`.`category_id` = `f`.`feature_id`) '.
			'WHERE `i`.`item_id` = "'.$itemId.'" '.
			'ORDER BY `c`.`y` ASC'
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				
				if($r['type'] == 1) $r['value'] = $r['value_text'];
				if($r['type'] == 2) $r['value'] = $r['value_number'];
				if($r['type'] == 3) $r['value'] = $r['value_select'];
				if($r['type'] == 4) {
					if($r['value_checkbox'] == 0) continue;
					$r['value'] = (($format == true) ? 'Tak' : 1);
				}
				
				if($r['value'] <> '' && $format == true) {
					if($r['prefix'] <> '') $r['value'] = $r['prefix'].' '.$r['value'];
					if($r['sufix'] <> '') $r['value'] = $r['value'].' '.$r['sufix'];
				}
				
				$array[$r['feature_id']] = $r;
			}
		}
		
		return($array);
	}
}

?>