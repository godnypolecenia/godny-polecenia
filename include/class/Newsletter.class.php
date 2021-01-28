<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages Newsletter
 */

class Newsletter {
	
	/**
	 *
	 */
	public function getEmailById($newsletterId) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_newsletter` '.
			'WHERE `newsletter_id` = "'.$newsletterId.'"'
		);
		if($db -> numRows() == 0) {
			return(false);
		}
		
		$r = $db -> fetchARray();
		
		return($r['email']);
	}
	
	/**
	 *
	 */
	public function add($email, $city = null, $category = null) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_newsletter` '.
			'WHERE `email` = "'.$email.'"'
		);
		if($db -> numRows() == 0) {
			$db -> query(
				'INSERT INTO `db_newsletter` (`email`, `city`, `category`) '.
				'VALUES("'.$email.'", "'.$city.'", "'.$category.'")'
			);
		}
		
		return(false);
	}
	
	/**
	 *
	 */
	public function delete($email) {
		global $db;
		
		$db -> query(
			'DELETE '.
			'FROM `db_newsletter` '.
			'WHERE `email` = "'.$email.'"'
		);
		
		return(false);
	}
	
	/**
	 *	This function counts emails
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countNewsletterList($sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_newsletter` '.
			'WHERE 1 '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get emails
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@param   string   $sqlLimit    Limit
	 *	@return  array
	 */
	public function getNewsletterList($sqlSearch = null, $sqlOrder = null, $sqlLimit = null) {
		global $db, $url;
		
		$array = [];
		
		$db -> query(
			'SELECT * '.
			'FROM `db_newsletter` '.
			'WHERE 1 '.$sqlSearch.' '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($sqlLimit <> null) ? $sqlLimit : ((($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N))
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['newsletter_id']] = $r;
			}
		}
		
		return($array);
	}
}

?>