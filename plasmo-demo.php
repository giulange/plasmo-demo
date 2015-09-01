<?php

echo "<HTML lang=\"en\">";
echo "  <head>";
echo "   <title>Plasmo webapp</title>";
echo "    <!--link rel=\"stylesheet\" href=\"ol3/ol.css\" type=\"text/css\"-->";
echo "    <link rel=\"stylesheet\" href=\"http://openlayers.org/en/v3.0.0/css/ol.css\" type=\"text/css\">";

echo "    <script src=\"https://code.jquery.com/jquery-1.11.2.min.js\"></script>";
echo "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.css\" type=\"text/css\">";
echo "    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.js\"></script>";

//echo "   <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js\"></script><script type=\"text/javascript\">";

echo "    <style>
           #map {
            background-color:black;
            height:  256px;
            width:   512px;
            padding: 2px;
           }
           .ol-attribution a{
             color: black;
           }
           #sim {
            line-height:0px;
            background-color:white;
            height:256px;
            width:500;
            float:left;
            padding:7px;
           }
           #plot {
            line-height:0px;
            background-color:white;
            height:256px;
            width:500;
            float:left;
            padding:7px;
           }

           hr{ 
            display: block;
            margin-top:   1.5em;
            margin-bottom:0.5em;
            margin-left:  auto;
            margin-right: auto;
            border-style: inset;
            border-width: 2px;
           }
          </style>";
echo "    <script src=\"http://openlayers.org/en/v3.0.0/build/ol.js\" type=\"text/javascript\"></script>";
echo "    <title>OpenLayers 3 example</title>";
echo "  </head>";
echo "  <body>
    <h1>Prototype Plasmo Wapp</h1>

    <div id=\"map\" class=\"map\"></div>
    <!-- the result of the php will be rendered inside this div -->
    <div id=\"sim\" class=\"sim\"></div>
    <div id=\"plot\" class=\"plot\"></div>

    <script type=\"text/javascript\">
      <!--var bounds = new ol.Bounds( 14.4386, 41.1540, 14.7258,  41.3020 );-->

      var map = new ol.Map({
        target: 'map',
        layers: [
          new ol.layer.Tile({
            source: new ol.source.BingMaps({
              imagerySet: 'Road',
              key: 'Ak-dzM4wZjSqTlzveKz5u0d4IQ4bRzVI309GxmkgSVr1ewS6iPSrOvOKhA-CJlm3'
            })
          })
        ],

        view: new ol.View({
          center: ol.proj.fromLonLat([14.5, 41.20]),
          zoom: 10
        }),

        <!--zoomToExtent: new ol.Bounds( 14.4386, 41.1540, 14.7258,  41.3020 ),-->

				controls: ol.control.defaults().extend([
					new ol.control.ScaleLine()
				])
      });
      var extent = new ol.Bounds( 14.4386, 41.1540, 14.7258,  41.3020 );
      map.zoomToExtent(extent);

/**
		* Add a click handler to the map to render the popup.
		*/
		map.on('singleclick', function(evt) {
		
			var coordinate = evt.coordinate;
			var hdms = ol.proj.toLonLat(coordinate, 'EPSG:3857');
			console.log(\"Coordinates [lon,lat] = \" +  hdms);
			//console.log(\"Coordinates \"+coordinate);
			//console.log(\"Coordinates [N,E]\" +  ol.coordinate.toStringHDMS(hdms));
			//var xy = ol.coordinate.toStringXY(ol.proj.transform(coordinate, new ol.proj.Projection('EPSG:3857'), new ol.proj.Projection('EPSG:32632')),6);
			//console.log(\"Coordinates \"+xy);

			// retrieve time series:
        
			// launch simulation model:
			// --Set current time of launch:
			var d = new Date();
			var tm = d.getTime();
			// --Send the data using post:
			// ----URL:
			var url = window.location.pathname;
			var myreq = \"LAT=\" + hdms[1] + \"&LON=\" + hdms[0] + \"&TIME=\" + tm;
			var posting = $.post( url, myreq );
			// Put the results in a div
			posting.done( function( data ) {
				ofile = \"./fig/sim_plasmo__\" + tm + \".png\";
				climfile = \"./fig/CL__\" + tm + \".png\";
				var img = document.createElement(\"img\");
				img.src = ofile;
				img.width  = 500;//1000;
				img.height = 200;//400;
				img.alt = \"Image not Found!\";
				// **PLOT**
				$(\"#sim\").append( \"<hr>\" );
				//$(\"#sim\").append( \"Running the simulation model... <BR>\" );
				//$(\"#sim\").append( \"Lat: \" + hdms[1] + \",   \" );
				//$(\"#sim\").append( \"Lon: \" + hdms[0] + \"<BR>\" );
				//$(\"#sim\").append( ofile + \"<BR>\" );
				// This next line will just add it to the <body> tag
				document.getElementById('sim').appendChild(img);
				$(\"#plot\").append( \"<hr>\" );
				var img2 = document.createElement(\"img\");
				img2.src = climfile;
				img2.width  = 500;//1000;
				img2.height = 200;//1000;
				img2.alt = \"Image not Found!\";
				document.getElementById('plot').appendChild(img2);

			});
		});

   </script>
 </body>
</HTML>";

if($_SERVER["REQUEST_METHOD"]=="POST"){
	//$tm = time();
  $lat = $_REQUEST['LAT'];
  $lon = $_REQUEST['LON'];
  $tm  = $_REQUEST['TIME'];
  $ofile = "/var/www/wapp/fig/sim_plasmo__$tm.png";
  $cmd = "matlab -nodisplay -nodesktop -nosplash -r \"webapp_plamopara_1d_v02($lat,$lon,2010,'0101','0701','$ofile');quit\"";
  exec( $cmd, $retArray, $retStatus );
  // if something goes wrong, print all catched information & an error image:
	if($retStatus){
		echo "CWD:    " . getcwd() . "<br>";
		echo "CMD:    $cmd<br>";
		echo "STATUS: $retStatus" . " (0:fine, 1:wrong)<br>";
		echo "OUT:    $ofile<br>";
		echo "RES:<br>";	
    foreach($retArray as $rx){
			echo "$rx<br>";
    }

	//if($retStatus){
  	$im = @imagecreatefrompng($ofile);
  	// See if it failed
  	if(!$im){
		// Create a blank image
    	$im  = imagecreatetruecolor(350, 30);
    	$bgc = imagecolorallocate($im, 255, 255, 255);
    	$tc  = imagecolorallocate($im, 0, 0, 0);
    	imagefilledrectangle($im, 0, 0, 350, 30, $bgc);
    	// Output an error message
    	imagestring($im, 1, 5, 5, 'Error loading ' . $ofile, $tc);
  	}
  }
	else{
		echo "CORRECT!!"; //"./fig/sim_plasmo__$tm.png"; //$ofile;
	}
  /*
	header('Content-Type: image/png');
  imagepng($im);
  //imagedestroy($im);
	*/
}

?>
