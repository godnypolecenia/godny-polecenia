<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class supports items
 */

class Item {
	
	public $itemId;
	private $itemArray = [];
	private $saveArray = [];
	
	/**
	 *	This function specifies the item ID
	 *
	 *	@param   int   $itemId   Item ID
	 *	@return  void
	 */
	public function __construct($itemId = null) {
		
		if($itemId <> null) {
			if(!$this -> getItemById($itemId)) {
				return(false);
			}
		}
		
		return(true);
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {
		global $db;
		
		if($this -> itemId > 0 && count($this -> saveArray) > 0) {
			$db -> query(
				'UPDATE `db_item` '.
				'SET '.implode(', ', array_map(function($v, $k) { return('`'.$k.'` = "'.$v.'"'); }, $this -> saveArray, array_keys($this -> saveArray))).' '.
				'WHERE `item_id` = "'.$this -> itemId.'"'
			);
		}
	}
	
	/**
	 *	This function retrieves the item by ID
	 *
	 *	@param   int   $itemId   Item ID
	 *	@return  boolean
	 */
	public function getItemById($itemId) {
		global $db;
		
		if(!($itemId > 0)) {
			return(false);
		}
		
		$db -> query(
			'SELECT `i`.*, `c`.`name` AS `category` '.
			'FROM `db_item` AS `i` '.
			'LEFT JOIN `db_category` AS `c` USING(`category_id`) '.
			'WHERE `i`.`item_id` = "'.$itemId.'" '.
			'GROUP BY `i`.`item_id`'
		);
		if($db -> numRows() == 0) {
			return(false);
		}
		
		$this -> itemArray = $db -> fetchArray();
		$this -> itemId = $this -> itemArray['item_id'];
		
		return(true);
	}
	
	/**
	 *	This function retrieves a value from the array
	 *
	 *	@param   string   $field   Value
	 *	@return  string
	 */
	public function __get($field) {
		
		if(!($this -> itemId > 0)) {
			return(false);
		}
		
		if(!isset($this -> itemArray[$field])) {
			return(false);
		}
		
		return($this -> itemArray[$field]);
	}
	
	/**
	 *	This function saves the page in the database
	 *
	 *	@param   string   $field   Field
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function __set($field, $value) {
		global $db;
		
		if(!($this -> itemId > 0)) {
			return(false);
		}
		
		if($this -> itemArray[$field] <> $value) {
			$this -> itemArray[$field] = $value;
			$this -> saveArray[$field] = $value;
		}
		
		return(true);
	}
	
	/**
	 *	This function creates a new item
	 *
	 *	@param   string   $title   Title
	 *	@param   int      $userId  User ID
	 *	@return  int
	 */
	public function add($title, $userId) {
		global $db;
		
		$db -> query(
			'INSERT INTO `db_item` (`user_id`, `date`, `title`) '.
			'VALUES("'.$userId.'", UNIX_TIMESTAMP(), "'.$title.'")'
		);
		$this -> itemId = $db -> insertId();
		$this -> itemArray['title'] = $title;
		$this -> itemArray['user_id'] = $userId;
		
		$addUrl = new Url;
		$addUrl -> add($title, toUrl($title), 'item/item', 'item_id='.$this -> itemId);
		$addUrl -> index = 1;
		
		return($this -> itemId);
	}
	
	/**
	 *
	 */
	public function createUrl($itemId = null) {
		global $db, $url;
		
		if($itemId == null) {
			if($this -> itemId == null) {
				return(false);
			}
			$itemId = $this -> itemId;
		} else {
			$this -> getItemById($itemId);
		}

		$urlItem = '';
		$nameCat = new Category($this -> itemArray['category_id']);
		if($nameCat -> categoryId > 0) {
			if($nameCat -> parent <> '') {
				$urlItem .= toUrl($nameCat -> parent).'/';
			}
			$urlItem .= toUrl($nameCat -> name).'/';
		}
		if($this -> itemArray['city'] <> '') {
			$urlItem .= toUrl($this -> itemArray['city']).'/';
		}
		$urlItem .= toUrl($this -> itemArray['title']);

		$url -> addUrl($this -> itemArray['title'], $urlItem, 'item/item', 'item', 'item_id='.$this -> itemId);
		
		return(true);
	}
	
	/**
	 *	This function removes the item from the database
	 *
	 *	@param   string   $itemId   Item ID
	 *	@return  boolean
	 */
	public function delete($itemId = null) {
		global $db;
		
		if($itemId == null) {
			if(!($this -> itemId > 0)) {
				return(false);
			}
			
			$itemId = $this -> itemId;
		}
		
		$db -> query(
			'DELETE '.
			'FROM `db_item` '.
			'WHERE `item_id` = "'.$itemId.'"'
		);
		
		$db -> query(
			'DELETE '.
			'FROM `db_favorite` '.
			'WHERE `item_id` = "'.$itemId.'"'
		);
		
		$delUrl = new Url;
		$getUrl = $delUrl -> getUrlByVar('item_id='.$itemId);
		if($getUrl <> false && $getUrl <> '') {
			$delUrl -> delete($getUrl);
		}
		
		return(true);
	}
	
	/**
	 *	This function counts items
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countItemList($sqlSearch = null, $radius = 0, $lat = 0, $lng = 0) {
		global $db;

		preg_match_all('@`f([0-9]+)`@', $sqlSearch, $out);
		$leftJoinArray = array_unique($out[1]);		
		$leftJoin = '';
		foreach($leftJoinArray as $v) {
			$leftJoin .= 'LEFT JOIN `db_item_feature_rel` AS `f'.$v.'` ON(`f'.$v.'`.`item_id` = `i`.`item_id`) ';
		}
		/*if($lat <> 0 && $lng <> 0 && $radius <> 0) {
			$db -> query(
				'SELECT COUNT(*) AS `count` '.
				'FROM ('.
					'SELECT (6371*acos(cos(radians('.$lat.'))*cos(radians(`lat`))*cos(radians(`lng`)-radians('.$lng.'))+sin(radians('.$lat.'))*sin(radians(`lat`)))) AS `distance` '.
					'FROM `db_item` '.
					'WHERE 1 '.$sqlSearch.' '.
					'GROUP BY `distance` HAVING `distance` <= "'.$radius.'" '.
				') AS `sub` '.
				$leftJoin
			);
		} else {	*/		
			$db -> query(
				'SELECT COUNT(*) AS `count` '.
				'FROM `db_item` AS `i` '.
				$leftJoin.
				'WHERE 1 '.$sqlSearch
			);
		//}
		
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get items
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@return  int
	 */
	public function getItemList($sqlSearch = null, $sqlOrder = null, $radius = 0, $lat = 0, $lng = 0) {
		global $db, $url;
		
		$array = [];

		preg_match_all('@`f([0-9]+)`@', $sqlSearch, $out);
		$leftJoinArray = array_unique($out[1]);		
		$leftJoin = '';
		foreach($leftJoinArray as $v) {
			$leftJoin .= 'LEFT JOIN `db_item_feature_rel` AS `f'.$v.'` ON(`f'.$v.'`.`item_id` = `i`.`item_id`) ';
		}

		/*if($lat <> 0 && $lng <> 0 && $radius <> 0) {
			$db -> query(
				'SELECT `i`.*, `c`.`name` AS `category` '.
				'FROM ('.
					'SELECT (6371*acos(cos(radians('.$lat.'))*cos(radians(`lat`))*cos(radians(`lng`)-radians('.$lng.'))+sin(radians('.$lat.'))*sin(radians(`lat`)))) AS `distance` '.
					'FROM `db_item` '.
					'WHERE 1 '.$sqlSearch.' '.
					'GROUP BY `distance` HAVING `distance` <= "'.$radius.'" '.
				') AS `sub` '.
				'LEFT JOIN `db_category` AS `c` ON(`c`.`category_id` = `i`.`category_id`) '.
				$leftJoin.
				(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
				'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
			);
		} else {	*/
			$db -> query(
				'SELECT `i`.*, `c`.`name` AS `category` '.
				'FROM `db_item` AS `i` '.
				'LEFT JOIN `db_category` AS `c` ON(`c`.`category_id` = `i`.`category_id`) '.
				$leftJoin.
				'WHERE 1 '.$sqlSearch.' '.
				(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
				'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
			);
		//}
		
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['item_id']] = $r;
			}
		}

