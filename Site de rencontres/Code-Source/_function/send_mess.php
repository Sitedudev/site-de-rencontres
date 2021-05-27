<?php
	session_start();
	
	include ('../bd/connexionDB.php');
	
	$DB = new connexionDB();

	
	if(isset($_SESSION['id'])){ 
		
		$guid = htmlentities(trim($_POST['guid']));
		$mess = htmlentities(trim($_POST['message']));
		
		if(isset($guid) && isset($mess) && !empty($guid) && !empty($mess)){
			
			$verif_relation = $DB->query("SELECT u.id AS id1, m.id AS id2 
				FROM user u
				INNER JOIN relation r ON (r.id_from, r.id_to) = (:id, u.id) OR (r.id_from, r.id_to) = (u.id, :id)
				INNER JOIN mp_titre m ON (m.id_from, m.id_to) = (r.id_from, r.id_to) OR (m.id_from, m.id_to) = (r.id_to, r.id_from)
				WHERE u.guid = :guid",
				array('id' => $_SESSION['id'], 'guid' => $guid));

			$verif_relation = $verif_relation->fetch();
			
			if(isset($verif_relation['id1'])){
			
				$date_mp = date('Y-m-d H:i:s');
				
				$DB->insert("UPDATE mp_titre SET id_from = ?, id_to = ?, text = ?, date_mp = ? WHERE id = ?",
					array($_SESSION['id'], $verif_relation['id1'], $mess, $date_mp, $verif_relation['id2']));

				$DB->insert("INSERT INTO mp (id_from, id_to, text, date_mp, lu) VALUES (?, ?, ?, ?, ?)",
					array($_SESSION['id'], $verif_relation['id1'], $mess, $date_mp, 1));				

				?>
					<div style="float: right;width: auto; max-width: 80%; margin-right: 26px;position: relative;padding: 7px 20px;color: #fff;background: #0B93F6;border-radius: 5px;margin-bottom: 15px; clear: both"><?= nl2br($mess) ?></div>

				<?php
					
			}else{

				$verif_relation = $DB->query("SELECT r.id_from AS id1, r.id_to AS id2 
					FROM user u
					INNER JOIN relation r ON (r.id_from, r.id_to) = (:id, u.id) OR (r.id_from, r.id_to) = (u.id, :id)
					WHERE u.guid = :guid",
					array('id' => $_SESSION['id'], 'guid' => $guid));

				$verif_relation = $verif_relation->fetch();
				
				if(isset($verif_relation['id1'])){
					
					
					if ($verif_relation['id1'] == $_SESSION['id']){ 
						$id_to = $verif_relation['id2'];
					}else{
						$id_to = $verif_relation['id1'];
					}
					
					$date_mp = date('Y-m-d H:i:s');
					
					$DB->insert("INSERT INTO mp_titre (id_from, id_to, text, date_mp) VALUES (?, ?, ?, ?)",
						array($_SESSION['id'], $id_to, $mess, $date_mp));
					
					$DB->insert("INSERT INTO mp (id_from, id_to, text, date_mp, lu) VALUES (?, ?, ?, ?, ?)",
						array($_SESSION['id'], $id_to, $mess, $date_mp, 1));

					
					?>
						<div style="float: right;width: auto; max-width: 80%; margin-right: 26px;position: relative;padding: 7px 20px;color: #fff;background: #0B93F6;border-radius: 5px;margin-bottom: 15px; clear: both"><?= nl2br($mess) ?></div>

					<?php
				}
			}
		}		
	}
	