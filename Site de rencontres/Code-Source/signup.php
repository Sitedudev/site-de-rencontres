<?php
	/*
	 * @author Sitedudev
	 */
	
	include_once('include.php');
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
	
		if (isset($_POST['register'])){
			
			$r_lgn 		= trim($lgn);
			$r_mail		= strtolower(trim($mail));
			$r_psw 		= trim($psw);
			$r_confpsw 	= trim($confpsw);
			//$r_dep		= trim($dep);
			$r_ville 	= trim($ville);
			
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
			if(isset($sex)) {
				$r_sex = 2; // 2 : Woman
				$checked = "checked";
			}else{
				$r_sex = 1; // 1 : Man
				$checked = "";
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
				
			}elseif(($year < date('Y', strtotime(date('Y-m-d') . '-80 years')) || $year > date('Y', strtotime(date('Y-m-d') . '-18 years'))) || !preg_match("/^[0-9]{4}$/u", $year)){
				$valid = false;
				$er_birthday = "L'année est compris entre 1914 et 2017";
				
			}elseif(!checkdate($month, $day, $year)){
				$valid = false;
				$er_birthday = "Entrez une date de naissance valide";
				
			}
			
			// ---- verif ville
			if(empty($r_ville)){
				$valid = false;
				$er_ville = "Veuillez renseigner une ville";
				
			}else{
				
				$rt_ville = explode(", ", $r_ville);
				
				if(!in_array(count($rt_ville), array(1, 2))){
					$valid = false;
					$er_ville = "La ville n'existe pas";
				}else{
					switch(count($rt_ville)){
						case 1:
							$ville_name = $rt_ville[0];
														
							$req_ville = $DB->prepare("SELECT v.ville_nom_reel as a, d.departement_nom as b, v.ville_id as c, d.departement_code as d 
									FROM villes_france v, departement d 
									WHERE d.departement_code = v.ville_departement AND ville_nom_reel = ? 
									LIMIT 1");
							$req_ville->execute(array($ville_name));
							
							$req_ville = $req_ville->fetch();
							
							if (isset($req_ville['a']) && isset($req_ville['b'])){
								$id_ville = $req_ville['c'];
								$code_dep = $req_ville['d'];
							}else{
								$valid = false;
								$er_ville = "La ville n'existe pas";
							}
						break;
						
						case 2:
							$ville_name = $rt_ville[0];
							$dep_name = $rt_ville[1];
							
							$req_ville = $DB->prepare("SELECT v.ville_nom_reel as a, d.departement_nom as b, v.ville_id as c, d.departement_code as d 
									FROM villes_france v, departement d 
									WHERE d.departement_code = v.ville_departement AND ville_nom_reel = ? 
									AND d.departement_nom = ? 
									LIMIT 1");
							$req_ville->execute(array($ville_name, $dep_name));
							
							$req_ville = $req_ville->fetch();
							
							if (isset($req_ville['a']) && isset($req_ville['b'])){
								$id_ville = $req_ville['c'];
								$code_dep = $req_ville['d'];
							}else{
								$valid = false;
								$er_ville = "La ville n'existe pas";
							}
						break;
						
						default:
							$valid = false;
							$er_ville = "La ville n'existe pas";
						break;
					}
				}
			}
			
			// ---- verif mail
			if(empty($r_mail)){
				$valid = false;
				$er_mail = "Le mail ne peut pas être vide";
				
			}elseif(!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $r_mail)){
				$valid = false;
				$er_mail = "Le mail n'est pas valide";
				
			}else{
				$req_mail = $DB->prepare("SELECT mail FROM user WHERE mail = :mail");
				$req_mail->execute(array('mail' => $r_mail));
				
				$req_mail = $req_mail->fetch();
				
				if (isset($req_mail['mail'])){
					$valid = false;
					$er_mail = "Le mail existe déjà";
				}
			}
			
			// ---- verif password
			if(empty($r_psw)) {
				$valid = false;
				$er_psw = "Le mot de passe ne peut pas être vide";
			
			}elseif(strlen($r_psw) < 3) {
				$valid = false;
				$er_psw = "Le mot de passe doit faire plus de 3 caractères";
				
			}elseif($r_psw != $confpsw){
				$valid = false;
				$er_psw = "La confirmation du mot de passe ne correspond pas";
			}
			
			if($valid){
				
				$r_psw = $__Crypt_password->password($r_psw);

				$unique_guid = $__GUID->check_guid();	
				
				$date_registration_connection = date('Y-m-d H:i:s');
				
				$req = $DB->prepare("INSERT INTO user (guid, pseudo, sexe, birthday, departement_code, 
					ville_id, mail, password, date_registration, date_connection) VALUES 
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					
				$req->execute(array($unique_guid, $r_lgn, $r_sex, ($year . "-" . $month . "-" . $day), 
					$code_dep, $id_ville, $r_mail, $r_psw, $date_registration_connection, $date_registration_connection));
				
				$_SESSION['flash']['info'] = "Votre compte a été créé";
				header('Location: ' . URL);
				exit;
				
			}		
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
		include_once('_head/meta.php');
		?>
        <title>Inscription</title>
        <?php
			include_once('_head/link.php');
			include_once('_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('menu.php');
		?>
		<div class="container">	
			<div class="row">
				<div class="col-12 col-md-3 col-xl-3"></div>
				<div class="col-12 col-md-12 col-xl-6" style="margin: 20px 0">
					<div class="signin__body">
						<h1>Inscription</h1>
						<form method="post">
							<?php
								if(isset($er_lgn)){
							?>
							<div class="mess__err"><?= $er_lgn ?></div>
							<?php
								}
							?>
							<label>Pseudo</label>
							<input type="text" placeholder="Entrez votre pseudo" name="lgn" maxlength="20" value="<?php if(isset($r_lgn)){ echo $r_lgn; }?>" required>			
							<label>Sexe</label>
							<div style="text-align: center">
								<label class="switch-sex" style="text-align: center">
									<input type="checkbox" name="sex" checked> 
									<div class="slider-sex round"></div>
									<span style="position: absolute; left: -105px; top: 7px; width: 100px">Homme</span>
									<span style="position: absolute; left: 120px; top: 7px; width: 100px">Femme</span>
								</label>
							</div>
							<?php
								if(isset($er_birthday)){
							?>
							<div class="mess__err"><?= $er_birthday ?></div>
							<?php
								}
							?>
							<label>Date de naissance</label>
							<div class="row">
								<div class="col-4 col-md-4 col-xl-4">
									<select name="day">
										<?php
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
											if (isset($year) && !empty($year)){	
										?>  
										<option value="<?= $year ?>"><?= $year ?></option>
										<?php
											} 
										?>
										<option value="" hidden>Année</option>
										<?php
											for($r = date('Y', strtotime(date('Y-m-d') . '-18 years')); $r >= date('Y', strtotime(date('Y-m-d') . '-80 years')); $r--) {
										?>
										<option value="<?= $r ?>"><?= $r ?></option>
										<?php
											}	
										?>
									</select>
								</div>
							</div>
							<?php
								if(isset($er_ville)){
							?>
							<div class="mess__err"><?= $er_ville ?></div>
							<?php
								}
							?>
							<label>Ville</label>
							<input type="text" name="ville" id="r_ville" placeholder="Entrez votre ville" value="<?php if(isset($r_ville)) echo $r_ville; ?>" required>		
							<?php
								if(isset($er_mail)){
							?>
							<div class="mess__err"><?= $er_mail ?></div>
							<?php
								}
							?>
							<label>Mail</label>
							<input type="email" placeholder="Entrez votre mail" name="mail" value="<?php if(isset($r_mail)){ echo $r_mail; }?>" required>
							<?php
								if(isset($er_psw)){
							?>
							<div class="mess__err"><?= $er_psw ?></div>
							<?php
								}
							?>
							<label>Mot pas de passe</label>
							<input type="password" placeholder="Entrez votre mot de passe" name="psw" value="<?php if(isset($r_psw)){ echo $r_psw; }?>" required>
							<label>Confirmation du mot pas de passe</label>
							<input type="password" placeholder="Confirmer votre mot de passe" name="confpsw" required>
							<div class="signin__body__btn__con">
								<button type="submit" name="register" class="signin__body__btn">S'inscrire</button>
							</div>
						</form>
					</div>
				</div>
			</div>	
		</div>
		<?php
			include_once('_footer/footer.php');
		?>
	</body>
</html>

