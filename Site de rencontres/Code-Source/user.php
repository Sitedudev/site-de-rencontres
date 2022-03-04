<?php
	class User {

		private $id;
		public $guid;
		private $pseudo;		
		private $email;
		private $role;
		
		private $valid;
		private $err_pseudo;
		private $err_mail;
		private $err_password;
		private $err_birthday;
		private $err_sex;
		private $err_ville;

		public function __construct($guid = 0){

			global $DB;

			$guid = (String) $guid;

			if($guid <= 0){
				return [];
			}
			
			$req = $DB->prepare("SELECT id, pseudo, guid, mail, avatar, role
				FROM user
				WHERE guid = ?");

			$req->execute([$guid]);

			$req = $req->fetch();

			if(!isset($req['id'])){
				return [];
			}
			
			$this->id = $req['id'];
			$this->pseudo = $req['pseudo'];
			$this->guid = $req['guid'];
			$this->email = $req['mail'];
			$this->avatar = $req['avatar'];
			$this->role = $req['role'];
		}
		
		public function getID(){
			return $this->id;
		}

		public function getPseudo(){
			return $this->pseudo;
		}

		public function getRole(){
			return $this->role;
		}

		public function form_inscription($p_pseudo, $p_mail, $p_psw, $p_confpsw, $p_ville, $p_sex, 
			$p_day, $p_month, $p_year){
			global $DB;
			global $__GUID;
			global $__Crypt_password;
			
			$p_pseudo	= (String) trim($p_pseudo);
			$p_mail		= (String) strtolower(trim($p_mail));
			$p_psw 		= (String) trim($p_psw);
			$p_confpsw 	= (String) trim($p_confpsw);
			$p_ville	= (String) trim($p_ville);
			$p_sex 		= (int) trim($p_sex);
			$p_day 		= (int) trim($p_day);
			$p_month 	= (int) trim($p_month);
			$p_year 	= (int) trim($p_year);

			$code_dep 	= (String) '';
			$id_ville	= (int) 0;

			$this->err_pseudo = (String) '';
			$this->err_mail = (String) '';
			$this->err_password = (String) '';
			$this->err_birthday = (String) '';
			$this->err_sex = (String) '';
			$this->err_ville = (String) '';

			$this->valid = (boolean) true;

			$this->verif_pseudo($p_pseudo);
			$this->verif_mail($p_mail);
			$this->verify_password($p_psw, $p_confpsw);
			$this->verify_birthday($p_day, $p_month, $p_year);
			$this->verify_sex($p_sex);
			[$code_dep, $id_ville] = $this->verify_ville($p_ville);

			if($this->valid){
				
				$crytp_password = $__Crypt_password->crypt_pass($p_psw);

				$unique_guid = $__GUID->check_guid();	
				
				$date_registration_connection = date('Y-m-d H:i:s');
				
				$req = $DB->prepare("INSERT INTO user (guid, pseudo, sexe, birthday, departement_code, 
					ville_id, mail, password, date_registration, date_connection) VALUES 
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					
				$req->execute([$unique_guid, $p_pseudo, $p_sex, ($p_year . "-" . $p_month . "-" . $p_day), 
					$code_dep, $id_ville, $p_mail, $crytp_password, $date_registration_connection, $date_registration_connection]);
				
				$_SESSION['flash']['info'] = "Votre compte a été créé";
				header('Location:' . CURRENT_URL);
				exit;
			}	

			return [$this->err_pseudo, $this->err_mail, $this->err_password, $this->err_birthday, 
				$this->err_sex, $this->err_ville];

		}
		
		public function form_connexion($p_mail, $p_psw, $p_remember){
			global $DB;
			global $__Crypt_password;
			global $__Crypto;
			global $__Secret__key;
			
			$p_mail = (String) htmlentities(trim($p_mail));
			$p_psw = (String) trim($p_psw);
			$p_remember = (String) trim($p_remember);

			$this->err_mail = (String) '';
			$this->err_password = (String) '';

			$this->valid = (boolean) true;
			
			if(!isset($p_mail) || empty($p_mail)){
				$this->valid = false;
				$this->err_mail = "Veuillez rensigner ce champ";
			}
				
			if(!isset($p_psw) || empty($p_psw)){
				$this->valid = false;
				$this->err_mail = "Veuillez rensigner ce champ";
			}
			
			
			$req = $DB->prepare("SELECT * 
				FROM user 
				WHERE mail = ?");
			$req->execute([$p_mail]);
	
			$signin = $req->fetch();
			
			if(!isset($signin['id'])){
				$this->valid = false;
				$this->err_mail = "Le mail ou le mot de passe est incorrecte.";
			
			}elseif(!password_verify($p_psw, $signin['password'])){
				$this->valid = false;
				$this->err_mail = "Le mail ou le mot de passe est incorrecte.";

			}elseif(isset($signin['id']) && $signin['state'] > 1){
				$this->valid = false;
				$this->err_mail = "Votre compte a été bloqué, veuillez essayer plus tard.";
			}
			
			if($this->valid){
				$update_date_connection = date('Y-m-d H:i:s');
				
				if ($signin['state'] == 1){
					$req = $DB->prepare("UPDATE user SET date_connection = ?, state = ? WHERE id = ?");
					$req->execute(array($update_date_connection, 0, $signin['id']));
				
				}else{
					$req = $DB->prepare("UPDATE user SET date_connection = ? WHERE id = ?");
					$req->execute(array($update_date_connection, $signin['id']));
				}	
								
				$_SESSION['id']					= $signin['id']; // id pour les requêtes 
				$_SESSION['guid'] 				= $signin['guid']; // id public
				$_SESSION['pseudo'] 			= $signin['pseudo'];
				$_SESSION['sexe'] 				= $signin['sexe'];
				$_SESSION['birthday'] 			= $signin['birthday'];
				$_SESSION['mail'] 				= $signin['mail'];
				$_SESSION['avatar']				= $signin['avatar'];
				$_SESSION['theme']				= $signin['theme'];	
				$_SESSION['date_connection'] 	= $update_date_connection;	
				$_SESSION['role']				= $signin['role'];	
					
				if(isset($p_remember) && !empty($p_remember)){	
					setcookie("comail", urlencode($_SESSION['mail']), time()+60*60*24*100, "/");  
					setcookie("copassword", $__Crypto::encryptWithPassword($p_psw, $__Secret__key), time()+60*60*24*100, "/");  
				} else {
					setcookie("comail", NULL , -1, "/");  
					setcookie("copassword", NULL , -1, "/");  
				}
				
				if ($signin['state'] == 1){
					$_SESSION['flash']['info'] = "Bon retour " . $_SESSION['pseudo'];
				}	
				header('Location:' . CURRENT_URL);
				exit;
			
			}else{
				return [$this->err_mail, $this->err_password];	
			}
		}

		public function form_modification_user($p_pseudo, $p_mail){
			global $DB;

			$p_pseudo = (String) trim($p_pseudo);
			$p_mail = (String) trim($p_mail);
			

			$this->err_pseudo = (String) '';
			$this->err_mail = (String) '';

			$this->valid = (boolean) true;

			$this->verif_pseudo($p_pseudo);
			$this->verif_mail($p_mail);

			if($this->valid){
				$req = $DB->prepare("UPDATE user SET pseudo = ?, mail = ? 
					WHERE guid = ?");
				$req->execute(array($p_pseudo, $p_mail, $_SESSION['guid']));	
				
				$_SESSION['pseudo'] = $p_pseudo;
				$_SESSION['mail'] 	= $p_mail;
				
				$_SESSION['flash']['success'] = "Modifications du profil effectuées !";
				header('Location:' . CURRENT_URL);
				exit;
			}

			return [$this->err_pseudo, $this->err_mail];
		}

		public function form_change_password($p_oldpsd, $p_newpsd, $p_confpsw){
			global $DB;
			global $__Crypt_password;
			global $__Email;

			$p_oldpsd = trim($p_oldpsd);
			$p_newpsd = trim($p_newpsd);
			$p_confpsw = trim($p_confpsw);

			$this->err_password = (String) '';
			
			$this->valid = (boolean) true;
			
			if(empty($p_oldpsd)){
				$this->valid = false;
				$this->$err_password = "Il faut renseigner votre mot de passe actuel";
			
			}elseif(!$this->getPassword($p_oldpsd)){
				$this->valid = false;
				$this->err_password = "Le mot de passe actuel n'est correct";

			}else{
				$this->verify_password($p_newpsd, $p_confpsw);
			}

			if ($this->valid){

				$crytp_password = $__Crypt_password->crypt_pass($p_newpsd);

				$req = $DB->prepare("UPDATE user SET password = ? WHERE guid = ?");
				$req->execute(array($crytp_password, $_SESSION['guid']));
				
				//$__Email->change_password_mail($_SESSION['pseudo'], $newpsd, $_SESSION['mail']);

				$_SESSION['flash']['success'] = "Votre mot de passe a bien été changé";
				header('Location:' . CURRENT_URL);
				exit;
			}

			return [$this->err_password];

		}

		public function getPassword($p_password){
			global $DB;

			if(!isset($_SESSION['id'])){
				return false;
			}

			$req = $DB->prepare("SELECT id, password
				FROM user 
				WHERE id = ?");

			$req->execute([$_SESSION['id']]);

			$req = $req->fetch();

			if(!$req){
				return false;
			}

			if(!password_verify($p_password, $req['password'])){
				return false;
			}

			return true;
		}
		
		public function getAvatar($p_guid){
			global $DB;
			
			$chemin_avatar = NULL;
			
			$req = $DB->prepare("SELECT guid, avatar, sexe
				FROM user 
				WHERE guid = ?");
				
			$req->execute(array($p_guid));
			
			$req = $req->fetch();
			
			if(isset($req['guid']) && (file_exists(__DIR__ . "/../public/avatars/" . $req['guid'] . "/" . $req['avatar']))){
				$chemin_avatar = "public/avatars/" . $req['guid'] . "/" . $req['avatar'] ; 
			}elseif($req['sexe'] == 1){
				$chemin_avatar = "public/avatars/defaults/man.svg";
			}else{
				$chemin_avatar = "public/avatars/defaults/women.svg";
			}
			
			return $chemin_avatar;
		}
		
		public function verif_pseudo($p_pseudo){
			global $DB;
			global $__Insulte;

			$p_pseudo = trim($p_pseudo);
			
			if(empty($p_pseudo)){
				$this->valid = false;
				$this->err_pseudo = "Le nom d'utilisateur ne peut pas être vide";
				
			}elseif(grapheme_strlen($p_pseudo) < 4){
				$this->valid = false;
				$this->err_pseudo = "Le pseudo doit être compris entre 4 et 20 caractères";
			
			}elseif(grapheme_strlen($p_pseudo) > 20){
				$this->valid = false;
				$this->err_pseudo = "Le pseudo doit faire moins de 21 caractères. (" . grapheme_strlen($p_pseudo) . "/21)";
				
			}elseif(!preg_match("/^[\p{L}0-9-]+$/u", $p_pseudo)){
				$this->valid = false;
				$this->err_pseudo = "Les caractères acceptés sont les lettres minuscules et majuscules, les chiffres et le tiret.";

			}elseif($__Insulte->insulte(strtolower($p_pseudo))){
				$this->valid = false;
				$this->err_pseudo = "Le pseudo ne peut pas être une insulte";
			}else{
				if(isset($_SESSION['id'])){
					$req = $DB->prepare("SELECT id 
						FROM user 
						WHERE pseudo = ? AND id <> ?");
						
					$req->execute([$p_pseudo, $_SESSION['id']]);
				}else{
					$req = $DB->prepare("SELECT id 
						FROM user 
						WHERE pseudo = ?");
					
					$req->execute([$p_pseudo]);
				}
				
				$req = $req->fetch();
				
				if (isset($req['id'])){
					$this->valid = false;
					$this->err_pseudo = "Ce pseudo est déjà pris";
				}
			}	
		}
		
		public function verif_mail($p_mail){
			global $DB;

			$p_mail = strtolower(trim($p_mail));
			
			if(empty($p_mail)){
				$this->valid = false;
				$this->err_mail = "Veuillez renseigner ce champ";
				
			}elseif(!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $p_mail)) {
				$this->valid = false;
				$this->err_mail = "Le mail n'est pas valide";
			
			}else{
				if(isset($_SESSION['id'])){
					$req = $DB->prepare("SELECT id 
						FROM user 
						WHERE mail = ? AND id <> ?");

					$req->execute([$p_mail, $_SESSION['id']]);
				}else{
					$req = $DB->prepare("SELECT id 
						FROM user 
						WHERE mail = ?");
						
					$req->execute(array($p_mail));
				}

				$req = $req->fetch();

				if(isset($req['id'])){
					$this->valid = false;
					$this->err_mail = "Ce mail existe déjà";
				}
			}
		}
		
		public function verify_password($p_password, $p_password_confirm){
			
			// Il faut au minimum : 
			//  - 1 Majuscule
			//  - 1 Minuscule
			//  - 1 chiffre
			//  - 1 caractère spécial
			$uppercase = preg_match('@[A-Z]@', $p_password);
			$lowercase = preg_match('@[a-z]@', $p_password);
			$number    = preg_match('@[0-9]@', $p_password);
			$specialChars = preg_match('@[^\w]@', $p_password);

			if(empty($p_password)) {
				$this->valid = false;
				$this->err_password = "Le mot de passe ne peut pas être vide";
			
			}elseif(grapheme_strlen($p_password) < 7) {
				$this->valid = false;
				$this->err_password = "Le mot de passe doit faire plus de 7 caractères";
				
			}elseif(!$uppercase){
				$this->valid = false;
				$this->err_password = "Le mot de passe doit contenir au minimum 1 majuscule";

			}elseif(!$lowercase){
				$this->valid = false;
				$this->err_password = "Le mot de passe doit contenir au minimum 1 minuscule";

			}elseif(!$number){
				$this->valid = false;
				$this->err_password = "Le mot de passe doit contenir au minimum 1 chiffre";

			}elseif(!$specialChars){
				$this->valid = false;
				$this->err_password = "Le mot de passe doit contenir au minimum 1 caractère spéciale";

			}elseif($p_password != $p_password_confirm){
				$this->valid = false;
				$this->err_password = "La confirmation du mot de passe ne correspond pas";
			}
		}

		public function verify_birthday($p_day, $p_month, $p_year){

			if((!isset($p_day) && empty($p_day)) || (!isset($p_month) && empty($p_month)) || (!isset($p_year) && empty($p_year))){
				$this->valid = false;
				$this->err_birthday = "Entrez une date de naissance valide";
	
			}elseif(($p_day < 0 || $p_day > 31) || !preg_match("/^[0-9]{1,2}$/u", $p_day)){
				$this->valid = false;
				$this->err_birthday = "Le jour est compris entre 1 et 31";
				
			}elseif(($p_month < 0 || $p_month > 12) || !preg_match("/^[0-9]{1,2}$/u", $p_month)){
				$this->valid = false;
				$this->err_birthday = "Le mois est compris entre 1 et 12";
				
			}elseif(($p_year < date('Y', strtotime(date('Y-m-d') . '-80 years')) || $p_year > date('Y', strtotime(date('Y-m-d') . '-18 years'))) || !preg_match("/^[0-9]{4}$/u", $p_year)){
				$this->valid = false;
				$this->err_birthday = "L'année est compris entre " . date('Y', strtotime(date('Y-m-d') . '-80 years')) . " et " . date('Y', strtotime(date('Y-m-d') . '-18 years')) . "";
				
			}elseif(!checkdate($p_month, $p_day, $p_year)){
				$this->valid = false;
				$this->err_birthday = "Entrez une date de naissance valide";
				
			}
		}

		public function verify_sex($p_sex){

			if(!in_array($p_sex, [1, 2, 3])) {
				$this->valid = false;
				$this->err_sex = "Ce champ ne peut pas être vide";
			}
		}

		public function verify_ville($p_ville){
			global $DB;

			if(empty($p_ville)){
				$this->valid = false;
				$this->err_ville = "Veuillez renseigner une ville";
				
			}else{
				
				$rt_ville = explode(", ", $p_ville);
				
				if(!in_array(count($rt_ville), array(1, 2))){
					$this->valid = false;
					$this->err_ville = "La ville n'existe pas";
				}else{
					switch(count($rt_ville)){
						case 1:
							$ville_name = $rt_ville[0];
														
							$req_ville = $DB->prepare("SELECT v.ville_nom_reel as a, d.departement_nom as b, v.ville_id as c, d.departement_code as d 
									FROM villes_france v, departement d 
									WHERE d.departement_code = v.ville_departement AND ville_nom_reel = ? 
									LIMIT 1");
							$req_ville->execute(array($ville_name));
							
							$req_ville = $req_ville->fetch();
							
							if (isset($req_ville['a']) && isset($req_ville['b'])){
								$id_ville = $req_ville['c'];
								$code_dep = $req_ville['d'];

								return [$code_dep, $id_ville];
							}else{
								$this->valid = false;
								$this->err_ville = "La ville n'existe pas";
							}
						break;
						
						case 2:
							$ville_name = $rt_ville[0];
							$dep_name = $rt_ville[1];
							
							$req_ville = $DB->prepare("SELECT v.ville_nom_reel as a, d.departement_nom as b, v.ville_id as c, d.departement_code as d 
									FROM villes_france v, departement d 
									WHERE d.departement_code = v.ville_departement AND ville_nom_reel = ? 
									AND d.departement_nom = ? 
									LIMIT 1");
							$req_ville->execute(array($ville_name, $dep_name));
							
							$req_ville = $req_ville->fetch();
							
							if (isset($req_ville['a']) && isset($req_ville['b'])){
								$id_ville = $req_ville['c'];
								$code_dep = $req_ville['d'];

								return [$code_dep, $id_ville];
							}else{
								$this->valid = false;
								$this->err_ville = "La ville n'existe pas";
							}
						break;
						
						default:
							$this->valid = false;
							$this->err_ville = "La ville n'existe pas";
						break;
					}
				}
			}
		}
	}
?>