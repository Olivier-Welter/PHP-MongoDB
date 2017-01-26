<?php

require_once("config_db_inc.php");

try {
    
    if(isset($_POST["recherche"])&&$_POST["recherche"]=="Chercher") {
        
		$ville = trim($_POST["ville"]);
		$dept = trim($_POST["dept"]);
		$region = trim($_POST["region"]);


		if(preg_match("/^\p{L}/", $ville)) { // Si le nom de ville commence bien par un caractère, chinois ou non


			if(preg_match("/^\p{L}/", $region)) { // Si on a recherché un caractère dans region

				$options = ['sort' => ['nom' => 1],'limit' => 3]; // Tri par ordre alphabétique les noms de région + limitation à 3 retours
				if(strlen($region) == 1) $filter = ['nom' => new MongoDB\BSON\Regex("^$region","i")]; // Si un seul caractère est cherché, on ne recherche que les entrées commençant par celui-ci
				else if(strlen($region) > 1) $filter = ['nom' => new MongoDB\BSON\Regex("$region","i")]; // Sinon on recherche les entrées contenant la chaîne recherchée
				else exit;
				$query = new MongoDB\Driver\Query($filter,$options); // Prépa requête
				$curs = $cnx->executeQuery('geo_france.regions', $query); // Exec requête
				$cursArray = $curs->toArray();  // Récup requête, dans un tableau pour pouvoir en compter le nombre de lignes retournées

				if(count($cursArray) > 3 || count($cursArray) < 1) $region = false; // Si plus de 3 régions sont retounées ou moins de une on arrête là
				else { // Sinon bah, on les affiche et on récupère leur _id
					$region = array();
					echo "<div>";
					if(count($cursArray) == 1) echo "<h3>Région recherchée :</h3>";
					else echo "<h3>Régions recherchées :</h3>";
					foreach($cursArray as $doc) {
						array_push($region, $doc->_id);
						echo "<p>".$doc->nom."</p>";
					}
					echo "</div>";
				}
				
			}


			if(preg_match("/^\p{L}/", $dept)) { // Si on a recherché un caractère dans dept

				if($region) { // Si on trouvé des régions dans la condition précédente
					for($i=0;$i<count($region);$i++) $rfilter[$i]['_id_region'] = $region[$i]; // On met les _id des régions un tableau bidimensionnel
					if(strlen($dept) == 1) $filter = ['nom' => new MongoDB\BSON\Regex("^$dept","i"),'$or' => $rfilter]; // Si un seul caractère est cherché, on ne recherche que les entrées commençant par celui-ci
					else if(strlen($dept) > 1) $filter = ['nom' => new MongoDB\BSON\Regex("$dept","i"),'$or' => $rfilter]; // Sinon on recherche les entrées contenant la chaîne recherchée
					else exit;
				}
				else { // Si pas de région, le filtre généré recherchera les dépt sans tenir compte d'une région
					if(strlen($dept) == 1) $filter = ['nom' => new MongoDB\BSON\Regex("^$dept","i")]; // Si un seul caractère est cherché, on ne recherche que les entrées commençant par celui-ci
					else if(strlen($dept) > 1) $filter = ['nom' => new MongoDB\BSON\Regex("$dept","i")]; // Sinon on recherche les entrées contenant la chaîne recherchée
					else exit;
				}

				$options = [ // On vire les contours, nombreux ils feraient ramer la requête
					'projection' => ['contours' => 0],
					'sort' => ['nom' => 1],  // Tri par ordre alphabétique les noms de région + limitation à 5 retours
					'limit' => 5
				];

				$query = new MongoDB\Driver\Query($filter,$options); // Prépa requête
				$curs = $cnx->executeQuery('geo_france.departements', $query); // Exec requête
				$cursArray = $curs->toArray();  // Récup requête, dans un tableau pour pouvoir en compter le nombre de lignes retournées

				if(count($cursArray) > 5 || count($cursArray) < 1) $dept = false; // Si plus de 5 depts sont retounées ou moins de un on arrête là
				else { // Sinon bah, on les affiche et on récupère leur _id
					$i=0;
					echo "<div>";
					echo "<h3>Département recherché :</h3>";
					foreach($cursArray as $doc) {
						$deptArr[$i]['_id'] = $doc->_id;
						echo "<p>".$doc->nom." (".$doc->code.")</p>";
						$i++;
					}
					$dept = $deptArr;
					echo "</div>";

				}

 			}
			$search = false;

			if($dept) { // Si des depts on été trouvés sans région

				for($i=0;$i<count($dept);$i++) $dfilter[$i]['_id_dept'] = $dept[$i]['_id'];
				if(strlen($ville) == 1) $filter = ['nom' => new MongoDB\BSON\Regex("^$ville","i"),'$or' => $dfilter];  // Si un seul caractère est cherché, on ne recherche que les entrées commençant par celui-ci
				else if(strlen($ville) > 1) $filter = ['nom' => new MongoDB\BSON\Regex("$ville","i"),'$or' => $dfilter];  // Sinon on recherche les entrées contenant la chaîne recherchée
				else exit;
				$search = true;

			}
			else if ($region){ // Sinon, si des régions on été trouvées sans dépt
			
				$options = ['limit' => 20];  // limitation à 20 retours

				for($i=0;$i<count($region);$i++) $rfilter[$i]['_id_region'] = $region[$i];
				$filter = ['$or' => $rfilter];
				$query = new MongoDB\Driver\Query($filter,$options); // Prépa requête
				$curs = $cnx->executeQuery('geo_france.departements', $query); // Exec requête
				$cursArray = $curs->toArray(); // Récup requête, dans un tableau pour pouvoir en compter le nombre de lignes retournées
			
				if(count($cursArray) <= 20 || count($cursArray) >= 1) {
					$i=0;
					foreach($cursArray as $doc) {
						$deptArr[$i]['_id'] = $doc->_id;
						$i++;
					}
					$dept = $deptArr;
					for($i=0;$i<count($dept);$i++) $dfilter[$i]['_id_dept'] = $dept[$i]['_id'];
					if(strlen($ville) == 1) $filter = ['nom' => new MongoDB\BSON\Regex("^$ville","i"),'$or' => $dfilter]; // Si un seul caractère est cherché, on ne recherche que les entrées commençant par celui-ci
					else if(strlen($ville) > 1) $filter = ['nom' => new MongoDB\BSON\Regex("$ville","i"),'$or' => $dfilter];  // Sinon on recherche les entrées contenant la chaîne recherchée
					else exit;
					$search = true;

				} else $search=false;

			}

			// Si aucune région ou dept n'ont été trouvés
			if(strlen($ville) == 1 && !$search) $filter = ['nom' => new MongoDB\BSON\Regex("^$ville","i")];  // Si un seul caractère est cherché, on ne recherche que les entrées commençant par celui-ci
			else if(strlen($ville) > 1 && !$search) $filter = ['nom' => new MongoDB\BSON\Regex("$ville","i")];  // Sinon on recherche les entrées contenant la chaîne recherchée

			$options = ['sort' => ['pop' => -1],'limit' => 10];  // Tri par ordre décroissant la population + limitation à 10 retours

			$query = new MongoDB\Driver\Query($filter,$options); // Prépa requête

			$curs = $cnx->executeQuery('geo_france.villes', $query); // Exec requête
				
			$cursArray = $curs->toArray();  // Récup requête, dans un tableau pour pouvoir en compter le nombre de lignes retournées
			
			if(count($cursArray) == 1) {
				echo "<div>";
				echo "<h3>Une ville trouvée :</h3>";
		
				foreach($cursArray as $doc) {
				    echo "<p>";
				    echo "<a href='index.php?Ville=".$doc->_id."&Dept=".$doc->_id_dept."'>".$doc->nom."</a> - ".$doc->pop." habitants.";
				    echo "</p>";
				}
				echo "</div>";
			}
			else if(count($cursArray) > 1){
				echo "<div>";
				echo "<h3>Plusieurs villes trouvées :</h3>";

				foreach($cursArray as $doc) {
				    echo "<p>";
				    echo "<a href='index.php?Ville=".$doc->_id."&Dept=".$doc->_id_dept."'>".$doc->nom."</a> - ".$doc->pop." habitants.";
				    echo "</p>";
				}
				echo "</div>";
			}
			else {echo "<div><h3>Aucune ville ne corresponds à votre recherche.</h3></div>";}

		}
		else {echo "<div><h3>Aucune ville ne corresponds à votre recherche.</h3></div>";}

	}

}
catch (MongoDB\Driver\Exception $e) { echo "exception interceptée :".$e->getMessage();}

?>
