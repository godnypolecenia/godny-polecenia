<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This class generates image thumbnails
 */

class Image {

	private $name;
	private $format;
	private $width = 0;
	private $height = 0;
	private $mime;

	/**
	 *	This function gets the file name and format
	 *
	 *	@param   string   $name     File name
	 *	@param   string   $format   File format
	 *	@return  void
	 */
	public function __construct($name) {

		$ex = explode('.', $name);

		$this -> name = $ex[0]; 
		$this -> format = end($ex); 
	}
	
	/**
	 *	This function gets the file name and format
	 *
	 *	@param   int   $width    Target width of the thumbnail
	 *	@param   int   $height   Target height of the thumbnail
	 *	@return  void
	 */
	public function size($width, $height) {

		$this -> width = $width; 
		$this -> height = $height; 		
	}
	
	/**
	 *	This function generates the image according to the given dimensions or the original dimensions
	 *
	 *	@return  file
	 */
	public function output() {
		global $setup;

		$sourceDir = './data/upload/'.$this -> name.'.'.$this -> format;
		
		if($this -> width > 0 && $this -> height > 0) {
			if(!preg_match('@'.$this -> width.'x'.$this -> height.'@is', $setup -> image)) return(false);
			$dir = './data/image/'.$this -> name.'.'.$this -> width.'x'.$this -> height.'.'.$this -> format;
		} else {
			$dir = './data/image/'.$this -> name.'.'.$this -> format;
		}
		
		if(!file_exists($dir)) {
			if(file_exists($sourceDir)) {
				$this -> mime = mime_content_type($sourceDir);
				if($this -> mime == 'image/jpg' || $this -> mime == 'image/jpeg') $file = imagecreatefromjpeg($sourceDir);
				if($this -> mime == 'image/gif') $file = imagecreatefromgif($sourceDir);
				if($this -> mime == 'image/png') $file = imagecreatefrompng($sourceDir);
				if(!$file) return(false);

				$sourceWidth = imagesx($file);
				$sourceHeight = imagesy($file);

				if($this -> width > 0 && $this -> height > 0) {

					$mini = imagecreatetruecolor($this -> width, $this -> height);
					if(($sourceWidth/$this -> width) >= ($sourceHeight/$this -> height)) {
						$new_tmp_width = floor($sourceWidth/($sourceHeight/$this -> height));
						$new_tmp_height = floor($sourceHeight/($sourceHeight/$this -> height));
						$w = floor(($new_tmp_width-$this -> width)/2);
						$h = 0;
					} else {
						$new_tmp_width = floor($sourceWidth/($sourceWidth/$this -> width));
						$new_tmp_height = floor($sourceHeight/($sourceWidth/$this -> width));
						$w = 0;
						$h = floor(($new_tmp_height-$this -> height)/2);
					}
					imagecopyresampled($mini, $file, (-$w), (-$h), 0, 0, ($this -> width+2*$w), ($this -> height+2*$h), $sourceWidth, $sourceHeight);
				} else {
					
					$mini = imagecreatetruecolor($sourceWidth, $sourceHeight);
					imagecopyresampled($mini, $file, 0, 0, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);
					$this -> width = $sourceWidth;
					$this -> height = $sourceHeight;
				}
				
				$mini = $this -> watermark($mini);
				imagejpeg($mini, $dir, 100);
				
				@imagedestroy($file);
				@imagedestroy($mini);
			} else {
				return(false);
			}
		} else {
			$this -> mime = mime_content_type($dir);
		}
	
		header('Content-Type: '.$this -> mime);
		readfile($dir);
	}
	
	/**
	 *	This function applies a watermark to the image
	 *
	 *	@param   file   $handle   Source file
	 *	@return  file
	 */
	private function watermark($handle) {
		global $setup;
		
		$watermark = './data/watermark.png';
		if($setup -> watermark = 1 && (($this -> width > 400 && $this -> height > 400) || ($this -> width == 0 && $this -> height == 0)) && file_exists($watermark)) {
			$stamp = imagecreatefrompng($watermark);
			$sx = imagesx($stamp);
			$sy = imagesy($stamp);
			imagecopy($handle, $stamp, $this -> width-$sx-10, $this -> height-$sy-10, 0, 0, $sx, $sy);
		}
		
		return($handle);
	}
	
