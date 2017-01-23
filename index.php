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
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/responsive.css">
        <link rel="stylesheet" href="css/index.css">
        
    </head>
    
    <body class="reset maxsize flex-column">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        

        <!-- START MAIN CONTENT -->
        
        <main class="maxsize flexible flex-column-center">
        
            <section id="main_container" class="flex-wrap">
                
                <header class="maxwidth txtcenter">
                    <h1>Trouve ta ville</h1>
                </header>
                
                <form class="maxwidth txtcenter">
                    <p><input type="text" name="ville" placeholder="Ville"/></p>
                    <p><input type="text" name="dept" placeholder="Département"/></p>
                    <p><input type="text" name="region" placeholder="Région"/></p>
                    <p><input type="submit" name="valider" value="Chercher"/></p>
                </form>    
                
                <div id="result" class="maxwidth">
                    <?php

                    $dsn='mongodb://192.168.1.42:27017';
                    $dbname = 'geo_france';
                    $collname = 'villes';

                    try {

                        $mgc = new MongoDB\Driver\Manager($dsn);

                        $options = [
                            //'projection' => ['_id' => 1, 'nom' => 1],
                            'limit' => 10
                        ];

                        $query = new MongoDB\Driver\Query(
                            ['nom' => ['$exists' => true]], // population > 2000000
                            $options
                        );

                        $curs = $mgc->executeQuery(
                            'geo_france.villes', // la collection visée
                            $query               // la requête
                        );
                        
                        echo "<ul>";
                        
                        foreach($curs as $doc) {
                            echo "<li>";
                            echo $doc->_id." ".$doc->nom;
                            echo "</li>";
                        }
                        
                        echo "</ul>";


                    } catch (Exception $e) {
                      echo "exception interceptée :".$e->getMessage();
                    }
                    ?>
                </div>
                
                <footer class="maxwidth txtcenter">
                    <p>La modification de la base de données requiert de <a href="#">se connecter</a></p>
                </footer>
                
            </section>
        
        </main>
        
        <!-- END MAIN CONTENT -->
        

        <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery.js"><\/script>')</script>
        
    </body>
    
</html>