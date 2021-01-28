<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages a single user
 */

class User {
	
	public $userId;
	public $ip;
	
	private $sessionId;
	private $userArray = [];
	private $saveArray = [];
	
	/**
	 *	This function initiates sessions and downloads user data to the array
	 *
	 *	@return  void
	 */
	public function __construct() {
		global $db;
		
		session_start();
		$this -> sessionId = session_id();
		$this -> ip = $_SERVER['REMOTE_ADDR'];
		
		if($this -> getUserBySessionId($this -> sessionId)) {
			$db -> query(
				'UPDATE `db_user` '.
				'SET `session_time` = UNIX_TIMESTAMP() '.
				'WHERE `user_id` = "'.$this -> userId.'"'
			);
		}
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {
		global $db;
		
		if($this -> userId > 0 && count($this -> saveArray) > 0) {
			$db -> query(
				'UPDATE `db_user` '.
				'SET '.implode(', ', array_map(function($v, $k) { return('`'.$k.'` = "'.$v.'"'); }, $this -> saveArray, array_keys($this -> saveArray))).' '.
				'WHERE `user_id` = "'.$this -> userId.'"'
			);
		}
	}
	
	/**
	 *	This function authorizes the user to his account
	 *
	 *	@param   string   $login     Login
	 *	@param   string   $password  Password
	 *	@return  boolean
	 */
	public function login($login, $password) {
		global $db;
		
		if($this -> userId > 0) {
			$this -> logout();
		}

		$db -> query(
			'SELECT * '.
			'FROM `db_user` '.
			'WHERE `status` = 1 && `email` = "'.$login.'" && `password` = "'.password($password).'"'
		);
		if($db -> numRows() <> 1){
			return(false);
		}
		
		$this -> userArray = $db -> fetchArray();
		$this -> userId = $this -> userArray['user_id'];	

		$db -> query(
			'UPDATE `db_user` '.
			'SET `session_id` = "'.$this -> sessionId.'", `session_time` = UNIX_TIMESTAMP() '.
			'WHERE `user_id` = "'.$this -> userId.'"'
		);
		
		return(true);
	}
	
	/**
	 *	This function forcibly logs into the account (without authorization)
	 *
	 *	@param   int     $userId   User ID
	 *	@return  boolean
	 */
	public function loginForced($userId) {
		global $db;
		
		if($this -> userId > 0) {
			$this -> logout();
		}
		
		$this -> getUserById($userId);
		
		$db -> query(
			'UPDATE `db_user` '.
			'SET `session_id` = "'.$this -> sessionId.'", `session_time` = UNIX_TIMESTAMP() '.
			'WHERE `user_id` = "'.$userId.'"'
		);
		
		return(true);
	}
	
	/**
	 *	This function logs the user out of the account
	 *
	 *	@return  boolean
	 */
	public function logout() {
		global $db;
		
		if($this -> userId > 0) {
			$db -> query(
				'UPDATE `db_user` '.
				'SET `session_id` = "", `session_time` = UNIX_TIMESTAMP() '.
				'WHERE `user_id` = "'.$this -> userId.'"'
			);
			$this -> userArray = [];
			$this -> userId = 0;	
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
		
		if(!($this -> userId > 0)) {
			return(false);
		}
		
		if(!isset($this -> userArray[$field])) {
			return(false);
		}
		
		return($this -> userArray[$field]);
	}
	
	/**
	 *	This function saves the user in the database
	 *
	 *	@param   string   $field   Field
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function __set($field, $value) {
		global $db;
		
		if(!($this -> userId > 0)) {
			return(false);
		}
		
		if($field == 'password') $value = password($value);

		if($this -> userArray[$field] <> $value) {
			$this -> userArray[$field] = $value;
			$this -> saveArray[$field] = $value;
		}
		
		return(true);
	}
	
	/**
	 *	This function allows access only to logged in users
	 *
	 *	@return  boolean
	 */
	public function onlyUser() {
		global $url, $mobile, $_SERVER;
		
		if($this -> userId == 0) {
			$url -> redirect('user/login');
			exit;
		}
		
		return(true);
	}
	
	/**
	 *	This feature only allows administrators
	 *
	 *	@return  boolean
	 */
	public function onlyAdmin($area = null) {
		global $url;
		
		if($this -> userId == 0) {
			$url -> redirect('admin/login');
			exit;
		}
		
		if($this -> userArray['type'] < 9) {
			$url -> redirect('403');
			exit;
		}
		
		if($area !== null) {
			if($this -> power <> '') {
				$ex = explode(';', $this -> power);
				if($ex[$area] <> 1) {
					$url -> redirect('403');
					exit;
				}
			}
		}
		
		return(true);
	}
	
	/**
	 *	This feature allows access only to unlogged users
	 *
	 *	@return  boolean
	 */
	public function onlyGuest() {
		global $url;
		
		if($this -> userId > 0) {
			$url -> redirect('user/index');
			exit;
		}
		
		return(true);
	}
	
	/**
	 *	This function removes the page from the database
	 *
	 *	@param   string   $userId   Page ID
	 *	@return  boolean
	 */
	public function delete($userId = null) {
		global $db;
		
		if($userId == null) {
			if(!($this -> userId > 0)) {
				return(false);
			}
			
			$userId = $this -> userId;
		}
		
		$db -> query(
			'DELETE '.
			'FROM `db_user` '.
			'WHERE `user_id` = "'.$userId.'"'
		);
		return(true);
	}
	
	/**
	 *	This function creates a user account
	 *
	 *	@param   string   $email   E-mail address (login)
	 *	@return  boolean
	 */
	public function add($email) {
		global $db;
		
		if(!$this -> availableLogin($email)) {
			return(false);
		}
		
		$db -> query(
			'INSERT INTO `db_user` (`email`, `register_time`, `register_ip`, `session_time`, `session_ip`) '.
			'VALUES("'.$email.'", UNIX_TIMESTAMP(), "'.$this -> ip.'", UNIX_TIMESTAMP(), "'.$this -> ip.'")'
		);
		$this -> userId = $db -> insertId();
		
		return($this -> userId);
	}
	
	/**
	 *	This function checks if the given login is available
	 *
	 *	@param   string   $email   E-mail address (login)
	 *	@return  boolean
	 */
	public function availableLogin($email) {
		global $db;
		
		$db -> query(
			'SELECT `email` '.
			'FROM `db_user` '.
			'WHERE `user_id` <> "'.$this -> userId.'" && `email` = "'.$email.'"'
		);
		if($db -> numRows() > 0) {
			return(false);
		}
		
		return(true);
	}
	
	/**
	 *	This function retrieves user data with the given ID
	 *
	 *	@param   int     $userId   User Id
	 *	@return  boolean
	 */
	public function getUserById($userId) {
		global $db;
		
		if(!($userId > 0)) {
			return(false);
		}
		
		$db -> query(
			'SELECT * '.
			'FROM `db_user` '.
			'WHERE `user_id` = "'.$userId.'"'
		);
		if($db -> numRows() <> 1) {
			return(false);
		}
		
		$this -> userArray = $db -> fetchArray();
		$this -> userId = $this -> userArray['user_id'];
		
		if(!isset($this -> userArray['firstname'])) {
			$ex = explode(' ', $this -> userArray['name']);
			$this -> userArray['firstname'] = $ex[0];
			$this -> userArray['lastname'] = $ex[1];
		}
		
		return(true);
	}
	
	/**
	 *	This function retrieves user data with the given Session ID
	 *
	 *	@param   int     $sessionId   Session Id
	 *	@return  boolean
	 */
	public function getUserBySessionId($sessionId) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_user` '.
			'WHERE `session_id` = "'.$sessionId.'"'
		);
		if($db -> numRows() <> 1) {
			return(false);
		}
		
		$this -> userArray = $db -> fetchArray();
		$this -> userId = $this -> userArray['user_id'];
		
		if(!isset($this -> userArray['firstname'])) {
			$ex = explode(' ', $this -> userArray['name']);
			$this -> userArray['firstname'] = $ex[0];
			$this -> userArray['lastname'] = $ex[1];
		}
		
		return(true);
	}
	
	/**
	 *	This function retrieves user data with the given login
	 *
	 *	@param   int     $email   User Login (email)
	 *	@return  boolean
	 */
	public function getUserByLogin($email) {
		global $db;
		
		$db -> query(
			'SELECT * '.
			'FROM `db_user` '.
			'WHERE `email` = "'.$email.'"'
		);
		if($db -> numRows() <> 1) {
			return(false);
		}
		
		$this -> userArray = $db -> fetchArray();
		$this -> userId = $this -> userArray['user_id'];
		
		if(!isset($this -> userArray['firstname'])) {
			$ex = explode(' ', $this -> userArray['name']);
			$this -> userArray['firstname'] = $ex[0];
			$this -> userArray['lastname'] = $ex[1];
		}
		
		return(true);
	}
	
	/**
	 *	This function counts users
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@return  int
	 */
	public function countUserList($sqlSearch = null) {
		global $db;
		
		$db -> query(
			'SELECT COUNT(*) AS `count` '.
			'FROM `db_user` '.
			'WHERE 1 '.$sqlSearch
		);
		$r = $db -> fetchArray();
		
		return($r['count']);
	}
	
	/**
	 *	This function get users
	 *
	 *	@param   string   $sqlSearch   Database query
	 *	@param   string   $sqlOrder    Sorting results
	 *	@return  int
	 */
	public function getUserList($sqlSearch = null, $sqlOrder = null) {
		global $db, $url;
		
		$array = [];
		
		$db -> query(
			'SELECT * '.
			'FROM `db_user` '.
			'WHERE 1 '.$sqlSearch.' '.
			(($sqlOrder <> null) ? 'ORDER BY '.$sqlOrder.' ' : '').
			'LIMIT '.(($url -> opd(URL_PAGE)-1)*PAGE_N).', '.PAGE_N
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$array[$r['user_id']] = $r;
			}
		}
		
		return($array);
	}	 
}

?>