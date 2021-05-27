<?php
	
	/**
	* Crypt password
	*/
	class Password {
		
		public function __construct(){
			
		}
		
		public function password($p_chaine){			
			$p_chaine = crypt($p_chaine, '$6$rounds=5000$plfEsPZnDPOgYebFp3bCtMKkZsK2JkTCssQUY5fDqmD8SvAeWoM+MDVKr7hKNcKjaAAIwo3DpcKDKsK4QMKic81azeY4Mo6IwFZ=$');
			return $p_chaine;
		}
		
		public function age($date_naissance){
		    $arr1 = explode('-', $date_naissance);
		    $arr2 = explode('-', date('Y-m-d'));
				
		    if(($arr1[1] < $arr2[1]) || (($arr1[1] == $arr2[1]) && ($arr1[2] <= $arr2[2])))
		    return $arr2[0] - $arr1[0];
		
		    return $arr2[0] - $arr1[0] - 1;
		}		
		
		public function gen_reg_key(){
	
		    $key = "";   /* Initialisation de la variable $key à "vide" */
		
		    $max_length_reg_key = 10;   /* On définit la taille de la chaine (10 caractères suffit) */
		    /* On définit le type de caractères ASCII de la chaine */
		
		    $chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l",
		        "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x",
		        "y", "z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
		    $count = count($chars) - 1;  /* On comptabilise le nombre total de caractères possibles */
		    srand((double) microtime() * 1000000);  /* On initialise la fonction rand pour le tirage aléatoire des chiffres */
		    for ($i = 0; $i < $max_length_reg_key; $i++)
		        $key .= $chars[rand(0, $count)]; /* on tire aléatoirement les $max_length_reg_key carac de la chaine */
		    return($key);  /* on renvois la clé générée */
		}
		
		public function month($p_int){
			
			$monthName = "";
											
			switch ($p_int) {
				case 1:
					$monthName = "Janvier"; break;
				case 2:
					$monthName = "Février"; break;
				case 3:
					$monthName = "Mars"; break;
				case 4:
					$monthName = "Avril"; break;
				case 5:
					$monthName = "Mai"; break;
				case 6:
					$monthName = "Juin"; break;
				case 7:
					$monthName = "Juillet"; break;
				case 8:
					$monthName = "Août"; break;
				case 9:
					$monthName = "Septembre"; break;
				case 10:
					$monthName = "Octobre"; break;
				case 11:
					$monthName = "Novembre"; break;
				case 12:
					$monthName = "Décembre"; break;	
			}
			
			return $monthName;
		}
	}
	