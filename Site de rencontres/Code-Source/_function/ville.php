<?php
	include('../include.php');

	$r_ville = trim(mb_strtolower(urldecode($_GET['term'])));
	
	if(isset($r_ville)) {
	
		$requete = $DB->prepare('SELECT v.ville_nom_reel as a, d.departement_nom as b
			FROM villes_france v, departement d 
			WHERE d.departement_code = v.ville_departement AND ville_nom_reel like ? limit 10');
		$requete->execute(array("$r_ville%"));
		
		$array = array();
		
		while ($donnee = $requete->fetch()) {
		    array_push($array, $donnee['a'] . ", " . $donnee['b']);		    
		}
		
		echo json_encode($array);
	}
?>