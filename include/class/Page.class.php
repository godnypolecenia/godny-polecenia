<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages content pages
 */

class Page {
	
	public $pageId;
	private $pageArray = [];
	private $saveArray = [];
	
	/**
	 *	This function specifies the page ID
	 *
	 *	@param   int   $pageId   Page ID
	 *	@return  void
	 */
	public function __construct($pageId = null) {
		
		if($pageId <> null) {
			$this -> getPageById($pageId);
		}
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {
		global $db;
		
		if($this -> pageId > 0 && count($this -> saveArray) > 0) {
			$db -> query(
				'UPDATE `db_page` '.
				'SET '.implode(', ', array_map(function($v, $k) { return('`'.$k.'` = "'.$v.'"'); }, $this -> saveArray, array_keys($this -> saveArray))).' '.
				'WHERE `page_id` = "'.$this -> pageId.'"'
			);
		}
	}
	
	/**
	 *	This function retrieves the page by ID
	 *
	 *	@param   int   $pageId   Page ID
	 *	@return  boolean
	 */
	public function getPageById($pageId) {
		global $db;
		
		if(!($pageId > 0)) {
			return(false);
		}
		
		$db -> query(
			'SELECT * '.
			'FROM `db_page` '.
			'WHERE `page_id` = "'.$pageId.'"'
		);
		if($db -> numRows() <> 1) {
			return(false);
		}
	
		$this -> pageArray = $db -> fetchArray();
		$this -> pageId = $pageId;
		return(true);
	}
	
	/**
	 *	This function retrieves a value from the array
	 *
	 *	@param   string   $field   Value
	 *	@return  string
	 */
	public function __get($field) {
		
		if(!($this -> pageId > 0)) {
			return(false);
		}
		
		if(!isset($this -> pageArray[$field])) {
			return(false);
		}
		
		return($this -> pageArray[$field]);
	}
	
	/**
	 *	This function saves the page in the database
	 *
	 *	@param   string   $field   Field
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function __set($field, $value) {
		
		if(!($this -> pageId > 0)) {
			return(false);
		}
		
		if($this -> pageArray[$field] <> $value) {
			$this -> pageArray[$field] = $value;
			$this -> saveArray[$field] = $value;
		}
		
		return(true);
	}
	
	/**
	 *	This function creates a new page
	 *
	 *	@param   string   $title   Title
	 *	@return  boolean
	 */
	public function add($title) {
		global $db;
		
		$db -> query(
			'INSERT INTO `db_page` (`title`) '.
			'VALUES("'.$title.'")'
		);
		$this -> pageId = $db -> insertId();

		$addUrl = new Url;
		$addUrl -> add($title, toUrl($title), 'page/page', 'page_id='.$this -> pageId);
		$addUrl -> index = 1;
		
		return($this -> pageId);
	}
	
	/**
	 *	This function removes the page from the database
	 *
	 *	@param   string   $pageId   Page ID
	 *	@return  boolean
	 */
	public function delete($pageId = null) {
		global $db, $url;
		
		if($pageId == null) {
			if(!($this -> pageId > 0)) {
				return(false);
			}
			
			$pageId = $this -> pageId;
		}
		
		$db -> query(
			'DELETE '.
			'FROM `db_page` '.
			'WHERE `page_id` = "'.$pageId.'"'
		);
		
		$delUrl = new Url;

		$getUrl = $delUrl -> getUrlByVar('page_id='.$pageId);
		if($getUrl <> '') {
			$delUrl -> delete($getUrl);
		}
		
		return(true);
	}
	
	/**
	 *	This function counts pages
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countPageList($sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_page` '.
			'WHERE 1 '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get pages
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@param   string   $sqlLimit    Limit
	 *	@return  array
	 */
	public function getPageList($sqlSearch = null, $sqlOrder = null, $sqlLimit = null) {
		global $db, $url;
		
		$array = [];
		
		$db -> query(
			'SELECT * '.
			'FROM `db_page` '.
			'WHERE 1 '.$sqlSearch.' '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($sqlLimit <> null) ? $sqlLimit : ((($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N))
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['page_id']] = $r;
			}
		}
		
		return($array);
	}
}

?>