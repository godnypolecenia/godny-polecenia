<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class manages the database connection
 */
 
class Database {

	public $connect;
	public $query;
	public $result;
	public $count = 0;
	
	/**
	 *	This function connects to the database
	 *
	 *	@param   string   $host      Hoast
	 *	@param   string   $user      User
	 *	@param   string   $password  Password
	 *	@param   string   $name      Database name
	 *	@param   string   $prefix    Table prefix
	 *	@return  void
	 */
	public function __construct($host = null, $user = null, $password = null, $name = null, $prefix = null) {
	
		if($host == null) $host = DB_HOST;
		if($user == null) $user = DB_USER;
		if($password == null) $password = DB_PASSWORD;
		if($name == null) $name = DB_NAME;
		if($prefix == null) $prefix = DB_PREFIX;
	
		$this -> prefix = $prefix;
		if(!($this -> connect = mysqli_connect($host, $user, $password, $name))) $this -> error();


		$this -> query('SET NAMES "utf8"');
	}
	
	/**
	 *	This function displays database errors
	 *
	 *	@return  void
	 */
	public function error() {
		
		//file_put_contents('./data/log/error-db.txt', date('Y-m-d H:i:s').' - '.mysqli_errno($this -> connect).' ('.mysqli_error($this -> connect).')'."\n", FILE_APPEND);
		exit('<h2>Database error '.mysqli_errno($this -> connect).'</h2><p>'.mysqli_error($this -> connect).'</p><p>'.$this -> query.'</p>');
	}
	
	/**
	 *	This function queries the database
	 *
	 *	@return  handle
	 */
	public function query($query, $show = 0) {

		$this -> count++;

		if($show == 1) echo($query);
		$this -> query = str_replace(' `db_', ' `'.DB_PREFIX, $query);
		if(!($this -> result = mysqli_query($this -> connect, $this -> query))) $this -> error();
		return($this -> result);
	}
	
	/**
	 *	This function retrieves data from the database into an array
	 *
	 *	@return  array
	 */
	public function fetchArray($result = null) {
		
		return(mysqli_fetch_assoc((empty($result)) ? $this -> result : $result));
	}
	
	/**
	 *	This function retrieves the number of records from the database
	 *
	 *	@return  int
	 */
	public function numRows($result = null) {

		return(mysqli_num_rows((empty($result)) ? $this -> result : $result));
	}
	
	/**
	 *	This function retrieves the last ID from the database (auto increment)
	 *
	 *	@return  int
	 */
	public function insertId() {
	
		return(mysqli_insert_id($this -> connect));
	}
	
	/**
	 *	This function generates a database backup
	 *
	 *	@return  file
	 */
	public function backup() {
	
		$tmp = DB_HOST;
		$ex = explode(':', $tmp);
		$host = $ex[0];
		$port = $ex[1];
	
		$file = './data/tmp/backup-db-'.time().'.sql';
		system('mysqldump --host='.$host.(($port <> '') ? ' --port='.$port : '').' --user='.DB_USER.' --password='.DB_PASSWORD.' '.DB_NAME.' > '.$file);
		return((file_exists($file)) ? $file : false);
	}
	
	/**
	 *	This function closes the database connection
	 *
	 *	@return  void
	 */
	public function __destruct() {

		mysqli_close($this -> connect);
	}
}

?>