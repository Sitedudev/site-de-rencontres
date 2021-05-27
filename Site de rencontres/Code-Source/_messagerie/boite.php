<?php
	/*
	 * @author Sitedudev
	 */
	 
	include_once('../include.php');
	
	if(!isset($_SESSION['guid'])){
		header('Location: ' . URL);
		exit;
	}	
	
	$nb_affiche_max = (int) 5;
	$nb_total_amis = (int) 0;
	
	$req = $DB->prepare("SELECT COUNT(id) AS nb_amis
		FROM relation
		WHERE (id_from = :id OR id_to = :id) AND statut = 2");
		
	$req->execute(array('id' => $_SESSION['id']));
		
	$nb_conversation = $req->fetch();
	
	$nb_total_amis = $nb_conversation['nb_amis'];

	$req = $DB->prepare("SELECT u.pseudo, u.id, u.guid, m.message, m.date_message, m.id_from, m.lu
		FROM (
			SELECT IF(r.id_from = :id, r.id_to, r.id_from) id_utilisateur, MAX(m.id) max_id
			FROM relation r
			LEFT JOIN messagerie m ON ((m.id_from, m.id_to) = (r.id_from, r.id_to) OR (m.id_from, m.id_to) = (r.id_to, r.id_from))
			WHERE (r.id_from = :id OR r.id_to = :id) AND r.statut = 2
			GROUP BY IF(m.id_from = :id, m.id_to, m.id_from), r.id) AS DM
		LEFT JOIN messagerie m ON m.id = DM.max_id
		LEFT JOIN user u ON u.id = DM.id_utilisateur
		ORDER BY m.date_message DESC
		LIMIT " . $nb_affiche_max);
		
	$req->execute(array('id' => $_SESSION['id']));
		
	$afficher_conversations = $req->fetchAll();
	
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php
			include('../_head/meta.php');
		?>
        <title>Messagerie | Daewen</title>
        <?php
			include('../_head/link.php');
			include('../_head/script.php');
		?>
	</head>
	<body>
		<?php
			include('../menu.php');
		?>
		<div class="container">		
			<div class="row">
				<div class="col-12 col-md-12 col-xl-1"></div>
				<div class="col-12 col-md-12 col-xl-10" style="margin: 20px 0">
					<div class="signin__body">
						<h1>Messagerie</h1>
						<div>
							<?php
								foreach($afficher_conversations as $ac){
							?>
							<a href="<?= URL ?>messagerie/<?= $ac['guid']?>" class="msg__a">
								<div class="msg__body">
									<div>
										<img src="<?= URL . $__User->getAvatar($ac['guid']) ?>" class="msg__img"/>
									</div>
									<div class="msg__body__info">
										<div class="msg__body__head">
											<div class="msg__body__title"><?= $ac['pseudo'] ?></div>
											<?php
												if(isset($ac['date_message'])){
											?>
											<div class="msg__body__date">
												<?= $__Time->give_time($ac['date_message']); ?>
											</div>
											<?php
												}
											?>
										</div>
										<?php
											if($ac['id_from'] <> $_SESSION['id'] && $ac['lu'] == 1){
										?>
										<div class="msg__new">
											<div class="msg__new__text">Nouveau</div>
										</div>
										<?php	
											}
										?>									
										<div class="msg__body__message">
											<?php 
												if(isset($ac['message'])){
													echo $ac['message'];
												}else{
													echo '<b>Dites lui bonjours !</b>';
												}
											?>
										</div>
									</div>
								</div>
							</a>		
							<?php
								}	
							?>
							<div id="afficher_liste"></div>
							<?php
								if($nb_total_amis > $nb_affiche_max){
							?>
							<button id="voir-plus" class="btn-voir-plus-message">Voir plus</button>
							<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
			include_once('../_footer/footer.php');
		?>
	</body>
</html>