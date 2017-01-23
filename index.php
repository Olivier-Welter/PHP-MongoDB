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
    
    <body class="abs reset maxsize flex-column">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        

        <!-- START MAIN CONTENT -->
        
        <main class="maxsize flexible flex-column-center">
        
            <section id="main_container" class="flex-column">
                
                <header class="maxwidth txtcenter">
                    <h1>Trouve ta ville</h1>
                </header>
                
                <form class="maxwidth txtcenter" method="post" action="index.php">
                    <p><input type="text" name="ville" placeholder="Ville"/></p>
                    <p><input type="text" name="dept" placeholder="Département"/></p>
                    <p><input type="text" name="region" placeholder="Région"/></p>
                    <p><input type="submit" name="recherche" value="Chercher"/></p>
                </form>    
                
                <div id="result" class="maxwidth">
                    <?php
                        require_once("php/afficherVille.php");
                    ?>
                </div>
                
                <footer class="maxwidth txtcenter">
                    <p>La modification de la base de données requiert de <a href="session.php">se connecter</a></p>
                </footer>
                
            </section>
        
        </main>
        
        <!-- END MAIN CONTENT -->

        
    </body>
    
</html>
