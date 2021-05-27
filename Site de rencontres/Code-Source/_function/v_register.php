<?php
	/* ---- Plan
		
		0. Pseudo
		1. Mail
	
	*/
	
	if(!isset($_SESSION['guid'])){
		
		if(isset($_GET['type'])){
			
						
			$type = $_GET['type'];
			
			switch ($type) {
			    case 0: // treatment pseudo
			       		        
			        $r_lgn = trim(urldecode($_GET['r_lgn']));
					
			        if(empty($r_lgn)){
						echo ("Le pseudo ne peut être vide");

					}elseif(iconv_strlen($r_lgn) < 3){
						echo ("Le pseudo doit être compris entre 3 et 20 caractères");
					
					}elseif(iconv_strlen($r_lgn) > 20){
						echo ("Le pseudo doit être compris entre 3 et 20 caractères");
						
					}elseif(!preg_match("/^[\p{L}0-9- ]*$/u", $r_lgn)){
						echo ("Caractères acceptés : a à z, A à Z, 0 à 9, -, espace.");

					}else{
						echo ("true");
					}
					
					
			        break;
			    case 1:
			        
			        $r_mail = trim(mb_strtolower(urldecode($_GET['r_mail'])));
	    
				    if (empty($r_mail)) {
				        echo ("Il faut un mail");
				        
				    }elseif (!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $r_mail)) {
				        echo ("Mail non valide");
				        
				    }else{
						echo ("true");
					}
				    			   			        
			        break;
			    case 2:
						
						
							
			        break;
			}
		}
	}