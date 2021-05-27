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
	
	if(isset($_SESSION['id'])){
		header('Location: ' . URL);
		exit;
	}
	
	$mail = NULL;
	$psw = NULL;
	$remember = NULL;
	$er_mail = NULL;
	$er_psw = NULL;
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		if (isset($_POST['signin'])){				
			list($er_mail, $er_psw) = $__User->verif_connexion($mail, $psw, $remember);
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
		include_once('_head/meta.php');
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
				<div class="col-12 col-md-12 col-xl-3"></div>
				<div class="col-12 col-md-12 col-xl-6" style="margin: 20px 0">
					<div class="signin__body">
						<h1>Se connecter</h1>
						<form method="post">
							<?php
								if(isset($er_mail)){
							?>
							<div class="mess__err"><?= $er_mail ?></div>
							<?php
								}
							?>
							<label for="mail">Mail</label>
							<input type="email" id="mail" placeholder="Entrez votre mail" name="mail" value="<?php if(isset($mail) && !isset($_COOKIE['comail'])){ echo $mail; } if(isset($_COOKIE['comail'])){ echo urldecode($_COOKIE['comail']); }?>" required>
							<?php
								if(isset($er_psw)){
							?>
							<div class="mess__err"><?= $er_psw ?></div>
							<?php
								}
							?>
							<label for="pws">Mot pas de passe</label>
							<input type="password" id="pws" placeholder="Entrez votre mot de passe" name="psw" value="<?php if(isset($_COOKIE['copassword'])){ echo $__Crypto->decryptWithPassword($_COOKIE['copassword'], $__Secret__key); } ?>" required>
							<div class="signin__body__btn__con">
								<button type="submit" name="signin" class="signin__body__btn">Connexion</button>
							</div>
							<label class="switch">
								<input type="checkbox" name="remember" <?php if(isset($_COOKIE['copassword']) && ($_COOKIE['copassword']!="")) {echo "checked";}?>> 
								<div class="slider round"></div>
								<span style="position: absolute; left: 70px; top: 7px; width: 100px">Se souvenir</span>
							</label>
							<div class="signin__body__btn__footer">
								<a href="<?= URL ?>forgot" class="signin__body__btn__psw">
									Mot de passe oublié ?
								</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
			include_once('_footer/footer.php');
		?>
	</body>
</html>