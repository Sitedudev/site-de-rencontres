<?php
	/**
	* Online
	*/
	class Online {
		
		public function __construct(){
			
		}
		
		public function is_online($date){
						
			if(isset($date)){
				$datetime1 = new DateTime(date("Y-m-d H:i:s"));
				$datetime2 = new DateTime($date);
				
				$interval = date_diff($datetime1, $datetime2);

				$time = $interval->format("%Y%M%D%H%I%S"); 
				
				if($time < 500){ 
					return 1;
				}elseif($time < 1000){
					return 2;
				}else{
					return 0;
				}
			}
			return false;
		}
		
		public function online(){
						
			if(isset($_SESSION['date_connection'])){
				$datetime1 = new DateTime(date("Y-m-d H:i:s"));
				$datetime2 = new DateTime($_SESSION['date_connection']);
				
				$interval = date_diff($datetime1, $datetime2);

				$time = $interval->format("%H%I%S"); 

				if($time > 500){
					Online::verif_online();
				}
			}
		}
		
		public function verif_online(){
			
			global $DB;
			
			$temps_session = 60 * 5; // To get 5 min
			$temps_actuel = date("U");
			$ip_user = $_SERVER['REMOTE_ADDR'];
			
			if(isset($_SESSION['id'])) {
			
				$update_date_connection = date("Y-m-d H:i:s");
				
				$req_ip_exist = $DB->prepare("SELECT * 
					FROM online 
					WHERE user_ip = ? and pseudo_id = ?");
				$req_ip_exist->execute(array($ip_user, $_SESSION['id']));
					
				$req_ip_exist = $req_ip_exist->fetch();
				
				$req = $DB->prepare("UPDATE user SET date_connection = ? WHERE id = ?");
				$req->execute(array($update_date_connection, $_SESSION['id']));
				
				$_SESSION['date_connection'] = $update_date_connection;
				
				if(!isset($req_ip_exist['id'])){
					$req = $DB->prepare("INSERT INTO online (time, user_ip, pseudo_id) VALUES (?, ?, ?)");
					$req->execute(array($temps_actuel, $ip_user, $_SESSION['id'])); 
				
				}else{
					$req = $DB->prepare("UPDATE online SET time = ? WHERE id = ?");
					$req->execute(array($temps_actuel, $req_ip_exist['id']));
				}
			}
			
			$session_delete_time = $temps_actuel - $temps_session;
			
			$del_ip = $DB->prepare("DELETE FROM online WHERE time < ?");
			$del_ip->execute(array($session_delete_time));
		}	
	}