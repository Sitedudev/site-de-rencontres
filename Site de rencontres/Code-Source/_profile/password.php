<?php
	include_once('../include.php');
	
	if (!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}

	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		if (isset($_POST['chgepsd'])){
			[$er_psd] = $__User->form_change_password($oldpsd, $newpsd, $cfmpsd);	
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('../_head/meta.php');
		?>
        <title>Modifier mon mot de passe | Daewen</title>
        <?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
			$profil__bar__number = 4;
			include_once('head_profile.php');
		?>
		<div class="container marg__bottom">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-4">
					<div class="profile__param__menu__body">
						<a href="<?= URL ?>parametres" class="profile__param_menu__link">
							<div class="profile__param_menu">Information sur le compte</div>
						</a>
						<a href="<?= URL ?>profil/modifier-avatar" class="profile__param_menu__link">
							<div class="profile__param_menu">Modifier ma photo de profil</div>
						</a>
						<a href="<?= URL ?>profil/modifier-mot-de-passe" class="profile__param_menu__link">
							<div class="profile__param_menu">
								Modifier mon mot de passe
							</div>
						</a>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-8">
					<div class="profile__param__mod__body">
						<form method="post">
							<div class="profile__param__form__stru">
								<?php
								if(isset($er_psd) && !empty($er_psd)){
								?>
								<div class="mess__err"><?= $er_psd ?></div>
								<?php
									}
								?>
								<label>Actuel</label>
								<input type="password" name="oldpsd" value="<?php if(isset($oldpsd)) echo $oldpsd; ?>" placeholder="Mot de passe actuel"/>
							</div>
							<div class="profile__param__form__stru">
								<label>Nouveau</label>
								<input type="password" name="newpsd" value="<?php if(isset($newpsd)) echo $newpsd; ?>" placeholder="Nouveau mot de passe"/>
							</div>
							<div class="profile__param__form__stru">
								<label>Confirmation</label>
								<input type="password" name="cfmpsd" value="" placeholder="Confirmez votre nouveau mot de passe"/>
							</div>
							<div class="profile__param__form__btn">
								<input type="submit" class="profil__param__form__btn__suc" name="chgepsd" value="Envoyer"/>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>