<?php
	/**
	 * @package			: Code source rencontres
	 * @version			: 1.0
	 * @author			: sitedudev aka clouder
	 * @link 			: https://sitedudev.com
	 * @since			: 2021
	 * @license			: Attribution-NomCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)
	 */
	include_once('include.php');
	
	if (!isset($_SESSION['id'])){
		header('Location: ' . URL);
		exit;
	}
	
	$__Online->online();
	
	$memo_search = $DB->prepare("SELECT * 
		FROM memo_search 
		WHERE pseudo_id = ?");
	$memo_search->execute(array($_SESSION['id']));
	
	$memo_search = $memo_search->fetch();
	
	$search_sex = (String) "u.sexe <> ";
	$search_dept = (String) "u.departement_code <> ";
	
	$var_search_sex = (String) "";
	$var_search_dept = (String) "";
	
	$verif_memo_search = false;
	
	if(isset($memo_search['id'])){
		
		$verif_memo_search = true;
		
		if($memo_search['sex'] > 0){
			$search_sex = "u.sexe = ";
			$var_search_sex = $memo_search['sex'];
		}
		
		if($memo_search['departement_code'] > 0){
			$search_dept = "u.departement_code = ";
			$var_search_dept = $memo_search['departement_code'];
		}
	}
	
	$search_profile = $DB->prepare("SELECT u.*, d.departement_nom
		FROM user u
		LEFT JOIN departement d ON d.departement_code = u.departement_code
		WHERE u.guid <> ? AND " . $search_sex . " ? AND " . $search_dept . " ?
		ORDER BY u.date_connection DESC");
	
	$search_profile->execute(array($_SESSION['guid'], $var_search_sex, $var_search_dept));

	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		if(isset($_POST['srch'])){
			
			// ---- verif sex
			if(!isset($sex)){
				$er_sex = "Sélectionner un sexe";
			
			}elseif($sex == 0){	
				$sex = 0; // 0 : ALL
				
			}elseif($sex == 1){
				$sex = 1; // 1 : Man
				//$checked = "";				
				
			}else{
				$sex = 2; // 2 : Women
				//$checked = "checked";
			}
			
			// ---- verif département
			if(!isset($dep) && empty($dep)){
				$valid = false;
				$er_dep = "Un département est obligatoire";
				
			}else{
				
				if($dep <> 0){
					
					$req_dep = $DB->prepare("SELECT departement_code as a, departement_nom as c 
						FROM departement 
						WHERE departement_code = ?");
					$req_dep->execute(array($dep));
					
					$req_dep = $req_dep->fetch();
					
					if ($req_dep['a'] == ""){
						$valid = false;
						$er_dep = "Le département n'existe pas";
						
					}else{
						$code_dep = $req_dep['a'];
					}
				}else{
					$code_dep = 0;
				}
			}

			if($valid){
					
				if($verif_memo_search){				
					$req = $DB->prepare("UPDATE memo_search SET sex = ?, departement_code = ? WHERE pseudo_id = ?");
					$req->execute(array($sex, $code_dep, $_SESSION['id']));
				}else{
					$req = $DB->prepare("INSERT INTO memo_search (pseudo_id, sex, departement_code) VALUES (?, ?, ?)");
					$req->execute(array($_SESSION['id'], $sex, $code_dep));
				}
				
				$_SESSION['flash']['success'] = "Nouvelle recherche enregistrée";
				header('Location: ' . URL . 'search');
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
        <title>Autour de moi | Daewen</title>
        <?php
		include_once('_head/link.php');
		include_once('_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('menu.php');
		?>
		<div class="container" id="show-search">
			<div class="search__search__body">
				<form method="post">
					<div class="row">
						<div class="col-12 col-md-2 col-xl-2"></div>
						<div class="col-12 col-md-4 col-xl-4">
							<label>Sexe</label>					
							<select name="sex">
								<?php
									if(isset($memo_search['sex'])) {
										if ($memo_search['sex'] == 1){ 
									?>
									<option value="1" hidden>Homme</option>
									<?php
										}elseif($memo_search['sex'] == 2){
									?>
									<option value="2" hidden>Femme</option>
									<?php		
										}
									?>
									<option value="0">Tout</option>
									
								<?php	
									}else{
								?>
									<option value="0" selected>Tout</option>
								<?php
									}
								?>
								
								<option value="1">Homme</option>
								<option value="2">Femme</option>
							</select>
						</div>
						<div class="col-12 col-md-4 col-xl-4">
							<label>Département</label>
							<select name="dep">
							<?php
								if(isset($memo_search['departement_code']) && !empty($memo_search['departement_code'])) {
									$req_search_dep = $DB->prepare("SELECT departement_code AS a, departement_nom AS b 
										FROM departement 
										WHERE departement_code = ?");
									$req_search_dep->execute(array($memo_search['departement_code']));
									$r = $req_search_dep->fetch();
							?>
								<option value="<?= $r['a'] ?>" hidden><?= $r['a'] . " " . $r['b'] ?></option>
								<option value="0">Tout</option>
							<?php
								
								}else{
							?>
								<option value="0" selected>Tout</option>
							<?php
								}
								
								$req_search_dep = $DB->prepare("SELECT departement_code AS a, departement_nom AS b 
									FROM departement");
								$req_search_dep->execute();
								
								foreach($req_search_dep as $r):	
							?>
					
								<option value="<?= $r['a'] ?>"><?= $r['a'] . " " . $r['b'] ?></option>
							
							<?php
								endforeach;	
							?>
							</select>
						</div>
						<div class="col-12 col-md-12 col-xl-12" style="text-align: center">
							<input type="submit" name="srch" class="fa" value="&#xf140;" style="border: none; background: transparent; font-size: 30px; outline: none"/>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="container">
			<div class="row">
			<?php						
				foreach($search_profile as $sp){
					
					if ($sp['role'] < 2 || $_SESSION['role'] > 0){
			?>
				<div class="col-6 col-md-4 col-xl-3" style="text-align: center; margin-bottom: 10px">
					<div class="search__body__card">
						<a href="<?= URL ?>profil/<?= $sp['guid'] ?>">
							<img src="<?= URL . $__User->getAvatar($sp['guid']) ?>" class="search__body__card__img"/>
						</a>
						<div>
							<div>
								<?php
									$rep_online = $__Online->is_online($sp['date_connection']);	
									
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
									echo $sp['pseudo'];
								?>	
							</div>	
							<div class="search__body__card__info">
								<div>
									<?= $sp['departement_nom'] ?>
								</div>
								<div>
									<?= $__Crypt_password->age($sp['birthday']); ?> ans
								</div>
							</div>
							<div class="search__body__card__seemore">
								<a href="<?= URL ?>profil/<?= $sp['guid'] ?>" class="search__body__card__seemore__btn">Voir profil</a>
							</div>
						</div>
					</div>			
				</div>
			<?php
					}
				}	
			?>
			</div>
		</div>
		<?php
		include_once('_footer/footer.php');
		?>
	</body>
</html>