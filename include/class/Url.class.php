<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class oversees the entire portal
 */

class Url {
	
	public $urlId;
	private $url;
	private $dir;
	private $dirList = [];
	private $urlList = [];
	private $saveArray = [];
	private $op = [];
	private $opd = [];
	private $bodyId;
	
	/**
	 *
	 */
	public function __construct($url = 'index') {

		$this -> url = $url;
		$this -> getUrlByUrl($url);
		$this -> dirList();
		
		if($this -> url <> '') {
			$this -> getDir();
		}
		
		$this -> bodyId = $this -> url;
	}
	
	/**
	 *
	 */
	public function getUrlByUrl($url = null) {
		
		$this -> url = $url.((substr($url, -1) == '/' || $url == '') ? 'index' : '');
	}
	
	/**
	 *
	 */
	public function getUrlById($urlId, $domain = 0) {
		global $db;
		
		$db -> query(
			'SELECT `url` '.
			'FROM `db_url` '.
			'WHERE `url_id` = "'.$urlId.'"'
		);
		if($db -> numRows() == 0) {
			return(false);
		}
		$r = $db -> fetchArray();
		
		$this -> url = $r['url'];
		$this -> getDir();
		
		return((($domain == 1) ? SITE_ADDRESS.'/' : '').$this -> url);
	}
	
	/**
	 *
	 */
	public function getUrlByFile($file, $var = null, $domain = 0) {
		global $db;
		
		$db -> query(
			'SELECT `url` '.
			'FROM `db_url` '.
			'WHERE `file` = "'.$file.'"'.(($var <> null) ? ' && `variables` = "'.$var.'"' : '')
		);
		if($db -> numRows() == 0) {
			return(false);
		}
		$r = $db -> fetchArray();


		return((($domain == 1) ? SITE_ADDRESS.'/' : '').$r['url']);
	}
	
	/**
	 *
	 */
	public function getUrlByVar($var, $domain = 0) {
		global $db;

		$db -> query(
			'SELECT `url` '.
			'FROM `db_url` '.
			'WHERE `variables` = "'.$var.'"'
		);
		if($db -> numRows() == 0) {
			return(false);
		}
		$r = $db -> fetchArray();

		return((($domain == 1) ? SITE_ADDRESS.'/' : '').$r['url']);
	}
	
	/**
	 *
	 */
	private function dirList() {
		global $db;
		
		/*$cacheDirList = __DIR__.'/../../data/cache/dirList.cache';
		if(file_exists($cacheDirList)) {
			$this -> dirList = unserialize(file_get_contents($cacheDirList));
		}
		
		$cacheUrlList = __DIR__.'/../../data/cache/urlList.cache';
		if(file_exists($cacheUrlList)) {
			$this -> urlList = unserialize(file_get_contents($cacheUrlList));
		}
		
		if(count($this -> urlList) > 0 && count($this -> urlList) > 0) {
			return(true);
		}*/
		
		$file = '';
		
		$db -> query(
			'SELECT * '.
			'FROM `db_url`'
		);
		if($db -> numRows() > 0) {
			while($r = $db -> fetchArray()) {
				$this -> dirList[$r['file'].(($r['variables'] <> '') ? '?'.$r['variables'] : '')] = $r['url'];
				if($r['variables'] <> '') {
					$exVar = explode('&', $r['variables']);
					foreach($exVar as $v) {
						$exV = explode('=', $v);
						$r['var'][$exV[0]] = $exV[1];
					}
				}
				$this -> urlList[$r['url']] = $r;
			}
		}

		//file_put_contents($cacheDirList, serialize($this -> dirList));
		//file_put_contents($cacheUrlList, serialize($this -> urlList));
		
		return(true);
	}
	
	/**
	 *	This function saves data changes to the database
	 *
	 *	@return  void
	 */
	public function __destruct() {
		global $db;

		if($this -> url <> '' && count($this -> saveArray) > 0) {
			$db -> query(
				'UPDATE `db_url` '.
				'SET '.implode(', ', array_map(function($v, $k) { return('`'.$k.'` = "'.$v.'"'); }, $this -> saveArray, array_keys($this -> saveArray))).' '.
				'WHERE `url` = "'.$this -> url.'"'
			);
		}
	}
	
