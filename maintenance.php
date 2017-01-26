<?php
// On prolonge la session
session_start();
// On teste si la variable de session existe et contient une valeur
if(!isset($_SESSION['profil']) || ($_SESSION['profil']!='admin' && $_SESSION['profil']!='edit'))
{
  // Si inexistante ou nulle, on redirige vers le formulaire de login
 header('Location: ./index.php',TRUE,303);
 exit();
}
?>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="icon" href="favicon.ico" />
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/responsive.css">
        <link rel="stylesheet" href="css/index.css">

        <title>Maintenance</title>
    </head>

    <body>
        <?php 
require_once ('config_db_inc.php');
	  
echo '<main class="maxsize flexible flex-column-center"><section id="main_container" class="flex-column">';
        
print ('<form action="" method="post">
            <fieldset>
                <legend>Maintenance</legend>'); echo '
                <br>'.$_SESSION['id'].', vous avez les droits de modification en tant que '.$_SESSION['profil'].'.</br>'; // INDEX ?>
            <?php if ((!isset($_POST["recherche"])) && (!isset($_GET['Ville']))) { ?>
                <header class="maxwidth txtcenter">
                    <h2>Cherchez l'élément à modifier</h2>
                </header>
                <form class="maxwidth txtcenter" method="post" action="index.php">
                    <p>
                        <input type="text" name="ville" placeholder="Ville" />
                    </p>
                    <?php 
                 // Affichage des paramètres selon le profil
                if ($_SESSION['profil']=='admin')
                    echo '
                    <p><input type="text" name="dept" placeholder="Département" /></p>
                    <p><input type="text" name="region" placeholder="Région" /></p>';
                else 
                    echo '<input type="hidden" name="dept" placeholder="Département" />
                    <input type="hidden" name="region" placeholder="Région" />';
                ?>
                        <p>
                            <input type="submit" name="recherche" value="Chercher" />
                        </p>
                </form>

                <div id="result" class="maxwidth">


                    <?php } 
        else {

                
    // Reprise du code de recherche d'index        
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
			
			
            if(count($cursArray) >= 1){
				echo "<h3>Résultat :</h3>";
				foreach($cursArray as $doc) {
				    echo "<p class='txtcenter'>";
				    echo "<a href='maintenance.php?Ville=".$doc->_id."&Dept=".$doc->_id_dept."'>".$doc->nom."</a> ".$doc->pop." habitants.";
				    echo "</p>";
				}
			}
			else {echo "<h3>Votre recherche n'a abouti à aucun résultat.</h3>";}
		}
		else {echo "<h3>Votre recherche n'a abouti à aucun résultat.</h3>";}
	}
              
// Vérification de sélection d'une ville
                
elseif ((isset($_GET['Ville'])) && (!isset($_POST['update']))) {
            $ville = floor($_GET["Ville"]);
            $options = [];
			$filter = ['_id' => $ville];
			$query = new MongoDB\Driver\Query($filter,$options);
			$curs = $cnx->executeQuery('geo_france.villes', $query);
    		
            foreach($curs as $doc) {
				    echo "<p class='txtcenter'>";
                    echo "<h3>Propriétés actuelles de $doc->nom :</h3>";
                    printf ('<input type="hidden" name="id" value="%s"/>', $doc->_id);
                    printf ('<input type="hidden" name="nom" value="%s"/>', $doc->nom);
                    printf ('<label>Population : <input type="text" name="pop" value="%s"/></label></p>', $doc->pop);
                    printf ('<p><label>Code postal : <input type="text" name="cp" value="%s"/></label></p>', $doc->cp); 
                   /* $cplist = explode("-", $doc->cp);
					$selected = '';
				    print ("CP : ");
					echo '<select name="Code postal">',"n";
					foreach($cplist as $valeurHexadecimale => $nomCP)
						{
						echo "\t",'<option value="', $valeurHexadecimale ,'"', $selected ,'>', $nomCP ,'</option>',"\n";
						$selected='';
						}
					echo '</select>',"\n";*/
				    echo "</p>";
				}
        
echo "<p>La modification de données sera répercutée dans la base de données !</p>";
print('<p><input type="submit" name="update" value="Modifier les données" /><br>');    
} 

}          
catch (Exception $e) { echo "exception interceptée :".$e->getMessage();}
                  
                
if (isset($_POST['update'])) {
    print ("<h2>Modification effectué pour ".$_POST['nom']."</h2>");
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
            ['_id' => floor($_POST['id'])],
            ['$set' => ['pop' => $_POST['pop'], 'cp'=> $_POST['cp']]],
            ['multi' => false, 'upsert' => false]
            );
    printf ("La population enregistrée est de %s habitants, le ou les code(s) postal(aux) sont %s.",$_POST['pop'], $_POST['cp']);

    
  
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$result = $cnx->executeBulkWrite('geo_france.villes', $bulk, $writeConcern);

    
/*  $command = new MongoDB\Driver\Command([
        'update' => 'villes',
        'updates' => [['q'=> ['_id' => 'floor($_POST["id"])'],
                       'u'=>['$set'=>['pop'=> '$_POST["pop]']]]]
    ]);
    $cursor = $cnx->executeCommand('geo_france', $command);
 */   
    
}
        
            
echo "<a href= 'maintenance.php'>Nouvelle recherche</a><br>";            
        }
echo '</div>';
      
echo "Accès à l'<a href= 'index.php'>accueil</a><br>";
print('<input type="submit" name="fin_session" value="Déconnexion" /></p>'); 
print("</fieldset></form>");
echo "</section></main>";     
?>
    </body>

    </html>