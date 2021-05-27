<?php
	include_once('../include.php');
	
	if(!isset($_SESSION['id'])){
		exit;
	}
	
	$limit = (int) trim($_POST['limit']);
	$get_guid = (String) htmlentities(trim($_POST['guid']));
	
	if($limit <= 0 || !isset($get_guid)){
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
	
	// C'est le nombre de message à afficher
	$nombre_total_message = 25;
	$limit_mini = 0;
	$limit_maxi = 0;
	
	$req = $DB->prepare("SELECT COUNT(id) as NbMessage
		FROM messagerie 
		WHERE ((id_from, id_to) = (:id1, :id2) OR (id_from, id_to) = (:id2, :id1))");
		
	$req->execute(array('id1' => $_SESSION['id'], 'id2' => $verifier_relation['id']));
		
	$nombre_message = $req->fetch();
	
	$limit_mini = $nombre_message['NbMessage'] - $limit;
	
	if($limit_mini > $nombre_total_message){
		$limit_maxi = $nombre_total_message;
		$limit_mini = $limit_mini - $nombre_total_message;
	}else{
		if($limit_mini > 0){
			$limit_maxi = $limit_mini;
		}else{
			$limit_maxi = 0;
		}
		
		$limit_mini = 0;
	}
	
	$req = $DB->prepare("SELECT *
		FROM messagerie
		WHERE ((id_from, id_to) = (:id1, :id2) OR (id_from, id_to) = (:id2, :id1))
		ORDER BY date_message
		LIMIT $limit_mini, $limit_maxi");
		
	$req->execute(array('id1' => $_SESSION['id'], 'id2' => $verifier_relation['id']));
		
	$afficher_message = $req->fetchAll();
	
	if($limit_mini <= 0){
?>
<div>
	<script>
		var el = document.getElementById('voir-plus');
		el.classList.add('messages__btn__seemore__hide');
	</script>
</div>
<?php	
	}
?>
<div id="voir-plus-message"></div>
<?php
	$var_garder_id = (int) 0;
	
	foreach($afficher_message as $im){
											
		if($var_garder_id <> $im['id_from'] && $im['id_from'] == $_SESSION['id']){
			if($var_garder_id > 0){										
				echo '</div>';
			}
			echo '<div class="messages__msg messages__miens">';
		}elseif($var_garder_id <> $im['id_from']){
			if($var_garder_id > 0){
				echo '</div>';
			}
			echo '<div class="messages__msg messages__siens">';
		}
		
		$var_garder_id = $im['id_from'];
	
?>
<div class="messages__message">
	<?= nl2br($im['message']) ?>
</div>
<?php
	}

if(count($afficher_message) > 0){
	echo '</div>';	
}
?>
