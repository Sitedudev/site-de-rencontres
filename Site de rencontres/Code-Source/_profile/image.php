<?php
	/*
	 * @author Sitedudev
	*/
	
	include_once('../include.php');	
	
	if (!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}
	
	$num_img = (int) $_GET['num']; 
	$guid_profile = (String) htmlspecialchars(trim($_GET['guid']));
	
	if(!isset($num_img) || !isset($guid_profile)){
		header('Location: ' . URL . 'profile');
		exit;
	}
	
	$show_album = $DB->prepare("SELECT p.name, p.date_upload, p.id, u.guid, u.pseudo, u.avatar, u.sexe, u.birthday, p.v_like
		FROM picture p
		INNER JOIN user u ON u.id = p.pseudo_id
		WHERE p.id = ? AND u.guid = ?");
	$show_album->execute(array($num_img, $guid_profile));
		
	$a = $show_album->fetch();	

	if(!isset($a['name'])){
		header('Location: ' . URL . 'profile');
		exit;
	}
	
	if(!empty($_POST)){
		extract($_POST);
		$valid = true;
		
		
		if(isset($_POST['like'])){

			if(!empty($a['name'])){
				
				$like = $DB->prepare("SELECT count(*) as l 
					FROM v_like 
					WHERE picture_id = ? AND pseudo_id = ?");
				$like->execute(array($a['id'], $_SESSION['id']));
							
				$like = $like->fetch();	
				
				if($like['l'] == 0){
					
					$req = $DB->prepare("INSERT INTO v_like (picture_id, pseudo_id) VALUES (?, ?)");
					$req->execute(array($a['id'], $_SESSION['id']));
						
					$req = $DB->prepare("UPDATE picture SET v_like = ? WHERE id = ?");
					$req->execute(array($a['v_like'] + 1, $a['id']));
						
					$_SESSION['flash']['success'] = "Tu as aimé la photo !";			
				
				}else{
					
					$_SESSION['flash']['info'] = "Tu as déjà aimé la photo !";
				}
				
				header('Location: ' . URL . 'profil/' . $guid_profile . '/' . $a['id']);
				exit;
			}
			
		}elseif(isset($_POST['warning'])){
			
			if(!empty($a['name'])){
				
				$verif_report = $DB->prepare("SELECT count(*) as r 
					FROM report 
					WHERE picture_id = ? AND pseudo_id = ?");
				$verif_report->execute(array($a['id'], $_SESSION['id']));
					
				$verif_report = $verif_report->fetch();
				
				if($verif_report['r'] == 0){
					
					$req = $DB->prepare("INSERT INTO report (picture_id, pseudo_id, date_report) VALUES (?, ?, ?)");
					$req->execute(array($a['id'], $_SESSION['id'], date('Y-m-d H:i:s')));
							
					$_SESSION['flash']['success'] = "La photo a été signalée !";
					
				}else{
					$_SESSION['flash']['info'] = "La photo a déjà été signalée !";
									
				}
				header('Location: ' . URL . 'profil/' . $guid_profile . '/' . $a['id']);
				exit;					
			}

		
		}elseif(isset($_POST['comment'])){
			
			$nameimg = trim($nameimg);
			
			$_SESSION['flash']['success'] = "La photo a été commentée !";
			header('Location: ' . URL . 'profil/' . $guid_profile . '/' . $num_img);
			exit;
			
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('../_head/meta.php');
		?>
		<title>Image | Daewen</title>
		<?php
			include_once('../_head/link.php');
			include_once('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once('../menu.php');
		?>
		<div class="container" style="color: #666">
			
			<div class="col-xs-12 col-sm-4 col-md-3" style="text-align: center;">
				<img src="<?= URL . $__User->getAvatar($a['guid']) ?>" width="120" style="width: 120px; border-radius: 100px"/>
			</div>
			<div class="col-xs-12 col-sm-8 col-md-9">
				<span style="font-size: 28px"><?= $a['pseudo'] ?>,</span>
				<span style="font-size: 22px"><?= $__Crypt_password->age($a['birthday']) ?> ans</span>
				<a href="<?= URL ?>profil/demandes" style="text-decoration: none; color: #666">
					<section style="border: 1px solid;border-radius: 20px;position: relative;padding: 5px 0">
						<i class="fa fa-users" style="border: 1px solid;border-radius: 100px;padding: 10px;font-size:20px;position: absolute;top: -5px;left: -12px;background: #FAFAFA;"></i>
						<?php
							$show_demande = $DB->prepare("SELECT * 
								FROM relation 
								WHERE id_to = ? AND statut = 1");
							$show_demande->execute(array($_SESSION['id']));
								
							$show_demande = $show_demande->fetchAll();
							
							$count_demande = count($show_demande);
						?>
						
						<span style="padding-left: 40px"><span class="badge"><?= $count_demande?></span> Gérer mes amis</span>
						<div style="position: absolute;width: 70px;height: 30px;border: 15px solid transparent;border-top-color: #666666;border-right-color: #666666;border-bottom-color: #666666;top: 0;border-radius: 0 20px 20px 0;right: -1px;"></div>
					</section>
				</a>
			</div>
		</div>
		
		<div class="container">
			
			<div class="row">								
				<div class="col-xs-12 col-sm-6 col-md-5 effectArt" style="margin: 20px 0; text-align: center">		
					<a href="" id="a_sp">
						<img src="<?= "public/pictures/". $a['guid'] . "/" . $a['name'] ?>" style="width: 100%"/>
						<div class="mask">
							<span class="fa fa-eye eyeArt"></span>
							<span class="numberArt"><?= $a['v_like'] ?></span>
							<span class="fa fa-heartbeat heartArt"></span>
							<!--<span class="numberCom">0</span>
							<span class="fa fa-comments-o ComArt"></span>-->
							<span class="dateArt"><?= date_format(new DateTime($a['date_upload']), "d/m/Y"); ?></span>
						</div>
					</a>	
				</div>	
				
				<div class="col-xs-12 col-sm-6 col-md-7" style="margin: 20px 0">
					<form method="post" action="">
						<input type="hidden" name="nameimg" value="<?= $a['name'] ?>"/>
						<div style="border: 2px solid #d35400;border-radius: 100px; width: 70px; height: 70px">
							<input type="submit" name="like" class="fa" value="&#xf21e;" style="font-size: 45px;border: none; color: #d35400; background: transparent; outline: none; padding: 13px 11px">
						</div>
					</form>
					
					<form method="post" action="">
						<input type="hidden" name="nameimg" value="<?= $a['name'] ?>"/>
						<div style="position: absolute; left:0; margin: auto;right:0; top:0;border: 2px solid #d35400;border-radius: 100px;width: 70px;height: 70px">
							<input type="submit" name="warning" class="fa fa-warning" value="&#xf071;" style="font-size: 45px;border: none;color: #d35400; background: transparent; outline: none; padding: 10px"/>
						</div>
					</form>
					<!--
					<form method="post" action="">
						<input type="hidden" name="nameimg" value="<?= $a['name'] ?>"/>
						<div style="position: absolute; right:15px; top:0;border: 2px solid #d35400;border-radius: 100px;width: 70px;height: 70px;">
							<input type="submit" name="comment" class="fa" value="&#xf1d9;" style="font-size: 45px; border: none; color: #d35400;background: transparent; outline: none; padding: 10px 8px"/>
						</div>
						
						<div style="margin-top: 30px">
							<textarea class="autoExpand" rows="3" data-min-rows="3" placeholder="Commentez la photo de <?= $a['pseudo'] ?>" style="border: 1px solid #CCC; border-radius: 5px;width: 100%; max-width: 100%; outline: none; padding: 5px 10px; overflow: none; resize: none"></textarea>
						</div>					
					</form>
					-->
				</div>		
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>