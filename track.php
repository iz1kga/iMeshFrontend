<?php

include('config.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$nodeID = isset($_GET["nodeID"]) ? $_GET["nodeID"] : die("nodeID Required");

$duration = isset($_GET["duration"]) ? $_GET["duration"] : 6;
$durationS = $duration * 3600;
$timeSpan = time() - $durationS;

$query = "SELECT * FROM nodesPositionHistory WHERE timestamp>".$timeSpan." AND nodeID=\"".$nodeID."\" ORDER BY timestamp ASC";
$result = mysqli_query($conn, $query);
$data_array = array("type"=>"LineString",
                     "coordinates"=>array()
                    );


while ($row = mysqli_fetch_assoc($result)) {
    array_push($data_array["coordinates"],
               array(floatval($row["longitude"]), floatval($row["latitude"])));

}
//print_r($data_array);
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
echo json_encode($data_array);
?>
