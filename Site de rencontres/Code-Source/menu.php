<?php
/*
 * @author Sitedudev
 */
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default">
	<div class="container-fluid">
		<a class="navbar-brand" href="<?= URL ?>">
			<i class="fa fa-heartbeat" style="font-size: 40px"></i>
			<span style="margin-left: 15px">Daewen</span>
		</a>
		<button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNavDropdown">
			<ul class="navbar-nav mr-auto">
				<?php
					if(isset($_SESSION['id'])){
				?>
				<li class="nav-item">
					<a href="<?= URL . "search" ?>"><i class="fa fa-location-arrow"></i> Autour de moi</a>
				</li>
				<?php
					}
				?>
			</ul>
			<ul class="navbar-nav">
				<?php
					if(isset($_SESSION['id'])){
				?>
				<li class="nav-item">
					<a href="<?= URL . "messagerie" ?>"><i class="fa fa-comments-o"></i><sup><?= $__Notification->notif_mess() ?></sup> Messages</a>
				</li>
				<li class="nav-item">
					<a href="" data-bs-toggle="modal" data-bs-target="#settings"><i class="fa fa-cog"></i> Paramètres</a>
				</li>
				<div id="settings" class="modal fade">
					<div class="modal-dialog" role="document">
						<form method="post" class="modal-content animate" action="" style="padding-top: 10px">
							<button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true" style="right: 15px;">&times;</button>
							
							<div class="container-fluid">
								<h3 style="margin-top: 0">Paramètres</h3>
								
								<div class="row" style="margin-bottom: 20px;font-size: 16px">
									<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
										<i class="fa fa-user-circle-o"></i>
									</div>
									
									<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
										<a href="<?= URL ?>profil" style="color: #666; text-decoration: none">Mon profil</a>
									</div>
								</div>
								
								<div class="row" style="margin-bottom: 20px;font-size: 16px">
									<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
										<i class="fa fa-cog"></i>
									</div>
									
									<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
										<a href="<?= URL ?>parametres" style="color: #666; text-decoration: none">Mes paramètres</a>
									</div>
								</div>
								
								<?php
									if($_SESSION['role'] > 0){	
								?>
								
								<div class="row" style="margin-bottom: 20px;font-size: 16px">
									<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
										<i class="fa fa-th"></i>
									</div>
									
									<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
										<a href="<?= URL . "admin/dashboard" ?>" style="color: #666; text-decoration: none">Console</a>
									</div>
								</div>
								
								<?php
									}	
								?>
								
								<div class="row" style="margin-bottom: 20px;font-size: 16px">
									<div class="col-xs-2 col-sm-2 col-md-2" style="border: 1px solid #CCC; border-left: none; text-align: center; color: #666; padding: 12px 0;">
										<i class="fa fa-power-off"></i>
									</div>
									
									<div class="col-xs-10 col-sm-10 col-md-10" style="padding: 12px 10px; border: 1px solid #CCC; border-left: none; border-right: none">
										<a href="<?= URL ?>deconnexion" style="color: #666; text-decoration: none">Déconnexion</a>
									</div>
								</div>	
													
							</div>
							
							<div class="container-fluid" style="background-color:#f1f1f1; padding: 10px;">
								<button type="button" data-bs-dismiss="modal" class="cancelbtn">Annuler</button>
							</div>
						</form>
					</div>
				</div>
				<?php 
					}else{
				?>
				<li class="nav-item">
					<a href="<?= URL . "inscription" ?>">S'inscrire</a>
				</li>
				<li class="nav-item">
					<a href="<?= URL . "connexion" ?>">Se connecter</a>
				</li>
				<?php
					}
				?>
			</ul>
		</div>
	</div>
</nav>
<?php 
	if(isset($_SESSION['flash'])){ 
		foreach($_SESSION['flash'] as $type => $message){
?>
<div id="alert" class="alert alert-<?= $type; ?> infoMessage"><a class="closef">X</span></a>
	<?= $message; ?>
</div>	
<?php
		}
		unset($_SESSION['flash']);
	}
?>