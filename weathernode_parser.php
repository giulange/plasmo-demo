<?php

/*  USEFUL LINKS
 *	http://www.w3schools.com/php/php_mysql_insert.asp
 *		
 *
 *
 *
 *
 *
 *
 *
 */



// --- START:
  echo "You reached the Waspmote parser with following parameters:<BR><BR>";

/*
  print_r($_REQUEST);
  echo "<BR><BR>";
*/
  echo "_________________________________<BR>";
  echo "<table>";
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


  echo "<BR> .1. Loading vars...<BR>";
//______________________________________________________________________________

  $tmstamp  = date('Y-m-d H:i:s');	// 00. CURR TIMESTAMP
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
  

  echo " .2. Connecting to MySQL db...";
  $servername     = "localhost";
  $username       = "root";
  $password       = "pedology-life-2014";
  $dbname         = "waspmote_net";
  $tblname        = "measurements";


  // Create connection
  $conn           = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  } 
  echo "  FINE!<BR>";

  $sql = "INSERT INTO $tblname ".
           "(cur_timestamp,node_id,date,time,B,T,R,RH,LW,WD,WS) ".
         "VALUES ".
           "('$tmstamp','$id','$date','$time','$batt','$temp','$rain','$relhum','$leafwet','$winddir','$windspeed')";
           //"('$id','$data','$time',$batt,$temp,$rain,$relhum,$leafwet,$winddir,$windspeed)";
  echo " .3. " . $sql . "<BR>";

  if ($conn->query($sql) === TRUE) {
    echo " .4. New record created successfully <BR>";
  }
  else {
    echo " .4. Error: " . $sql . "<BR>" . $conn->error . "<BR>";
  }


  echo " .5. Data checking...<BR>";
  // SELECT node_id,date,time,B,T,R,RH,LW,WD,WS FROM measurements WHERE cur_timestamp="2015-09-09 12:52:39"
  $sql = "SELECT node_id,date,time,B,T,R,RH,LW,WD,WS FROM $tblname WHERE cur_timestamp='$tmstamp'";
  echo " .6. " . $sql . "<BR>";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
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
  else {
    echo "0 results";
  }

  $conn->close();
  echo " .7. Connection closed<BR>";

  echo "<BR>Bye!<BR><BR>";

?>
