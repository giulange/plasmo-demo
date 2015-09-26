<?php

/*
 * Usefull Links:
 *  - Draw a box, coordinate conversion (**) --> http://harrywood.co.uk/maps/examples/openlayers/bbox-selector.html
 *  - Map & Baselayer --> http://www.peterrobins.co.uk/it/olbase.html
 *  - Examples on input boxes (**) --> https://select2.github.io/examples.html
 *  - How to drow features on map (***) --> http://dev.openlayers.org/examples/draw-feature.html
 *  - How to CLEARLY set divs within a web page (****) --> http://lau.csi.it/realizzare/accessibilita/fogli_di_stile/position/float-clear.htm
 *  - How to CLEARLY divide a web page in different areas using divs (***) --> http://www.html.it/articoli/div-dal-layout-allattributo-id-1/
 *									   --> http://www.w3schools.com/html/html_layout.asp
 *  - ...
 *  - ...
 *  - ...
 */

echo "<HTML lang=\"en\">";
echo "  <head>";
echo "   <title>Plasmo webapp</title>";
echo "    <link rel=\"stylesheet\" href=\"http://openlayers.org/en/v3.0.0/css/ol.css\" type=\"text/css\">";
echo "    <script src=\"https://code.jquery.com/jquery-1.11.2.min.js\"></script>";
echo "    <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css\">";
echo "    <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js\"></script>";
echo "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.css\" type=\"text/css\">";
echo "    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.js\"></script>";

echo "    <style>
           #map {
            background-color:black;
            height:  256px;
            width:   512px;
            padding: 1px;
           }
           .ol-attribution a{
             color: black;
           }
	   #selectors{
            line-height:0px;
            background-color:white;
            height:236px;
            width:350;
            float:left;
            padding:20px;
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
echo "  </head>";
echo "
 <body>
    <h1>Prototype Plasmo Wapp</h1>

    <div id=\"map\" class=\"map\"></div>
    <div id=\"selectors\" class=\"selectors\">
     <form class=\"form-inline\">
       <label>Geometry type</label>
       <select id=\"type\">
         <option value=\"None\">None</option>
         <option value=\"Point\">Point</option>
         <option value=\"LineString\">LineString</option>
         <option value=\"Polygon\">Polygon</option>
         <option value=\"Circle\">Circle</option>
       </select>
     </form>
     <form class=\"form-inline\">
       <label>Background map</label>
       <select id=\"layer-select\">
         <option value=\"Aerial\">Aerial</option>
         <option value=\"AerialWithLabels\" selected>Aerial with labels</option>
         <option value=\"Road\">Road</option>
         <option value=\"collinsBart\">Collins Bart</option>
         <option value=\"ordnanceSurvey\">Ordnance Survey</option>
       </select>
     </form>
    </div>
    <div class=\"span6\" id=\"mouse-position\">&nbsp;</div>
    <!-- the result of the php will be rendered inside these divs -->
    <div id=\"sim\" class=\"sim\"></div>
    <div id=\"plot\" class=\"plot\"></div>

    <script type=\"text/javascript\">

	var Lsat = new ol.layer.Tile({
  	  source: new ol.source.MapQuest({layer: 'sat'})
	});
	var Lbing_styles = [
	  'Road',
	  'Aerial',
	  'AerialWithLabels',
	  'collinsBart',
	  'ordnanceSurvey'
	];

	var Lbing = [];
	var i, ii;
	for (i = 0, ii = Lbing_styles.length; i < ii; ++i) {
	  Lbing.push(new ol.layer.Tile({
	    visible: false,
	    preload: Infinity,
	    source: new ol.source.BingMaps({
	      key: 'Ak-dzM4wZjSqTlzveKz5u0d4IQ4bRzVI309GxmkgSVr1ewS6iPSrOvOKhA-CJlm3',
	      imagerySet: Lbing_styles[i]
	      // use maxZoom 19 to see stretched tiles instead of the BingMaps
	      // \"no photos at this zoom level\" tiles
	      // maxZoom: 19
	    })
	  }));
	}
	var Vsource = new ol.source.Vector({wrapX: false});
	var Lvec = new ol.layer.Vector({
	  source: Vsource,
	  style: new ol.style.Style({
	    fill: new ol.style.Fill({
	      color: 'rgba(255, 255, 255, 0.2)'
	    }),
	    stroke: new ol.style.Stroke({
	      color: '#ffcc33',
	      width: 2
	    }),
	    image: new ol.style.Circle({
	      radius: 7,
	      fill: new ol.style.Fill({
	        color: '#ffcc33'
	      })
	    })
	  })
	});

	var mousePositionControl = new ol.control.MousePosition({
	  coordinateFormat: ol.coordinate.createStringXY(4),
	  projection: 'EPSG:4326',
	  // comment the following two lines to have the mouse position
	  // be placed within the map.
	  className: 'custom-mouse-position',
	  target: document.getElementById('mouse-position'),
	  undefinedHTML: '&nbsp;'
	});

        map = new ol.Map({
          target: 'map',
          layers: Lbing,
          loadTilesWhileInteracting: true,
          view: new ol.View({
            center: ol.proj.fromLonLat([14.5, 41.20]),
            zoom: 11
          }),
	  controls: ol.control.defaults({
	    attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
	      collapsible: true 
	    })
	  }).extend([
	    new ol.control.ScaleLine(),
	    mousePositionControl
	  ])
        });
	map.addLayer(Lvec);
	//map.addControl(mousePositionControl);
	map.addControl( new ol.control.ZoomToExtent({
          extent: ol.proj.fromLonLat([14.4386, 41.1540, 14.7258,  41.3020])
        }) );
	//map.addControl(new ol.control.ZoomSlider());

	// Create the graticule component
	var graticule = new ol.Graticule({
	  // the style to use for the lines, optional.
	  strokeStyle: new ol.style.Stroke({
	    color: 'rgba(255,120,0,0.9)',
	    width: 2,
	    lineDash: [0.5, 4]
	  })
	});
	//graticule.setMap(map);

	var typeSelect = document.getElementById('type');
	var draw; // global so we can remove it later
	function addInteraction() {
	  var value = typeSelect.value;
	  if (value !== 'None') {
	    draw = new ol.interaction.Draw({
	      source: Vsource,
	      type: /** @type {ol.geom.GeometryType} */ (value)
	    });
	    map.addInteraction(draw);
	  }
	}
	/**
	 * Let user change the geometry type.
	 * @param {Event} e Change event.
	 */
	typeSelect.onchange = function(e) {
	  map.removeInteraction(draw);
	  addInteraction();
	};
	addInteraction();

	var select = document.getElementById('layer-select');
	function onChange() {
	  var style = select.value;
	  for (var i = 0, ii = Lbing.length; i < ii; ++i) {
	    Lbing[i].setVisible(Lbing_styles[i] === style);
	  }
	}
	select.addEventListener('change', onChange);
	onChange();

	/**
	* Add a click handler to the map to render the popup.
	*/
	map.on('singleclick', function(evt) {
		
		var coordinate = evt.coordinate;
		var hdms = ol.proj.toLonLat(coordinate, 'EPSG:3857');
		console.log(\"Coordinates [lon,lat] = \" +  hdms);
		//console.log(\"Coordinates \"+coordinate);
		//console.log(\"Coordinates [N,E]\" +  ol.coordinate.toStringHDMS(hdms));
		var xy = ol.coordinate.toStringXY(ol.proj.transform(coordinate, new ol.proj.Projection('EPSG:3857'), new ol.proj.Projection('EPSG:32632')),6);
		console.log(\"Coordinates \"+xy);

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
