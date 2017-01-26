<?Php
	 //
	 //
	 //
	 
	 function projection($lon, $lat) 
		{
			 return [RATIO*($lon+TX)*cos(LAT_MOY*(M_PI / 180)), RATIO*(-$lat+TY)];
		}
	 
	 function affcarte($dep,$vil)
		{
			 
			 global $cnx;
			 $depart=$dep;
			 $ville=$vil;
			 
			 $height=700;
			 
			 $dbname='geo_france';
			 
$map = <<<EOJSBOXMAP
	  function() {
		var res = [180, -180, 90, -90];
		for (i=0; i<this.poly.length; i++) {
		  if (res[0] > this.poly[i][0]) res[0] = this.poly[i][0];
		  if (res[1] < this.poly[i][0]) res[1] = this.poly[i][0];
		  if (res[2] > this.poly[i][1]) res[2] = this.poly[i][1];
		  if (res[3] < this.poly[i][1]) res[3] = this.poly[i][1];
		}
		emit(1, res);
	  }
EOJSBOXMAP;
	 
$reduce = <<<EOJSBOXRED
	  function(key, vals) {
		var res = [180, -180, 90, -90];
	   vals.forEach(function(val) {
		  if (res[0] > val[0]) res[0] = val[0];
		  if (res[1] < val[1]) res[1] = val[1];
		  if (res[2] > val[2]) res[2] = val[2];
		  if (res[3] < val[3]) res[3] = val[3];
	   });
	   return {minmax: res};
	  }
EOJSBOXRED;
	 
			 
$svgheader = <<<EOSVGH
<svg xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 %s %s"
     style="background:#DDD">
    <style type="text/css">
        polygon {stroke:#000;stroke-width:1px}
        polygon:hover {stroke:#FFF;fill:#000;stroke-width:2px}
        text {font-size:12px;fill:#000;alignment-baseline:middle}
        text:hover {fill:#F00;font-weight:bold}
        g circle {stroke:#F00;stroke-width:1;stroke-opacity:0.8;fill:#000;fill-opacity:0.5}
        g.cities circle {stroke:#ff0;stroke-width:1;stroke-opacity:0.8;fill:#F00;fill-opacity:0.8}
        g.cities text {fill:#F00 !important;stroke:#FF0;;stroke-width:0.5px}
        g.regions circle {stroke:#fff;stroke-width:1;stroke-opacity:0.3;fill:#000;fill-opacity:0.3}
        g.regions:hover text {fill:#fff;stroke:#000;stroke-width:0.5px}
        g.regions:hover circle {fill-opacity:0.4}
        g:hover circle {fill:#FF0;fill-opacity:1;stroke-opacity:1}
        g text {fill:#000;font-size:0px;fill-opacity:0;alignment-baseline:middle;text-anchor:middle}
        g:hover text {font-size:30px;fill:#000;font-weight:bold;fill-opacity:1}
    </style>
EOSVGH;
	 
			 $filter=['nom' => $ville];
			 $options=[ 'limit' => 1];
			 
			 $requete=new MongoDB\Driver\Query($filter,$options);
			 
			 $ligne=$cnx->executeQuery('geo_france.villes',$requete);
	 			 
			 foreach($ligne as $docu)
				{
					 echo '<br>ville:'.$docu->nom.' latitude: '.$docu->lat.' long:'.$docu->lon.' depart:'.$docu->_id_dept;
					 
					 $f2=['_id' => $docu->_id_dept];
					 $depart=$docu->_id_dept;
					 $o2=['limit' => 1];
					 $requete2=new MongoDB\Driver\Query($f2,$o2);
					 $ligne2=$cnx->executeQuery($dbname.'departements',$requete2);
					 
					 foreach($ligne2 as $result)
						{
							 echo ' Nom depart: '.$result->nom;
							 $ndepart=$result->nom;
						}
					 
				}
			 
			 $boxCmd = new MongoDB\Driver\Command([
				'mapreduce' => 'contours',
				'map' => $map,
				'reduce' => $reduce,
				'out' => ['inline' => 1]]);
			 $box = $cnx->executeCommand($dbname, $boxCmd)->toArray()[0];
			 
			 $minmax = $box->results[0]->value->minmax;
			 
			 list($lon_min, $lon_max, $lat_min, $lat_max) = $minmax;
			  // création des constantes (pour éviter une déclaration avec "global").
			 define('RATIO', $height/($lat_max - $lat_min));
			 define('LAT_MOY', ($lat_max + $lat_min)/2);
			 define('TX', -$lon_min);
			 define('TY', $lat_max);
			 list($width,$unused) = projection($lon_max, LAT_MOY);
					 
			 echo '<div style="width: 100%;">';
			 printf($svgheader, $width, $height);
			 
			 $query = new MongoDB\Driver\Query(['_id' => ['$gt' => 0]]);      // astuce car commande 'find' pas suportée par anciennes bases de données
			 $curseur = $cnx->executeQuery($dbname.'.contours', $query);
			 
			 foreach($curseur as $doc) 
				{
					 echo '<polygon style="fill:#CCF" points="'."\n";
					 $contour = $doc->poly;
					 foreach ($contour as $ptb) 
						{
							 list($lon, $lat) = $ptb;
							 list($px, $py) = projection($lon, $lat);
							 printf(' %d %d', $px, $py);
						}
					 echo '"/>'."\n";
				}
			 
			 $filter = ['_id' => $depart]; // on veut que le département présente bien un contour
			 $options = [
				 'projection' => ['contours' => 1, '_id_region' => 1, 'nom' => 1,'_id' => 0],  // on ne veut que ces colonnes sans l'identifiant
				 'sort'       => ['contours' => 1],                                 // triées selon le nombre de contours ordre croissant
						];
			 
			 $query = new MongoDB\Driver\Query($filter, $options);
			 $curseur = $cnx->executeQuery($dbname.'.departements', $query);
			 
			 foreach($curseur as $crs) 
				{   
					 // boucle sur l'ensemble des résultats
					 // si la région existe on prend la couleur attribuée sinon on génère une couleur de région aléatoire.
					 $idr = $crs->_id_region;
					 $ndepart=$crs->nom;					 
					 if (empty($colr[$idr])) 
						{
							 $colr[$idr] = [rand(5,14), rand(5,14), rand(5,14)];
						}
					 // la couleur du département a une composante régionale forte + une composante départementale faible et aléatoire
					 $color = sprintf('#%1x%1x%1x', rand(0,1)+$colr[$idr][0], rand(0,1)+$colr[$idr][1], rand(0,1)+$colr[$idr][2]);
					 foreach ($crs->contours as $contour) 
						{
							 printf('<polygon fill="%s" points="'."\n", $color);
							 $minpx=0;
							 $minpy=0;
							 $maxpx=0;
							 $maxpy=0;
							 foreach ($contour as $ptb) 
								{
									 list($lon, $lat) = $ptb;
									 list($px, $py) = projection($lon, $lat);
									 if($minpx>=$px || $minpx==0)
											 $minpx=$px;
									 
									 if($maxpx<$px && $minpx<$px)
											 $maxpx=$px;
									 
									 if($minpy>=$py || $minpy==0)
											 $minpy=$py;
									 
									 if($maxpy<$py && $minpy<$py)
											 $maxpy=$py;
									 
									 printf(' %d %d', $px, $py);
								}
							 echo '"/>'."\n";
							 $mpx=floor(($maxpx-$minpx)/2)+$minpx;
							 $mpy=floor(($maxpy-$minpy)/2)+$minpy;

							 printf('<g class="regions"><text x="%d" y="%d">%s</text>'."\n", $px, $py, $ndepart);
							 printf('<circle cx="%d" cy="%d" r="8"/></g>'."\n", $mpx, $mpy);
						}
				}
			 
			 $filter = ['_id' => $ville]; 
			 $options = ['projection' => ['nom' => 1, 'lon' => 1, 'lat' => 1, '_id' => 0]];  // on ne veut que ces colonnes sans l'identifiant
			 $query = new MongoDB\Driver\Query($filter, $options);
			 $curseur = $cnx->executeQuery($dbname.'.villes', $query);
			 
			 foreach($curseur as $doc) 
				{
					 list($px, $py) = projection($doc->lon, $doc->lat);
					 printf('<g class="cities"><text x="%d" y="%d">%s</text>'."\n", $px, $py, $doc->nom);
					 printf('<circle cx="%d" cy="%d" r="4"/></g>'."\n", $px, $py);
				}
			 
			 echo '</svg>';
			 echo '</div>';
			 
		}	// Fin function
	 
	 
?>