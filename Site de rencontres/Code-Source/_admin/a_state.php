<?php
	include_once('../include.php');
		
	if (!isset($_SESSION['id'])){
		header('Location: ' . URL);
		exit;
	}	
	
	if ($_SESSION['role'] < 2){
		header('Location: ' . URL);
		exit;
	}
	
	if(!isset($_GET['guid'])){
		header('Location: ' . URL);
		exit;
	}

	$user_guid = (String) $_GET['guid'];
	
	$info_profile = $DB->prepare("SELECT * 
		FROM user 
		WHERE guid = ?"); 
	
	$info_profile->execute([$user_guid]);
		
	$info_profile = $info_profile->fetch();
	
	if (count($info_profile) == 0){ // s'il n'y a aucun compte 
		header('Location: ' . URL);
		exit;
	}
	
	if ($info_profile['role'] >= $_SESSION['role']){ // On ne peut pas modifier un admin
		header('Location: ' . URL . 'profil/' . $info_profile['guid']);
		exit;
	}
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		if (isset($_POST['update'])){
				
			$r_lgn 		= trim($lgn);
	
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
			}
	
			// ---- verif sex
			if(empty($sex)){
				$valid = false;
				
			}elseif($sex == 2) {
				$r_sex = 2; // 2 : Woman
			}else{
				$r_sex = 1; // 1 : Man
			}
	
			// ---- verif date birthday
			if((!isset($day) && empty($day)) || (!isset($month) && empty($month)) || (!isset($year) && empty($year))){
				$valid = false;
				$er_birthday = "Entrez une date de naissance valide";
	
			}elseif(($day < 0 || $day > 31) || !preg_match("/^[0-9]{1,2}$/u", $day)){
				$valid = false;
				$er_birthday = "Le jour est compris entre 1 et 31";// . var_dump(is_int($day));
				
			}elseif(($month < 0 || $month > 12) || !preg_match("/^[0-9]{1,2}$/u", $month)){
				$valid = false;
				$er_birthday = "Le mois est compris entre 1 et 12";
				
			}elseif(($year < 1914 || $year > 2017) || !preg_match("/^[0-9]{4}$/u", $year)){
				$valid = false;
				$er_birthday = "L'année est compris entre 1914 et 2017";
				
			}elseif(!checkdate($month, $day, $year)){
				$valid = false;
				$er_birthday = "Entrez une date de naissance valide";
				
			}
				
			if ($valid){
				
				$req = $DB->insert("UPDATE user SET pseudo = ?, sexe = ?, birthday = ?
					WHERE guid = ?", 
					array($r_lgn, $r_sex, ($year . "-" . $month . "-" . $day), $user_guid));
					
				$_SESSION['flash']['success'] = "Modifications apportées au profil de " . $info_profile['pseudo'];
					
				header('Location: ' . URL . 'admin/statut/' . $info_profile['guid']);
				exit;

				
			}

		}elseif (isset($_POST['state'])){
			
			$state_account = htmlentities(trim($state_account));
			
			$verif_state = array(0, 2, 3);
			
			if (!in_array($state_account, $verif_state)){
				$valid = false;
			}
			
			if ($valid){

				$DB->insert("UPDATE user SET state = ? WHERE guid = ?", 
					array($state_account, $info_profile['guid']));
				
				switch($state_account){
					case 0:
						$mess_info_state = "actif";
					break;
					case 2:
						$mess_info_state = "suspendu";
					break;
					case 3:
						$mess_info_state = "banni";
					break;
				}
				
				
				$_SESSION['flash']['success'] = "Le compte de " . $info_profile['pseudo'] . " est " . $mess_info_state;
				header('Location: ' . URL . 'admin/statut/' . $info_profile['guid']);
				exit;
				
			}
			
		}elseif (isset($_POST['dltimg'])){
			
			$nameimg = htmlentities(trim($nameimg));
			
			if (empty($nameimg)){
				$valid = false;
				
			}else{
				
				$verif_img = $DB->query("SELECT * FROM picture WHERE name = ? AND pseudo_id = ?", 
					array($nameimg, $info_profile['id']));
					
				$verif_img = $verif_img->fetch();
				
				if (!isset($verif_img['id'])){
					$valid = false;
				
				}
			}
			
			if ($valid){

				if(file_exists("../public/pictures/". $info_profile['guid'] . "/" . $verif_img['name'])){
					
					unlink("../public/pictures/". $info_profile['guid'] . "/" . $verif_img['name']);
					
					$DB->insert("DELETE FROM picture WHERE id = ?", 
						array($verif_img['id']));
				}
			
				$_SESSION['flash']['success'] = "L'image de " . $info_profile['pseudo'] . " a été supprimé";
				header('Location: ' . URL . 'admin/statut/' . $info_profile['guid']);
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
        <title>Gestion utilisateurs | Daewen</title>
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
					<div class="admin__law__body">
						<h1>État du compte</h1>
						<?php
							
							switch ($info_profile['state']) {
								case 0:
									$mess_state = "actif";
								break;
								
								case 1:
									$mess_state = "inactif";
								break;
								
								case 2:
									$mess_state = "suspendu";
								break;
								
								case 3:
									$mess_state = "bani";
								break;
							}	
						?>
						<div style="margin: 20px 0">Le compte est <i><?= $mess_state ?></i></div>
						<form method="post">
							<label>État</label>
							<select name="state_account">
								<option hidden="" selected>Sélectionner</option>
								<?php
									if ($info_profile['state'] != 0){
									?>
										<option value="0">Actif</option>
									<?php
									}
									
									if ($info_profile['state'] != 2){
									?>
										<option value="2">Suspendre</option>
									<?php	
									}	
									
									if ($info_profile['state'] != 3){
									?>
										<option value="3">Banir</option>
									<?php	
									}	
								?>
							</select>
							<input type="submit" name="state" value="Confirmer" style="background: transparent; border: 1px solid #ccc; outline: none; padding: 5px 10px"/>
						</form>
					</div>
				</div>
				<div class="col-12 col-md-12 col-xl-12">
					<div class="admin__law__body">
						<h1>Informations</h1>
						<form method="post">
							<label>Pseudo</label>
							<input type="text" name="lgn" value="<?= $info_profile['pseudo']?>" placeholder="Pseudo"/>		
							<label>Sexe</label>	
							<select name="sex">
								<?php
									if (isset($info_profile['sexe'])){	
										
										if ($info_profile['sexe'] == 1){
										?>
											<option value="1">Homme</option>
											<option value="2">Femme</option>
										<?php	
										}else{
										?>
											<option value="2">Femme</option>
											<option value="1">Homme</option>
										<?php	
										}
									}
								?>								
							</select>
							<label><b>Date de naissance</b></label>
							<div class="row">
								<div class="col-4 col-md-4 col-xl-4">
									<select name="day">
										<?php
											
											$date_birhday = date_create($info_profile['birthday']);
											$day = date_format($date_birhday, "d");
											
											if (isset($day) && !empty($day)){	
										?>  
										<option value="<?= $day ?>"><?= $day ?></option>
										<?php
											} 
										?>
										<option value="" hidden>Jour</option>
										<?php
											for($r = 1; $r <= 31; $r++) {
										?>
										<option value="<?= $r ?>"><?= $r ?></option>
										<?php
											}	
										?>
									</select>
								</div>
								<div class="col-4 col-md-4 col-xl-4">
									<select name="month">
										<?php
											
											$date_birhday = date_create($info_profile['birthday']);
											$month = date_format($date_birhday, "m");
											
											if (isset($month) && !empty($month)){	
												$monthName = $__Crypt_password->month($month);
										?>  
												<option value="<?= $month ?>"><?= $monthName ?></option>
										<?php
											} 
										?>
										<option value="" hidden>Mois</option>
										<?php
											for($r = 1; $r <= 12; $r++) {
												
											$monthName = $__Crypt_password->month($r);
												
										?>
											<option value="<?= $r ?>"><?= $monthName ?></option>
										<?php
											}	
										?>
									</select>
								</div>
								<div class="col-4 col-md-4 col-xl-4">
									<select name="year">
										<?php
											
											$date_birhday = date_create($info_profile['birthday']);
											$year = date_format($date_birhday, "Y");
											
											if (isset($year) && !empty($year)){	
										?>  
												<option value="<?= $year ?>"><?= $year ?></option>
										<?php
											} 
										?>
										<option value="" hidden>Année</option>
										<?php
											for($r = 1999; $r >= 1914; $r--) {
										?>
											<option value="<?= $r ?>"><?= $r ?></option>
										<?php
											}	
										?>
									</select>
								</div>
							</div>

							<label>Mail</label>
							<input type="email" value="<?= $info_profile['mail']?>" name="mail" placeholder="Mail" disabled/>
							
							<input type="submit" name="update" value="Modifier" style="background: transparent; border: 1px solid #ccc; padding: 5px 10px; outline: none"/>
						</form>
					</div>
				</div>
				<div class="col-12 col-md-12 col-xl-12">
					<div class="admin__law__body">
						<h1>Photos</h1>
						<div class="row">			
							<?php 
								$user_picture = $DB->prepare("SELECT * 
									FROM picture 
									WHERE pseudo_id = ?"); 
								
								$user_picture->execute([$info_profile['id']]);
								
								$user_picture = $user_picture->fetchAll();
								
								if (count($user_picture) == 0){
									echo "<div style='padding: 10px; display: flex; justify-content: center; align-items: center'>Aucune photo</div>";
								}
														
								foreach($user_picture as $u){
							?>
							<div class="col-6 col-md-4 col-xl-3" style="margin: 20px 0; text-align: center">		
								<form method="post">
									<img src="<?= URL . "public/pictures/". $info_profile['guid'] . "/" . $u['name'] ?>" style="width: 100%"/>
									<input type="hidden" name="nameimg" value="<?= $u['name'] ?>"/>
									<input type="submit" name="dltimg" value="Supprimer" style="border: none; background: #c0392b; width: 100%; color: white; outline: none; font-weight: 400"/>
								</form>
							</div>
							<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>