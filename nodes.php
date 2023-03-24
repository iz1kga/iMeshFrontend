<?php
include('config.php');

function validateNode($lat, $lon, $alt, $pts, $ts, $isRouter)
{
    $posTime = 7200;
    $infoTime = 18000;
    $status = 0x00;
    if(($lat>0) && ($lon>0) && ($alt<5000) && (time()-$pts<$posTime))
        $status = $status | 0x01;
    if((time()-$ts<$infoTime))
        $status = $status | 0x02;
    if ($isRouter==1)
        $status = 0x04;
    if ($isRouter==2)
        $status = 0x05;
    return $status;
}

function scalePressure($alt, $temp, $press)
{
    try {
        return (int)($press * pow((1-(0.0065*$alt)/($temp+0.0065*$alt+237.15)), -5.257));
    }
    catch (Exception $e){
        return 0;
    }
}

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['timeSpan'])) { $getSpan = $_GET['timeSpan']; } else { $getSpan = 24;  }
if (isset($_GET['QRG']) &&  $_GET['QRG']!=0) { $QRG = "AND m1.qrg=".$_GET['QRG']." "; } else { $QRG = "";  }
if (isset($_GET['channel']) &&  $_GET['channel']!="ALL") { $CH = "AND m1.channel=\"".$_GET['channel']."\" "; } else { $CH = "";  }

$timeSpan = time() - $getSpan * 3600;

//$query = "SELECT * FROM meshNodes WHERE (timestamp>".$timeSpan." OR isRouter <> 0) AND latitude<>0 AND longitude<>0 ORDER BY timestamp DESC";
$query = "SELECT m1.*, m2.longName as gwLongName FROM meshNodes m1 LEFT JOIN meshNodes m2 ON m1.gwID = m2.id WHERE (m1.timestamp >".$timeSpan." OR m1.isRouter <> 0) AND (m1.latitude <> 0 AND m1.longitude <> 0) ".$QRG.$CH."ORDER BY timestamp DESC";
//echo $query;
//$query = "SELECT m1.*, m2.longName as gwLongName FROM meshNodes m1 LEFT JOIN meshNodes m2 ON m1.gwID = m2.id WHERE (m1.timestamp >".$timeSpan." OR m1.isRouter <> 0) AND (m1.latitude <> 0 AND m1.longitude <> 0) ORDER BY timestamp DESC";

$result = mysqli_query($conn, $query);
$data_array = array();
while ($row = mysqli_fetch_assoc($result)) {
//    print_r($row);
    array_push($data_array,
               array("type"=>"Feature",
                     "properties"=>array("nodeID"=>$row["id"],
                                         "longName"=>$row["longName"],
                                         "shortName"=>$row["shortName"],
                                         "hardware"=>$row["hardware"],
                                         "channel"=>$row["channel"],
                                         "altitude"=>$row["altitude"],
                                         "latitude"=>$row["latitude"],
                                         "longitude"=>$row["longitude"],
                                         "nodeStatus"=>validateNode($row["latitude"], $row["longitude"],
                                                                   $row["altitude"], $row["positionTimestamp"], $row["timestamp"], $row["isRouter"], ),
                                         "temperature"=>$row["temperature"],
                                         "pressure"=>$row["pressure"],
                                         "seaLevelPressure"=>scalePressure($row["altitude"], $row["temperature"], $row["pressure"]),
                                         "humidity"=>$row["humidity"],
                                         "airUtil"=>$row["airUtil"],
                                         "chUtil"=>$row["chUtil"],
                                         "batteryLevel"=>$row["batteryLevel"],
                                         "batteryVoltage"=>$row["batteryVoltage"],
                                         "envCurrent"=>$row["envCurrent"],
                                         "envVoltage"=>$row["envVoltage"],
                                         "lastHeard"=>date('H:i:s d-m-Y', $row["timestamp"]),
                                         "qrg"=>$row["qrg"],
                                         "pts"=>$row["positionTimestamp"],
                                         "ts"=>time()-$row["timestamp"],
                                         "rxSnr"=>$row["rxSnr"],
                                         "rxRssi"=>$row["rxRssi"],
                                         "gwID"=>$row["gwID"],
                                         "gwLongName"=>$row["gwLongName"],
                                        ),
                     "geometry"=>array("type"=>"Point", "coordinates"=>array(floatval($row["longitude"]), floatval($row["latitude"])))
                    ));
}
//print_r($data_array);
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
echo json_encode($data_array);
?>
