<?php

require_once("config_db_inc.php");

try {
    
    if(isset($_POST["recherche"])&&$_POST["recherche"]=="Chercher") {
        
		$ville = trim($_POST["ville"]);
		$dept = trim($_POST["dept"]);
		$region = trim($_POST["region"]);


		if(preg_match("/^\p{L}/", $ville)) { // Si le nom de ville commence bien par un caractère, chinois ou non


			if(preg_match("/^\p{L}/", $region)) { // Si on a recherché un caractère dans region

				$options = [
					'sort' => ['nom' => 1],
					'limit' => 3
				];
				$filter = ['nom' => new MongoDB\BSON\Regex("$region","i")];
				$query = new MongoDB\Driver\Query($filter,$options);
				$curs = $cnx->executeQuery('geo_france.regions', $query);
				$cursArray = $curs->toArray();

				if(count($cursArray) > 3 || count($cursArray) < 1) {
					$region = false;
				}
				else {
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

				$options = [
					'projection' => ['contours' => 0],
					'sort' => ['nom' => 1],
					'limit' => 5
				];
				$filter = ['nom' => new MongoDB\BSON\Regex("$dept","i")];
				$query = new MongoDB\Driver\Query($filter,$options);
				$curs = $cnx->executeQuery('geo_france.departements', $query);
				$cursArray = $curs->toArray();

				if(count($cursArray) > 5 || count($cursArray) < 1) {
					$dept = false;
				}
				else {
					$dept = array();
					echo "<div>";
					echo "<h3>Département recherché :</h3>";
					foreach($cursArray as $doc) {
						array_push($dept, $doc->_id);
						echo "<p>".$doc->nom." (".$doc->code.")</p>";
					}
					echo "</div>";

				}

 			}

			if($region) {
				echo "toto";
			}
			else if($dept) {
				$dfilter = "'\$or' => [";
				for($i=0;$i<count($dept);$i++) { 
					$dfilter = $dfilter."['_id_dept' => ".$dept[$i]."]";
					if($i != count($dept)-1) $dfilter = $dfilter.",";
				}
				$dfilter = $dfilter."]";
				$filter = ['nom' => new MongoDB\BSON\Regex("$ville","i"),
							$dfilter
				];
				echo $dfilter;
			}
			else $filter = ['nom' => new MongoDB\BSON\Regex("$ville","i")];

			$options = [
				'sort' => ['pop' => -1],
				'limit' => 10
			];

			

			$query = new MongoDB\Driver\Query($filter,$options);

			$curs = $cnx->executeQuery('geo_france.villes', $query);
				
			$cursArray = $curs->toArray();
			
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
			else {echo "<div><h3>Votre recherche n'a abouti à aucun résultat.</h3></div>";}

		}
		else {echo "<div><h3>Votre recherche n'a abouti à aucun résultat.</h3></div>";}

	}

}
catch (MongoDB\Driver\Exception $e) { echo "exception interceptée :".$e->getMessage();}

?>
