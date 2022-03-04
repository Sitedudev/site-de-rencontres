<?php
	include_once('../include.php');
		
	if (!isset($_SESSION['id'])){
		header('Location: ' . URL);
		exit;
	}

	$req = $DB->prepare("SELECT u.guid, u.id, u.pseudo, u.date_connection, u.birthday, d.departement_nom
		FROM relation r
		INNER JOIN user u ON ((r.id_to = u.id OR r.id_from  = u.id) AND u.id <> :id)
		LEFT JOIN departement d ON d.departement_code = u.departement_code
		WHERE (r.id_to = :id OR r.id_from = :id) AND r.statut = 2 AND r.id_block = 0");

	$req->execute(['id' => $_SESSION['id']]);

	$req_amis = $req->fetchAll();


	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
	
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('../_head/meta.php');
		?>
        <title>Mes amis | Daewen</title>
		<?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
			$profil__bar__number = 2;
			include_once('head_profile.php');
		?>
		<div class="container">
			<div class="row">
				<?php
					foreach($req_amis as $ra){
				
				?>	
				<div class="col-xs-6 col-sm-4 col-md-3 effectArt" style="margin: 20px 0; text-align: center">		
					<div class="search__body__card">
						<a href="<?= URL ?>profil/<?= $ra['guid'] ?>">
							<img src="<?= URL . $__User->getAvatar($ra['guid']) ?>" class="search__body__card__img"/>
						</a>	
						<div>
							<?php
								$rep_online = $__Online->is_online($ra['date_connection']);	
								
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
								echo $ra['pseudo'];
							?>	
						</div>	
						<div class="search__body__card__info">
							<div>
								<?= $ra['departement_nom'] ?>
							</div>
							<div>
								<?= $__Crypt_password->age($ra['birthday']); ?> ans
							</div>
						</div>
					</div>
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
