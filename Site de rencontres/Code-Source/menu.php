
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
				<li class="nav-item dropdown">
		        	<a class="dropdown-toggle menu__dropdown__toggle" href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="menu__profile"><?= $_SESSION['pseudo'] ?><span class="menu__profile__img"><img src="<?= URL . $__User->getAvatar($_SESSION['guid']) ?>" width="35" style="width: 35px; border-radius: 100px"/></span></span>
		        	</a>
		        	<ul class="dropdown-menu dropdown-menu-lg-end" aria-labelledby="navbarScrollingDropdown">
		            	<li><a class="dropdown-item" href="<?= URL ?>profil">Profil</a></li>
		            	<li><a class="dropdown-item" href="<?= URL ?>parametres">Paramètres</a></li>
		            	<?php
							if($_SESSION['role'] > 0){	
						?>
		            	<li><hr class="dropdown-divider"></li>
		            	<li><a class="dropdown-item" href="<?= URL ?>admin/dashboard">Dashboard</a></li>
		            	<?php
		            		}
		            	?>
		            	<li><hr class="dropdown-divider"></li>
		            	<li><a class="dropdown-item" href="<?= URL ?>deconnexion">Déconnexion</a></li>
		        	</ul>
		        </li>
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
<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
	<div class="toast-header">
		<svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#007aff"></rect></svg>
		<strong class="me-auto">Notification</strong>
		<small></small>
		<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
	</div>
	<div class="toast-body">
		<?= $message; ?>
	</div>
</div>
<script>
	$(document).ready(function(){
		toastList.forEach(Toastshow);
	})
</script>
<?php
		}
		unset($_SESSION['flash']);
	}
?>