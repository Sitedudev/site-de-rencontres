<?php
	session_start();
	
	include('bd/connexionDB.php');
	include('function/domaine.php');
	include('user.php');
	include('function/guid.php');
	include('function/password.php');
	
	$domain = new Domain;
	$crypt_password = new Password;
	
	define("URL", $domain->domain());
	
	if (isset($_SESSION['guid'])){
		header('Location: ' . URL . "profile.php");
		exit;
	}
	
	// CODE POUR LA PHOTO DE PROFIL
	if (!empty($_POST)) {
	    extract($_POST);
	    $valid = true;

		if ($picture == "add"){

			
			if (isset($_FILES['Nom']) and !empty($_FILES['Nom']['name'])) {
				
				$filename = $_FILES['Nom']['tmp_name'];
				
				list($width_orig, $height_orig) = getimagesize($filename);
				if($width_orig >= 400 && $height_orig >= 400 && $width_orig <= 6000 && $height_orig <= 6000){
	
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
			        
					if ($_FILES['Nom']['size'] <= $tailleMax){ // Si le fichier et bien de taille inférieur ou égal à 5 Mo
				        
						$extensionUpload = strtolower(substr(strrchr($_FILES['Nom']['name'], '.'), 1)); // Prend l'extension après le point, soit "jpg, jpeg ou png"
				        
						if (in_array($extensionUpload, $extensionsValides)){ // Vérifie que l'extension est correct
					        
					        $dossier = "public/pictures/"; // On se place dans le dossier de la personne 
					        
					        if (!is_dir($dossier)){ // Si le nom de dossier n'existe pas alors on le crée
						        mkdir($dossier);
					        }
						
							$dossier = "public/pictures/small/";
							if (!is_dir($dossier)){ // Si le nom de dossier n'existe pas alors on le crée
					   			mkdir($dossier);
					        }
					        
					        $nom = md5(uniqid(rand(), true)); // Permet de générer un nom unique à la photo
							$chemin = "public/pictures/".$nom.".".$extensionUpload; // Chemin pour placer la photo
							$resultat = move_uploaded_file($_FILES['Nom']['tmp_name'], $chemin); // On fini par mettre la photo dans le dossier
					        
							if ($resultat){ // Si on a le résultat alors on va comprésser l'image
								
								if (is_readable("public/pictures/".$nom.".".$extensionUpload)) {
										
									$verif_ext = getimagesize("public/pictures/".$nom.".".$extensionUpload);
									
									if($verif_ext['mime'] == $ListeExtension[$extensionUpload]  || $verif_ext['mime'] == $ListeExtensionIE[$extensionUpload]){	
										
										// J'enregistre le chemin de l'image dans filename
										$filename = "public/pictures/".$nom.".".$extensionUpload;
										
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
											imagejpeg($image_p,"public/pictures/".$nom.".".$extensionUpload, 75);
											imagejpeg($image_p2,"public/pictures/small/".$nom.".".$extensionUpload, 75);
											imagedestroy($image_p); imagedestroy($image_p2);
										}
										
										header('Location: ' . URL . 'upload-picture.php');
										exit;
										
										////////////////FIN DE COMPRÉSSION DE L'IMAGE ENREGISTRÉE////////////////
										
									}else{
		                                $message1 = "Le type MIME de l'image n'est pas bon";
		                            }
								}			        
							}else
								$message1 = "Erreur lors de l'importation de votre photo.";
					        
						}else
							$message1 = "Votre photo doit être au format jpg.";
				        
					}else
						$message1 = "Votre photo de profil ne doit pas dépasser 5 Mo !";
				}else
					$message1 = "Dimension de l'image minimum 400 x 400 et maximum 6000 x 6000 !";
			}else
				$message1 = "Veuillez mettre une image !";	
		}	
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Accueil</title>
        <link href="<?= URL ?>css/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?= URL ?>css/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?= URL ?>css/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?= URL ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
		<link href="<?= URL ?>css/style.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="<?= URL ?>js/automatic_page_loard.js" type="text/javascript"></script>
		
		
	</head>
	
	<body>
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" onclick="myFunction(this)">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar bar1"></span>
						<span class="icon-bar bar2"></span>
						<span class="icon-bar bar3"></span>
					</button>
					<a class="navbar-brand" href="<?= URL; ?>">
						<i class="fa fa-heartbeat" style="font-size: 40px; position: absolute; top: 5px;"></i>
					</a>
			    </div>
			    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				    <ul class="nav navbar-nav navbar-right">
					    <form class="navbar-left">
							<button type="button" class="navbar-btn-default navbar-btn" data-toggle="modal" data-target="#Register">S'inscrire</button>
					    </form>
					    <form class="navbar-left">
							<button type="button" class="navbar-btn-default navbar-btn" data-toggle="modal" data-target="#SignIn">Se connecter</button>
					    </form>
				    </ul>
			    </div>
			</div>
		</nav>
		
		<div class="container">
			
			<?php 
				if (isset($message1))
                echo "<div class='alert alert-danger'><center>" . $message1 . "</center></div>";
	        ?>
			 			        
	        <form method="post" action="" enctype="multipart/form-data">
								
				
                <div class="input-file-container">
				  <input class="input-file" name="Nom" id="my-file" type="file">
				  <label for="my-file" class="input-file-trigger" tabindex="0">Choisissez votre image</label>
				</div>
				
                
				<p style="color: red">La photo doit être au format .jpg et peser au maximum 5 Mo.</p>
				
				<div class="row" style="margin-bottom: 20px">
										
					<div class="col-xs-0 col-sm-8 col-md-10"></div>
					<div class="col-xs-12 col-sm-4 col-md-2">
						<input type="hidden" value="add" name="picture"/>
                        <button type="submit">Ajouter</button>
					</div>																	
				</div>
				
	        </form>

		</div>
		
		
		<footer>
			<i class="fa fa-twitter social-cust"></i>
			<i class="fa fa-facebook social-cust"></i>
			<i class="fa fa-google-plus social-cust"></i>
		</footer>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="<?= URL ?>js/bootstrap.min.js"></script>
		<script src="<?= URL ?>js/register.js"></script>
		<script>			
			function myFunction(x) {
			    x.classList.toggle("change");
			}
		</script>				
	</body>
</html>