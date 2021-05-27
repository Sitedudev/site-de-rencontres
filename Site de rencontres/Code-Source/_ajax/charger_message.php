<?php
	include_once('../include.php');
	
	if(!isset($_SESSION['id'])){
		exit;
	}
	
	$get_guid = (String) htmlentities(trim($_POST['guid']));

	if(!isset($get_guid)){
		exit;
	}
	
	$req = $DB->prepare("SELECT u.pseudo, u.id, u.guid, u.avatar
		FROM user u
		INNER JOIN relation r ON (r.id_from, r.id_to) = (u.id, :id) OR (r.id_from, r.id_to) = (:id, u.id)
		WHERE u.guid = :guid AND r.statut = :statut");
		
	$req->execute(array('id' => $_SESSION['id'], 'guid' => $get_guid, 'statut' => 2));
		
	$verifier_relation = $req->fetch();
	
	if(!isset($verifier_relation['id'])){
		exit;
	}
	
	$req = $DB->prepare("SELECT *
		FROM messagerie
		WHERE id_to = ? AND id_from = ? AND lu = ?");
		
	$req->execute(array($_SESSION['id'], $verifier_relation['id'],  1));
		
	$afficher_message = $req->fetchAll();
	
	
	$req = $DB->prepare("UPDATE messagerie SET lu = ? WHERE id_to = ? AND id_from = ?");
		
	$req->execute(array(0, $_SESSION['id'], $verifier_relation['id']));
	
	
	foreach($afficher_message as $am){
?>
<div class="messages__msg messages__siens">
	<div class="messages__message">
		<?= nl2br($am['message']) ?>
	</div>
</div>
<?php
	}	
?>
