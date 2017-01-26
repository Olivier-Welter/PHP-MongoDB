<?php
session_start();
   if (isset($_GET['deco']))
                {session_destroy();
                header('Location: ./index.php');}
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

    <body class="abs reset maxsize flex-column">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->


        <!-- START MAIN CONTENT -->

        <main class="maxsize flexible flex-column-start">

            <section id="main_container" class="flex-column">

                <header class="maxwidth">
                    <h1>Trouve ta ville</h1>
                </header>

                <form class="maxwidth txtcenter" method="post" action="index.php">
                    <p>
                        <input type="text" name="ville" placeholder="Ville" />
                    </p>
                    <p>
                        <input type="text" name="dept" placeholder="Département" />
                    </p>
                    <p>
                        <input type="text" name="region" placeholder="Région" />
                    </p>
                    <p>
                        <input type="submit" name="recherche" value="Chercher" />
                    </p>
                </form>

                <div id="result" class="maxwidth">
                    <?php 
                        require_once("afficherVille.php");
						require_once('functions.inc.php');
						if(isset($_GET['Ville']) && isset($_GET['Dept']))
						{
                            
            $ville = floor($_GET["Ville"]);
            $options = [];
			$filter = ['_id' => $ville];
			$query = new MongoDB\Driver\Query($filter,$options);
			$curs = $cnx->executeQuery('geo_france.villes', $query);
            foreach($curs as $doc) {
				    echo "<hr><p class='txtcenter'>";
                    echo "<h3>$doc->nom :</h3>";
                    printf ("<p>Code postal ou codes postaux : %s. Latitude : %s. Longitude : %s. Population: %s habitants.</p>", $doc->cp, $doc->lat, $doc->lon, $doc->pop);
            }
                
							 $vil=(int) $_GET['Ville'];
							 $dep=(int) $_GET['Dept'];
							 affcarte($dep,$vil);
						}
                    ?>
                </div>

                <footer class="maxwidth txtcenter">
                    <?php 
            
                if (isset($_SESSION['id']))
                {
                echo "<p>Accès à la <a href= 'maintenance.php'>maintenance</a></p>";
                echo "<p><a href= 'index.php?deco'>Déconnexion</a></p>";
                }
                else { ?>
                        <p>La modification de la base de données requiert de <a href="authentification.php">se connecter</a></p>
                        <?php } ?>
                </footer>

            </section>

            <script>
                (function () {
                    var movementStrength = 25;
                    var height = movementStrength / $(window).height();
                    var width = movementStrength / $(window).width();
                    $("#top-image").mousemove(function (e) {
                        var pageX = e.pageX - ($(window).width() / 2);
                        var pageY = e.pageY - ($(window).height() / 2);
                        var newvalueX = width * pageX * -1 - 25;
                        var newvalueY = height * pageY * -1 - 50;
                        $('#top-image').css("background-position", newvalueX + "px     " + newvalueY + "px");
                    });
                })();
            </script>

        </main>

        <!-- END MAIN CONTENT -->


    </body>

    </html>