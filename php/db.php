<?php

$dsn='mongodb://192.168.1.42:27017';
$dbname = 'geo_france';
$collname = 'villes';

try { $mgc = new MongoDB\Driver\Manager($dsn);}
catch (Exception $e) { echo "exception interceptée :".$e->getMessage();}

?>