	/**
	 *	This function rotates the image by the specified angle
	 *
	 *	@param   int   $degrees    Degrees
	 *	@return  boolean
	 */
	public function rotate($degrees) {
		
		if(!($degrees == 90 || $degrees == 180 || $degrees == 270)) return(false);
		
		$this -> removeThumbnails();
		
		$sourceDir = './data/upload/'.$this -> name.'.'.$this -> format;
		if(!file_exists($dir)) {
			if(file_exists($sourceDir)) {
				$this -> mime = mime_content_type($sourceDir);
				if($this -> mime == 'image/jpg' || $this -> mime == 'image/jpeg') $file = imagecreatefromjpeg($sourceDir);
				if($this -> mime == 'image/gif') $file = imagecreatefromgif($sourceDir);
				if($this -> mime == 'image/png') $file = imagecreatefrompng($sourceDir);
				if(!$file) return(false);
				
				$sourceWidth = imagesx($file);
				$sourceHeight = imagesy($file);
			} else {
				return(false);
			}
		}
		
		if($degrees == 90 || $degrees == 270) {
			$width = $sourceHeight;
			$height = $sourceWidth;
		} else {
			$width = $sourceWidth;
			$height = $sourceHeight;
		}
		
		$rotate = imagerotate($file, $degrees, 0);
		
		imagejpeg($rotate, './data/upload/'.$this -> name.'.jpg', 100);
		@imagedestroy($file);
		@imagedestroy($rotate);
		
		return(true);
	}
	
	/**
	 *	This function cuts out a part of the image with given dimensions
	 *
	 *	@param   int   $x        Starting point in the X axis
	 *	@param   int   $y        Starting point in the Y axis
	 *	@param   int   $width    Selected width
	 *	@param   int   $height   Selected height
	 *	@return  boolean
	 */
	public function cut($x, $y, $width, $height) {
		
		$this -> removeThumbnails();
		
		$sourceDir = './data/upload/'.$this -> name.'.'.$this -> format;
		if(!file_exists($dir)) {
			if(file_exists($sourceDir)) {
				$this -> mime = mime_content_type($sourceDir);
				if($this -> mime == 'image/jpg' || $this -> mime == 'image/jpeg') $file = imagecreatefromjpeg($sourceDir);
				if($this -> mime == 'image/gif') $file = imagecreatefromgif($sourceDir);
				if($this -> mime == 'image/png') $file = imagecreatefrompng($sourceDir);
				if(!$file) return(false);
				
				$sourceWidth = imagesx($file);
				$sourceHeight = imagesy($file);
			} else {
				return(false);
			}
		}
		
		$mini = imagecreatetruecolor($width, $height);
		imagecopyresampled($mini, $file, 0, 0, $x, $y, $width, $height, $width, $height);
		
		imagejpeg($mini, './data/upload/'.$this -> name.'.jpg', 100);
		@imagedestroy($file);
		@imagedestroy($rotate);
		
		return(true);
	}
	
	/**
	 *	This function deletes all thumbnails
	 *
	 *	@return  boolean
	 */
	public function removeThumbnails() {
		global $setup;
		
		$ex = explode(';', $setup -> image);
		foreach($ex as $v) {
			$dir = './data/image/'.$this -> name.'.'.$v.'.'.$this -> format;
			if(file_exists($dir)) unlink($dir);
		}
		
		$dir = './data/image/'.$this -> name.'.'.$this -> format;
		if(file_exists($dir)) unlink($dir);
		
		return(true);
	}
	
	/**
	 *	This function deletes the image and all its thumbnails
	 *
	 *	@return  boolean
	 */
	public function remove() {
		
		$this -> removeThumbnails();
		
		$dir = './data/upload/'.$this -> name.'.'.$this -> format;
		if(file_exists($dir)) unlink($dir);
		
		return(true);
	}
}

?>