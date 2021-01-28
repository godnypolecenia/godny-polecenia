<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages messages
 */

class Message {
	
	public $messageId;
	
	/**
	 *	This function creates a new message
	 *
	 *	@param   int     $fromUserId   Sender User ID
	 *	@param   int     $toUserId     Addressee User ID
	 *	@param   string  $content      Content
	 *	@return  boolean
	 */
	public function add($fromUserId, $toUserId, $content, $sender = null) {
		global $db;
		
		
		$db -> query(
			'INSERT INTO `db_message` (`from_user_id`, `to_user_id`, `date`, `content`, `sender`) '.
			'VALUES("'.$fromUserId.'", "'.$toUserId.'", UNIX_TIMESTAMP(), "'.$content.'", "'.$sender.'")'
		);
		$this -> messageid = $db -> insertId();
		
		return($this -> messageId);
	}
	
	/**
	 *	This function counts messages of user
	 *
	 *	@param   int      $userId      User ID
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countMessageListOfUser($userId, $sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_message` '.
			'WHERE (`from_user_id` = "'.$userId.'" || `to_user_id` = "'.$userId.'") '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get the messages of the users with whom it has spoken
	 *
	 *	@param   int      $userId      User ID
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countUserMessageListOfUser($userId, $sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_message` '.
			'WHERE (`from_user_id` = "'.$userId.'" || `to_user_id` = "'.$userId.'") '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function counts the messages of the users with whom it has spoken
	 *
	 *	@param   int      $userId      User ID
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function getUserMessageListOfUser($userId, $sqlSearch = null) {
		global $db;
		
		$array = [];
		
		$db -> query(
			'SELECT * '.
			'FROM `db_message` '.
			'WHERE `from_user_id` = "'.$userId.'" || `to_user_id` = "'.$userId.'" '.$sqlSearch.' '.
			'ORDER BY `date` DESC'
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *	This function count messages of user with other user
	 *
	 *	@param   int      $fromUserId   User ID
	 *	@param   int      $toUserId     User ID
	 *	@param   string   $sqlSearch    Database query
	 *	@return  int
	 */
	public function countMessageListOfUserWithUser($fromUserId, $toUserId, $sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_message` '.
			'WHERE ((`from_user_id` = "'.$fromUserId.'" && `to_user_id` = "'.$toUserId.'") || (`to_user_id` = "'.$fromUserId.'" && `from_user_id` = "'.$toUserId.'")) '.$sqlSearch.' '.
			'ORDER BY `date` ASC '.
			'LIMIT 100'
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get messages of user with other user
	 *
	 *	@param   int      $fromUserId   User ID
	 *	@param   int      $toUserId     User ID
	 *	@param   string   $sqlSearch    Database query
	 *	@return  int
	 */
	public function getMessageListOfUserWithUser($fromUserId, $toUserId, $sqlSearch = null) {
		global $db;
		
		$array = [];
		
		$db -> query(
			'SELECT * '.
			'FROM `db_message` '.
			'WHERE ((`from_user_id` = "'.$fromUserId.'" && `to_user_id` = "'.$toUserId.'") || (`to_user_id` = "'.$fromUserId.'" && `from_user_id` = "'.$toUserId.'")) '.$sqlSearch.' '.
			'ORDER BY `date` ASC '.
			'LIMIT 100'
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['message_id']] = $r;
			}
		}
		
		return($array);
	}
	
	/**
	 *
	 */
	public function saveShow($msgId) {
		global $db;
		
		$db -> query(
			'UPDATE `db_message` '.
			'SET `show_date` = UNIX_TIMESTAMP() '.
			'WHERE `message_id` = "'.$msgId.'" && `show_date` = 0'
		);
		
		return(true);
	}
	
	/**
	 *
	 */
	public function countNewMessageOfUser($toUserId, $fromUserId = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_message` '.
			'WHERE `to_user_id` = "'.$toUserId.'" && `show_date` = 0'.(($fromUserId > 0) ? ' && `from_user_id` = "'.$fromUserId.'"' : '')
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *
	 */
	public function getMessageById($messageId, $toUserId) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_message` '.
			'WHERE `message_id` = "'.$messageId.'" && `to_user_id` = "'.$toUserId.'" '
		);
		if($db -> numRows() == 0) {
			return(false);
		}
		
		return($db -> fetchArray());
	}
}

?>