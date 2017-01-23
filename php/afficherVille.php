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
        
    $filter = ['nom' => new MongoDB\BSON\Regex("^$ville$","i")];

    $query = new MongoDB\Driver\Query($filter,$options);

    $curs = $mgc->executeQuery('geo_france.villes', $query);
        
    $cursArray = $curs->toArray();
        
    if(count($cursArray) == 1) {
        
        echo "<ul>";

        foreach($cursArray as $key => $doc) {
            echo "<li>";
            echo $key." : ".$doc->_id." ".$doc->nom;
            echo "</li>";
        }

        echo "</ul>";

        }
    }

}
catch (Exception $e) { echo "exception interceptée :".$e->getMessage();}

?>