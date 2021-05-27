<?php
	/*
	 * @author Sitedudev
	*/
	
	include_once('../include.php');
		
	if (!isset($_SESSION['id'])){
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
					$extensionsValides = array('jpg','jpeg', 'png'); // Format accepté
			        
					if ($_FILES['file']['size'] <= $tailleMax){ // Si le fichier et bien de taille inférieur ou égal à 5 Mo
				        
						$extensionUpload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1)); // Prend l'extension après le point, soit "jpg, jpeg ou png"
				        
						if (in_array($extensionUpload, $extensionsValides)){ // Vérifie que l'extension est correct
					    						    	    
					        $dossier = "../public/pictures/" . $_SESSION['guid'] . "/"; // On se place dans le dossier de la personne 
					        
					        if (!is_dir($dossier)){ // Si le nom de dossier n'existe pas alors on le crée
						        mkdir($dossier);
					        }			
					        
					        $nom = md5(uniqid(rand(), true)); // Permet de générer un nom unique à la photo
							$chemin = "../public/pictures/" . $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload; // Chemin pour placer la photo
							$resultat = move_uploaded_file($_FILES['file']['tmp_name'], $chemin); // On fini par mettre la photo dans le dossier
					        
							if ($resultat){ // Si on a le résultat alors on va comprésser l'image
								
								if (is_readable("../public/pictures/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload)) {
										
									$verif_ext = getimagesize("../public/pictures/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload);
									
									// Vérification des extensions avec la liste des extensions autorisés
									if($verif_ext['mime'] == $ListeExtension[$extensionUpload]  || $verif_ext['mime'] == $ListeExtensionIE[$extensionUpload]){	
										
										// J'enregistre le chemin de l'image dans filename
										$filename = "../public/pictures/" . $_SESSION['guid'] . "/" .$nom . "." . $extensionUpload;
										
										// Vérification des extensions que je souhaite prendre
										if($extensionUpload == 'jpg' || $extensionUpload == 'jpeg' || $extensionUpload == "pjpg" || $extensionUpload == 'pjpeg'){
		                    				
		                   					$image2 = imagecreatefromjpeg($filename);
		                				}elseif ($extensionUpload == "png"){
			                				
			                				$image2 = imagecreatefrompng($filename);
		                				}
										
										// Définition de la largeur et de la hauteur maximale
										$width2 = 720;
										$height2 = 720;
		
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
										
											$exif = exif_read_data-bs-($filename);
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
											imagejpeg($image_p2, "../public/pictures/" . $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload, 75);
											imagedestroy($image_p2);
											
											$req = $DB->prepare("INSERT INTO picture (pseudo_id, name, date_upload) 
												VALUES (?, ?, ?)");
											$req->execute(array($_SESSION['id'], ($nom . "." . $extensionUpload), date('Y-m-d H:i:s')));
											
										}elseif ($extensionUpload == "png"){
											
											imagejpeg($image_p2, "../public/pictures/" . $_SESSION['guid'] . "/" . $nom . ".jpg", 75);
											imagedestroy($image_p2);
											
											if(file_exists("../public/pictures/" . $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload)){
												unlink("../public/pictures/" . $_SESSION['guid'] . "/" . $nom . "." . $extensionUpload);
											}
											
											$req = $DB->prepare("INSERT INTO picture (pseudo_id, name, date_upload) 
												VALUES (?, ?, ?)");
											$req->execute(array($_SESSION['id'], ($nom . ".jpg"), date('Y-m-d H:i:s')));
										}										
										
										
										$_SESSION['flash']['success'] = "Nouvel image dans l'album !";
                                        
										header('Location: ' . URL . 'profil');
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
				
		}elseif(isset($_POST['dlt'])){
			
			$nameimg = trim($nameimg);
			
			if(isset($nameimg)){
				
				$imageBD = $DB->prepare("SELECT name FROM picture WHERE pseudo_id = ? AND name = ?");
		    	$imageBD->execute(array($_SESSION['id'], $nameimg));
		    		 
		    	$imageBD = $imageBD->fetch();
		    	
		    	if(isset($imageBD['name'])){
			    	// Permet de supprimer une image dans un dossier 
					if(file_exists("../public/pictures/" . $_SESSION['guid'] . "/" . $imageBD['name'])){
						unlink("../public/pictures/" . $_SESSION['guid'] . "/" . $imageBD['name']);
					}
				
					
					$req = $DB->prepare("DELETE FROM picture WHERE pseudo_id = ? AND name = ?");
					$req->execute(array($_SESSION['id'], $imageBD['name']));
		    	}
			}
			
			$_SESSION['flash']['success'] = "Votre image a été supprimé de l'album !";
			header('Location: ' . URL . 'profile');
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
        <title>Profil | Daewen</title>
		<?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
		?>
		<div class="container" style="color: #666">
			<div class="col-12 col-md-4 col-xl-3" style="margin: 20px 0">
				<img src="<?= URL . $__User->getAvatar($_SESSION['guid']) ?>" width="120" style="width: 120px; border-radius: 100px"/>
			</div>
			<div class="col-xs-12 col-sm-8 col-md-9">
				<span style="font-size: 28px"><?= $_SESSION['pseudo'] ?>,</span>
				<span style="font-size: 22px"><?= $__Crypt_password->age($_SESSION['birthday']) ?> ans</span>
				<a href="<?= URL ?>parametres" style="color: #666;font-size: 16px; text-decoration: none"><i class="fa fa-cog"></i></a>
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
			<div class="row">
				<div class="col-xs-6 col-sm-4 col-md-3 effectArt" style="margin: 20px 0; text-align: center">		
					<div style="background: #EEEEEE; width: 100%; padding-bottom: 100%; position: relative">
						<span class="image-upload-pfe">
							<form method="post" enctype="multipart/form-data" style="position: absolute;left: 50%;top: 50%;transform: translate(-50%,-50%);">
								<label for="file" style="display: inline-flex">
								    <input id="file" type="file" name="file" class="hide-upload-pfe" required/>
								    <i class="image-plus-pfe" style="background-image: url('<?= URL ?>public/others/plus.svg');"></i>
								    <input type="submit" id="send_pfe" class="fa send-upload-pfe" name="avatar" value="&#xf093;">
								</label>
							</form>
						</span>
					</div>
				</div>
								
				<?php
					$show_album = $DB->prepare("SELECT * 
						FROM picture 
						WHERE pseudo_id = ? ORDER BY date_upload DESC");
					$show_album->execute(array($_SESSION['id']));
					
					foreach($show_album as $key => $a){
						$key += 1;							
				?>	
				<div class="col-xs-6 col-sm-4 col-md-3 effectArt" style="margin: 20px 0; text-align: center">		
					<a href="<?= URL ?>photo/<?= $a['id'] ?>">
						<img src="<?= URL . "public/pictures/". $_SESSION['guid'] . "/" . $a['name'] ?>" style="width: 100%"/>
						<div class="mask">
							<span class="fa fa-eye eyeArt"></span>
							<span class="numberArt"><?= $a['v_like'] ?></span>
							<span class="fa fa-heartbeat heartArt"></span>
							<!--<span class="numberCom">0</span>
							<span class="fa fa-comments-o ComArt"></span>-->
							<span class="dateArt"><?= date_format(new DateTime($a['date_upload']), "d/m/Y"); ?></span>
							<span class="SousCatArt">
								<form method="post" action="">
									<input type="hidden" name="nameimg" value="<?= $a['name'] ?>"/>
									<input type="submit" name="dlt" class="fa" value="&#xf014;" style="border: none; background: transparent; outline: none"/>
								</form>
							</span>
						</div>
					</a>	
				</div>	
				<?php		
					}	
				?>				
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>
