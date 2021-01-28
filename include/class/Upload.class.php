<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class uploads files
 */

class Upload {
	
	private $fileArray;
	private $defaultDir = './files/upload';
	private $name;
	private $format;
	
	/**
	 *	This function returns a URL
	 *
	 *	@param   array   $array   An array with a file
	 *	@return  boolean
	 */
	public function __construct($array) {
		
		if(!is_array($array)) {
			return(false);
		}
		$this -> fileArray = $array;
		
		return(true);
	}
	
	/**
	 *	This function checks if the file has a type
	 *
	 *	@param   array   $mimeType   An array with a type or null to all types
	 *	@return  boolean
	 */
	public function allowedType($mimeType = null) {
		
		if(is_array($mimeType)) {
			if(!in_array($this -> fileArray['type'], $mimeType)) {
				return(false);
			}
		}
		
		return(true);
	}
	
	/**
	 *	This function checks the file size
	 *
	 *	@param   array   $max   Maximum file size in bytes
	 *	@return  boolean
	 */
	public function maxSize($max = null) {
		
		if($max > 0) {
			if($this -> fileArray['size'] >= $max) {
				return(false);
			}
		}
		
		return(true);
	}
	
	/**
	 *	This function saves the file on the server
	 *
	 *	@param   string   $dir   Save path
	 *	@param   string   $name  File name
	 *	@return  boolean or string
	 */
	public function save($dir = null, $name = null) {
		
		$ex = explode('.', $this -> fileArray['name']);
		$format = strtolower(end($ex));
		
		if($dir == null) {
			$dir = $this -> defaultDir;
		}
		if($name == null) {
			do {
				$tmp = password(time().rand(0, 9999));
				if(!file_exists($dir.'/'.$tmp.'.'.$format)) {
					$name = $tmp;
				}
			} while($name == null);
		}

		if(!move_uploaded_file($this -> fileArray['tmp_name'], $dir.'/'.$name.'.'.$format)) {
			return(false);
		}
		
		$this -> name = $name;
		$this -> format = $format;

		return($name.'.'.$format);
	}
}

?>