		return($array);
	}
	
	public function getItemListAll($sqlSearch = null, $sqlOrder = null, $radius = 0, $lat = 0, $lng = 0) {
		global $db, $url;
		
		$array = [];

		preg_match_all('@`f([0-9]+)`@', $sqlSearch, $out);
		$leftJoinArray = array_unique($out[1]);		
		$leftJoin = '';
		foreach($leftJoinArray as $v) {
			$leftJoin .= 'LEFT JOIN `db_item_feature_rel` AS `f'.$v.'` ON(`f'.$v.'`.`item_id` = `i`.`item_id`) ';
		}

		/*if($lat <> 0 && $lng <> 0 && $radius <> 0) {
			$db -> query(
				'SELECT `i`.*, `c`.`name` AS `category` '.
				'FROM ('.
					'SELECT (6371*acos(cos(radians('.$lat.'))*cos(radians(`lat`))*cos(radians(`lng`)-radians('.$lng.'))+sin(radians('.$lat.'))*sin(radians(`lat`)))) AS `distance` '.
					'FROM `db_item` '.
					'WHERE 1 '.$sqlSearch.' '.
					'GROUP BY `distance` HAVING `distance` <= "'.$radius.'" '.
				') AS `sub` '.
				'LEFT JOIN `db_category` AS `c` ON(`c`.`category_id` = `i`.`category_id`) '.
				$leftJoin.
				(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
				'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
			);
		} else {	*/
			$db -> query(
				'SELECT `i`.*, `c`.`name` AS `category` '.
				'FROM `db_item` AS `i` '.
				'LEFT JOIN `db_category` AS `c` ON(`c`.`category_id` = `i`.`category_id`) '.
				$leftJoin.
				'WHERE 1 '.$sqlSearch.' '.
				(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '')
			);
		//}
		
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['item_id']] = $r;
			}
		}

		return($array);
	}
	
	/**
	 *	This function get items to slider
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@param   string   $sqlLimit    Number of records
	 *	@return  int
	 */
	public function getItemListToSlider($sqlSearch = null, $sqlOrder = null, $sqlLimit = null) {
		global $db, $url;
		
		$array = [];
		
		$db -> query(
			'SELECT `i`.*, `c`.`name` AS `category` '.
			'FROM `db_item` AS `i` '.
			'LEFT JOIN `db_category` AS `c` USING(`category_id`) '.
			'WHERE 1 '.$sqlSearch.' '.
			'GROUP BY `i`.`item_id` '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			(($sqlLimit <> null) ? 'LIMIT '.$sqlLimit : '')
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['item_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *	This function counts items
	 *
	 *	@param   int      $userId      User ID
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countItemListOfUser($userId, $sqlSearch = null) {
		global $db;
		
		if(!($userId > 0)) {
			return(false);
		}
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_item` '.
			'WHERE `user_id` = "'.$userId.'" '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get items
	 *
	 *	@param   int      $userId      User ID
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@return  int
	 */
	public function getItemListOfUser($userId, $sqlSearch = null, $sqlOrder = null) {
		global $db, $url;
	
		$array = [];

		$db -> query(
			'SELECT `i`.*, `c`.`name` AS `category` '.
			'FROM `db_item` AS `i` '.
			'LEFT JOIN `db_category` AS `c` USING(`category_id`) '.
			'WHERE `i`.`user_id` = "'.$userId.'" '.$sqlSearch.' '.
			'GROUP BY `i`.`item_id` '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['item_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *	This function adds the item to favorites
	 *
	 *	@param   int   $itemId   Item ID
	 *	@param   int   $userId   User ID
	 *	@return  boolean
	 */
	public function addFavorite($itemId, $userId) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_favorite` '.
			'WHERE `item_id` = "'.$itemId.'" && `user_id` = "'.$userId.'"'
		);
		$r = $db -> fetchArray();
		if($r['count'] == 0) {
			$db -> query(
				'INSERT INTO `db_favorite` (`item_id`, `user_id`) '.
				'VALUES("'.$itemId.'", "'.$userId.'")'
			);
		}
		
		return(true);
	}
	
	/**
	 *	This function deletes the item to favorites
	 *
	 *	@param   int   $itemId   Item ID
	 *	@param   int   $userId   User ID
	 *	@return  boolean
	 */
	public function deleteFavorite($itemId, $userId) {
		global $db;
		
		$db -> query(
			'DELETE '.
			'FROM `db_favorite` '.
			'WHERE `item_id` = "'.$itemId.'" && `user_id` = "'.$userId.'"'
		);
		
		return(true);
	}
	
	/**
	 *	This function deletes the item to favorites
	 *
	 *	@param   int   $itemId   Item ID
	 *	@param   int   $userId   User ID
	 *	@return  boolean
	 */
	public function isFavorite($itemId, $userId) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_favorite` '.
			'WHERE `item_id` = "'.$itemId.'" && `user_id` = "'.$userId.'"'
		);
		$r = $db -> fetchArray();
		if($r['count'] == 0) {
			return(false);
		}
		
		return(true);
	}
	
	/**
	 *	This function counts favorite items
	 *
	 *	@param   int      $userId      User ID
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countFavoriteItemListOfUser($userId, $sqlSearch = null) {
		global $db;
		
		if(!($userId > 0)) {
			return(false);
		}
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_favorite` '.
			'WHERE `user_id` = "'.$userId.'" '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get favorite items
	 *
	 *	@param   int      $userId      User ID
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@return  int
	 */
	public function getFavoriteItemListOfUser($userId, $sqlSearch = null, $sqlOrder = null) {
		global $db, $url;
	
		$array = [];

		$db -> query(
			'SELECT `i`.*, `c`.`name` AS `category` '.
			'FROM `db_item` AS `i` '.
			'LEFT JOIN `db_category` AS `c` ON(`c`.`category_id` = `i`.`category_id`) '.
			'LEFT JOIN `db_favorite` AS `f` ON(`f`.`item_id` = `i`.`item_id`) '.
			'WHERE `f`.`user_id` = "'.$userId.'" '.$sqlSearch.' '.
			'GROUP BY `i`.`item_id` '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['item_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *
	 */
	public function counter($itemId = null) {
		global $db;
		
		if($itemId == null) {
			if(!($this -> itemId > 0)) {
				return(false);
			}
			
			$itemId = $this -> itemId;
		}
		
		$this -> counter += rand(4, 6);
		
		$db -> query(
			'SELECT * '.
			'FROM `db_stat` '.
			'WHERE `item_id` = "'.$itemId.'" && `date` = "'.date('Y-m-d').'"'
		);
		if($db -> numRows() == 0) {
			$db -> query(
				'INSERT INTO `db_stat` (`date`, `item_id`, `counter`) '.
				'VALUES("'.date('Y-m-d').'", "'.$itemId.'", 1)'
			);
		} else {
			$db -> query(
				'UPDATE `db_stat` '.
				'SET `counter` = `counter`+1 '.
				'WHERE `item_id` = "'.$itemId.'" && `date` = "'.date('Y-m-d').'"'
			);
		}
		
		return(true);
	}
	
	/**
	 *
	 */
	public function counterPhone($itemId = null) {
		global $db;
		
		if($itemId == null) {
			if(!($this -> itemId > 0)) {
				return(false);
			}
			
			$itemId = $this -> itemId;
		}
		
		$db -> query(
			'SELECT * '.
			'FROM `db_stat` '.
			'WHERE `item_id` = "'.$itemId.'" && `date` = "'.date('Y-m-d').'"'
		);
		if($db -> numRows() == 0) {
			$db -> query(
				'INSERT INTO `db_stat` (`date`, `item_id`, `phone`) '.
				'VALUES("'.date('Y-m-d').'", "'.$itemId.'", 1)'
			);
		} else {
			$db -> query(
				'UPDATE `db_stat` '.
				'SET `phone` = `phone`+1 '.
				'WHERE `item_id` = "'.$itemId.'" && `date` = "'.date('Y-m-d').'"'
			);
		}
		
		return(true);
	}
	
	/**
	 *
	 */
	public function counterMsg($itemId = null) {
		global $db;
		
		if($itemId == null) {
			if(!($this -> itemId > 0)) {
				return(false);
			}
			
			$itemId = $this -> itemId;
		}
		
		$db -> query(
			'SELECT * '.
			'FROM `db_stat` '.
			'WHERE `item_id` = "'.$itemId.'" && `date` = "'.date('Y-m-d').'"'
		);
		if($db -> numRows() == 0) {
			$db -> query(
				'INSERT INTO `db_stat` (`date`, `item_id`, `message`) '.
				'VALUES("'.date('Y-m-d').'", "'.$itemId.'", 1)'
			);
		} else {
			$db -> query(
				'UPDATE `db_stat` '.
				'SET `message` = `message`+1 '.
				'WHERE `item_id` = "'.$itemId.'" && `date` = "'.date('Y-m-d').'"'
			);
		}
		
		return(true);
	}
}

?>