<?php
	include_once('../include.php');
	
	if (!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}

	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		if (isset($_POST['avatar'])){	
			
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
					    						    	    
					        $dossier = "../public/avatars/" . $_SESSION['guid'] . "/"; // On se place dans le dossier de la personne 
					        
					        if (!is_dir($dossier)){ // Si le nom de dossier n'existe pas alors on le crée
						        mkdir($dossier);
					        }			

					        $nom = md5(uniqid(rand(), true)); // Permet de générer un nom unique à la photo
							$chemin = "../public/avatars/" . $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload; // Chemin pour placer la photo
							$resultat = move_uploaded_file($_FILES['file']['tmp_name'], $chemin); // On fini par mettre la photo dans le dossier
					        
							if ($resultat){ // Si on a le résultat alors on va comprésser l'image
								
								if (is_readable("../public/avatars/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload)) {
										
									$verif_ext = getimagesize("../public/avatars/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload);
									
									// Vérification des extensions avec la liste des extensions autorisés
									if($verif_ext['mime'] == $ListeExtension[$extensionUpload]  || $verif_ext['mime'] == $ListeExtensionIE[$extensionUpload]){				
										
										// J'enregistre le chemin de l'image dans filename
										$filename = "../public/avatars/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload;
										
										// Vérification des extensions que je souhaite prendre
										if($extensionUpload == 'jpg' || $extensionUpload == 'jpeg' || $extensionUpload == "pjpg" || $extensionUpload == 'pjpeg'){
		                    				
		                   					$image2 = imagecreatefromjpeg($filename);
		                				}
										
										// Définition de la largeur et de la hauteur maximale
										$width2 = 500;
										$height2 = 500;
		
										list($width_orig, $height_orig) = getimagesize($filename);
										
										// Redimensionnement
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
										
										imagecopyresampled($image_p2, $image2, 0, 0, $point2, 0, $width2, $height2, $width_orig, $height_orig);
										
										imagedestroy($image2);
										
										
										if($extensionUpload == 'jpg' || $extensionUpload == 'jpeg' || $extensionUpload == "pjpg" || $extensionUpload == 'pjpeg'){
										
											// Content type
											header('Content-Type: image/jpeg');
										
											$exif = exif_read_data($filename);
											if(!empty($exif['Orientation'])) {
												switch($exif['Orientation']) { 
													case 8:
														$image_p2 = imagerotate($image_p2,90,0);
													break;
													case 3:
														$image_p2 = imagerotate($image_p2,180,0);
	
													break;
													case 6:
														$image_p2 = imagerotate($image_p2,-90,0);
	
													break;
												}
											}
											// Affichage
											imagejpeg($image_p2, "../public/avatars/" . $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload, 75);
											imagedestroy($image_p2);
										}
										
										$imageBD = $DB->prepare("SELECT avatar
											FROM user 
											WHERE guid = ?");
								    	$imageBD->execute(array($_SESSION['guid']));
								    		 
								    	$imageBD = $imageBD->fetch(); 
								    	
								    	$_SESSION['avatar'] = $imageBD['avatar'];
										
										if(file_exists("../public/avatars/". $_SESSION['guid'] . "/" . $_SESSION['avatar']) && isset($_SESSION['avatar'])){
											unlink("../public/avatars/" . $_SESSION['guid'] . "/" . $_SESSION['avatar']);
										}	
										
										$req = $DB->prepare("UPDATE user SET avatar = ? where id = ?");
										$req->execute(array(($nom.".".$extensionUpload), $_SESSION['id']));
											
										$_SESSION['avatar'] = ($nom.".".$extensionUpload); // On met à jour l'avatar
										
										$_SESSION['flash']['success'] = "Nouvel avatar enregistré !";
										header('Location: ' . URL . 'parametres');
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
				
		}elseif(isset($_POST['dltav'])){
			
			$imageBD = $DB->prepare("SELECT avatar 
				FROM user 
				WHERE guid = ?"); 
	    	$imageBD->execute(array($_SESSION['guid']));
	    		 
	    	$imageBD = $imageBD->fetch(); 
	    	
	    	$_SESSION['avatar'] = $imageBD['avatar']; 
			
			// Permet de supprimer une image dans un dossier 
			if(file_exists("../public/avatars/". $_SESSION['guid'] . "/" . $_SESSION['avatar']) && isset($_SESSION['avatar'])){
				
				unlink("../public/avatars/". $_SESSION['guid'] . "/" . $_SESSION['avatar']);
				rmdir("../public/avatars/". $_SESSION['guid'] . "/");
				
				$req = $DB->prepare("UPDATE user SET avatar = ? where  id = ?");
				$req->execute(array(NULL, $_SESSION['id']));
											
					$_SESSION['avatar'] = NULL; // On met à jour l'avatar	
			}
			
			$_SESSION['flash']['success'] = "Votre avatar a été supprimé !";
			header('Location: ' . URL . '/parametres');
			exit;
			
		}
	}

?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('../_head/meta.php');
		?>
        <title>Modifier avatar | Daewen</title>
        <?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
			$profil__bar__number = 4;
			include_once('head_profile.php');
		?>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-4">
					<div class="profile__param__menu__body">
						<a href="<?= URL ?>profil/modifier" class="profile__param_menu__link">
							<div class="profile__param_menu">Information sur le compte</div>
						</a>
						<a href="<?= URL ?>profil/modifier-avatar" class="profile__param_menu__link">
							<div class="profile__param_menu">Modifier ma photo de profil</div>
						</a>
						<a href="<?= URL ?>profil/modifier-mot-de-passe" class="profile__param_menu__link">
							<div class="profile__param_menu">
								Modifier mon mot de passe
							</div>
						</a>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-8">
					<div class="profile__param__mod__body">
						<div class="profile__param__form__stru">
							<label>Modifier l'avatar</label>
						</div>
						<div>
							<span class="image-upload">
								<form method="post" action="" enctype="multipart/form-data">
									<label for="file" style="margin-bottom: 0; display: flex; justify-content: flex-start; align-items: center">
									    <input id="file" type="file" name="file" class="hide-upload" required/>
									    <i class="bi bi-cloud-upload image-plus"></i>
									    <button type="submit" class="send-upload" name="avatar">Envoyer</button>
									</label>
								</form>
							</span>
						</div>
						<form method="post">
							<div class="profile__param__form__stru">
								<label>Supprimer l'avatar</label>
							</div>
							<div>
								<input type="submit" class="profil__param__form__btn__suc" name="dltav" value="Supprimer"/>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>