	/**
	 *
	 */
	private function clearCache() {
		
		$cacheDirList = __DIR__.'/../../data/cache/dirList.cache';
		if(file_exists($cacheDirList)) {
			unlink($cacheDirList);
		}
		
		$cacheUrlList = __DIR__.'/../../data/cache/urlList.cache';
		if(file_exists($cacheUrlList)) {
			unlink($cacheUrlList);
		}
		
		return(true);
	}
	
	/**
	 *
	 */
	public function getUrl($dir = null, $existOp = false, $addOp = null) {
		
		if($dir == null) {
			$dir = $this -> dir;
		}

		if(!($this -> dirList[$dir] <> '')) {
			return(false);
		}

		$url = $this -> dirList[$dir];
		
		if($existOp == true && count($this -> op) > 0) {
			$url .= '/'.implode('/', $this -> op);
		}
		
		if($addOp <> null) {
			$url .= $addOp;
		}
		
		return(SITE_ADDRESS.'/'.$url);
	}
	
	/**
	 *
	 */
	public function getLink($dir = null, $existOp = false, $addOp = null, $text = null) {
		
		if($dir == null) {
			$dir = $this -> dir;
		}

		if(!($this -> dirList[$dir] <> '')) {
			return(false);
		}

		$url = $this -> dirList[$dir];
		
		if($existOp == true && count($this -> op) > 0) {
			$url .= '/'.implode('/', $this -> op);
		}
		
		if($addOp <> null) {
			$url .= $addOp;
		}
		
		$title = $this -> urlList[$this -> dirList[$dir]]['title'];

		if($text == null) {
			$text = $this -> urlList[$this -> dirList[$dir]]['button'];
		}
		
		return(['url' => SITE_ADDRESS.'/'.$url, 'title' => $text]);
	}
	
	/**
	 *
	 */
	public function getButton($dir = null, $existOp = false, $addOp = null, $html = [], $text = null) {
		global $user;
		
		$button = $this -> getLink($dir, $existOp, $addOp, trim(strip_tags($text)));
		if($text == null) $text = $button['title'];
		
		if($dir == 'user/login' || ($user -> userId == 0 && $dir == 'user/index')) {
			if(isset($html['class'])) {
				$html['class'] .= ' ';
			}
			$html['class'] .= 'login-window';
		}
		
		if($this -> dir == $dir) {
			if(isset($html['class'])) {
				$html['class'] .= ' ';
			}
			$html['class'] .= 'selected';
		}
		
		if(isset($html['count'])) {
			$text .= ' <span class="count-box">'.$html['count'].'</span>';
			unset($html['count']);
		}
		
		if(is_array($html)) {
			$html = implode('', array_map(function($v, $k) { return(' '.$k.'="'.$v.'"'); }, $html, array_keys($html)));
		}

		return('<a href="'.$button['url'].'" title="'.$button['title'].'"'.$html.'>'.$text.'</a>');
	}
	
	/**
	 *
	 */
	public function getBookmark($key = 0, $text) {
		
		$selected = '';
		if($this -> opd(URL_BOOKMARK) == $key || ($this -> opd(URL_BOOKMARK) == false && $key == 0)) {
			$selected = ' class="selected"';
		}
		
		$adr =  str_replace('/'.URL_BOOKMARK.'-'.$this -> opd(URL_BOOKMARK), '', $this -> getUrl(null, true));
		if($key <> 0) $adr .= '/'.URL_BOOKMARK.'-'.$key; 
		
		if($this -> opd(URL_PAGE) > 1) {
			$adr = preg_replace('@\/'.URL_PAGE.'\-([0-9]+)@', '/'.URL_PAGE.'-1', $adr);
		}
		
		return('<a href="'.$adr.'"'.$selected.'>'.$text.'</a>');
	}
	
	/**
	 *
	 */
	public function getDir($url = null) {
		
		if($url == null) {
			$url = $this -> url;
		}

		$opReversed = [];

		$exUrl = explode('/', $url);
		foreach($exUrl as $v) {
			$tmpDir = implode('/', $exUrl);
			if(in_array($tmpDir, $this -> dirList)) {
				$this -> dir = $this -> urlList[$tmpDir]['file'].(($this -> urlList[$tmpDir]['variables'] <> '') ? '?'.$this -> urlList[$tmpDir]['variables'] : '');
				break;
			}
			$opReversed[] = end($exUrl);
			array_pop($exUrl);
		}

		if(!($this -> dir <> '')) {
			return(false);
		}
		
		if(is_array($opReversed) && count($opReversed) > 0) {
			$this -> op = array_reverse($opReversed);
		}
		
		foreach($this -> op as $v) {
			if(preg_match('@\-@is', $v)) {
				$ex = explode('-', $v);
				$this -> opd[$ex[0]] = str_replace($ex[0].'-', '', $v);
			}
		}

		return($this -> dir);
	}
	
