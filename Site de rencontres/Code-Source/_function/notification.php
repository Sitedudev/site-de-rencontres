<?php
	/**
	* Notifications
	*/
	class Notification {
		
	
		public function __construct(){
						
		}
		
		public function notif_mess(){
			
			$nb_mess = 0;
			
			global $DB;
			
			$nb_mess = $DB->prepare("SELECT count(m.id) as nb
				FROM user u
				INNER JOIN relation r ON (r.id_from = u.id) OR (r.id_to = u.id)
				INNER JOIN messagerie m ON (m.id_from, m.id_to) = (r.id_from, r.id_to) OR (m.id_from, m.id_to) = (r.id_to, r.id_from)
				WHERE u.id = ? AND r.statut = 2 AND m.id_to = ? AND m.lu = 1 
				ORDER BY m.date_message DESC");
				
			$nb_mess->execute(array($_SESSION['id'], $_SESSION['id']));

			$nb_mess = $nb_mess->fetch();
			
            if(isset($nb_mess['nb'])){
                $nb_mess = (int) $nb_mess['nb'];
            }else{
                $nb_mess = 0;
            }
			
			if ($nb_mess == 0){
				
				return "";
				
			}elseif($nb_mess > 999){
				
				return "+999";
					
			}else{
				
				return $nb_mess;
				
			}
		}
	}
