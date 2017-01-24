<?php
session_start();
?>

    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="icon" href="favicon.ico" />
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/responsive.css">
        <link rel="stylesheet" href="css/index.css">

        <title>Authentification</title>
    </head>

    <body>
        <?php 
require_once ('config_db_inc.php');
	  
        
echo '<main class="maxsize flexible flex-column-center"><section id="main_container" class="flex-column">';
        
print ('<form action="" method="post"><fieldset><legend>Authentification</legend>');
    
        
// Click sur déconnexion 
if (isset($_POST['fin_session'])) {
  print ("<h2>Vous êtes déconnecté de votre session</h2>");
  session_destroy();
  
}
  // Si le formulaire à été soumis      
  if (isset($_POST['id']))
  {
    // Requétage pour vérification des données
    $filter=[];
    $options=[];
    $requete=new MongoDB\Driver\Query($filter,$options);
    $ligne=$cnx->executeQuery('geo_france.users',$requete);
    $ok =0;
    foreach($ligne as $docu)
        {
        if ($_POST['id'] == $docu->identifiant)
            {
            $_SESSION['id'] = $_POST['id'];
            $ok= 1;
            if ($_POST['mdp'] == $docu->mdp)
                {
                // Connexion valide, création des cariables sessions
                echo '<br>Bienvenue  '.$docu->identifiant.', vous êtes connecté en tant que '.$docu->profil.'.</br>';
                $_SESSION['mdp'] = $_POST['mdp'];
                $_SESSION['profil'] = $docu->profil;
                echo "Accès à l'<a href= 'index.php'>accueil </a><br>";
                echo "Accès à la <a href= 'maintenance.php'>maintenance</a><br>";
                print('<input type="submit" name="fin_session" value="Déconnexion" /></p>');
                exit();
                }
                else echo "<br>Erreur de mot de passe<br>";
            }
        }
        if ($ok==0)
          echo "<br>Erreur d'idendifiant.<br>";
  }
  
elseif (!isset($_SESSION['id'])) { $_SESSION['id'] = ''; $_SESSION['mdp'] =''; }
      
printf('<label>Identifiant : <input type="text" name="id" value="%s"/></label>', $_SESSION['id']);
printf('<label>Mot de passe : <input type="text" name="mdp" value="%s"/></label>', $_SESSION['mdp']);
print('<p><input type="submit" value="Connexion" /><br>');
echo "Retour à l'<a href= 'index.php'>accueil </a><br>";
print("</fieldset></form>");
echo "</section></main>";
        
?>
    </body>

    </html>