	/**
	 *
	 */
	public function getUrlData($url = null) {
		
		if($url == null) {
			$url = $this -> url;
		}

		if(!is_array($this -> urlList[$url])) {
			return(false);
		}
		
		return($this -> urlList[$url]);
	}
	
	/**
	 *	This function redirects to the URL
	 *
	 *	@param   string   $url   Module address (null means current location)
	 *	@param   boolean  $op    Parameters attached to the address
	 *	@param   string   $add   Additional parameters
	 */
	public function redirect($url = null, $op = false, $add = null) {
		
		header('Location: '.$this -> getUrl($url, $op, $add));
		exit;
	}
	
	/**
	 *	This function returns a value from the OP table
	 *
	 *	@param    int     $key   Key of value
	 *	@return   string
	 */
	public function op($key) {
		
		if(!isset($this -> op[$key])) {
			return(false);
		}
		
		return($this -> op[$key]);
	}
	
	/**
	 *	This function assigns a value to OP
	 *
	 *	@param    string   $value   Value
	 *	@return   void
	 */
	public function setOp($value) {
		
		$this -> op[] = $value;
	}
	
	/**
	 *	This function returns the last value from the OP table
	 *
	 *	@return   string
	 */
	public function opLast() {
		
		return(end($this -> op));
	}
	
	/**
	 *	This function returns a value from the OPD table
	 *
	 *	@param    int     $key   Key of value
	 *	@return   string
	 */
	public function opd($key) {
		global $setup;
		
		if(!isset($this -> opd[$key])) {
			if($key == URL_PAGE) {
				$this -> setOpd(URL_PAGE, 1);
			} else {
				return(false);
			}
		}
		
		return($this -> opd[$key]);
	}
	
	/**
	 *	This function assigns a value to OP and OPD
	 *
	 *	@param    string   $key     Key
	 *	@param    string   $value   Value
	 *	@return   void
	 */
	public function setOpd($key, $value) {
		
		$this -> op[] = $key.'-'.$value;
		$this -> opd[$key] = $value;
	}
	
	/**
	 *	
	 */
	public function issetOpd($key) {
		
		if($this -> opd[$key] <> '') {
			return(true);
		}
		
		if(in_array($key, $this -> op)) {
			return(true);
		}
		
		return(false);
	}
	
	/**
	 *
	 */
	public function removeOpd($key) {
		
		unset($this -> opd[$key]);
		
		return(true);
	}
	
	/**
	 *	This function returns a file with the module
	 *
	 *	@return  string
	 */
	public function includePage() {

		$exDir = explode('?', $this -> dir);

		$path = './module/'.$exDir[0].'.php';

		if(!file_exists($path)) {
			$path = './module/tool/404.php';
		}

		return($path);
	}
	
	/**
	 *
	 */
	public function add($title, $url, $file, $variables = null) {
		global $db;
		
		$url = $this -> availableUrl($url);

		if($url == false) {
			return(false);
		}
		
		$db -> query(
			'INSERT INTO `db_url` (`url`, `file`, `button`, `title`, `variables`, `date`) '.
			'VALUES("'.$url.'", "'.$file.'", "'.$title.'", "'.$title.'", "'.$variables.'", UNIX_TIMESTAMP())'
		);
		$urlId = $db -> insertId();
		
		$this -> clearCache();
		
		$this -> getUrlById($urlId);
		
		return($urlId);
	}
	
	/**
	 *
	 */
	public function availableUrl($url, $currentUrl = null) {
		global $db;
		
		$baseUrl = $url;
		$count = 1;

		do {
			$db -> query(
				'SELECT COUNT(*) AS `count` '.
				'FROM `db_url` '.
				'WHERE `url` = "'.$url.'"'.(($currentUrl <> null) ? ' && `url` <> "'.$currentUrl.'"' : '')
			);
			$r = $db -> fetchArray();
			if($r['count'] > 0) {
				$count++;
				$url = $baseUrl.'-'.$count;
			} else {
				return($url);
			}
		} while(1);
		
		return(false);
	}
	
