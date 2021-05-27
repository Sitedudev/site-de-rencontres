<?php 
/**
 * @package			: Code source rencontres
 * @version			: 0.7
 * @author			: sitedudev aka clouder
 * @link 			: https://sitedudev.com
 * @since			: 2021
 * @license			: Attribution-NomCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)
 */
class Domain {
		
	private $_url = "";
	private $_domain = "";
		
	
	public function __construct(){
		
	}
	
	public function domain(){
		
		$_host = (String) null;
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
			$this->_url = "https";
		}else{
			$this->_url = "http"; 
		}  
		
		$this->_url .= "://"; 
		
		if($_SERVER['SERVER_NAME'] == "localhost"){
			
			$this->_url .= $_SERVER['HTTP_HOST'];
			
			if($_SERVER['SCRIPT_FILENAME'] != $_SERVER['DOCUMENT_ROOT']){
				$_host = explode("/", substr($_SERVER['SCRIPT_FILENAME'], (strlen($_SERVER['DOCUMENT_ROOT']) + 1), strlen($_SERVER['SCRIPT_FILENAME'])), 2); 	
			}

			if(isset($_host[0])){
				$this->_url .= '/' . $_host[0] . '/';
			}
		}else{
			$this->_url .= $_SERVER['HTTP_HOST'] . '/'; 
		}
		
		$this->_domain = $this->_url;
		
		/* (1)
		//$this->_domain = "http://" . $this->_url . "/Code-Source/";
		// Exemple : http://localhost:8888/Code-Source/
		
		/*
		/ Si le CSS ne s'affiche pas utiliser le n° 1 avec le nom de votre site
		/ si le nom est différent de Code-Source
		*/
		
		return $this->_domain;
		
	}
	
	public function current_url(){
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
			$this->_url = "https";
		}else{
			$this->_url = "http"; 
		}  
		
		$this->_url .= "://"; 
		$this->_url .= $_SERVER['HTTP_HOST']; 
		$this->_url .= $_SERVER['REQUEST_URI']; 
		
		return $this->_url;
	}
}
