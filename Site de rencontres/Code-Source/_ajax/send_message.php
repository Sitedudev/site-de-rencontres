<?php
	include_once('../include.php');
	
	if(!isset($_SESSION['id'])){
		exit;
	}
	
	$get_guid = (String) htmlentities(trim($_POST['guid']));
	$get_message = (String) urldecode(trim($_POST['message']));
	
	if(!isset($get_guid) || empty($get_message)){
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
	
	$date_message = date("Y-m-d H:i:s");
	
	$req = $DB->prepare("INSERT INTO messagerie (id_from, id_to, message, date_message, lu) VALUES (?, ?, ?, ?, ?)");
		
	$req->execute(array($_SESSION['id'], $verifier_relation['id'], $get_message, $date_message, 1));
	
?>
<div class="messages__msg messages__miens">
	<div class="messages__message">
		<?= nl2br($get_message) ?>
	</div>
</div>