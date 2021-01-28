<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	
 */

class Vote {

	/**
	 *
	 */
	public function add($itemId, $vote, $email, $nick, $content, $vote_feature) {
		global $db;
		
		$db -> query(
			'SELECT `email` '.
			'FROM `db_vote` '.
			'WHERE `item_id` = "'.$itemId.'" && `email` = "'.$email.'"'
		);
		if($db -> numRows() > 0) {
			return(false);
		}
		
		$db -> query(
			'INSERT INTO `db_vote` (`item_id`, `vote`, `nick`, `date`, `email`, `content`, `vote_feature`) '.
			'VALUES("'.$itemId.'", "'.$vote.'", "'.$nick.'", "'.time().'", "'.$email.'", "'.$content.'", "'.$vote_feature.'")'
		);
		
		return($db -> insertId());
	}
	
	/**
	 *
	 */
	public function getVoteById($voteId) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_vote` '.
			'WHERE `vote_id` = "'.$voteId.'"'
		);
		if($db -> numRows() == 0) {
			return(false);
		}
		
		return($db -> fetchArray());
	}
	
	/**
	 *
	 */
	public function delete($voteId) {
		global $db;
		
		$db -> query(
			'DELETE '.
			'FROM `db_vote` '.
			'WHERE `vote_id` = "'.$voteId.'"'
		);
		
		return(true);
	}
	
	/**
	 *
	 */
	public function active($voteId) {
		global $db;
		
		$db -> query(
			'UPDATE `db_vote` '.
			'SET `status` = 1 '.
			'WHERE `vote_id` = "'.$voteId.'"'
		);
		
		return(true);
	}
	
	/**
	 *	This function counts votes
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countVoteList($sqlSearch = null, $sqlOrder = null, $sqlLimit = null) {
		global $db;

		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_vote` AS `v` '.
			'WHERE 1 '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get votes
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@param   string   $sqlLimit    Limit
	 *	@return  array
	 */
	public function getVoteList($sqlSearch = null, $sqlOrder = null, $sqlLimit = null) {
		global $db, $url;

		$db -> query(
			'SELECT `v`.*, `i`.`title` '.
			'FROM `db_vote` AS `v` '.
			'LEFT JOIN `db_item` AS `i` USING(`item_id`) '.
			'WHERE 1 '.$sqlSearch.' '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($sqlLimit <> null) ? $sqlLimit : ((($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N))
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['vote_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *
	 */
	public function countVoteListOfItem($itemId) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_vote` '.
			'WHERE `item_id` = "'.$itemId.'"'
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *
	 */
	public function getVoteListOfItem($itemId) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_vote` '.
			'WHERE `item_id` = "'.$itemId.'" '.
			'ORDER BY `date` DESC '
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['vote_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *
	 */
	public function countActiveVoteListOfItem($itemId) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_vote` '.
			'WHERE `item_id` = "'.$itemId.'" && `status` = 1'
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *
	 */
	public function getActiveVoteListOfItem($itemId) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_vote` '.
			'WHERE `item_id` = "'.$itemId.'" && `status` = 1 '.
			'ORDER BY `date` DESC '
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['vote_id']] = $r;
			}
		}
		
		return($array);
	}
}

?>