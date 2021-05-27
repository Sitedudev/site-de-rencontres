<?php	
	/**
	 * @package			: Code source rencontres
	 * @version			: 0.7
	 * @author			: sitedudev aka clouder
	 * @link 			: https://sitedudev.com
	 * @since			: 2021
	 * @license			: Attribution-NomCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)
	 */
	
	include_once('include.php');
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include_once('_head/meta.php');
		?>
        <title>Daewen</title>
        <?php
			include_once('_head/link.php');
			include_once('_head/script.php');
		?>
	</head>
	<body>
		<?php
			include_once 'menu.php';
		
			if(!isset($_SESSION['id'])){
		?>
		<div class="container"  style="text-align: center">
			<div class="row">
				<div class="col-12 col-md-12 col-xl-12" style="margin: 20px 0">
					<h1 style="margin-bottom: 20px">Venez comme vous êtes !</h1>
					<i class="fa fa-venus-mars fa-size"></i>
					<i class="fa fa-venus-double fa-size"></i>
					<i class="fa fa-venus fa-size"></i>
					<i class="fa fa-transgender-alt fa-size"></i>
					<i class="fa fa-transgender fa-size"></i>
					<i class="fa fa-neuter fa-size"></i>
					<i class="fa fa-mercury fa-size"></i>
					<i class="fa fa-mars-stroke-v fa-size"></i>
					<i class="fa fa-mars-stroke-h fa-size"></i>
					<i class="fa fa-mars-stroke fa-size"></i>
					<i class="fa fa-mars-double fa-size"></i>
					<i class="fa fa-mars fa-size"></i>
				</div>
				<div class="col-12 col-md-12 col-xl-6">
			    	<h2>Plus cool <i class="fa fa-beer"></i></h2>
					<i class="fa fa-codepen" style="font-size: 40px; color: transparent; -webkit-background-clip: text; background-clip: text; background-image: linear-gradient(to right, #e74c3c,#f39c12);"></i>
				</div>
				<div class="col-12 col-md-12 col-xl-6">
				    <h2>Plus rapide <i class="fa fa-plane"></i></h2>
					<i class="fa fa-tachometer" style="font-size: 40px; color: transparent; -webkit-background-clip: text; background-clip: text; background-image: linear-gradient(to right, #e74c3c,#f39c12);"></i>
				</div>
			</div>
			<section style="text-align: center">
				<i class="fa fa-gg" style="font-size: 150px; color: transparent; -webkit-background-clip: text; background-clip: text; background-image: linear-gradient(to right, #e74c3c,#f39c12);"></i>			
				<h1>Créer de nouveaux liens</h1>
			</section>
		</div>
		<?php
			}else{	
		?>	
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-12 col-xl-12" style="margin: 20px 0">
					<div class="signin__body">
						<div class="">
							Derniers membres inscrits
						</div>
						<div class="row">
							<?php 
								$req = $DB->prepare("SELECT u.*, o.id as is_online
									FROM user u
									LEFT JOIN online o ON o.pseudo_id = u.id
									ORDER BY u.date_registration DESC 
									LIMIT 0, 12");
								$req->execute(array());
								
								$chose_color = 0;
								
							foreach($req as $r){						
								$En_ligne = "";
								if(isset($r['is_online'])){
									$En_ligne = "<div class='Online'></div>";
								} ?>
								<div class="col-4 col-md-2 col-xl-3" style="text-align: center">
									<a href="<?= URL ?>profil/<?= $r['guid'] ?>">
										<img src="<?= URL . $__User->getAvatar($r['guid']) ?>" style="width: 80px; max-width: 100%; border-radius: 100px; margin: 10px 0"/>
									</a>
								</div>
								<?php
								} 
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php	
			}
			
			include_once('_footer/footer.php');
		?>
	</body>
</html>

