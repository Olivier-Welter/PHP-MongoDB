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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>Administration</title>
  </head>
  <body>
 <?php
    // Ici on est bien loggué, on affiche un message
    echo '<h1></h>Bienvenue sur la page de maintenance</h1><br>';
    echo 'Identifiant :', $_SESSION['id'];
    echo '<br>Profil : ', $_SESSION['profil'];
    echo "<br><a href= 'index.php'>Accès à l'accueil </a><br>";
  ?>
  </body>
</html>
