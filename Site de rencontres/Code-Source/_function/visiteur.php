<?php
	/**
	* Generate a unique ID
	*/
	class Visiteur {
		
		public function __construct(){
			
		}
		
		public function check_visiteur(){
			
			global $DB;
			
			if(!isset($_SESSION['id'])) {
		
				// Number views / day
				
				$ip_user = $_SERVER['REMOTE_ADDR'];
				
				$req = $DB->prepare("SELECT * 
					FROM a_view 
					WHERE ip = ? AND date_view = ?");
				
				$req->execute(array($ip_user, date('Y-m-d')));
					
				$req = $req->fetch();
				
				if(!$req){
					$req = $DB->prepare("INSERT INTO a_view (ip, date_view) VALUES (?, ?)");
					$req->execute(array($ip_user, date('Y-m-d')));
				
				}
			}
		}
	}
