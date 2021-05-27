<?php
/**
* Give Time
*/
class Time {
	
	public function __construct(){
		
	}
	
	// Fonction pour donner la bonne date et heure
	public function give_time($date_time){
		
		$dateactu = date_create(date('Y-m-d H:i:s'));
		$dateNotif = date_create($date_time);
		$JourNotif = date_diff($dateactu, $dateNotif);
		
		
		if(($JourNotif->format('%I') == 0) && ($JourNotif->format('%H') == 0) && ($JourNotif->format('%D') == 0) && ($JourNotif->format('%M') == 0) && ($JourNotif->format('%Y') == 0)){
			if($JourNotif->format('%S') == 0 || $JourNotif->format('%S') == 1){
				$JourNotif = $JourNotif->format('%s seconde');
				
			}else{
				$JourNotif = $JourNotif->format('%s secondes');
				
			}			
		}else if(($JourNotif->format('%H') == 0) && ($JourNotif->format('%D') == 0) && ($JourNotif->format('%M') == 0) && ($JourNotif->format('%Y') == 0)){
			if($JourNotif->format('%I') == 0 || $JourNotif->format('%I') == 1){
				$JourNotif = $JourNotif->format('%i minute');
				
			}else{
				$JourNotif = $JourNotif->format('%i minutes');
				
			}			
		}else if(($JourNotif->format('%D') == 0) && ($JourNotif->format('%M') == 0) && ($JourNotif->format('%Y') == 0)){
			if($JourNotif->format('%H') == 1){
				$JourNotif = $JourNotif->format('%h heure');
				
			}else{
				$JourNotif = $JourNotif->format('%h heures');
	
			}
		}else if($JourNotif->format('%M') == 0 && ($JourNotif->format('%Y') == 0)){
			if($JourNotif->format('%D') == 1){
				$JourNotif = $JourNotif->format('%d jour');
			}else{
				$JourNotif = $JourNotif->format('%d jours');
			}
		}else if($JourNotif->format('%Y') == 0){
			$JourNotif = $JourNotif->format('%m mois');
			
		}else{
			if($JourNotif->format('%Y') == 1){
				$JourNotif = $JourNotif->format('%y an');
			}else{
				$JourNotif = $JourNotif->format('%y ans');
			}
		}
	
		return $JourNotif;
	}
	
	public function just_time($date_time){

		$date_time = date_create($date_time);
		
		$JourNotif = $date_time->format('H\hi');
		
		return $JourNotif;
	}
}
?>
