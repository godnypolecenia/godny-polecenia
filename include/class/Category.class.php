<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages content categories
 */

class Category {
	
	public $categoryId;
	private $categoryArray = [];
	private $saveArray = [];
	private $allCategoriesArray;
	
	/**
	 *	This function specifies the category ID
	 *
	 *	@param   int   $categoryId   Category ID
	 *	@return  void
	 */
	public function __construct($categoryId = null) {
		
		if($categoryId <> null) {
			$this -> getCategoryById($categoryId);
		}
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {
		global $db;
		
		if($this -> categoryId > 0 && count($this -> saveArray) > 0) {
			$db -> query(
				'UPDATE `db_category` '.
				'SET '.implode(', ', array_map(function($v, $k) { return('`'.$k.'` = "'.$v.'"'); }, $this -> saveArray, array_keys($this -> saveArray))).' '.
				'WHERE `category_id` = "'.$this -> categoryId.'"'
			);
		}
	}
	
	/**
	 *	This function retrieves the category by ID
	 *
	 *	@param   int   $categoryId   Category ID
	 *	@return  boolean
	 */
	public function getCategoryById($categoryId) {
		global $db;
		
		if(!($categoryId > 0)) {
			return(false);
		}
		
		$db -> query(
			'SELECT * '.
			'FROM `db_category` '.
			'WHERE `category_id` = "'.$categoryId.'"'
		);
		if($db -> numRows() <> 1) {
			return(false);
		}
	
		$this -> categoryArray = $db -> fetchArray();
		$this -> categoryId = $categoryId;
		
		if($this -> parent_id > 0) {
			$db -> query(
				'SELECT `name` '.
				'FROM `db_category` '.
				'WHERE `category_id` = "'.$this -> parent_id.'"'
			);
			if($db -> numRows() == 1) {
				$r = $db -> fetchArray();
				$this -> categoryArray['parent'] = $r['name'];
			}
		}
		
		return(true);
	}
	
	/**
	 *	This function retrieves a value from the array
	 *
	 *	@param   string   $field   Value
	 *	@return  string
	 */
	public function __get($field) {
		
		if(!($this -> categoryId > 0)) {
			return(false);
		}
		
		if(!isset($this -> categoryArray[$field])) {
			return(false);
		}
		
		return($this -> categoryArray[$field]);
	}
	
	/**
	 *	This function saves the category in the database
	 *
	 *	@param   string   $field   Field
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function __set($field, $value) {
		global $db;
		
		if(!($this -> categoryId > 0)) {
			return(false);
		}
		
		if($this -> categoryArray[$field] <> $value) {
			$this -> categoryArray[$field] = $value;
			$this -> saveArray[$field] = $value;
		}
		
		return(true);
	}
	
	/**
	 *	This function creates a new category
	 *
	 *	@param   int      $parentId  Parent Category ID
	 *	@param   string   $name      Name
	 *	@return  boolean
	 */
	public function add($parentId, $name) {
		global $db, $cityName;

		$y = ($this -> getHighestY($parentId)+1);

		$db -> query(
			'INSERT INTO `db_category` (`parent_id`, `name`, `y`) '.
			'VALUES("'.$parentId.'", "'.$name.'", "'.$y.'")'
		);

		$this -> categoryId = $db -> insertId();
		
		$parent = '';
		if($parentId > 0) {
			$parentCat = new Category;
			$parentCat -> getCategoryById($parentId);
			if($parentCat -> categoryArray['name'] <> '') {
				$parent = toUrl($parentCat -> categoryArray['name']).'/';
			}
		}
		
		$addUrl = new Url;
		$addUrl -> add($name, $parent.toUrl($name), 'item/index', 'parent_id='.$parentId.'&category_id='.$this -> categoryId);
		$addUrl -> index = 1;
		
		if(is_array($cityName)) {
			foreach($cityName as $v) {
				$addUrl -> add($name.' - '.$v, $parent.toUrl($name).'/'.toUrl($v), 'item/index', 'parent_id='.$parentId.'&category_id='.$this -> categoryId.'&city='.rawurlencode($v));
				$addUrl -> index = 1;
			}
		}
		
		return($this -> categoryId);
	}
	
	/**
	 *	This function removes the category from the database
	 *
	 *	@param   string   $categoryId   Category ID
	 *	@return  boolean
	 */
	public function delete($categoryId = null) {
		global $db, $cityName;
		
		if($categoryId == null) {
			if(!($this -> categoryId > 0)) {
				return(false);
			}
			
			$this -> getCategoryById($this -> categoryId);
		}
		
		$delChildren = new Category;
		foreach($delChildren -> getCategoryList($this -> categoryId) as $r) {
			$delChildren -> getCategoryById($r['category_id']);
			$delChildren -> delete();
		}
		
		$db -> query(
			'DELETE '.
			'FROM `db_category` '.
			'WHERE `category_id` = "'.$this -> categoryId.'"'
		);
		
		$db -> query(
			'DELETE '.
			'FROM `db_category_feature_rel` '.
			'WHERE `category_id` = "'.$this -> categoryId.'"'
		);
		
		$db -> query(
			'UPDATE `db_category` '.
			'SET `y` = `y`-1 '.
			'WHERE `y` > "'.$this -> y.'" && `parent_id` = "'.$this -> categoryArray['parent_id'].'"'
		);
		
		$delUrl = new Url;
		$delUrl -> getUrlByFile('item/index', 'parent_id='.$this -> categoryArray['parent_id'].'&category_id='.$this -> categoryId);
		$delUrl -> delete();

		if(is_array($cityName)) {
			foreach($cityName as $v) {
				$delUrl -> getUrlByFile('item/index', 'parent_id='.$this -> categoryArray['parent_id'].'&category_id='.$this -> categoryId.'&city='.rawurlencode($v));
				$delUrl -> delete();
			}
		}
		
		return(true);
	}
	
	/**
	 *
	 */
	public function isCategory($url) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_category` '.
			'WHERE `url` = "'.$name.'"'
		);
		$r = $db -> fetchArray();
		if($r['count'] == 0) {
			return(false);
		}
		
		return(true);
	}
	
	/**
	 *	This function counts categories
	 *
	 *	@return  int
	 */
	public function countCategoryList() {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_category`'
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function counts categories by parent ID
	 *
	 *	@param   int   $parentId   Parent Category ID
	 *	@return  int
	 */
	public function countParentCategoryList($parentId = 0) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_category` '.
			'WHERE `parent_id` = "'.$parentId.'"'
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get all categories
	 *
	 *	@return  array
	 */
	private function getAllCategoryList() {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_category` '.
			'ORDER BY `y` ASC'
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$this -> allCategoriesArray[$r['parent_id']][$r['category_id']] = $r;
				$this -> categoryArray[$r['category_id']] = $r;
			}
		}
		
		return($this -> allCategoriesArray);
	}
	
	/**
	 *	This function get categories
	 *
	 *	@param   int   $parentId   Parent Category ID
	 *	@return  array
	 */
	public function getCategoryList($parentId = 0) {
		
		if(!is_array($this -> allCategoriesArray)) {
			$this -> getAllCategoryList();
		}
		
		if(!is_array($this -> allCategoriesArray[$parentId])) {
			return([]);
		}
		
		return($this -> allCategoriesArray[$parentId]);
	}
	
	/**
	 *	This function gethighset Y categories
	 *
	 *	@param   int   $parentId   Parent Category ID
	 *	@return  array
	 */
	public function getHighestY($parentId = 0) {
		
		if(!is_array($this -> allCategoriesArray)) {
			$this -> getAllCategoryList();
		}
		
		if(!is_array($this -> allCategoriesArray[$parentId])) {
			return(0);
		}
		
		$maxY = 0;
		foreach($this -> allCategoriesArray[$parentId] as $v) {
			if($v['y'] > $maxY) $maxY = $v['y'];
		}
		
		return($maxY);
	}
	
	/**
	 *	This function moves the category up
	 *
	 *	@param   int   $categoryId   Category ID
	 *	@return  boolean
	 */
	public function yPositionUp($categoryId) {
		global $db;
		
		if(!is_array($this -> allCategoriesArray)) {
			$this -> getAllCategoryList();
		}
		
		if(!is_array($this -> categoryArray[$categoryId])) {
			return(false);
		}
		
		if($this -> categoryArray[$categoryId]['y'] <= 1) {
			return(false);
		}
		
		$db -> query(
			'UPDATE `db_category` '.
			'SET `y` = `y`+1 '.
			'WHERE `parent_id` = "'.$this -> categoryArray[$categoryId]['parent_id'].'" && `y` = "'.($this -> categoryArray[$categoryId]['y']-1).'"'
		);
		$db -> query(
			'UPDATE `db_category` '.
			'SET `y` = `y`-1 '.
			'WHERE `category_id` = "'.$categoryId.'"'
		);

		return(true);
	}
	
	/**
	 *	This function moves the category down
	 *
	 *	@param   int   $categoryId   Category ID
	 *	@return  boolean
	 */
	public function yPositionDown($categoryId) {
		global $db;
		
		if(!is_array($this -> allCategoriesArray)) {
			$this -> getAllCategoryList();
		}
		
		if(!is_array($this -> categoryArray[$categoryId])) {
			return(false);
		}
		
		if($this -> categoryArray[$categoryId]['y'] >= $this -> getHighestY($this -> categoryArray[$categoryId]['parent_id'])) {
			return(false);
		}
		
		$db -> query(
			'UPDATE `db_category` '.
			'SET `y` = `y`-1 '.
			'WHERE `parent_id` = "'.$this -> categoryArray[$categoryId]['parent_id'].'" && `y` = "'.($this -> categoryArray[$categoryId]['y']+1).'"'
		);
		$db -> query(
			'UPDATE `db_category` '.
			'SET `y` = `y`+1 '.
			'WHERE `category_id` = "'.$categoryId.'"'
		);

		return(true);
	}
	
	/**
	 *	This function change parent ID
	 *
	 *	@param   int   $newParentId   Parent Category ID
	 *	@return  boolean
	 */
	public function changeParent($newParentId = 0) {
		global $db, $cityName;
		
		if(!is_array($this -> allCategoriesArray)) {
			$this -> getAllCategoryList();
		}
		
		if(!($this -> categoryId > 0)) {
			return(false);
		}
		
		if(!is_array($this -> categoryArray[$this -> categoryId])) {
			return(false);
		}
		
		$y = ($this -> getHighestY($newParentId)+1);
		
		$delUrl = new Url;
		$delUrl -> getUrlByFile('item/index', 'parent_id='.$this -> categoryArray['parent_id'].'&category_id='.$this -> categoryId);
		$delUrl -> delete();

		if(is_array($cityName)) {
			foreach($cityName as $v) {
				$delUrl -> getUrlByFile('item/index', 'parent_id='.$this -> categoryArray['parent_id'].'&category_id='.$this -> categoryId.'&city='.rawurlencode($v));
				$delUrl -> delete();
			}
		}
		
		$db -> query(
			'UPDATE `db_category` '.
			'SET `y` = `y`-1 '.
			'WHERE `parent_id` = "'.$this -> categoryArray[$this -> categoryId]['parent_id'].'" && `y` > "'.$this -> categoryArray[$this -> categoryId]['y'].'"'
		);
		
		$db -> query(
			'UPDATE `db_category` '.
			'SET `parent_id` = "'.$newParentId.'", `y` = "'.$y.'" '.
			'WHERE `category_id` = "'.$this -> categoryId.'"'
		);
		
		$parent = '';
		if($newParentId > 0) {
			$parentCat = new Category;
			$parentCat -> getCategoryById($newParentId);
			if($parentCat -> categoryArray['name'] <> '') {
				$parent = toUrl($parentCat -> categoryArray['name']).'/';
			}
		}
		
		$addUrl = new Url;
		$addUrl -> add($name, $parent.toUrl($this -> categoryArray['name']), 'item/index', 'parent_id='.$newParentId.'&category_id='.$this -> categoryId);
		$addUrl -> index = 1;
		
		if(is_array($cityName)) {
			foreach($cityName as $v) {
				$addUrl -> add($name.' - '.$v, $parent.toUrl($this -> categoryArray['name']).'/'.toUrl($v), 'item/index', 'parent_id='.$newParentId.'&category_id='.$this -> categoryId.'&city='.rawurlencode($v));
				$addUrl -> index = 1;
			}
		}
		
		return(true);
	}
}

?>