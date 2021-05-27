<?php
	/*
	 * @author Sitedudev
	 */
	 
	include_once('../include.php');
	
	if(!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}	
	
	$info_request = $DB->prepare("SELECT r.*, u.*, d.departement_nom
		FROM relation r
		INNER JOIN user u ON u.id = r.id_from
		LEFT JOIN departement d ON d.departement_code = u.departement_code
		WHERE r.id_to = ? AND r.statut = 1");
	$info_request->execute(array($_SESSION['id']));
	
	$info_request = $info_request->fetchALL();
	
	$nb_request = count($info_request);
	
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		$guid = trim($guid);
		
		if (isset($_POST['acp'])){	
			
			if(!isset($guid)){
				$valid = false;
				
			}
			
			// On vérifie qu'il y est bien une relation, s'il y en a pas alors on ne fait rien
			$verif_friend = $DB->prepare("SELECT r.id, u.id AS id_user FROM user u 
				INNER JOIN relation r ON (r.id_from, r.id_to) = (:id, u.id) OR (r.id_from, r.id_to) = (u.id, :id)
				WHERE u.guid = :guid");
			$verif_friend->execute(array('id' => $_SESSION['id'], 'guid' => $guid));
			
			$verif_friend = $verif_friend->fetch();
			
			if(!isset($verif_friend['id'])){
				$valid = false;
			}
			
			
			if($valid){
				
				// S'il y a une relation alors on met à jour la relation
				$req = $DB->prepare("UPDATE relation SET statut = ? WHERE id = ?");
				$req->execute(array(2, $verif_friend['id']));
				
				$_SESSION['flash']['success'] = "Demande acceptée";
				header('Location: ' . URL . 'profil/demandes');
				exit;		
			}
			
		}elseif(isset($_POST['dcl'])){
			
			if(!isset($guid)){
				$valid = false;
				
			}
			
			$verif_friend = $DB->prepare("Select r.id from user u 
				inner join relation r on (r.id_from, r.id_to) = (:id, u.id) or (r.id_from, r.id_to) = (u.id, :id)
				where u.guid = :guid");
			$verif_friend->execute(array('id' => $_SESSION['id'], 'guid' => $guid));
			
			$verif_friend = $verif_friend->fetch();
			
			if(!isset($verif_friend['id'])){
				$valid = false;
			}
			
			
			if($valid){
				$req = $DB->prepare("DELETE FROM relation WHERE id = ?");
				$req->execute(array($verif_friend['id']));
				
				$_SESSION['flash']['success'] = "Demande refusée";
				header('Location: ' . URL . 'profil/demandes');
				exit;
			}
		}
	}
	
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('../_head/meta.php');
		?>
		<title>Demandes | Daewen</title>
		<?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
		?>
		<div class="container">		
			<div class="row">
				<div class="col-12 col-md-12 col-xl-12">
					<h3>Mes demandes</h3>
					<div class="row">
					<?php
						if($nb_request == 0){
							?>
							<div style="text-align: center; padding: 10px; font-size: 24px">
								Aucune demande
							</div>
							<?php								
						}

						foreach($info_request as $ir){
					?>
						<div class="col-6 col-md-4 col-xl-3" style="text-align: center; margin-bottom: 10px">
							<div class="search__body__card">
								<a href="<?= URL ?>profil/<?= $ir['guid'] ?>">
									<img src="<?= URL . $__User->getAvatar($ir['guid']) ?>" class="search__body__card__img"/>
								</a>
								<div>
									<div>
										<?php
											$rep_online = $__Online->is_online($ir['date_connection']);	
											
											if($rep_online == 1){
										?>
											<i class="fa fa-circle" style="color: #2ecc71"></i>
										<?php
											}elseif($rep_online == 2){
										?>	
											<i class="fa fa-circle" style="color: #e67e22"></i>
										<?php
											}else{
										?>
											<i class="fa fa-circle"></i>
										<?php
											}									
											echo $ir['pseudo'];
										?>	
									</div>	
									<div class="search__body__card__info">
										<div>
											<?= $ir['departement_nom'] ?>
										</div>
										<div>
											<?= $__Crypt_password->age($ir['birthday']); ?> ans
										</div>
									</div>
									<div class="search__body__card__seemore" style="margin-bottom: 20px">
										<form method="post">
											<input type="hidden" name="guid" value="<?= $ir['guid'] ?>"/>
											<div class="row">
												<div class="col-12 col-md-6 col-xl-6">
													<input type="submit" name="acp" class="search__body__acp" value="Accepter"/>
												</div>
												<div class="col-12 col-md-6 col-xl-6">
													<input type="submit" name="dcl" class="search__body__dcl" value="Refuser"/>
												</div>
											</div>
										</form>
									</div>
									<div class="search__body__card__seemore">
										<a href="<?= URL ?>profil/<?= $ir['guid'] ?>" class="search__body__card__seemore__btn">Voir profil</a>
									</div>
								</div>
							</div>			
						</div>						
					<?php
						}	
					?>
					</div>
				</div>
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>