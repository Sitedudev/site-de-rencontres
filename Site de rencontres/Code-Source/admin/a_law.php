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
	
	$search = false;
	
	
	if (!empty($_POST)){
		extract($_POST);
		$valid = true;
	
		if (isset($_POST['sch'])){
			
			$user = trim($user);
			
			if (empty($user)){
				$valid = false;
			}
			
			
			if ($valid){
				
				$find_user = $DB->query("SELECT pseudo, guid, mail FROM user WHERE role = 0 AND pseudo like ?", 
					array($user . "%"));
				
				$find_user = $find_user->fetchAll();
				
				if (count($find_user) != 0){
					$search = true;		
				}
			}
		}elseif (isset($_POST['more'])){
			
			$guid = trim($guid);
			
			if (empty($guid)){
				$valid = false;
			}
			
			if ($valid){
				
				$DB->insert("UPDATE user SET role = 1 WHERE guid = ?", 
					array($guid));
					
				$_SESSION['flash']['success'] = "Nouveau modérateur";
				header('Location: ' . URL . 'admin/droit');
				exit;	
					
			}

		}elseif (isset($_POST['less'])){
			
			$guid = trim($guid);
			
			if (empty($guid)){
				$valid = false;
			}
			
			if ($valid){
				
				$DB->insert("UPDATE user SET role = 0 WHERE guid = ?", 
					array($guid));
				
				$_SESSION['flash']['success'] = "Modérateur supprimé";
				header('Location: ' . URL . 'admin/droit');
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
						    if ($_SESSION['role'] > 2){
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
								<a href="profil" style="color: #666; text-decoration: none">Mon profil</a>
							</div>
						</div>
						
						<div class="row" style="margin-bottom: 20px;font-size: 16px">
							<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
								<i class="fa fa-cog"></i>
							</div>
							
							<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
								<a href="settings" style="color: #666; text-decoration: none">Mes paramètres</a>
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
								<a href="logon" style="color: #666; text-decoration: none">Déconnexion</a>
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
				
				<?php
					$user_law = $DB->query("SELECT * FROM user WHERE role > 0");
					
					$user_law = $user_law->fetchAll();					
				?>
				
				
				<div class="col-xs-12 col-sm-12 col-md-12" style="margin-bottom: 20px">
					<h1>Super utilisateur</h1>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
					<?php
						
						$nb_user = 0;
						
						foreach($user_law as $u):
						
							if ($u['role'] > 1){
								
								$nb_user += 1;
								
								echo "<div>" . $u['pseudo'] . "</div>";															
							}
						
						endforeach;
						
						if ($nb_user == 0){
							echo "<span>Aucun</span>";
						}
							
					?>				
					</div>
				</div>
				
				
				<div class="col-xs-12 col-sm-12 col-md-12" style="margin-bottom: 20px">
					<h1>Modérateurs</h1>
					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
					
						<?php
							
							$nb_user = 0;
							$nb_line = 0;
							
							foreach($user_law as $u):
							
								if ($u['role'] == 1){
									
									$nb_user += 1;
									
									if ($nb_line == 0){
													
										$nb_line += 1;
													
									?>
										<div style="padding: 10px; position: relative"> <!-- OUVERTURE -->
											<a href="<?= URL . "profil/" . $u['guid'] ?>" style="color: #666; text-decoration: none"><?= $u['pseudo'] . " (" . $u['mail'] . ")" ?></a>
														
										<?php
									}else{
										?>
											<div style="padding: 10px; position: relative; border-top: 2px solid #ccc"> <!-- OUVERTURE -->
												<a href="<?= URL . "profil/" . $u['guid'] ?>" style="color: #666; text-decoration: none"><?= $u['pseudo'] . " (" . $u['mail'] . ")" ?></a>
											
										<?php											
									}			
								?>
										<div style="position: absolute; right: 0; top: 10px">
											<form method="post">		
												<input type="hidden" name="guid" value="<?= $u['guid'] ?>"/>
												<input type="submit" name="less" class="fa" value="&#xf063;" style="outline: none; background: white;  border: 1px solid #CCC"/>
											</form>
										</div>
									</div> <!-- FERMETURE -->
								<?php
												
								}
								
							endforeach;
							
							if ($nb_user == 0){
								echo "<span>Aucun</span>";
							}
							
						?>	
					</div>
				</div>
				
				<div class="col-xs-12 col-sm-12 col-md-12" style="margin-bottom: 20px">
					<h1>Ajouter</h1>
					<form method="post">
						<input type="text" name="user" placeholder="Saisissez le pseudo de la personne que vous recherchez"/>
						<input type="submit" name="sch" value="rechercher" style="outline: none; border: 1px solid #CCC; background: white; color: #666; padding: 5px 10px"/>
						
					</form>
					
					<?php
						if ($search){
							$nb_line = 0;	
						
						?>
							<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 0 15px; margin-top: 20px">
								
								<?php
									foreach($find_user as $u):

										if ($nb_line == 0){
											
											$nb_line += 1;
											
											?>
												<div style="padding: 10px; position: relative"> <!-- OUVERTURE -->
													<a href="<?= URL . "profil/" . $u['guid'] ?>" style="color: #666; text-decoration: none"><?= $u['pseudo'] . " (" . $u['mail'] . ")" ?></a>
												
											<?php
										}else{
											?>
												<div style="padding: 10px; position: relative; border-top: 2px solid #ccc"> <!-- OUVERTURE -->
													<a href="<?= URL . "profil/" . $u['guid'] ?>" style="color: #666; text-decoration: none"><?= $u['pseudo'] . " (" . $u['mail'] . ")" ?></a>
												
											<?php											
										}
										
										?>	
												<div style="position: absolute; right: 0; top: 10px">
													<form method="post">
														<input type="hidden" name="guid" value="<?= $u['guid'] ?>"/>
														<input type="submit" name="more" class="fa" value="&#xf062;" style="outline: none; background: white; border: 1px solid #CCC"/>
													</form>
												</div>
											</div> <!-- FERMETURE -->
										<?php
										
									endforeach;
								?>
							</div>
						<?php
						}	
					?>
					
					
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