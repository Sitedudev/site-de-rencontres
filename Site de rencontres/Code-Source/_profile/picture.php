<?php
	include_once('../include.php');		
	
	if (!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}
	
	$num_img = (int) $_GET['num']; 

	if($num_img <= 0){
		header('Location: ' . URL . 'profil');
		exit;
	}
	
	$show_album = $DB->prepare("SELECT name, date_upload 
		FROM picture 
		WHERE id = ? AND pseudo_id = ?"); 
	$show_album->execute(array($num_img, $_SESSION['id']));
	
	$a = $show_album->fetch();	
	
	if(!isset($a['name'])){
		header('Location: ' . URL . 'profil');
		exit;
	}

	$req = $DB->prepare("SELECT COUNT(id) AS NbDemandes 
		FROM relation 
		WHERE id_to = ? AND statut = 1");
	
	$req->execute([$_SESSION['id']]);
		
	$count_demande = $req->fetch();

	$lib_demande = "";

	if($count_demande['NbDemandes'] > 0){
		$lib_demande = '<span class="badge badge__profile">' . $count_demande['NbDemandes'] . '</span>'; 
	}
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		if(isset($_POST['dlt'])){
			
			$nameimg = trim($nameimg);
			
			if(isset($nameimg)){
				
				$imageBD = $DB->query("SELECT name FROM picture WHERE pseudo_id = :id AND name = :name", 
		    		array('id' => $_SESSION['id'], 'name' => $nameimg));
		    		 
		    	$imageBD = $imageBD->fetch();
		    	
		    	if(isset($imageBD['name'])){
			    	// Permet de supprimer une image dans un dossier 
					if(file_exists("public/pictures/" . $_SESSION['guid'] . "/" . $imageBD['name'])){
						unlink("public/pictures/" . $_SESSION['guid'] . "/" . $imageBD['name']);
					}
					
					$DB->insert("DELETE FROM picture WHERE pseudo_id = :id AND name = :name", 
						array('id' => $_SESSION['id'], 'name' => $imageBD['name']));
		    	}
			}
			
			$_SESSION['flash']['success'] = "Votre image a été supprimé de l'album !";
			header('Location: ' . URL . '/profil');
			exit;
			
		}elseif(isset($_POST['sdcet'])){
			
			$nameimg = trim($nameimg);
			
			$_SESSION['flash']['success'] = "Commentaire posté";
			header('Location: ' . URL . '/photo/' . $num_img);
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
		<title>Image | Daewen</title>
		<?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
		?>
		<div style="position: relative; border-bottom: 2px solid #ecf0f1; z-index: 0;">
			<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: -1; background-image: url(<?= URL . $__User->getAvatar($_SESSION['guid']) ?>); background-repeat: no-repeat; background-position: center; background-size: cover;"></div>
			<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: -1; background: rgba(255, 255, 255, .7); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px)"></div>
			<div class="container">
				<div class="row">
					<div class="col-12 col-md-12 col-xl-12">
						<div style="display: flex; justify-content: center; align-items: center; flex-direction: column; margin: 20px 0">
							<img src="<?= URL . $__User->getAvatar($_SESSION['guid']) ?>" width="120" style="width: 120px; border-radius: 100px"/>
							<div style="font-size: 1.2rem; margin-top: 5px;">
								<span style="font-weight: bold;"><?= $_SESSION['pseudo'] ?>,</span>
								<span><?= $__Crypt_password->age($_SESSION['birthday']) ?> ans</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12">
					<div class="profile__bar__body">
						<a href="<?= URL ?>profil" class="profile__bar__link">
							<div class="profile__bar__text profile__bar__text__checked">
								<span>Profil</span>
								<div class="profile__bar__bar"></div>
							</div>
						</a>
						<a href="<?= URL ?>profil/amis" class="profile__bar__link">
							<div class="profile__bar__text">Mes amis</div>
						</a>
						<a href="<?= URL ?>profil/demandes" class="profile__bar__link">
							<div class="profile__bar__text">Mes demandes <?= $lib_demande ?></div>
						</a>
						<a href="<?= URL ?>parametres" class="profile__bar__link">
							<div class="profile__bar__text">Mes paramètres</div>
						</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container">
			
			<div class="row">								
				<div class="col-xs-12 col-sm-6 col-md-5 effectArt" style="margin: 20px 0; text-align: center">		
					<a href="" id="a_sp">
						<img src="<?= URL . "public/pictures/". $_SESSION['guid'] . "/" . $a['name'] ?>" style="width: 100%"/>
						<div class="mask">
							<span class="fa fa-eye eyeArt"></span>
							<span class="numberArt">0</span>
							<span class="fa fa-heartbeat heartArt"></span>
							<!--<span class="numberCom">0</span>
							<span class="fa fa-comments-o ComArt"></span>-->
							<span class="dateArt"><?= date_format(new DateTime($a['date_upload']), "d/m/Y"); ?></span>
						</div>
					</a>	
				</div>	
				
				<div class="col-xs-12 col-sm-6 col-md-7" style="margin: 20px 0">
					<form method="post" action="">
						<input type="hidden" name="nameimg" value="<?= $a['name'] ?>"/>
						<div style="border: 2px solid #d35400; width: 70px;border-radius: 100px;height: 70px;">
							<button type="submit" name="dlt" style="font-size: 45px;border: none;color: #d35400;border-radius: 100px; padding: 10px 15.5px;background: transparent;outline: none"><i class="bi bi-trash"></i></button>
						</div>
					</form>
					<!--
					<form method="post" action="">
						<input type="hidden" name="nameimg" value="<?= $a['name'] ?>"/>
						<div style="border: 2px solid #d35400;position: absolute;top: 0;left: 0;margin: auto;right: 0;width: 70px;border-radius: 100px;height: 70px;">
							<input type="submit" name="sdcet" class="fa" value="&#xf1d9;" style="font-size: 45px;border: none;color: #d35400;padding-right: 10px;padding: 10px 8px; background: transparent; outline: none"/>
						</div>
						<div style="margin-top: 30px">
							<textarea name="comment" class="autoExpand" rows="3" data-min-rows="3" placeholder="Commentez votre photo" style="border: 1px solid #CCC; border-radius: 5px;width: 100%; max-width: 100%; outline: none; padding: 5px 10px; overflow: none; resize: none"></textarea>
						</div>					
					</form>
					-->
				</div>	
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>