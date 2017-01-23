<?php

require_once("php/db.php");

try {

    $options = [
        //'projection' => ['_id' => 1, 'nom' => 1],
        'limit' => 10
    ];

    $query = new MongoDB\Driver\Query(
        ['nom' => ['$exists' => true]], // population > 2000000
        $options
    );

    $curs = $mgc->executeQuery(
        'geo_france.villes', // la collection visée
        $query               // la requête
    );

    echo "<ul>";

    foreach($curs as $doc) {
        echo "<li>";
        echo $doc->_id." ".$doc->nom;
        echo "</li>";
    }

    echo "</ul>";


} catch (Exception $e) {
    echo "exception interceptée :".$e->getMessage();
}

?>