<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class generates breadcrumb
 */

class Breadcrumb {
	
	private $list = [];
	
	/**
	 *	This function adds the first item to the list
	 *
	 *	@return  void
	 */
	public function __construct() {
		global $setup;
		
		$this -> list[0] = [SITE_ADDRESS, $setup -> name];
	}
	
	/**
	 *	This function adds items to the list
	 *
	 *	@return  void
	 */
	public function add($data, $name = null) {

		if(is_array($data)) {
			$url = $data['url'];
			if($name == null) $name = $data['title'];
		} else {
			$url = $data;
		}
		
		$this -> list[] = [$url, $name];
	}
	
	/**
	 *	This function generates breadcrumb
	 *
	 *	@param   array   $dir   Path
	 *	@return  string
	 */
	public function output() {
		
		$html = '<ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">'."\n";
		foreach($this -> list as $k => $v) {
			$html .= $this -> breadcrumbPosition($v[0], $v[1], $k);
			$i++;
		}		
		$html .= '</ol>'."\n";
		
		echo($html);
	}
	
	/**
	 *	This function generates the position in breadcrumb
	 *
	 *	@param   array   $dir   Path
	 *	@return  string
	 */
	public function breadcrumbPosition($url, $name, $position) {
		
		$html = '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">';
		$html .= '<a href="'.$url.'" itemprop="item">';
		$html .= '<span itemprop="name">'.$name.'</span>';
		$html .= '<meta itemprop="position" content="'.$position.'">';
		$html .= '</a>';
		$html .= '</li>';
		$html .= "\n";
		
		return($html);
	}
}

?>