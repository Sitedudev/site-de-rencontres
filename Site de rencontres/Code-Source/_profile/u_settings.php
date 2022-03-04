<?php
	include_once('../include.php');
	
	if (!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}

	
	if(!empty($_POST)){
		extract($_POST);
		
		if (isset($_POST['profile'])){		

			[$err_pseudo, $err_mail] = $__User->form_modification_user($lgn, $mail);

		}
	}

?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('../_head/meta.php');
		?>
        <title>Paramètres | Daewen</title>
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
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-12 col-xl-4">
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
				<div class="col-12 col-md-12 col-xl-8">
					<div class="profile__param__mod__body">
						<form method="post">
							<div class="profile__param__form__stru">
								<?php
									if(isset($err_pseudo) && !empty($err_pseudo)){
								?>
								<div class="mess__err"><?= $err_pseudo ?></div>
								<?php
									}
								?>
								<label>Nom d'utilisateur</label>
								<input type="text" name="lgn" value="<?= $_SESSION['pseudo']; ?>"/>
							</div>
							<div class="profile__param__form__stru">
								<?php
									if(isset($err_mail) && !empty($err_mail)){
								?>
								<div class="mess__err"><?= $err_mail ?></div>
								<?php
									}
								?>
								<label>Mail</label>
								<input type="email" name="mail" value="<?= $_SESSION['mail']; ?>"/>
							</div>
							<div class="profile__param__form__btn">
								<input type="submit" class="profil__param__form__btn__suc" name="profile" value="Envoyer"/>
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