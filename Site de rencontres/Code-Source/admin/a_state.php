<?php
	session_start();
	
	include ('../function/domaine.php');
	include ('../function/password.php');
	
	$domain = new Domain;
	$f_password = new Password;
	
	define("URL", $domain->domain());	
	
	if (!isset($_SESSION['guid'])){
		header('Location: ' . URL); // À mettre dans la page erreur not found plus tard
		exit;
	}
	
	if ($_SESSION['role'] < 2){ // que pour les admins
		header('Location: ' . URL);
		exit;
	}
	
	$user_guid = htmlentities($_GET['guid']);
	
	if (!isset($user_guid)){ // s'il n'y a pas de guid
		header('Location: ' . URL);
		exit;
	}
	
	include('../bd/connexionDB.php');
	
	
	$info_profile = $DB->query("SELECT * FROM user WHERE guid = ?", 
		array($user_guid));
		
	$info_profile = $info_profile->fetch();
	
	if (count($info_profile) == 0){ // s'il n'y a aucun compte 
		header('Location: ' . URL);
		exit;
	}
	
	if ($info_profile['role'] > 1){ // On ne peut pas modifier un admin
		header('Location: ' . URL . 'profil');
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
		<base href="<?= URL ?>"/>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gérer compte | Daewen</title>
        <link href="<?= URL ?>css/bootstrap.min.css" rel="stylesheet">
		<link href="<?= URL ?>css/style.css" rel="stylesheet" />
		<?php
			if(isset($_SESSION['theme']) && $_SESSION['theme'] <> 0){
				switch($_SESSION['theme']){
					case 1:
						$css_theme = "blue_theme";
					break;
					case 2:
						$css_theme = "dark_theme";
					break;
					case 3:
						$css_theme = "sobre_theme";
					break;
				}
		?>
			<link href="<?= URL ?>css/<?= $css_theme ?>.css" rel="stylesheet" />
		<?php	
			}
		?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="<?= URL ?>js/automatic_page_loard.js" type="text/javascript"></script>
		
	</head>
	
	<body>
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" onclick="openNav(this)">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar bar1"></span>
						<span class="icon-bar bar2"></span>
						<span class="icon-bar bar3"></span>
					</button>
					<a class="navbar-brand" href="<?= URL . "admin/dashboard" ?>">Dashboard</a>
			    </div>
			    
			    <div id="myNav" class="overlay">
					<div class="overlay-content">
						<?php
						    if ($_SESSION['role'] > 1){
							?>
							<a href="<?= URL . "admin/droit" ?>"> Droit</a>
							<?php	    
						    }
						?>
						<a href="<?= URL . "" ?>"> </a>
						<a href="" data-toggle="modal" data-target="#settings"><i class="fa fa-cog"></i> Paramètres</a>
					</div>
				</div>
				
			    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				    <ul class="nav navbar-nav">
					    <?php
						    if ($_SESSION['role'] > 1){
							?>
							<li><a href="<?= URL . "admin/droit" ?>" style="font-size: 16px"> Droit</a></li>
							<?php	    
						    }
						?>
				    </ul>
				    <ul class="nav navbar-nav navbar-right">
					    <li><a href="<?= URL . "" ?>" style="font-size: 16px"> </a></li>
					    <li><a href="" style="font-size: 16px" data-toggle="modal" data-target="#settings"><i class="fa fa-cog"></i> Paramètres</a></li>

				    </ul>
			    </div>
			</div>
		</nav>
		
		<?php 
		    if(isset($_SESSION['flash'])){ 
		        foreach($_SESSION['flash'] as $type => $message): ?>
				<div id="alert" class="alert alert-<?= $type; ?> infoMessage"><a class="closef">X</span></a>
					<?= $message; ?>
				</div>	
		    
		    <?php
			    endforeach;
			    unset($_SESSION['flash']);
			}
		?> 
		
		<div id="settings" class="modal fade">
			
			<div class="modal-dialog" role="document">
				<form method="post" class="modal-content animate" action="" style="padding-top: 10px">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="right: 15px;">&times;</button>
					
					<div class="container-fluid">
						<h3 style="margin-top: 0">Paramètres</h3>
						
						<div class="row" style="margin-bottom: 20px;font-size: 16px">
							<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
								<i class="fa fa-user-circle-o"></i>
							</div>
							
							<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
								<a href="<?= URL . "profil" ?>" style="color: #666; text-decoration: none">Mon profil</a>
							</div>
						</div>
						
						<div class="row" style="margin-bottom: 20px;font-size: 16px">
							<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
								<i class="fa fa-cog"></i>
							</div>
							
							<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
								<a href="<?= URL . "settings" ?>" style="color: #666; text-decoration: none">Mes paramètres</a>
							</div>
						</div>
						
						<?php
							if($_SESSION['role'] > 0){	
						?>
						
						<div class="row" style="margin-bottom: 20px;font-size: 16px">
							<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
								<i class="fa fa-th"></i>
							</div>
							
							<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
								<a href="<?= URL . "admin/dashboard" ?>" style="color: #666; text-decoration: none">Console</a>
							</div>
						</div>
						
						<?php
							}	
						?>
						
						<div class="row" style="margin-bottom: 20px;font-size: 16px">
							<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
								<i class="fa fa-power-off"></i>
							</div>
							
							<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
								<a href="<?= URL . "logon" ?>" style="color: #666; text-decoration: none">Déconnexion</a>
							</div>
						</div>	
											
					</div>
					
					<div class="container-fluid" style="background-color:#f1f1f1; padding: 10px;">
						<button type="button" data-dismiss="modal" class="cancelbtn">Annuler</button>
					</div>
				</form>
			</div>
		</div>
		
		
		<div class="container">
			
			<div class="row">
				
				<div class="col-xs-12 col-sm-12 col-md-12">
					<h1>État du compte</h1>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
						
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
						<div style="margin-bottom: 20px">Le compte est <i><?= $mess_state ?></i></div>
						
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
				
				<div class="col-xs-12 col-sm-12 col-md-12">
					<h1>Informations</h1>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">

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
					
								<div class="col-xs-4 col-sm-4 col-md-4">
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
								<div class="col-xs-4 col-sm-4 col-md-4">
									<select name="month">
										<?php
											
											$date_birhday = date_create($info_profile['birthday']);
											$month = date_format($date_birhday, "m");
											
											if (isset($month) && !empty($month)){	
												$monthName = $f_password->month($month);
										?>  
												<option value="<?= $month ?>"><?= $monthName ?></option>
										<?php
											} 
										?>
										<option value="" hidden>Mois</option>
										<?php
											for($r = 1; $r <= 12; $r++) {
												
											$monthName = $f_password->month($r);
												
										?>
											<option value="<?= $r ?>"><?= $monthName ?></option>
										<?php
											}	
										?>
									</select>
								</div>
								<div class="col-xs-4 col-sm-4 col-md-4">
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
				
				<div class="col-xs-12 col-sm-12 col-md-12" style="margin: 20px 0">
					<h1>Photos</h1>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 0 15px">
						<div class="row">			
							<?php 
								$user_picture = $DB->query("SELECT * FROM picture WHERE pseudo_id = ?", 
									array($info_profile['id']));
								
								$user_picture = $user_picture->fetchAll();
								
								if (count($user_picture) == 0){
									echo "<div style='padding: 10px'>Aucune photo</div>";
								}
														
								foreach($user_picture as $u):
									
									?>
										<div class="col-xs-6 col-sm-4 col-md-3" style="margin: 20px 0; text-align: center">		
											<form method="post">
												<img src="<?= URL . "public/pictures/". $info_profile['guid'] . "/" . $u['name'] ?>" style="width: 100%"/>
												<input type="hidden" name="nameimg" value="<?= $u['name'] ?>"/>
												<input type="submit" name="dltimg" value="Supprimer" style="border: none; background: #c0392b; width: 100%; color: white; outline: none; font-weight: 400"/>
											</form>
										</div>
									<?php
								
								endforeach;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<footer>
			<i class="fa fa-twitter social-cust"></i>
			<i class="fa fa-facebook social-cust"></i>
			<i class="fa fa-google-plus social-cust"></i>
		</footer>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="<?= URL ?>js/bootstrap.min.js"></script>
		<script src="<?= URL ?>js/register.js"></script>
		<script>				
			var isopen = false;
			
			function openNav(x) {
				
				if(!isopen){
					isopen = !isopen;
					document.getElementById("myNav").style.height = "100%";
					x.classList.toggle("change");
				}else{
					isopen = !isopen;
					document.getElementById("myNav").style.height = "0%";
					x.classList.toggle("change");
				}
			    	
			}
		</script>
		
	</body>
</html>