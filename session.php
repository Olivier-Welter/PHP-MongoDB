<?php
session_start();
?>

    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>TP PHP/MongoDB : Extraction de documents</title>
    </head>

    <body>
        <?php
 
require_once ('php/db.php');
	  
        
 
if (isset($_POST['fin_session'])) {
  print ("<h2 class=\"destroy\">Déconnexion </h2>");
  printf("<h3><a href=\"%s\">Se reconnecter</a></h3>", $_SERVER["SCRIPT_NAME"]);
  session_destroy();
  exit();
}
 
print ('<form action="" method="post"><fieldset><legend>Authentification</legend>');
   

  if (isset($_POST['id']))
  {
    
    $filter=[];
    $options=[];
    $requete=new MongoDB\Driver\Query($filter,$options);
    $ligne=$mgc->executeQuery('geo_france.users',$requete);
    $ok =0;
    foreach($ligne as $docu)
        {
        if ($_POST['id'] == $docu->identifiant)
            {
            $_SESSION['id'] = $_POST['id'];
            $ok= 1;
            if ($_POST['mdp'] == $docu->mdp)
                {
                echo '<br>Bienvenue  : '.$docu->identifiant.'</br>';
                $_SESSION['mdp'] = $_POST['mdp'];
                $_SESSION['profil']= $docu->profil;
                }
                else echo "<br>Erreur de mot de passe<br>";
            }
        }
      if ($ok==0)
          echo "<br>Erreur d'idendifiant.<br>";
  }
  
        
        
elseif (!isset($_SESSION['id']))
{
$_SESSION['id'] = '';
$_SESSION['mdp'] ='';
}
      
printf('<label>Identifiant : <input type="text" name="id" value="%s"/></label>', $_SESSION['id']);
printf('<label>Mot de passe : <input type="text" name="mdp" value="%s"/></label>', $_SESSION['mdp']);

   
print('<p><input type="submit" value="Connexion" />');
print('<input type="submit" name="fin_session" value="Déconnexion" /></p>');
print("</fieldset></form>");

//if(isset($session))
printf( 'Session : %s / %s<br>',$_SESSION['id'], $_SESSION['profil'] ); 

echo '<br /><a href="index.php">Page d\'accueil</a>';
        
        

        
?>
    </body>
    </html>
