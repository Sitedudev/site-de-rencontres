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
	
	if ($_SESSION['role'] < 1){
		header('Location: ' . URL);
		exit;
	}
	
	include('../bd/connexionDB.php');
	
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
	
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<base href="<?= URL ?>"/>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Console | Daewen</title>
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
					<h1>Annonces</h1>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
						<li>Nouveautés : </li>
						<ul>
							<li>Gérer les comptes des utilisateurs</li>
						</ul>
						
						<br>
						<li>Anciennes annonces :</li>
						<ul>
							<li>Possibilité d'ajouter directement une personne pour un admin / modo</li>
							<li>Envoi de mail lorsqu'un message est envoyé à un admin / modo</li>
							<li>Statistiques (comptes, vues)</li>
							<li>Filtres des insultes activés sur le changement de pseudo</li>
						</ul>
						
						<br>
						
						<li>Prochainement :</li>
						<ul>
							<li>Gestion de l'avatar</li>
							<li>Report des photos</li>
						</ul>
						
					</div>
					
				</div>
				
				<div class="col-xs-12 col-sm-12 col-md-12" style="margin: 20px 0">
					<h1>Utilisateurs</h1>
										
					<?php 
						$user = $DB->query("SELECT * FROM user");
						
						$user = $user->fetchAll();
						
						$nb_men = 0; 
						$nb_woman = 0;
						
						foreach($user as $u):
							
							if ($u['sexe'] == 1){
								$nb_men += 1;
								
							}else{
								$nb_woman += 1;
							}
						
						endforeach;
					?>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
						<?= count($user) ?> utilisateurs
						<br>
						<br>
						<?= $nb_men ?> hommes
						<br>
						<br>
						<?= $nb_woman ?> femmes
						
					</div>
				</div>

				
				<div class="col-xs-12 col-sm-12 col-md-12" style="margin: 20px 0">
					<h1>Comptes créés</h1>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-4">
								<div style="font-size: 20px; margin-bottom: 15px">Aujourd'hui</div>
								<?php 
									
									$nb_account_create = 0;
									$nb_account_create_pourcent = 0;
									$pourcent_account_create = 0;
									
									foreach($user as $u):
										
										$d = date_create($u['date_registration']);
										
										if (date_format($d, "Y-m-d") == date("Y-m-d")){
											$nb_account_create += 1;
											
										}elseif (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 1 day'))){
											$nb_account_create_pourcent += 1;
											
										}
									
									endforeach;
									
									if ($nb_account_create == $nb_account_create_pourcent){ // (10 == 10) : 0%
											
										$pourcent_account_create = 0; 
										
										
									}elseif ($nb_account_create > $nb_account_create_pourcent){ // (20 > 10) : 
										
										if ($nb_account_create_pourcent == 0){
										
											$pourcent_account_create = "+" . $nb_account_create;	
									
										}else{
											
											$pourcent_account_create = ( $nb_account_create * 100) / $nb_account_create_pourcent;
											$pourcent_account_create = "+" . number_format($pourcent_account_create, 0, '', '');	
											
										}
										
									}elseif ($nb_account_create < $nb_account_create_pourcent){
										
										if ($nb_account_create == 0){
										
											$pourcent_account_create = 100 - $nb_account_create;	
											$pourcent_account_create = "-" . $pourcent_account_create;	
									
										}else{
											
											$pourcent_account_create = 100 - ( $nb_account_create * 100) / $nb_account_create_pourcent;
											$pourcent_account_create = "-" . number_format($pourcent_account_create, 0, '', '');		
											
										}
										
									}
									
									echo $nb_account_create . "<sup>(" . $pourcent_account_create . "%)</sup>";
								
									
								?>
							</div>
							<div class="col-xs-12 col-sm-4 col-md-4">
								<div style="font-size: 20px; margin-bottom: 15px">La semaine</div>
								<?php 
									
									$nb_account_create = 0;
									$nb_account_create_pourcent = 0;
									$pourcent_account_create = 0;
									
									foreach($user as $u):
										
										$d = date_create($u['date_registration']);
										
										if (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 1 week'))){
											$nb_account_create += 1;
											
										}elseif (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 2 week'))){
											$nb_account_create_pourcent += 1;
											
										}
									
									endforeach;
									
									if ($nb_account_create == $nb_account_create_pourcent){ // (10 == 10) : 0%
											
										$pourcent_account_create = 0; 
										
										
									}elseif ($nb_account_create > $nb_account_create_pourcent){ // (20 > 10) : 
										
										if ($nb_account_create_pourcent == 0){
										
											$pourcent_account_create = "+" . $nb_account_create;	
									
										}else{
											
											$pourcent_account_create = ( $nb_account_create * 100) / $nb_account_create_pourcent;
											$pourcent_account_create = "+" . number_format($pourcent_account_create, 0, '', '');	
											
										}
										
									}elseif ($nb_account_create < $nb_account_create_pourcent){
										
										if ($nb_account_create == 0){
										
											$pourcent_account_create = 100 - $nb_account_create;	
											$pourcent_account_create = "-" . $pourcent_account_create;	
									
										}else{
											
											$pourcent_account_create = 100 - ( $nb_account_create * 100) / $nb_account_create_pourcent;
											$pourcent_account_create = "-" . number_format($pourcent_account_create, 0, '', '');		
											
										}
										
									}
									
									echo $nb_account_create . "<sup>(" . $pourcent_account_create . "%)</sup>";
								
								?>
							</div>
							<div class="col-xs-12 col-sm-4 col-md-4">
								<div style="font-size: 20px; margin-bottom: 15px">Le mois</div>
								<?php 
									
									$nb_account_create = 0;
									$nb_account_create_pourcent = 0;
									$pourcent_account_create = 0;
									
									foreach($user as $u):
										
										$d = date_create($u['date_registration']);
										
										if (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 1 month'))){
											$nb_account_create += 1;
											
										}elseif (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 2 month'))){
											$nb_account_create_pourcent += 1;
											
										}
									
									endforeach;
									
									if ($nb_account_create == $nb_account_create_pourcent){ // (10 == 10) : 0%
											
										$pourcent_account_create = 0; 
										
										
									}elseif ($nb_account_create > $nb_account_create_pourcent){ // (20 > 10) : 
										
										if ($nb_account_create_pourcent == 0){
										
											$pourcent_account_create = "+" . $nb_account_create;	
									
										}else{
											
											$pourcent_account_create = ( $nb_account_create * 100) / $nb_account_create_pourcent;
											$pourcent_account_create = "+" . number_format($pourcent_account_create, 0, '', '');	
											
										}
										
									}elseif ($nb_account_create < $nb_account_create_pourcent){
										
										if ($nb_account_create == 0){
										
											$pourcent_account_create = 100 - $nb_account_create;	
											$pourcent_account_create = "-" . $pourcent_account_create;
									
										}else{
											
											$pourcent_account_create = 100 - ( $nb_account_create * 100) / $nb_account_create_pourcent;
											$pourcent_account_create = "-" . number_format($pourcent_account_create, 0, '', '');		
											
										}
										
									}
									
									echo $nb_account_create . "<sup>(" . $pourcent_account_create . "%)</sup>";
									
								?>
							</div>
						</div>
					</div>

				</div>
				
				<div class="col-xs-12 col-sm-12 col-md-12" style="margin: 20px 0">
					<h1>Vues</h1>
					
					<?php
						$view = $DB->query("SELECT * FROM a_view");
						
						$view = $view->fetchAll();	
						
					?>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-4">
								<div style="font-size: 20px; margin-bottom: 15px">Aujourd'hui</div>
								
								<?php
								
									$nb_view_unique = 0;
									$nb_view_unique_pourcent = 0;
									$pourcent_view_unique = 0;
									
									foreach($view as $v):
										
										$d = date_create($v['date_view']);
										
										if (date_format($d, "Y-m-d") == date('Y-m-d')){
											$nb_view_unique += 1;
											
										}elseif (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 1 day'))){
											$nb_view_unique_pourcent += 1;
											
										}											
									
									endforeach;
									
									
									if ($nb_view_unique == $nb_view_unique_pourcent){ // (10 == 10) : 0%
										
										$pourcent_view_unique = 0; 
										
										
									}elseif ($nb_view_unique > $nb_view_unique_pourcent){ // (20 > 10) : 
										
										if ($nb_view_unique_pourcent == 0){
										
											$pourcent_view_unique = "+" . $nb_view_unique;	
									
										}else{
											
											$pourcent_view_unique = ( $nb_view_unique * 100) / $nb_view_unique_pourcent;
											$pourcent_view_unique = "+" . number_format($pourcent_view_unique, 0, '', '');	
											
										}
										
									}elseif ($nb_view_unique < $nb_view_unique_pourcent){
										
										if ($nb_view_unique == 0){
										
											$pourcent_view_unique = 100 - $nb_view_unique;	
											$pourcent_view_unique = "-" . $pourcent_view_unique;	
									
										}else{
											
											$pourcent_view_unique = 100 - ( $nb_view_unique * 100) / $nb_view_unique_pourcent;
											$pourcent_view_unique = "-" . number_format($pourcent_view_unique, 0, '', '');		
											
										}
										
									}
									
									echo $nb_view_unique . "<sup>(" . $pourcent_view_unique . "%)</sup>";
									
								?>
								
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4">
								<div style="font-size: 20px; margin-bottom: 15px">La semaine</div>
								
								<?php
								
									$nb_view_unique = 0;
									$nb_view_unique_pourcent = 0;
									$pourcent_view_unique = 0;
									
									foreach($view as $v):
										
										$d = date_create($v['date_view']);
										
										if (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 1 week'))){
											$nb_view_unique += 1;
											
										}elseif (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 2 week'))){
											$nb_view_unique_pourcent += 1;
											
										}
									
									endforeach;
									
									if ($nb_view_unique == $nb_view_unique_pourcent){ // (10 == 10) : 0%
										
										$pourcent_view_unique = 0; 
										
										
									}elseif ($nb_view_unique > $nb_view_unique_pourcent){ // (20 > 10) : 
										
										if ($nb_view_unique_pourcent == 0){
										
											$pourcent_view_unique = "+" . $nb_view_unique;	
									
										}else{
											
											$pourcent_view_unique = ( $nb_view_unique * 100) / $nb_view_unique_pourcent;
											$pourcent_view_unique = "+" . number_format($pourcent_view_unique, 0, '', '');	
											
										}
										
									}elseif ($nb_view_unique < $nb_view_unique_pourcent){
										
										if ($nb_view_unique == 0){
										
											$pourcent_view_unique = 100 - $nb_view_unique;	
											$pourcent_view_unique = "-" . $pourcent_view_unique;	
									
										}else{
											
											$pourcent_view_unique = 100 - ( $nb_view_unique * 100) / $nb_view_unique_pourcent;	
											$pourcent_view_unique = "-" . number_format($pourcent_view_unique, 0, '', '');
											
										}
										
									}

									echo $nb_view_unique . "<sup>(" . $pourcent_view_unique . "%)</sup>";
									
								?>
								
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4">
								<div style="font-size: 20px; margin-bottom: 15px">Le mois</div>
								
								<?php
								
									$nb_view_unique = 0;
									$nb_view_unique_pourcent = 0;
									$pourcent_view_unique = 0;
									
									foreach($view as $v):
										
										$d = date_create($v['date_view']);
										
										if (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 1 month'))){
											$nb_view_unique += 1;
											
										}elseif (date_format($d, "Y-m-d") >= date('Y-m-d', strtotime(date("Y-m-d") . ' - 2 month'))){
											$nb_view_unique_pourcent += 1;
											
										}
									
									endforeach;
									
									if ($nb_view_unique == $nb_view_unique_pourcent){ // (10 == 10) : 0%
										
										$pourcent_view_unique = 0; 
										
										
									}elseif ($nb_view_unique > $nb_view_unique_pourcent){ // (20 > 10) : 
										
										if ($nb_view_unique_pourcent == 0){
										
											$pourcent_view_unique = "+" . $nb_view_unique;	
									
										}else{
											
											$pourcent_view_unique = ( $nb_view_unique * 100) / $nb_view_unique_pourcent;
											$pourcent_view_unique = "+" . number_format($pourcent_view_unique, 0, '', '');	
											
										}
										
									}elseif ($nb_view_unique < $nb_view_unique_pourcent){
										
										if ($nb_view_unique == 0){
										
											$pourcent_view_unique = 100 - $nb_view_unique;	
											$pourcent_view_unique = "-" . $pourcent_view_unique;
									
										}else{
											
											$pourcent_view_unique = 100 - ( $nb_view_unique * 100) / $nb_view_unique_pourcent;	
											$pourcent_view_unique = "-" . number_format($pourcent_view_unique, 0, '', '');
											
										}
										
									}

									echo $nb_view_unique . "<sup>(" . $pourcent_view_unique . "%)</sup>";									
								?>
								
							</div>
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