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
<?php

	if(!isset($profil__bar__number)){
		$profil__bar__number = 1;
	}

	$my__profil__class = "profile__bar__text__checked";
	$my__profil__class1 = '<div class="profile__bar__bar"></div>';


	$profil__bar__class1 = "";
	$profil__bar__class2 = "";
	$profil__bar__class3 = "";
	$profil__bar__class4 = "";

	$profil__bar__class1_1 = "";
	$profil__bar__class2_1 = "";
	$profil__bar__class3_1 = "";
	$profil__bar__class4_1 = "";

	switch($profil__bar__number){
		case 1:
			$profil__bar__class1 = $my__profil__class;
			$profil__bar__class1_1 = $my__profil__class1;
		break;
		case 2;
			$profil__bar__class2 = $my__profil__class;
			$profil__bar__class2_1 = $my__profil__class1;
		break;
		case 3;
			$profil__bar__class3 = $my__profil__class;
			$profil__bar__class3_1 = $my__profil__class1;
		break;
		case 4;
			$profil__bar__class4 = $my__profil__class;
			$profil__bar__class4_1 = $my__profil__class1;
		break;
		default:
			$profil__bar__class1 = $my__profil__class;
			$profil__bar__class1_1 = $my__profil__class1;
		break;
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

?>
<div class="container">
	<div class="row">
		<div class="col-12 col-md-12 col-xl-12">
			<div class="profile__bar__body">
				<a href="<?= URL ?>profil" class="profile__bar__link">
					<div class="profile__bar__text <?= $profil__bar__class1 ?>">
						<span>Profil</span>
						<?= $profil__bar__class1_1 ?>
					</div>
				</a>
				<a href="<?= URL ?>profil/amis" class="profile__bar__link">
					<div class="profile__bar__text  <?= $profil__bar__class2 ?>">
						<span>Mes amis</span>
						<?= $profil__bar__class2_1 ?>
					</div>
				</a>
				<a href="<?= URL ?>profil/demandes" class="profile__bar__link">
					<div class="profile__bar__text <?= $profil__bar__class3 ?>">
						<span>Mes demandes <?= $lib_demande ?></span>
						<?= $profil__bar__class3_1 ?>
					</div>
				</a>
				<a href="<?= URL ?>parametres" class="profile__bar__link">
					<div class="profile__bar__text <?= $profil__bar__class4 ?>">
						<span>Mes paramètres</span>
						<?= $profil__bar__class4_1 ?>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>