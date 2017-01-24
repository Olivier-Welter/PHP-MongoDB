<?php
// On prolonge la session
session_start();
// On teste si la variable de session existe et contient une valeur
if(!isset($_SESSION['profil']) || ($_SESSION['profil']!='admin' && $_SESSION['profil']!='edit'))
{
  // Si inexistante ou nulle, on redirige vers le formulaire de login
 header('Location: ./index.php');
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
        
print ('<form action="" method="post"><fieldset><legend>Maintenance</legend>');
    
                echo '<br>'.$_SESSION['id'].', vous avez les droits de modification en tant que '.$_SESSION['profil'].'.</br>';
              

        // INDEX 
        ?>
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


                <?php
                //AFFICHER VILLE
    require_once("php/db.php");
    try {
    
    if(isset($_POST["recherche"])&&$_POST["recherche"]=="Chercher") {
        
		$ville = trim($_POST["ville"]);
		$dept = trim($_POST["dept"]);
		$region = trim($_POST["region"]);

        
		if(preg_match("/^\p{L}/", $ville)) { // Si le nom de ville commence bien par un caractère, chinois ou non
			if(!preg_match("/^\p{L}/", $region)) { // Si on a recherché un caractère dans region
				$options = [
					'sort' => ['nom' => 1],
					'limit' => 3
				];
				$filter = ['nom' => new MongoDB\BSON\Regex("^$region","i")];
				$query = new MongoDB\Driver\Query($filter,$options);
				$curs = $mgc->executeQuery('geo_france.regions', $query);
				$cursArray = $curs->toArray();
				if(count($cursArray) != 1) {
					$region = false;
				}
				else {
					foreach($cursArray as $doc) {
						echo "<h3>";
						echo "Région recherchée : ".$doc->nom;
						echo "</h3>";
					}
				}
			}
			if(preg_match("/^\p{L}/", $dept)) { // Si on a recherché un caractère dans dept
				$options = [
					'projection' => ['contours' => 0],
					'sort' => ['nom' => 1],
					'limit' => 3
				];
				$filter = ['nom' => new MongoDB\BSON\Regex("^$dept","i")];
				$query = new MongoDB\Driver\Query($filter,$options);
				$curs = $mgc->executeQuery('geo_france.departements', $query);
				$cursArray = $curs->toArray();
				if(count($cursArray) > 3 || count($cursArray) < 1) {
					$dept = false;
				}
				else {
					echo "<h3>Département recherché :</h3>";
					foreach($cursArray as $doc) {
						echo "<p class='txtcenter'>".$doc->nom." (".$doc->code.")</p>";
					}
				}
 			}
			$options = [
				'sort' => ['pop' => -1],
				'limit' => 10
			];
			$filter = ['nom' => new MongoDB\BSON\Regex("^$ville","i")];
			$query = new MongoDB\Driver\Query($filter,$options);
			$curs = $mgc->executeQuery('geo_france.villes', $query);
				
			$cursArray = $curs->toArray();
			
            // modif
            
            
                
               
			if(count($cursArray) == 1) {
	
				foreach($cursArray as $doc) {
				    echo "<p class='txtcenter'>";
                    echo "<h3>Propriétés actuelles de $doc->nom :</h3>";
                    printf ('<label>Population : <input type="text" name="id" value="%s"/></label></p>', $doc->pop);
                    printf ('<p><label>Code postal : <input type="text" name="id" value="%s"/></label></p>', $doc->cp); 
				    echo "</p>";
				}
			}
            
            
            // \modif
			else if(count($cursArray) > 1){
				echo "<h3>Plusieurs villes trouvées :</h3>";
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
}          
catch (Exception $e) { echo "exception interceptée :".$e->getMessage();}
                
// Vérification de sélection d'une ville
                
 if (isset($_GET['Ville'])) {
        
    
            echo "TAAAAAAAAAAAAAAAAAAA MERE LA CHIENNE";
     
            $ville = trim($_GET["Ville"]);
            			$options = [];
			$filter = ['_id' => $ville];
			$query = new MongoDB\Driver\Query($filter,$options);
			$curs = $mgc->executeQuery('geo_france.villes', $query);
     
            echo "<pre>";
            print_r ($curs);
            echo"</pre>";
     
			$cursArray = $curs->toArray();
	
            foreach($cursArray as $doc) {
				    echo "<p class='txtcenter'>";
                    echo "<h3>Propriétés actuelles de $doc[nom] :</h3>";
                    printf ('<label>Population : <input type="text" name="id" value="%s"/></label></p>', $doc->pop);
                    printf ('<p><label>Code postal : <input type="text" name="id" value="%s"/></label></p>', $doc->cp); 
				    echo "</p>";
				}
            
        } 
            
  

                
                
        
        // \AFFICHER VILLE
        
       echo '</div>';
        
      
echo "<p>La modification de données sera répercutée dans la base de données !</p>";
print('<p><input type="submit" value="Modifier les données" /><br>');
echo "Accès à l'<a href= 'index.php'>accueil</a><br>";
print('<input type="submit" name="fin_session" value="Déconnexion" /></p>'); 
print("</fieldset></form>");
echo "</section></main>";     
?>
    </body>

    </html>