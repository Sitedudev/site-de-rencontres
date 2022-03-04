<?php
	include_once('include.php');
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
	
		if (isset($_POST['register'])){
			
			[$er_lgn, $er_mail, $er_psw, $er_birthday, 
				$er_sex, $er_ville] = $__User->form_inscription($lgn, $mail, $psw, $confpsw, $ville, $sex, 
				$day, $month, $year);
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
								if(isset($er_lgn) && !empty($er_lgn)){
							?>
							<div class="mess__err"><?= $er_lgn ?></div>
							<?php
								}
							?>
							<label>Pseudo</label>
							<input type="text" placeholder="Entrez votre pseudo" name="lgn" maxlength="20" value="<?php if(isset($lgn)){ echo $lgn; }?>" required>	
							<?php
								if(isset($er_sex) && !empty($er_sex)){
							?>
							<div class="mess__err"><?= $er_sex ?></div>
							<?php
								}
							?>		
							<label>Sexe</label>
							<div style="display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap;">
								<label class="container__radio">Homme
							  		<input type="radio" <?php if((isset($sex) && $sex == 1) || (!isset($sex))){ echo 'checked="checked"';} ?> name="sex" value="1">
							  		<span class="checkmark"></span>
								</label>
								<label class="container__radio">Femme
							  		<input type="radio" <?php if(isset($sex) && $sex == 2){ echo 'checked="checked"';} ?> name="sex" value="2">
							  		<span class="checkmark"></span>
								</label>
								<label class="container__radio">Autres
							  		<input type="radio" <?php if(isset($sex) && $sex == 3){ echo 'checked="checked"';} ?> name="sex" value="3">
							  		<span class="checkmark"></span>
								</label>
							</div>
							<!--<div style="text-align: center">
								<label class="switch-sex" style="text-align: center">
									<input type="checkbox" name="sex" checked> 
									<div class="slider-sex round"></div>
									<span style="position: absolute; left: -105px; top: 7px; width: 100px">Homme</span>
									<span style="position: absolute; left: 120px; top: 7px; width: 100px">Femme</span>
								</label>
							</div>-->
							<?php
								if(isset($er_birthday) && !empty($er_birthday)){
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
								if(isset($er_ville) && !empty($er_ville)){
							?>
							<div class="mess__err"><?= $er_ville ?></div>
							<?php
								}
							?>
							<label>Ville</label>
							<input type="text" name="ville" id="r_ville" placeholder="Entrez votre ville" value="<?php if(isset($ville)) echo $ville; ?>" required>		
							<?php
								if(isset($er_mail) && !empty($er_mail)){
							?>
							<div class="mess__err"><?= $er_mail ?></div>
							<?php
								}
							?>
							<label>Mail</label>
							<input type="email" placeholder="Entrez votre mail" name="mail" value="<?php if(isset($mail)){ echo $mail; }?>" required>
							<?php
								if(isset($er_psw) && !empty($er_psw)){
							?>
							<div class="mess__err"><?= $er_psw ?></div>
							<?php
								}
							?>
							<label>Mot pas de passe</label>
							<input type="password" placeholder="Entrez votre mot de passe" name="psw" value="<?php if(isset($psw)){ echo $psw; }?>" required>
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

