<?php
	/*
	 * @author Sitedudev
	 */
	include_once('../include.php');	
	
	if (!isset($_SESSION['id'])){
		header('Location: ' . URL);
		exit;
	}
		
	$guid_profile = (String) htmlentities($_GET['guid']);

	if(!isset($guid_profile)){
		header('Location: ' . URL . 'profil');
		exit;
	}
	
	$verif_guid_profile = $DB->prepare("SELECT * 
		FROM user 
		WHERE guid = ?");
	$verif_guid_profile->execute(array($guid_profile));
	
	$info_profile = $verif_guid_profile->fetch();
	
	if(!isset($info_profile['guid'])){
		header('Location: ' . URL . 'profil');
		exit;
	}
	
	$info_relation = $DB->prepare("SELECT * 
		FROM relation 
		WHERE (id_from, id_to) = (:id1, :id2) OR (id_to, id_from) = (:id1, :id2)");
	$info_relation->execute(array('id1' => $_SESSION['id'], 'id2' => $info_profile['id']));
		
	$info_relation = $info_relation->fetch();
	
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		// Pour faire une demande d'amitié
		if(isset($_POST['add'])){
			
			//
			//  Fonction pour les modos ou admins, on accepte directement la relation
			//
			if($_SESSION['role'] > 0){
				
				// Vérifie qu'il y a bien une relation
				$verif_relation = $DB->prepare("SELECT id 
					FROM relation 
					WHERE (id_from, id_to) = (:id1, :id2) OR (id_to, id_from) = (:id1, :id2)");
				$verif_relation->execute(array("id1" => $_SESSION['id'], "id2" => $info_profile['id'])); 
					
				$verif_relation = $verif_relation->fetch();
				
				// Si oui on met à jour sinon on insert une relation
				if (isset($verif_relation['id'])){
					
					$req = $DB->prepare("UPDATE relation SET statut = ? WHERE id = ?");
					$req->execute(array(2, $info_relation['id']));
						
				}else{
					$req = $DB->prepare("INSERT INTO relation (id_from, id_to, statut) VALUES (?, ?, ?)");
					$req->execute(array($_SESSION['id'], $info_profile['id'], 2));
				
				}
				
				// On recherche s'il y a un message titre déjà existant
				$verif_mp_titre = $DB->prepare("SELECT id 
					FROM mp_titre 
					WHERE (id_from, id_to) = (:id1, :id2) OR (id_to, id_from) = (:id1, :id2)");
				$verif_mp_titre->execute(array("id1" => $_SESSION['id'], "id2" => $info_profile['id']));
					
				$verif_mp_titre = $verif_mp_titre->fetch();
				
				// S'il y a un message titre alors on le met à jour sinon on en crée un nouveau
				if (isset($verif_mp_titre['id'])){
					
					$req = $DB->prepare("UPDATE mp_titre SET id_from = ?, id_to = ?, text = ?, date_mp = ? WHERE id = ?");
					$req->execute(array($_SESSION['id'], $info_profile['id'], "Dites lui bonjour !", date('Y-m-d H:i:s'), $verif_mp_titre['id']));
					
				}else{
					
					$req = $DB->prepare("INSERT INTO mp_titre (id_from, id_to, text, date_mp) VALUES (?, ?, ?, ?)");
					$req->execute(array($_SESSION['id'], $info_profile['id'], "Dites lui bonjour !", date('Y-m-d H:i:s')));
						
				}
								
				$_SESSION['flash']['success'] = "Vous êtes ami avec cette personne";
				header('Location: ' . URL . 'profil/' . $guid_profile);
				exit;
			}
			//
			// FIN fonction modos et admins
			//
			
			if(isset($info_relation['id'])){
				$_SESSION['flash']['warning'] = "Une demande existe déjà";
								
			}else{
				$req = $DB->prepare("INSERT INTO relation (id_from, id_to, statut) VALUES (?,  ?,  ?)");
				$req->execute(array($_SESSION['id'], $info_profile['id'], 1));
					
				$_SESSION['flash']['success'] = "Une demande a été faite à " . $info_profile['pseudo'];
			}

			header('Location: ' . URL . 'profil/' . $guid_profile);
			exit;
			
		}elseif(isset($_POST['block'])){ // Bloquer la personne
			
			if(isset($info_relation['id'])){
				$req = $DB->prepare("UPDATE relation SET statut = ?, id_block = ? WHERE id = ?");
				$req->execute(array(3, $info_profile['id'], $info_relation['id']));
					
			}else{
				$req = $DB->prepare("INSERT INTO relation (id_from, id_to, statut, id_block) VALUES (?, ?, ?, ?)");
				$req->execute(array($_SESSION['id'], $info_profile['id'], 3, $info_profile['id']));
			}	
				
			if($info_profile['sexe'] == 1){
				$_SESSION['flash']['success'] = $info_profile['pseudo'] . " a été bloqué";
			}else{
				$_SESSION['flash']['success'] = $info_profile['pseudo'] . " a été bloquée";
			}
			
			header('Location: ' . URL . 'profil/' . $info_profile['guid']);
			exit;
			
		}elseif(isset($_POST['unblock'])){ // Débloquer la personne

			$req = $DB->prepare("DELETE FROM relation WHERE id = ?");
			$req->execute(array($info_relation['id']));
				
			if($info_profile['sexe'] == 1){
				$_SESSION['flash']['success'] = $info_profile['pseudo'] . " a été débloqué";
			}else{
				$_SESSION['flash']['success'] = $info_profile['pseudo'] . " a été débloquée";
			}
			
			header('Location: ' . URL . 'profil/' . $info_profile['guid']);
			exit;
			
		}elseif(isset($_POST['delete'])){ // Supprimer la relation
			
			$req = $DB->prepare("DELETE FROM relation WHERE id = ?");
			$req->execute(array($info_relation['id']));
			
			if($info_profile['sexe'] == 1){
				$_SESSION['flash']['success'] = $info_profile['pseudo'] . " a été supprimé de tes amis";
			}else{
				$_SESSION['flash']['success'] = $info_profile['pseudo'] . " a été supprimée de tes amis";
			}
			header('Location: ' . URL . 'profil/' . $guid_profile);
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
        <title><?= $info_profile['pseudo'] ?> | Daewen</title>
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
			<div class="row">
				<div class="col-12 col-md-4 col-xl-3" style="text-align: center;">
					<img src="<?= URL . $__User->getAvatar($info_profile['guid'])?>" width="120" style="width: 120px; border-radius: 100px"/>
				</div>
				<div class="col-12 col-md-8 col-xl-9">
					<?php
						$rep_online = $__Online->is_online($info_profile['date_connection']);	
						
						if($rep_online == 1){
					?>
						<span class="fa fa-circle" style="transform: translate(0, -4px); color: #2ecc71"></span>
					<?php
						}elseif($rep_online == 2){
					?>	
						<span class="fa fa-circle" style="transform: translate(0, -4px); color: #e67e22"></span>
					<?php
						}else{
					?>
						<span class="fa fa-circle" style="transform: translate(0, -4px)"></span>
					<?php
						}
					?>
					<span style="font-size: 28px"><?= $info_profile['pseudo'] ?>,</span>
					<span style="font-size: 22px"><?= $__Crypt_password->age($info_profile['birthday']) ?> ans</span>
					
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
		</div>
		<div class="container">
			<div class="row">	
				<div class="col-6 col-md-4 col-xl-3 effectArt" style="margin: 20px 0; text-align: center">		
					<div style="background: #EEEEEE; width: 100%; padding-bottom: 100%; position: relative">
						<button type="button" class="" data-bs-toggle="modal" data-bs-target="#action" style="position: absolute;left: 50%;top: 50%;transform: translate(-50%,-50%);background: transparent; border: none; font-size: 60px; outline: none">
							<i class="fa fa-ioxhost"></i>
						</button>
						<label style="position: absolute; top: 10px; left: 0;right: 0; font-size: 18px">Action</label>
					</div>
					<div id="action" class="modal fade">
						<div class="modal-dialog" role="document">
							<form method="post" class="modal-content animate" action="" style="padding-top: 10px">
								<button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">&times;</button>
								
								<div class="container-fluid">
									<h3 style="margin-top: 0; text-align: left">Actions</h3>
									<?php
										//
										// Pour ajouter une personne
										//
										if($_SESSION['role'] > 1 && $info_profile['role'] < 2){	
									?>																
										<div class="row" style="margin-bottom: 20px;font-size: 16px">												
											<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
												<a href="<?= URL . "admin/statut/" . $info_profile['guid'] ?>" style="color: #666; border: none; background: transparent; padding:0;text-decoration: none"><i class="fa fa-drivers-license-o"></i> Gérer ce compte</a>
											</div>
										</div>
									<?php
										}

										//
										// Pour ajouter une personne
										//
										if(!isset($info_relation['statut'])){
									?>																
										<div class="row" style="margin-bottom: 20px;font-size: 16px">												
											<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
												<button type="submit" name="add" style="color: #666; border: none; background: transparent; padding:0;outline: none"><i class="fa fa-plus"></i> Ajouter</button>
											</div>
										</div>
									<?php
										}
										//
										// Personne en attente de validation
										//
										elseif($info_relation['statut'] == 1 && $info_relation['statut'] <> 3){
									?>																
										<div class="row" style="margin-bottom: 20px;font-size: 16px">
											<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
												<span style="color: #666"><i class="fa fa-hourglass-o"></i> Demande en cours ...</span>
											</div>
										</div>
									<?php
										}
										//
										// Personne qui peuvent communiquer
										//
										elseif($info_relation['statut'] == 2 && $info_relation['statut'] <> 3){
									?>																
										<div class="row" style="margin-bottom: 20px;font-size: 16px">
											<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
												<a href="<?= URL ?>messagerie/<?= $info_profile['guid'] ?>" style="color: #666; text-decoration: none"><i class="fa fa-comments-o"></i> Discuter</a>
											</div>
										</div>
									<?php
										}
									?>
									
									<?php
										//
										// Bloquer une personne
										//
										if(!isset($info_relation['statut']) || $info_relation['statut'] <> 3){
									?>									
										<div class="row" style="margin-bottom: 20px;font-size: 16px">
											<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
												<button type="submit" name="block" style="color: #666; border: none; background: transparent; padding:0;outline: none"><i class="fa fa-ban"></i> Bloquer</button>
											</div>
										</div>
									<?php
										}
										//
										// Débloquer une personne
										//
										elseif($info_relation['statut'] == 3 && $info_relation['id_block'] <> $_SESSION['id']){
									?>
										<div class="row" style="margin-bottom: 20px;font-size: 16px">
											<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
												<button type="submit" name="unblock" style="color: #666; border: none; background: transparent; padding:0;outline: none"><i class="fa fa-ban"></i> Débloquer</button>
											</div>
										</div>
									<?php
										}else{
									?>
										<div class="row" style="margin-bottom: 20px;font-size: 16px">
											<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
												<span style="color: #666"><i class="fa fa-ban"></i> Vous ne pouvez pas interagir avec ce membre</button>
											</div>
										</div>
									<?php
										}
									?>
									
									<?php
										//
										// Supprimer une personne
										//
										if(isset($info_relation['statut']) && $info_relation['statut'] == 2 && $info_relation['statut'] <> 3){
									?>
									<div class="row" style="margin-bottom: 20px;font-size: 16px">
										<div class="col-xs-12 col-sm-12 col-md-12" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
											<button type="submit" name="delete" style="color: #666; border: none; background: transparent; padding:0;outline: none"><i class="fa fa-close"></i> Supprimer</button>
										</div>
									</div>	
									<?php
										}
									?>
								</div>						
							</form>
						</div>
					</div>
				</div>						
				<?php
					$show_album = $DB->prepare("SELECT id, name, date_upload, v_like 
						FROM picture 
						WHERE pseudo_id = ? 
						ORDER BY date_upload DESC");
					$show_album->execute(array($info_profile['id']));
					
					foreach($show_album as $key => $a){
						$key += 1;
				?>	
				<div class="col-xs-6 col-sm-4 col-md-3 effectArt" style="margin: 20px 0; text-align: center">		
					<a href="<?= URL ?>profil/<?= $info_profile['guid'] . "/" . $a['id'] ?>">
						<img src="<?= URL . "public/pictures/". $info_profile['guid'] . "/" . $a['name'] ?>" style="width: 100%"/>
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
									<input type="submit" name="warning" class="fa" value="&#xf071;" style="border: none; background: transparent; outline: none"/>
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
