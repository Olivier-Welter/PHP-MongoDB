<?Php
	 
	 // MongoDB
	 
	 try 
		{
			 $cnx=new MongoDB\Driver\Manager("mongodb://localhost:27017");
			 
		}
	 catch (MongoDB\Driver\Exception $e)
		{
			 die('Erreur de connexion au serveur MongoDB '.$ex->getMessage());
			 
		}
