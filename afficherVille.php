<?php

require_once("php/db.php");

try {
    
    if(isset($_POST["recherche"])&&$_POST["recherche"]=="Chercher") {
        
		$ville = trim($_POST["ville"]);
		$dept = trim($_POST["dept"]);
		$region = trim($_POST["region"]);
		    
		$options = [
		    'sort' => ['pop' => -1],
		    'limit' => 10
		];

		if(preg_match("/^\p{L}/", $ville)) { // Si le nom de ville commence bien par un caractère, chinois ou non

			if(preg_match("/^\p{L}/", $dept)) { 

				$filter = ['nom' => new MongoDB\BSON\Regex("^$ville","i")];

 			}
			
			if(!preg_match("/^\p{L}/", $region)) { $region = false; }

			$filter = ['nom' => new MongoDB\BSON\Regex("^$ville","i")];

			$query = new MongoDB\Driver\Query($filter,$options);

			$curs = $mgc->executeQuery('geo_france.villes', $query);
				
			$cursArray = $curs->toArray();
				
			if(count($cursArray) == 1) {
		
				echo "<h3>Une ville trouvée :</h3>";
		
				foreach($cursArray as $key => $doc) {
				    echo "<p>";
				    echo "<a href='index.php?Ville=".$doc->_id."&Dept=".$doc->_id_dept."'>".$doc->nom."</a> ".$doc->pop." habitants.";
				    echo "</p>";
				}

			}
			else if(count($cursArray) > 1){

				echo "<h3>Plusieurs villes trouvées :</h3>";

				foreach($cursArray as $key => $doc) {
				    echo "<p>";
				    echo "<a href='index.php?Ville=".$doc->_id."&Dept=".$doc->_id_dept."'>".$doc->nom."</a> ".$doc->pop." habitants.";
				    echo "</p>";
				}

			}
			else {

				echo "<h3>Votre recherche n'a abouti à aucun résultat.</h3>";

				}

		}

	}

}
catch (Exception $e) { echo "exception interceptée :".$e->getMessage();}

?>
