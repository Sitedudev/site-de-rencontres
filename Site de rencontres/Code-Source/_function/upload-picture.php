<?php
	
	if (isset($_FILES['file']) and !empty($_FILES['file']['name'])) {
				
		$filename = $_FILES['file']['tmp_name'];
		
		list($width_orig, $height_orig) = getimagesize($filename);
		if($width_orig >= 500 && $height_orig >= 500 && $width_orig <= 6000 && $height_orig <= 6000){

	        $ListeExtension = array('jpg' => 'image/jpeg', 'jpeg'=>'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
			$ListeExtensionIE = array('jpg' => 'image/pjpg', 'jpeg'=>'image/pjpeg');
			$tailleMax = 5242880; // Taille maximum 5 Mo
			// 2mo  = 2097152
            // 3mo  = 3145728
            // 4mo  = 4194304
            // 5mo  = 5242880
            // 7mo  = 7340032
            // 10mo = 10485760
            // 12mo = 12582912
			$extensionsValides = array('jpg','jpeg'); // Format accepté
	        
			if ($_FILES['file']['size'] <= $tailleMax){ // Si le fichier et bien de taille inférieur ou égal à 5 Mo
		        
				$extensionUpload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1)); // Prend l'extension après le point, soit "jpg, jpeg ou png"
		        
				if (in_array($extensionUpload, $extensionsValides)){ // Vérifie que l'extension est correct
			        
			        $dossier = "public/pictures/" . $_SESSION['guid'] . "/"; // On se place dans le dossier de la personne 
			        
			        if (!is_dir($dossier)){ // Si le nom de dossier n'existe pas alors on le crée
				        mkdir($dossier);
			        }
				
					$dossier = "public/pictures/" . $_SESSION['guid'] . "/small/";
					if (!is_dir($dossier)){ // Si le nom de dossier n'existe pas alors on le crée
			   			mkdir($dossier);
			        }
			        
			        $nom = md5(uniqid(rand(), true)); // Permet de générer un nom unique à la photo
					$chemin = "public/pictures/" . $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload; // Chemin pour placer la photo
					$resultat = move_uploaded_file($_FILES['file']['tmp_name'], $chemin); // On fini par mettre la photo dans le dossier
			        
					if ($resultat){ // Si on a le résultat alors on va comprésser l'image
						
						if (is_readable("public/pictures/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload)) {
								
							$verif_ext = getimagesize("public/pictures/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload);
							
							// Vérification des extensions avec la liste des extensions autorisés
							if($verif_ext['mime'] == $ListeExtension[$extensionUpload]  || $verif_ext['mime'] == $ListeExtensionIE[$extensionUpload]){				
								
								// J'enregistre le chemin de l'image dans filename
								$filename = "public/pictures/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload;
								
								// Vérification des extensions que je souhaite prendre
								if($extensionUpload == 'jpg' || $extensionUpload == 'jpeg' || $extensionUpload == "pjpg" || $extensionUpload == 'pjpeg'){
                    				
                   					$image = imagecreatefromjpeg($filename);
                   					$image2 = imagecreatefromjpeg($filename);
                				}
								
								// Définition de la largeur et de la hauteur maximale
								$width = 1080; $width2 = 500;
								$height = 1080; $height2 = 500;

								list($width_orig, $height_orig) = getimagesize($filename);

								$whFact = $width / $height;
								$imgWhFact = $width_orig / $height_orig;
								 
								if($whFact <= $imgWhFact){
								    $width = $width;
								    $height = $width / $imgWhFact;
								     
								}else{
								    $height = $height;
								    $width = $height * $imgWhFact;
								} 
								
								// Redimensionnement
								$image_p = imagecreatetruecolor($width, $height);
								imagealphablending($image_p, false);
								imagesavealpha($image_p, true);
								
								$image_p2 = imagecreatetruecolor($width2, $height2);
								imagealphablending($image_p2, false);
								imagesavealpha($image_p2, true);

								
								// Cacul des nouvelles dimensions
								$point2 = 0;							
								$ratio = null;
								if($width_orig <= $height_orig){
									$ratio = $width2 / $width_orig;
								}else if($width_orig > $height_orig){
									$ratio = $height2 / $height_orig;

								}
								
								$width2 = ($width_orig * $ratio) + 1;
								$height2 = ($height_orig * $ratio) + 1;
								
								imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
								
								imagecopyresampled($image_p2, $image2, 0, 0, $point2, 0, $width2, $height2, $width_orig, $height_orig);
								
								imagedestroy($image); imagedestroy($image2);
								
								
								if($extensionUpload == 'jpg' || $extensionUpload == 'jpeg' || $extensionUpload == "pjpg" || $extensionUpload == 'pjpeg'){
								
									// Content type
									header('Content-Type: image/jpeg');
								
									$exif = exif_read_data($filename);
									if(!empty($exif['Orientation'])) {
										switch($exif['Orientation']) { 
											case 8:
												$image_p = imagerotate($image_p,90,0);
												$image_p2 = imagerotate($image_p2,90,0);
											break;
											case 3:
												$image_p = imagerotate($image_p,180,0);
												$image_p2 = imagerotate($image_p2,180,0);

											break;
											case 6:
												$image_p = imagerotate($image_p,-90,0);
												$image_p2 = imagerotate($image_p2,-90,0);

											break;
										}
									}
									// Affichage
									imagejpeg($image_p,"public/pictures/". $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload, 75);
									imagejpeg($image_p2,"public/pictures/" . $_SESSION['guid'] . "/small/" . $nom . "." . $extensionUpload, 75);
									imagedestroy($image_p); imagedestroy($image_p2);
								}
								
								$_SESSION['flash']['success'] = "Nouvel avatar enregistré !";
								header('Location: ' . URL . 'settings.php');
								exit;
								
								////////////////FIN DE COMPRÉSSION DE L'IMAGE ENREGISTRÉE////////////////
								
							}else{
                                $_SESSION['flash']['warning'] = "Le type MIME de l'image n'est pas bon";
                            }
						}			        
					}else
						$_SESSION['flash']['error'] = "Erreur lors de l'importation de votre photo.";
			        
				}else
					$_SESSION['flash']['warning'] = "Votre photo doit être au format jpg.";
		        
			}else
				$_SESSION['flash']['warning'] = "Votre photo de profil ne doit pas dépasser 5 Mo !";
		}else
			$_SESSION['flash']['warning'] = "Dimension de l'image minimum 400 x 400 et maximum 6000 x 6000 !";
	}else
		$_SESSION['flash']['warning'] = "Veuillez mettre une image !";