<?php
	include_once('../include.php');
		
	if (!isset($_SESSION['id'])){
		header('Location: ' . URL);
		exit;
	}	
	
	if ($_SESSION['role'] < 1){
		header('Location: ' . URL);
		exit;
	}
	
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
        <title>Dashboard | Daewen</title>
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
					<h1>Annonces</h1>					
					<div style="background: white; border: 4px solid #ccc; border-radius: 20px; font-weight: bold; padding: 10px 15px">
						<li>Nouveautés V2.0: </li>
						<ul>
							<li>Nouveau design</li>
							<li>Utilisation de Boostrap 5 sur l'intégralité du site</li>
							<li>Nouveau footer (pied de page)</li>
							<li>Accès au dashboard</li>
							<li>Espace membre revue (profil, paramètres, modifier informations, etc.)</li>
							<li>Corection de bogues</li>
							<li>Optimisation du code</li>
						</ul>
					</div>
				</div>
				<div class="col-12 col-md-12 col-xl-12">
					<div style="margin: 20px 0">
						<a href="<?= URL ?>admin/gest">Gérer modérateurs / administrateurs</a>
					</div>
				</div>
				<div class="col-12 col-md-12 col-xl-12" style="margin: 20px 0">
					<h1>Utilisateurs</h1>
					<?php 
						$user = $DB->prepare("SELECT * 
							FROM user");
						
						$user->execute();

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
				<div class="col-12 col-md-12 col-xl-12" style="margin: 20px 0">
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
							<div class="col-12 col-md-4 col-xl-4">
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
							<div class="col-12 col-md-4 col-xl-4">
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
				
				<div class="col-12 col-md-12 col-xl-12" style="margin: 20px 0">
					<h1>Vues</h1>
					<?php
						$view = $DB->prepare("SELECT * 
							FROM a_view");

						$view->execute();
						
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
							<div class="col-12 col-md-12 col-xl-4">
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
							<div class="col-12 col-md-12 col-xl-4">
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
		<?php
			include_once('../_footer/footer.php');
		?>		
	</body>
</html>