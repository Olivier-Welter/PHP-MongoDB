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
                        require_once("php/afficherVille.php");
                    ?>
            </div>
            <?php
        // \INDEX
        
    $filter=[];
    $options=[];
    $requete=new MongoDB\Driver\Query($filter,$options);
    $ligne=$cnx->executeQuery('geo_france.ville',$requete);
    foreach($ligne as $docu)
        {
        echo "$docu->nom";
       printf('<label>Mo : <input type="text" name="id" value="%s"/></label>', $_SESSION['id']);
    }
        
printf('<label>Nom ville : <input type="text" name="nom" value=""/></label>');
echo "<p>La modification de données sera répercutée dans la base de données !</p>";
print('<p><input type="submit" value="Modifier les données" /><br>');
echo "Accès à l'<a href= 'index.php'>accueil</a><br>";
print('<input type="submit" name="fin_session" value="Déconnexion" /></p>'); 
print("</fieldset></form>");
echo "</section></main>";     
?>
    </body>

    </html>