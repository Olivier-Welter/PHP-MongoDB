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
	  
/*if (isset($_COOKIE['id']))
    $_SESSION['id']=$_COOKIE['id'];
        
        setcookie("id",  $_POST['id'], time()+36000); */
        
// Click sur déconnexion 
if (isset($_POST['fin_session'])) {
  print ("<h2 class=\"destroy\">Déconnexion </h2>");
  printf("<h3><a href=\"%s\">Se reconnecter</a></h3>", $_SERVER["SCRIPT_NAME"]);
  session_destroy();
  exit();
}
 
        
echo '<main class="maxsize flexible flex-column-center"><section id="main_container" class="flex-column">';
        
print ('<form action="" method="post"><fieldset><legend>Authentification</legend>');
    
  if (isset($_POST['id']))
  {
    
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
                echo '<br>Bienvenue  '.$docu->identifiant.', vous êtes connecté en tant que '.$docu->profil.'.</br>';
                $_SESSION['mdp'] = $_POST['mdp'];
                $_SESSION['profil'] = $docu->profil;
                echo "<a href= 'index.php'>Accès à l'accueil </a><br>";
                echo "<a href= 'maintenance.php'>Accès à la maintenance</a><br>";
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
print('<p><input type="submit" value="Connexion" />');
print('<input type="submit" name="fin_session" value="Déconnexion" /></p>');
print("</fieldset></form>");
echo "</section></main>";
        
?>
    </body>

    </html>