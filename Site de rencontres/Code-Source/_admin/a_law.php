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

	$user_law = $DB->prepare("SELECT id, guid, pseudo, mail, role 
		FROM user 
		WHERE role > ?
		ORDER BY role DESC, pseudo");

	$user_law->execute([0]);
	
	$user_law = $user_law->fetchAll();					
	
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
				
				$find_user = $DB->prepare("SELECT pseudo, guid, mail 
					FROM user 
					WHERE role = 0 AND pseudo like ?"); 
				
				$find_user->execute([$user . "%"]);
				
				$find_user = $find_user->fetchAll();
				
				if (count($find_user) != 0){
					$search = true;		
				}
			}
		}elseif (isset($_POST['plus'])){
			
			$guid = trim($guid);
			
			if(empty($guid)){
				$valid = false;
			}

			$Info_user = new User($guid);
				
			if(empty($Info_user->guid)){
				$valid = false;
			};

			if($Info_user->getRole() >= $_SESSION['role'] || (($Info_user->getRole() + 1) == 3)){
				$valid = false;
			}

			if($valid){

				$req = $DB->prepare("UPDATE user SET role = ? WHERE guid = ?"); 
				$req->execute([($Info_user->getRole() + 1), $Info_user->guid]);
					
				$_SESSION['flash']['success'] = "L'utilisateur a été promu";
				header('Location:' . CURRENT_URL);
				exit;	
					
			}

		}elseif (isset($_POST['less'])){
			
			$guid = trim($guid);
			
			if(empty($guid)){
				$valid = false;
			}

			$Info_user = new User($guid);
				
			if(empty($Info_user->guid)){
				$valid = false;
			};

			if($Info_user->getRole() >= $_SESSION['role'] || (($Info_user->getRole() - 1) == 3)){
				$valid = false;
			}

			if($valid){

				$req = $DB->prepare("UPDATE user SET role = ? WHERE guid = ?"); 
				$req->execute([($Info_user->getRole() - 1), $Info_user->guid]);
					
				$_SESSION['flash']['success'] = "L'utilisateur a été rétrogradé";
				header('Location:' . CURRENT_URL);
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
        <title>Gestion admin / modérateur | Daewen</title>
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
						<table class="table">
						 	<thead>
							    <tr>
							      <th scope="col">#</th>
							      <th scope="col">Pseudo</th>
							      <th scope="col">Role</th>
							      <th scope="col"></th>
							      <th scope="col"></th>
							    </tr>
						  	</thead>
						  	<tbody>
						  		<?php
						  			$cpt = 1;

						  			foreach($user_law as $ul){
						  				$lib_role = '';
						  				switch ($ul['role']){
						  					case 3:
						  						$lib_role = "Super Admin";
						  					break;
						  					case 2:
						  						$lib_role = "Administrateur";
						  					break;
						  					case 1:
						  						$lib_role = "Modérateur";
						  					break;
						  				}
						  		?>
							    <tr>
									<th scope="row"><?= $cpt++ ?></th>
									<td><?= $ul['pseudo'] ?></td>
									<td><?= $lib_role ?></td>
									<td>
									<?php
										if($ul['role'] < 2){
									?>
							      	<form method="post">
							      		<input type="hidden" name="guid" value="<?= $ul['guid'] ?>">
							      		<button type="submit" name="plus" class="admin__law__btn__plus">
							      			<i class="bi bi-plus-lg"></i>
							      		</button>
							      	</form>
							      	<?php
							      		}
							      	?>
							      </td>
							      <td>
									<?php
										if($ul['role'] < 3){
									?>
							      	<form method="post">
							      		<input type="hidden" name="guid" value="<?= $ul['guid'] ?>">
							      		<button type="submit" name="less" class="admin__law__btn__less">
							      			<i class="bi bi-dash-lg"></i>
							      		</button>
							      	</form>
							      	<?php
							      		}
							      	?>
							      </td>
							    </tr>
							    <?php
							    	}
							    ?>
						  	</tbody>
						</table>
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
		<?php
			include_once('../_footer/footer.php');
		?>		
	</body>
</html>