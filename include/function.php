<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This function loads classes
 */
function __autoload($name) {
	$dir = './include/class/'.$name.'.class.php';
	if(file_exists($dir)) {
		require_once($dir);
	}
}

/**
 *	This function encodes the password
 */
function password($value) {
	return(sha1(md5(sha1($value.'x4!&523q#$').'o(04zwf:]a').'c53s53#25t'));
}

/**
 *	This function changes the words
 */
function inflect($value, $words) {
	if($value == 1) return($words[0]);
	if($value % 100 > 10 && $value % 100 < 20) return($words[2]);
	switch($value % 10) {
		case 2:
		case 3:
		case 4: return($words[1]);
		default: return($words[2]);
	}
}

/**
 *	This function clears letters
 */
function mbStrtr($str, $from, $to) {
	$keys = array();
	$values = array();
	preg_match_all('/./u', $from, $keys);
	preg_match_all('/./u', $to, $values);
	$mapping = array_combine($keys[0], $values[0]);
	return(strtr($str, $mapping));
}

/**
 *	This function changes the chain to url
 */
function toUrl($link, $rhomb = false) {

	$link = str_replace(array(' ', '_'), array('-', '-'), mb_strtolower($link, 'UTF-8'));
	$link = mbStrtr($link, 'ęóąśłżźćń', 'eoaslzzcn');
	$link = preg_replace('/[^a-z0-9\-\/]/', '', $link);
	
	return($link);
}

/**
 *	This function cleans the chains
 */
function good_var(&$arr) {
	foreach($arr as &$v) {
		if(is_array($v)) {
			good_var($v);
		} else {
			$v = addslashes($v);
		}
	}
	unset($v);
}

/**
 *	This function generates random text
 */
function randomText($length) {
	
	$arr = ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '!', '@', '#', '$', '%', '^', '&', '*'];
	$c_arr = count($arr)-1;
	$tmp = '';
	for($i = 0; $i < $length; $i++) {
		$tmp .= $arr[rand(0, $c_arr)];
	}
	
	return($tmp);
}

?>