<?php

function validateNode($lat, $lon, $alt, $pts, $ts)
{
    $status = 0x00;
    if(($lat>0) && ($lon>0) && ($alt<5000) && (time()-$pts<7200))
        $status = $satus | 0x01;
    if((time()-$ts<18000))
        $status = $status | 0x02;
    return $status;
}


date_default_timezone_set('Europe/Rome');
$servername = "localhost";
$username = "USERNAME";
$password = "PASSWORD";
$db = "DATABASE";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$yesterday = strtotime('-1 day');

$query = "SELECT * FROM meshNodes WHERE timestamp>".$yesterday." ORDER BY timestamp DESC";
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
                                         "altitude"=>$row["altitude"],
                                         "latitude"=>$row["latitude"],
                                         "longitude"=>$row["longitude"],
                                         "nodeStatus"=>validateNode($row["latitude"], $row["longitude"],
                                                                   $row["altitude"], $row["positionTimestamp"], $row["timestamp"] ),
                                         "airUtil"=>$row["airUtil"],
                                         "chUtil"=>$row["chUtil"],
                                         "batteryLevel"=>$row["batteryLevel"],
                                         "batteryVoltage"=>$row["batteryVoltage"],
                                         "lastHeard"=>date('H:i:s d-m-Y', $row["timestamp"]),
                                         "pts"=>$row["positionTimestamp"],
                                         "ts"=>time()-$row["timestamp"],
                                        ),
                     "geometry"=>array("type"=>"Point", "coordinates"=>array(floatval($row["longitude"]), floatval($row["latitude"])))
                    ));
}
//print_r($data_array);
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
echo json_encode($data_array);
?>
