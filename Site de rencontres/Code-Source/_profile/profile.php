<?php
	include_once('../include.php');
		
	if (!isset($_SESSION['id'])){
		header('Location: ' . URL);
		exit;
	}		


	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
	
		if (isset($_POST['avatar'])){	

			$Upload_avatar = new Image($_FILES['file']);
			
			$Upload_avatar->upload_img($_FILES['file']);

		}elseif(isset($_POST['dlt'])){
			
			$nameimg = trim($nameimg);
			
			$__Image->delete_img($nameimg);
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('../_head/meta.php');
		?>
        <title>Profil | Daewen</title>
		<?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
			include_once('head_profile.php');
		?>
		<div class="container">
			<div class="row">
				<div class="profile__gal__body">
					<div class="profile__gal__list effectArt">
						<span class="image-upload-pfe">
							<form method="post" enctype="multipart/form-data" style="position: absolute;left: 50%;top: 50%;transform: translate(-50%,-50%);">
								<label for="file" style="display: inline-flex">
								    <input id="file" type="file" name="file" class="hide-upload-pfe" required/>
								    <i class="image-plus-pfe" style="background-image: url('<?= URL ?>public/others/plus.svg');"></i>
								    <button type="submit" id="send_pfe" class="send-upload-pfe" name="avatar"><i class="bi bi-cloud-upload"></i></button>
								</label>
							</form>
						</span>
					</div>
					<?php
						$show_album = $DB->prepare("SELECT * 
							FROM picture 
							WHERE pseudo_id = ? ORDER BY date_upload DESC");
						$show_album->execute(array($_SESSION['id']));
						
						foreach($show_album as $key => $a){
							$key += 1;							
					?>
					<div class="profile__gal__list">
						<a href="<?= URL ?>photo/<?= $a['id'] ?>" class="profile__gal__more">Voir plus</a>
						<div class="profile__gal__info">
							<div class="profile__gal__info__lik">
								<i class="bi bi-hearts"></i>
								<span class="numberArt"><?= $a['v_like'] ?></span>
							</div>
							<div class="profile__gal__info__del">
								<span class="SousCatArt">
									<form method="post" action="">
										<input type="hidden" name="nameimg" value="<?= $a['name'] ?>"/>
										<button type="submit" name="dlt" style="border: none; background: transparent; outline: none"><i class="bi bi-trash"></i></button>
									</form>
								</span>
							</div>
						</div>
						<div class="profile__gal__image">
							<a href="<?= URL ?>photo/<?= $a['id'] ?>">
								<img width="300" height="170" src="<?= URL . "public/pictures/". $_SESSION['guid'] . "/" . $a['name'] ?>" alt="" class="profile__gal__img"/>
							</a>
						</div>
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