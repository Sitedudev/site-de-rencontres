<?php
	class User {

		public function __construct(){
		}
		
		public function getID($p_guid){
			global $DB;
			
			$p_guid = (String) htmlentities(trim($p_guid));
			
			$req = $DB->prepare("SELECT id 
				FROM user
				WHERE id = ?");
			$req->execute(array($guid));

			$req = $req->fetch();
			
			return $req['id'];
		}
		
		public function verif_connexion($p_mail, $p_psw, $p_remember){
			global $DB;
			global $__Crypt_password;
			global $__Crypto;
			global $__Secret__key;
			
			$p_mail = (String) htmlentities(trim($p_mail));
			$p_psw = (String) trim($p_psw);
			$p_remember = (int) trim($p_remember);
			
			$valid = true;
			$r_mail = null;
			$r_psw = null;
			
			if(!isset($p_mail) || empty($p_mail)){
				$valid = false;
				$r_mail = "Veuillez rensigner ce champ";
			}
				
			if(!isset($p_psw) || empty($p_psw)){
				$valid = false;
				$r_psw = "Veuillez rensigner ce champ";
			}
			
			
			$req = $DB->prepare("SELECT * 
				FROM user 
				WHERE mail = ? AND password = ?");
			$req->execute(array($p_mail, $__Crypt_password->password($p_psw)));
			
			$signin = $req->fetch();
			
			if(!isset($signin['id'])){
				$valid = false;
				$r_mail = "Le mail ou le mot de passe est incorrecte.";
				
			}elseif(isset($signin['id']) && $signin['state'] > 1){
				$valid = false;
				$r_mail = "Votre compte a été bloqué, veuillez essayer plus tard.";
			}
			
			if($valid){
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
					
				if(isset($p_remember)){			
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
				return array($r_mail, $r_psw);	
			}
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
		
		public function verif_pseudo($pseudo){
			global $DB;
			
			$message = null;
			
			if(empty($pseudo)){
				$message = ("Le nom d'utilisateur ne peut pas être vide");
				
			}elseif(iconv_strlen($pseudo) < 3){
				$message = ("Le nom d'utilisateur doit être compris entre 3 et 20 caractères");
			
			}elseif(iconv_strlen($pseudo) > 20){
				$message = ("Le nom d'utilisateur doit être compris entre 3 et 20 caractères");
				
			}elseif(!preg_match("/^[\p{L}0-9-]+$/u", $pseudo)){
				$message = ("Les caractères acceptés sont les lettres minuscules et majuscules, les chiffres et le tiret.");
			
			}else{
				if(isset($_SESSION['id'])){
					$req = $DB->query("SELECT id 
						FROM user 
						WHERE pseudo = ? AND id <> ?",
						array($pseudo, $_SESSION['id']));
				}else{
					$req = $DB->query("SELECT id 
						FROM user 
						WHERE pseudo = ?",
						array($pseudo));
				}
				
				$req = $req->fetch();
				
				if (isset($req['id'])){
					$message = "Ce pseudo est déjà pris";
				}
			}	
			
			return $message;
		}
		
		public function verif_mail($mail){
			global $DB;
			
			$valid = true;
			$r_mail = null;
			
			if(empty($mail)){
				$valid = false;
				$r_mail = "Veuillez renseigner ce champ";
				
			}elseif(!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $mail)) {
				$valid = false;
				$r_mail = "Le mail n'est pas valide";
			
			}else{
			
				$req = $DB->prepare("SELECT id 
					FROM user 
					WHERE mail = ?");
					
				$req->execute(array($mail));
				
				$req = $req->fetch();
				
				if(isset($req['id'])){
					$valid = false;
					$r_mail = "Ce mail existe déjà";
				}
			}
			
			if(!$valid){
				return array(false, $r_mail);
			}else{
				return array(true, $r_mail);
			}
		}
		
		public function verify_password($password, $password_confirm){
			
			global $DB;
			
			$message = null;
			
			// ---- verif password
			if(empty($password)) {
				$message = "Le mot de passe ne peut pas être vide";
			
			}elseif(strlen($password) < 7) {
				$message = "Le mot de passe doit faire plus de 7 caractères";
				
			}elseif($password != $password_confirm){
				$message = "La confirmation du mot de passe ne correspond pas";
			}
			
			return $message;
		}
	}
?>