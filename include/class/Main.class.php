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

class Main {
	
	private $alert = [];
	
	/**
	 *
	 */
	public function __construct() {

		session_start();
		
		if(isset($_SESSION['alert']) && is_array($_SESSION['alert'])) {
			$this -> alert = $_SESSION['alert'];
			unset($_SESSION['alert']);
		}
	}
	
	/**
	 *	This function remembers the alert content in the session
	 *
	 *	@return  void
	 */
	public function __destruct() {
		
		if(is_array($this -> alert) && count($this -> alert) > 0) {
			$_SESSION['alert'] = $this -> alert;
		}
	}
	
	/**
	 *	This function saves the alerts
	 *
	 *	@return  boolean
	 */
	public function alertPrepare($status, $array = null) {
		
		if(!is_bool($status)) {
			return(false);
		}
		$this -> alert[] = [
			'status' => $status,
			'array' => $array
		];
		return(true);
	}
	
	/**
	 *	This function displays alerts
	 *
	 *	@return  void
	 */
	public function alert($key = null) {

		if(is_array($this -> alert) && count($this -> alert) > 0) {
			if($key == null) {
				foreach($this -> alert as $k => $v) {
					$this -> alertOutput($k);
				}
			} else {
				$this -> alertOutput($key);
			}
		}
	}
	
	/**
	 *	This function makes an alert to the html code
	 *
	 *	@param   int    $key   Alert key
	 *	@return  string
	 */
	private function alertOutput($key) {

		if(!is_array($this -> alert[$key])) {
			return(false);
		}
		
		$html = '<section class="alert '.(($this -> alert[$key]['status'] == true) ? 'alert-success' : 'alert-error').'">';
		$html .= '<h2>'.(($this -> alert[$key]['status'] == true) ? '<span class="ri-checkbox-circle-line icon"></span>Gotowe' : '<span class="ri-error-warning-line icon"></span>Błąd').'</h2>';
		
		if(!is_array($this -> alert[$key]['array']) && $this -> alert[$key]['array'] <> '') {
			$html .= '<p>'.$this -> alert[$key]['array'].'</p>';
		} else {
			$html .= '<p>'.(($this -> alert[$key]['status'] == true) ? 'Operacja została wykonana pomyślnie' : 'Operacja nie została wykonana, gdyż natrafiono na błąd').'</p>';
			if(is_array($this -> alert[$key]['array'])) {
				$html .= '<ul>';
				foreach($this -> alert[$key]['array'] as $k => $v) {
					$html .= '<li>'.(($k <> '') ? '<span class="bold">'.$k.'</span>: ' : '').$v.'</li>';
				}
				$html .= '</ul>';
			}
		}
		
		$html .= '</section>';
		$html .= "\n";
		
		echo($html);
		
		unset($this -> alert[$key]);
	}
	
	/**
	 *
	 */
	public function showDeveloperBar() {
		/*global $setup, $db, $url, $user, $timeStart;
		
		echo('<div id="developer-bar">');
		echo('<span class="ri-flask-line icon"></span> <strong>Tryb developerski jest włączony!</strong>');
		echo('<ul>');
		echo('<li><span class="ri-user-line icon"></span> Użytkownik: <a href="'.$url -> getUrl('user/index').'" class="bold">'.(($user -> userId > 0) ? $user -> name : 'Gość').'</a></li>');
		if($user -> userId > 0) echo('<li><span class="ri-vip-crown-2-line icon"></span> Typ: '.(($user -> type == 9) ? '<a href="'.$url -> getUrl('admin/index').'" class="bold">Administrator</a>' : 'Użytkownik').'</li>');
		echo('<li><span class="ri-database-2-line icon"></span> Zapytań: <span class="bold">'.$db -> count.'</span></li>');
		echo('<li><span class="ri-time-line icon"></span> Wygenerowano: <span class="bold">'.round(microtime()-$timeStart, 5).' s</span></li>');
		echo('</ul>');
		echo('</div>');*/
	}
	
	/**
	 *
	 */
	public function bodyId() {
		
		return(str_replace('/', '-', $this -> dir));
	}
}

?>