	/**
	 *
	 */
	public function delete($url = null) {
		global $db;
		
		if($url == null) {
			if(!($this -> url <> '')) {
				return(false);
			}
			$url = $this -> url;
		}

		if($url == 'index') {
			return(false);
		}

		$db -> query(
			'DELETE '.
			'FROM `db_url` '.
			'WHERE `url` = "'.$url.'" && `constant` = 0'
		);
		
		$this -> clearCache();
		
		return(true);
	}
	
	/**
	 *
	 */
	public function deleteUrlById($urlId) {
		global $db;
		
		$db -> query(
			'DELETE '.
			'FROM `db_url` '.
			'WHERE `url_id` = "'.$urlId.'"'
		);
		
		$this -> clearCache();
		
		return(true);
	}
	
	/**
	 *
	 */
	public function deleteUrlByUrl($url) {
		global $db;
		
		$db -> query(
			'DELETE '.
			'FROM `db_url` '.
			'WHERE `url` = "'.$url.'"'
		);
		
		$this -> clearCache();
		
		return(true);
	}
	
	/**
	 *	This function retrieves a value from the array
	 *
	 *	@param   string   $field   Value
	 *	@return  string
	 */
	public function __get($field) {

		if(!($this -> dir <> '')) {
			return(false);
		}

		if(!isset($this -> dirList[$this -> dir])) {
			return(false);
		}
		
		return($this -> urlList[$this -> dirList[$this -> dir]][$field]);
	}
	
	/**
	 *	This function saves the page in the database
	 *
	 *	@param   string   $field   Field
	 *	@param   string   $value   Value
	 *	@return  boolean
	 */
	public function __set($field, $value) {
		
		if(!($this -> url <> '')) {
			return(false);
		}
		
		if(!($this -> dir <> '')) {
			return(false);
		}

		if($field == 'url') {
			
			$value = $this -> availableUrl($value, $this -> url);

			if($value == false) {
				return(false);
			}
		}

		if($this -> urlList[$this -> url][$field] <> $value) {
			$this -> urlList[$this -> url][$field] = $value;
			$this -> saveArray[$field] = $value;
		}
		
		return(true);
	}
	
	/**
	 *
	 */
	public function setBodyId($value) {
		
		$this -> bodyId = $value;
		return(true);
	}
	
	/**
	 *
	 */
	public function bodyId() {
		
		return($this -> bodyId);
	}
	
	/**
	 *	This function counts URLs
	 *
	 *	@return  int
	 */
	public function countUrlList($filter = null) {
		
		if($filter == null) {
			return(count($this -> dirList));
		} else {
			$count = 0;
			$ex = explode('=', $filter);
			foreach($this -> urlList as $v) {
				if($v[$ex[0]] == $ex[1]) {
					$count++;
				}
			}
			return($count);
		}
	}
	
	/**
	 *	This function get URLs
	 *
	 *	@return  array
	 */
	public function getUrlList($filter = null, $paging = false) {
		
		if($filter == null && $paging == false) {
			return($this -> urlList);
		}
		
		$array = [];
		
		if($filter <> null) {
			$ex = explode('=', $filter);
			foreach($this -> urlList as $v) {
				if($v[$ex[0]] == $ex[1]) {
					$array[] = $v;
				}
			}
		} else {
			$array = $this -> urlList;
		}
		
		if($paging == true) {
			return(array_slice($array, (($this -> opd(URL_PAGE)-1)*PAGE_N), PAGE_N));
		}
		
		return($array);
	}
	
	/**
	 *
	 */
	public function addBackUrl($url = null) {
		
		if($url == null) {
			$url = $this -> url;
		}
		
		if(!is_array($_SESSION['back-url'])) {
			$_SESSION['back-url'] = [];
		}
		
		if($url <> '') {
			$_SESSION['back-url'][] = SITE_ADDRESS.'/'.$url;
		}
		
		return(true);
	}
	
	/**
	 *
	 */
	public function getBackUrl($step = 0) {
		
		if(!is_array($_SESSION['back-url'])) {
			return(false);
		}
		
		return($_SESSION['back-url'][$step]);
	}
	
	/**
	 *
	 */
	public function goBackUrl($step = 0) {
		
		if(!($this -> getBackUrl() <> null)) {
			return(false);
		}
		
		header('Location: '.$this -> getBackUrl($step));
		exit;
	}
}

?>