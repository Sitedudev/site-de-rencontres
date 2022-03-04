<?php
	class Image {

		private $name;
		private $type;
		private $tmp_name;
		private $error;
		private $size;

		public function __construct($p_file = ''){
			
			if(!isset($p_file)){
				return [];
			}

			if(isset($p_file['name'])){
				$this->name = $p_file['name'];	
			}
			
			if(isset($p_file['type'])){
				$this->type = $p_file['type'];
			}

			if(isset($p_file['tmp_name'])){
				$this->tmp_name = $p_file['tmp_name'];
			}

			if(isset($p_file['error'])){
				$this->error = $p_file['error'];
			}

			if(isset($p_file['size'])){
				$this->size = $p_file['size'];
			}
		}

		public function upload_img(){
			global $DB;
						
			if (!isset($this->name)) {
				$_SESSION['flash']['warning'] = "Veuillez mettre une image !";
				header('Location:' . CURRENT_URL);
				exit;
			}

			// Récupération de la largeur et de la hauteur de l'image			
			list($width_orig, $height_orig) = getimagesize($this->tmp_name);

			// L'image doit faire mini 500x500 et maxi 6000x6000
			$tailleMini = 500;

			if($width_orig < 500 && $height_orig < 500 && $width_orig > 6000 && $height_orig > 6000){
				$_SESSION['flash']['warning'] = "Dimension de l'image minimum 400 x 400 et maximum 6000 x 6000 !";
				header('Location:' . CURRENT_URL);
				exit;
			}

			// Taille maximum lors de l'importation de l'image 5 Mo
			// En local
			$tailleMax = 5242880; 
			// 2mo  = 2097152
            // 3mo  = 3145728
            // 4mo  = 4194304
            // 5mo  = 5242880
            // 7mo  = 7340032
            // 10mo = 10485760
            // 12mo = 12582912
			
			// Si le fichier et bien de taille inférieur ou égal à 5 Mo
			if ($this->size > $tailleMax){ 
				$_SESSION['flash']['warning'] = "Votre photo de profil ne doit pas dépasser 5 Mo !";	        
				header('Location:' . CURRENT_URL);
				exit;
			}

			// Liste des formats qu'on accepte 
			$extensionsValides = ['jpg', 'jpeg', 'png']; 

			// Prend l'extension après le point, soit "jpg, jpeg ou png"
			$extensionUpload = strtolower(substr(strrchr($this->name, '.'), 1)); 
	        
			if (!in_array($extensionUpload, $extensionsValides)){ // Vérifie que l'extension est correct
				$_SESSION['flash']['warning'] = "Votre photo doit être au format jpg, jpeg ou png.";
				header('Location:' . CURRENT_URL);
			}	    						    	    
			
			// On se place dans le dossier de la personne 
	        $dossier = "../public/pictures/" . $_SESSION['guid'] . "/"; 
	        
	        // Si le nom de dossier n'existe pas alors on le crée
	        if (!is_dir($dossier)){ 
		        mkdir($dossier);
	        }			
					        
			// Permet de générer un nom unique à la photo
	        $nom = md5(uniqid(rand(), true)); 

	        // Chemin pour placer la photo dans le dossier définitif
			$chemin = $dossier . $nom . "." . $extensionUpload; 

			// On fini par déplacer la photo du dossier temporaire vers dans le dossier de l'utilisateur
			$resultat = move_uploaded_file($this->tmp_name, $chemin); 
					        
			// Si le déplacement à réussi alors on va comprésser l'image
			if (!$resultat){ 
				$_SESSION['flash']['error'] = "Erreur lors de l'importation de votre photo.";
				header('Location:' . CURRENT_URL);
				exit;
			}
			
			// On vérifit qu'on arrive à lire le fichier pour travailler sur ce fichier
			if (!is_readable($chemin)){
				$_SESSION['flash']['warning'] = "Le type MIME de l'image n'est pas bon";
				header('Location:' . CURRENT_URL);
				exit;
			}


			$ListeExtension = ['jpg' => 'image/jpeg', 'jpeg'=>'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
			$ListeExtensionIE = ['jpg' => 'image/pjpg', 'jpeg'=>'image/pjpeg'];
			
			// Vérification des extensions avec la liste des extensions autorisés
			if($this->type != $ListeExtension[$extensionUpload]  && $this->type != $ListeExtensionIE[$extensionUpload]){	
				$_SESSION['flash']['warning'] = "L'extension n'est pas ok";
				header('Location:' . CURRENT_URL);
				exit;
			}

			// Définition de la largeur et de la hauteur maximale
			$width = 720;
			$height = 720;

			list($width_orig, $height_orig) = getimagesize($chemin);										
			
			// Cacul des nouvelles dimensions
			$ratio_orig = $width_orig / $height_orig;

			if(($width / $height) > $ratio_orig){
				$width = round($height * $ratio_orig);
			}else{
				$height = round($width / $ratio_orig);
			}
			
			/*echo $width . '<br>ok<br>';
			echo $height;exit;*/

			// Redimensionnement
			$image_p = imagecreatetruecolor($width, $height);
			imagealphablending($image_p, false);
			imagesavealpha($image_p, true);


			// Création de l'image en fonction du type de l'extension
			if(in_array($extensionUpload, ['jpg', 'jpeg', "pjpg", 'pjpeg'])){
				$image = imagecreatefromjpeg($chemin);
			}elseif ($extensionUpload == "png"){
				$image = imagecreatefrompng($chemin);
			}
			
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
			imagedestroy($image);
			
				
			if(in_array($extensionUpload, ['jpg', 'jpeg', 'pjpg', 'pjpeg'])){
			
				// Content type
				header('Content-Type: image/jpeg');
				
				$exif = exif_read_data($chemin);
				
				if(!empty($exif['Orientation'])) {
					switch($exif['Orientation']) { 
						case 8:
							$image_p = imagerotate($image_p,90,0);
						break;
						case 3:
							$image_p = imagerotate($image_p,180,0);

						break;
						case 6:
							$image_p = imagerotate($image_p,-90,0);

						break;
					}
				}
				
				// Affichage
				imagejpeg($image_p, $chemin, 75);
				imagedestroy($image_p);
				
			}elseif ($extensionUpload == "png"){
				header('Content-Type: image/png');

				imagepng($image_p, $chemin, 7);
				imagedestroy($image_p);
			}										

			$req = $DB->prepare("INSERT INTO picture (pseudo_id, name, date_upload) 
					VALUES (?, ?, ?)");

			$req->execute(array($_SESSION['id'], ($nom . "." . $extensionUpload), date('Y-m-d H:i:s')));


			$_SESSION['flash']['success'] = "Nouvel image dans l'album !";
			header('Location:' . CURRENT_URL);
			exit;
		}

		public function delete_img($p_NameImg){
			global $DB;

			$p_NameImg = (String) trim($p_NameImg);

			if(!isset($p_NameImg)){
				$_SESSION['flash']['error'] = "Il n'y a pas d'image a supprimer";
				header('Location:' . CURRENT_URL);
				exit;
			}
				
			$req = $DB->prepare("SELECT id, name 
				FROM picture 
				WHERE pseudo_id = ? AND name = ?");

	    	$req->execute([$_SESSION['id'], $p_NameImg]);
	    		 
	    	$imageBD = $req->fetch();
	    	
	    	if(!isset($imageBD['id'])){
	    		$_SESSION['flash']['warning'] = "L'image a déjà été supprimé";
				header('Location:' . CURRENT_URL);
				exit;
	    	}
	    	
	    	// Permet de supprimer une image dans un dossier 
			if(file_exists("../public/pictures/" . $_SESSION['guid'] . "/" . $imageBD['name'])){
				unlink("../public/pictures/" . $_SESSION['guid'] . "/" . $imageBD['name']);
			}
			
			$req = $DB->prepare("DELETE FROM picture 
				WHERE pseudo_id = ? AND name = ?");
			
			$req->execute([$_SESSION['id'], $imageBD['name']]);
	    	
			
			$_SESSION['flash']['success'] = "Votre image a été supprimé de l'album !";
			header('Location:' . CURRENT_URL);
			exit;


		}		
	}
?>