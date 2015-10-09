<?php

/*  USEFUL LINKS
 *	  http://www.w3schools.com/php/php_mysql_insert.asp
 *		
 *
 *
 *
 *
 *
 *  EXAMPLE RUN:
 *    http://143.225.214.136/wapp/weathernode_parser.php?id=gproc_002&date=2015-01-02&time=15:40:00&B=78&T=20.1&R=0&RH=72&LW=9&WD=W&WS=1.2
 */

//___________________________
// S E T   V A R I A B L E S
//___________________________

  $isTEST         = 0;// <––– put this in the POST body to manage it from outside!
  $servername     = "localhost";
  $username       = "wwwdata";
  $password       = "mywappsql";
  $dbname         = "waspmote_net";
  $tblname        = "measurements";


//______________________
// R E A D  ::  P O S T
//______________________

  if($isTEST){
    echo "You reached the Waspmote parser with following parameters:<BR><BR>";
      echo "_________________________________<BR>";
      echo "<table>";
      echo "<tr><td width=\"100\">" . "VAR" . "</td><td>" . "POST" . "</td></tr>";
      foreach ($_REQUEST as $key => $value) {
        echo "<tr>";
        echo "  <td width=\"100\">";
        echo      $key;
        echo "  </td>";
        echo "  <td>";
        echo      $value;
        echo "  </td>";
        echo "</tr>";
      }
      echo "</table>";
      echo "_________________________________<BR>";
    /*
    print_r($_REQUEST);
    echo "<BR><BR>";
    */
  }
  
  if($isTEST){ echo "<BR> .01. Loading vars...<BR><BR>"; }
//______________________________________________________________________________

  $tmstamp  = date('Y-m-d H:i:s');      // 00. CURR TIMESTAMP
  $id       = $_REQUEST['id'];          // 01. ID
  $date     = $_REQUEST['date'];        // 02. DATE
  $time     = $_REQUEST['time'];        // 03. TIME
  $batt     = $_REQUEST['B'];           // 04. BATTERY
  $temp     = $_REQUEST['T'];           // 05. TEMPERATURE
  $rain     = $_REQUEST['R'];           // 06. RAINFALL
  $relhum   = $_REQUEST['RH'];          // 07. RELATIVE HUMIDITY
  $leafwet  = $_REQUEST['LW'];          // 08. LEAF WETNESS
  $winddir  = $_REQUEST['WD'];          // 09. WIND DIRECTION
  $windspeed= $_REQUEST['WS'];          // 10. WIND SPEED
//______________________________________________________________________________
  

//___________________________________
// C O N N E C T I O N  ::  O P E N
//___________________________________

  if($isTEST){ echo " .02. Connecting to MySQL db..."; }
  // Create connection
  $conn           = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    if($isTEST){ die("[Connection failed: " . $conn->connect_error . "]"); }
    else{ die("[ERR_#01 :: CONNECTION TO DB FAILED]"); }
  } 
  if($isTEST){ echo "  FINE!<BR><BR>"; }


//___________________________________
// A L R E A D Y   I N S E R T E D ?
//___________________________________

  // Before to insert new record, check if it is already existent
  if($isTEST){ echo " .03. CHECKING :: if data is already in the db...<BR>"; }
  $sql = "SELECT node_id,date,time FROM $tblname WHERE node_id='$id' AND date='$date' AND time='$time'";
  if($isTEST){ echo " .04. SQL string :: " . $sql . "<BR><BR>"; }
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    if($isTEST){
      echo " .05. Records with same { id | date | time } :: " . $result->num_rows . "<BR>";
      // retrieve the whole record for printing
      $sql = "SELECT node_id,date,time,B,T,R,RH,LW,WD,WS FROM $tblname WHERE node_id='$id' AND date='$date' AND time='$time' LIMIT 1";
      $result = $conn->query($sql);
      echo "_________________________________<BR>";
      echo "<table>";
      // output data of each row
      echo "<tr><td width=\"100\">" . "POST" . "</td><td>" . "MySQL" . "</td></tr>";
      while($row = $result->fetch_assoc()) {
        echo "<tr><td width=\"100\">" . $id         . "</td><td>" . $row['node_id']  . "</td></tr>";
        echo "<tr><td width=\"100\">" . $date       . "</td><td>" . $row['date']     . "</td></tr>";
        echo "<tr><td width=\"100\">" . $time       . "</td><td>" . $row['time']     . "</td></tr>";
        echo "<tr><td width=\"100\">" . $batt       . "</td><td>" . $row['B']        . "</td></tr>";
        echo "<tr><td width=\"100\">" . $temp       . "</td><td>" . $row['T']        . "</td></tr>";
        echo "<tr><td width=\"100\">" . $rain       . "</td><td>" . $row['R']        . "</td></tr>";
        echo "<tr><td width=\"100\">" . $relhum     . "</td><td>" . $row['RH']       . "</td></tr>";
        echo "<tr><td width=\"100\">" . $leafwet    . "</td><td>" . $row['LW']       . "</td></tr>";
        echo "<tr><td width=\"100\">" . $winddir    . "</td><td>" . $row['WD']       . "</td></tr>";
        echo "<tr><td width=\"100\">" . $windspeed  . "</td><td>" . $row['WS']       . "</td></tr>";
      }
      echo "</table>";
      echo "_________________________________<BR>";

      die( " .06. Current record will not be inserted!" . "<BR>Bye!<BR><BR>" );
    }
    die( "[ERR_#02 :: RECORD ALREADY EXISTENT]" );
  }
  else {
    if($isTEST){ echo " .05. Current record is new!" . "<BR>"; }
  }


