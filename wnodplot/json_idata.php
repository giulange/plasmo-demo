<?php
   /*
    Script  : PHP--googlechart
    Author  : Giuliano Langella
    Source  : http://forum.highcharts.com/viewtopic.php?f=9&t=20707 
    version : 0.1
    */

   /*
    --------------------------------------------------------------------
    Usage:
    --------------------------------------------------------------------

    Requirements: PHP, Apache and MySQL

    Installation:

      --- Create a database named in 	$dbname
   */

   /*
    Link    : http://stackoverflow.com/questions/24929767/mysql-datetime-in-google-chart
    */


    /* PARs */
    $TimeVar = 'id';//'cur_timestamp';// 'id' , 'time' , 'cur_timestamp'
  
    $colsName     = array("$TimeVar", "T", "R", "RH", "LW", "WS","B");
    $colsLabel    = array("Time","Temperature","Rainfall","Relative Humidity","Leaf Wetness","Wind Speed","Battery");
    $colsUnits    = array("","°C","mm","%","%","km/h","%");
    $colsDecimals = array(0,1,1,1,1,2,0);
    $colsPlottype = array("","line","line","line","line","line","line");
  
    /* Your Database Name */
    $dbname = 'waspmote_net';
    $tblname = 'measurements';

    /* Your Database User Name and Passowrd */
    $server = 'localhost';
    $username = 'wwwdata';
    $password = 'mywappsql';

    try {
      /* Establish the database connection */
      $conn = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      /* select all the weekly tasks from the table googlechart */
      $qstr = implode(",", $colsName);
      $qry = 'SELECT ' . $qstr . ' FROM ' . $tblname;
      //echo $qry . '<BR>';
      $result = $conn->query($qry);

      $xData = array();
      /* Extract the information from $result */
      foreach($result as $r) {
        // Values of each slice
        /*id*/ $xData[] = (float)   $r[$colsName[0]];
        //$xData[] =         strtotime($r[$colsName[0]]);
        //$xData[] =         date('Y,n,d,H,i,s', strtotime($r[$colsName[0]]));
        /*cur_timestamp*/ //$xData[] =         'Date.UTC(' . date('Y,n,j,G,i,s', strtotime($r[$colsName[0]])) . ')';
        /*cur_timestamp*/ //$xData[] =         'Date.UTC(' . date('Y,n,d,H,i,s', strtotime($r[$colsName[0]])) . ')';
        /*cur_timestamp*/ //$xData[] =         'Date.UTC(' . date('Y,n,j', strtotime($r[$colsName[0]])) . ')';

        /* OBJECTIVE :: http://forum.highcharts.com/viewtopic.php?f=9&t=20707
        $datetime1 = date('Y, n, j', strtotime($datetime)); //converts date from 2012-01-10 (mysql date format) to the format Highcharts understands 2012, 1, 10
        $datetime2 = 'Date.UTC('.$datetime1.')'; //getting the date into the required format for pushing the Data into the Series
        */
        // dynamical variable name:

        for($ii=1;$ii<count($colsName);$ii++){
          ${"VAR" . $ii}[]  = (float) $r[$colsName[$ii]];
        }

/*
        $VAR1[]  = (float) $r[$colsName[1]];
        $VAR2[]  = (float) $r[$colsName[2]];
        $VAR3[]  = (float) $r[$colsName[3]];
        $VAR4[]  = (float) $r[$colsName[4]];
        $VAR5[]  = (float) $r[$colsName[5]];
        $VAR6[]  = (float) $r[$colsName[6]];
*/
      }
      //echo json_encode(${"VAR" . "1"}) . "<BR>";

      $datasets = array();
      for($x = 1; $x < count($colsName); $x++){
        $tmp = array(
          'name' => "$colsLabel[$x]",
          'data' => ${"VAR" . $x},
          'unit' => $colsUnits[$x],
          'type' => $colsPlottype[$x],
          'valueDecimalas' => $colsDecimals[$x],
        );
        array_push($datasets,$tmp);
      }

      //echo json_encode($datasets);
      $table = array(
        "xData"    => $xData,
        "datasets" => $datasets,
      ); 

      // convert data into JSON format
      //$jsonTable = json_encode($table);
      //echo $jsonTable;
      echo json_encode($table) . ';';

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }

?>
