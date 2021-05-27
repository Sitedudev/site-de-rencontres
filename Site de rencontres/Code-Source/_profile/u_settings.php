<?php
	/*
	 * @author Sitedudev
	*/
	
	include_once('../include.php');
	
	if (!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		if (isset($_POST['profile'])){		

			$r_lgn 		= trim($lgn);
			$r_mail		= strtolower(trim($mail));
			/*$r_psw 		= trim($psw);
			$r_confpsw 	= trim($confpsw);
			$r_ville 	= trim($ville);*/
			
			// ---- verif login
			if(empty($r_lgn)){
				$valid = false;
				$er_lgn = ("Le pseudo ne peut pas être vide");
				
			}elseif(iconv_strlen($r_lgn) < 3){
				$valid = false;
				$er_lgn = ("Le pseudo doit être compris entre 3 et 20 caractères");
			
			}elseif(iconv_strlen($r_lgn) > 20){
				$valid = false;
				$er_lgn = ("Le pseudo doit être compris entre 3 et 20 caractères");
				
			}elseif(!preg_match("/^[\p{L}0-9- ]*$/u", $r_lgn)){
				$valid = false;
				$er_lgn = ("Caractères acceptés : a à z, A à Z, 0 à 9, -, espace.");
				
			}elseif($__Insulte->insulte(strtolower($r_lgn))){
				$valid = false;
				$er_lgn = ("Le pseudo ne doit pas être une insulte");
				
			}
			
			// ---- verif mail
			if(empty($r_mail)){
				$valid = false;
				$er_mail = "Le mail ne peut pas être vide";
				
			}elseif(!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $r_mail)){
				$valid = false;
				$er_mail = "Le mail n'est pas valide";
				
			}else{
				$req_mail = $DB->prepare("SELECT mail 
					FROM user 
					WHERE mail = ?");
				$req_mail->execute(array('mail' => $r_mail));
				
				$req_mail = $req_mail->fetch();
				
				if ($req_mail['mail'] <> "" && $r_mail <> $_SESSION['mail']){
					$valid = false;
					$er_mail = "Le mail existe déjà";
				}
			}
			
			if(!isset($theme) && empty($theme)){
				$valid = false;
				
			}elseif($theme < 0 || $theme > 3){
				$valid = false;
			}

			if($valid){
				
				$req = $DB->prepare("UPDATE user SET pseudo = ?, mail = ?, theme = ? WHERE guid = ?");
				$req->execute(array($r_lgn, $r_mail, $theme, $_SESSION['guid']));	
				
				$_SESSION['pseudo'] = $r_lgn;
				$_SESSION['mail'] 	= $r_mail;
				$_SESSION['theme'] 	= $theme;
				
				$_SESSION['flash']['success'] = "Modifications du profil effectuées !";
				header('Location: ' . URL . 'settings');
				exit;
				
			}
			
		}elseif (isset($_POST['chgepsd'])){
			
			$oldpsd = htmlentities($oldpsd);
			$newpsd = htmlentities($newpsd);
			$cfmpsd = htmlentities($cfmpsd);
			
			
			if(!isset($oldpsd)){
				$valid = false;
				$er_oldpsd = "Il faut renseigner votre mot de passe actuel";
			
			}elseif(!isset($newpsd)){
				$valid = false;
				$er_oldpsd = "Il faut renseigner votre nouveau mot de passe";
			
			}elseif(!isset($cfmpsd)){
				$valid = false;
				$er_oldpsd = "Il faut confirmer votre nouveau mot de passe";
			
			}elseif($newpsd <> $cfmpsd){
				$valid = false;
				$er_oldpsd = "La confirmation est différente de votre nouveau mot de passe";
			}
			
			
			if ($valid){

				$req = $DB->prepare("UPDATE user SET password = ? WHERE guid = ?");
				$req->execute(array($__Crypt_password->password($newpsd), $_SESSION['guid']));
				
				$__Email->change_password_mail($_SESSION['pseudo'], $newpsd, $_SESSION['mail']);
				
				
				$_SESSION['flash']['success'] = "Votre mot de passe a bien été changé";
				header('Location: ' . URL . 'settings');
				exit;
				
			}
			
		}elseif (isset($_POST['avatar'])){	
			
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
        <title>Paramètres | Daewen</title>
        <?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
		?>
		<div class="container" style="color: #666; padding-bottom: 25px;">			
			<div class="col-12 col-md-4 col-xl-3" style="text-align: center; position: relative">
				<img src="<?= URL . $__User->getAvatar($_SESSION['guid'])?>" width="120" class="sz-image"/>
				<span class="image-upload">
					<form method="post" action="" enctype="multipart/form-data">
						<label for="file" style="margin-bottom: 0; margin-top: 5px; display: inline-flex">
						    <input id="file" type="file" name="file" class="hide-upload" required/>
						    <i class="fa fa-plus image-plus"></i>
						    <input type="submit" class="fa send-upload" name="avatar" value="&#xf093;">
						</label>
					</form>
				</span>
			</div>
			<div class="col-12 col-md-8 col-xl-9">
				<span style="font-size: 28px"><?= $_SESSION['pseudo'] ?>,</span>
				<span style="font-size: 22px"><?= $__Crypt_password->age($_SESSION['birthday']) ?> ans</span>
				<a href="<?= URL ?>profil/demandes" style="text-decoration: none; color: #666">
					<section style="border: 1px solid;border-radius: 20px;position: relative;padding: 5px 0">
						<i class="fa fa-users" style="border: 1px solid;border-radius: 100px;padding: 10px;font-size:20px;position: absolute;top: -5px;left: -12px;background: #FAFAFA;"></i>
						<?php
							$show_demande = $DB->prepare("SELECT * 
								FROM relation 
								WHERE id_to = ? AND statut = 1");
							$show_demande->execute(array($_SESSION['id']));
								
							$show_demande = $show_demande->fetchAll();
							
							$count_demande = count($show_demande);
						?>
						<span style="padding-left: 40px"><span class="badge"><?= $count_demande?></span> Gérer mes amis</span>
						<div style="position: absolute;width: 70px;height: 30px;border: 15px solid transparent;border-top-color: #666666;border-right-color: #666666;border-bottom-color: #666666;top: 0;border-radius: 0 20px 20px 0;right: -1px;"></div>
					</section>
				</a>
			</div>
		</div>
		<div class="container">
			<div class="col-12 col-md-12 col-xl-12" style="border: 1px solid #CCC; padding-top: 10px; padding-bottom: 10px; background: white">
				<form method="post">
					<label>Supprimer l'avatar</label>
					<input type="submit" class="fa trash-avatar" name="dltav" value="&#xf014;"/>
				</form>
				<form method="post">
					<label>Nom d'utilisateur</label>
					<input type="text" name="lgn" value="<?= $_SESSION['pseudo']; ?>"/>
					<div style="margin: 20px 0;font-size: 18px; color: #666">Informations privées</div>
					<label>Mail</label>
					<input type="email" name="mail" value="<?= $_SESSION['mail']; ?>"/>
					<label>Thème</label>
					<input type="submit" class="btn-send-upload" name="profile" value="Envoyer"/>
				</form>
				<form method="post">
					<div style="margin: 40px 0 20px 0;font-size: 18px; color: #666">Mot de passe </div>
					<label>Actuel</label>
					<input type="password" name="oldpsd" value="<?php if(isset($oldpsd)) echo $oldpsd; ?>" placeholder="Entrez votre mot de passe actuel"/>
					<label>Nouveau</label>
					<input type="password" name="newpsd" value="<?php if(isset($newpsd)) echo $newpsd; ?>" placeholder="Entrez votre nouveau mot de passe"/>
					<label>Confirmation</label>
					<input type="password" name="cfmpsd" value="" placeholder="Confirmez votre nouveau mot de passe"/>
					<input type="submit" class="btn-send-upload" name="chgepsd" value="Envoyer"/>
				</form>
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>