//_____________
// I N S E R T
//_____________

  $sql = "INSERT INTO $tblname ".
          "(cur_timestamp,node_id,date,time,B,T,R,RH,LW,WD,WS) ".
          "VALUES ".
          "('$tmstamp','$id','$date','$time','$batt','$temp','$rain','$relhum','$leafwet','$winddir','$windspeed')";
  if($isTEST){ echo " .06. Inserting :: " . $sql . "<BR>"; }

  if ($conn->query($sql) === TRUE) {
    if($isTEST){ echo " .07. New record successfully created! <BR>"; }
  }
  else {
    if($isTEST){ echo " .07. Error: " . $sql . "<BR>" . $conn->error . "<BR>"; }
    else{ die("[ERR_#03 :: 'INSERT INTO']"); }
  }


//_________________________
// C H E C K   I N S E R T
//_________________________

  if($isTEST){ echo " .08. Data checking :: "; }
  $sql = "SELECT node_id,date,time,B,T,R,RH,LW,WD,WS FROM $tblname WHERE cur_timestamp='$tmstamp'";
  if($isTEST){ echo $sql . "<BR><BR>"; }

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    if($isTEST){

      echo "_________________________________<BR>";
      echo "<table>";
      // output data of each row
      echo "<tr><td width=\"100\">" . "POST" . "</td><td>" . "MySQL" . "</td></tr>";
      while($row = $result->fetch_assoc()) {
        echo "<tr><td width=\"100\">" . $id         . "</td><td>" . $row['node_id']  . "</td></tr>";
        echo "<tr><td width=\"100\">" . $date       . "</td><td>" . $row['date']     . "</td></tr>";
        echo "<tr><td width=\"100\">" . $time       . "</td><td>" . $row['time']     . "</td></tr>";
        echo "<tr><td width=\"100\">" . $batt       . "</td><td>" . $row['B']        . "</td></tr>";
        echo "<tr><td width=\"100\">" . $temp       . "</td><td>" . $row['T']        . "</td></tr>";
        echo "<tr><td width=\"100\">" . $rain       . "</td><td>" . $row['R']        . "</td></tr>";
        echo "<tr><td width=\"100\">" . $relhum     . "</td><td>" . $row['RH']       . "</td></tr>";
        echo "<tr><td width=\"100\">" . $leafwet    . "</td><td>" . $row['LW']       . "</td></tr>";
        echo "<tr><td width=\"100\">" . $winddir    . "</td><td>" . $row['WD']       . "</td></tr>";
        echo "<tr><td width=\"100\">" . $windspeed  . "</td><td>" . $row['WS']       . "</td></tr>";
      }
      echo "</table>";
      echo "_________________________________<BR><BR>";
    }
  }
  else {
    die("[ERR_#04 :: EMPTY FETCH, AFTER INSERT]");
  }


//____________________________________
// C O N N E C T I O N  ::  C L O S E
//____________________________________

  $conn->close();
  if($isTEST){ echo " .09. Connection closed<BR>"; }
  if($isTEST){ echo "<BR>Bye!<BR><BR>"; }
  else{ echo "[SUCCESSFULL]"; }

?>