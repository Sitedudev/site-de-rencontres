<?php
	/**
	 * @package			: Code source rencontres
	 * @version			: 0.7
	 * @author			: sitedudev aka clouder
	 * @link 			: https://sitedudev.com
	 * @since			: 2021
	 * @license			: Attribution-NomCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)
	 */
	
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
		<div class="container" style="color: #666; padding-bottom: 25px;">
			<div class="col-12 col-md-4 col-xl-3" style="text-align: center;">
				<img src="<?= URL . $__User->getAvatar($_SESSION['guid']) ?>" width="120" style="width: 120px; border-radius: 100px"/>
			</div>
			<div class="col-12 col-md-8 col-xl-9">
				<span style="font-size: 28px"><?= $_SESSION['pseudo'] ?>,</span>
				<span style="font-size: 22px"><?= $__Crypt_password->age($_SESSION['birthday']) ?> ans</span>
				<a href="settings" style="color: #666;font-size: 16px; text-decoration: none"><i class="fa fa-cog"></i></a>
				<a href="request" style="text-decoration: none; color: #666">
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
							<input type="submit" name="dlt" class="fa" value="&#xf014;" style="font-size: 45px;border: none;color: #d35400;border-radius: 100px; padding: 10px 15.5px;background: transparent;outline: none"/>
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