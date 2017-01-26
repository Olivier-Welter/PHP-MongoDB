<?php
session_start();
   if (isset($_GET['deco']))
                {session_destroy();
                header('Location: ./authentification.php');}
?>
    <!doctype html>

    <html>

    <head>

        <meta charset="utf-8">
        <meta name="author" content="Vince | Olive | Trisou | Kéké">
        <meta name="description" content="Projet MongoDB">
        <meta name="keywords" content="Projet MongoDB">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Trouve ta ville</title>

        <link rel="icon" href="favicon.ico" />
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/index.css">

    </head>

    <body id="background" class="abs reset maxsize flex-column">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->


        <!-- START MAIN CONTENT -->

        <main class="maxsize flexible flex-column-start">

            <section id="main_container" class="flex-column">

                <form action="" method="post">
                    <fieldset>
                        <legend>Authentification</legend>

                        <?php require_once ('config_db_inc.php'); 
						// Si le formulaire à été soumis      
						  if (isset($_POST['id']))
						  {
							// Requétage pour vérification des données
							$filter=[];
							$options=[];
							$requete=new MongoDB\Driver\Query($filter,$options);
							$ligne=$cnx->executeQuery('geo_france.users',$requete);
							$ok =0;
							foreach($ligne as $docu) {
								if ($_POST['id'] == $docu->identifiant) {
									$_SESSION['id'] = $_POST['id'];
									$ok= 1;
									if ($_POST['mdp'] == $docu->mdp) {
											// Connexion valide, création des cariables sessions
											echo '<p>Bienvenue  '.$docu->identifiant.', vous êtes connecté en tant que '.$docu->profil.'.</p>';
											$_SESSION['mdp'] = $_POST['mdp'];
											$_SESSION['profil'] = $docu->profil;
											echo "<p>Accès à l'<a href= 'index.php'>accueil </a></p>";
											echo "<p>Accès à la <a href= 'maintenance.php'>maintenance</a></p>";
											echo "<p><a href= 'authentification.php?deco'>Déconnexion</a></p>";
											exit();
									}
									else echo "<br>Erreur de mot de passe<br>";

								}
								if ($ok==0)
								  echo "<br>Erreur d'idendifiant.<br>";
							}
						}
						elseif (!isset($_SESSION['id'])) { $_SESSION['id'] = ''; $_SESSION['mdp'] =''; }
      
						printf('<p><label class="maxwidth flex-around"><span>Identifiant : </span><input type="text" name="id" value="%s"/></label></p>', $_SESSION['id']);
						printf('<p><label class="maxwidth flex-around"><span>Mot de passe : </span><input type="text" name="mdp" value="%s"/></label></p>', $_SESSION['mdp']);
					?>

                            <p>
                                <input type="submit" value="Connexion"/>
                            </p>
                            <a href='index.php'>Accueil </a>
                    </fieldset>

                </form>

            </section>

        </main>

		<script>
			(function() {
				var movementStrength = 25;
				var height = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
				var width = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
				height = movementStrength / height;
				width = movementStrength / width;
				var body = document.getElementById("background");
				body.addEventListener("mousemove", function(e) {
						  var pageX = e.pageX - (width / 2);
						  var pageY = e.pageY - (height / 2);
						  var newvalueX = width * pageX * -1 -25;
						  var newvalueY = height * pageY * -1 -50;
						  body.style.backgroundPosition = newvalueX+"px "+newvalueY+"px";
				});
			})();

		</script>

    </body>

    </html>