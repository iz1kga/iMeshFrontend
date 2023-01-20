<?PHP
include('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script
    src="https://code.jquery.com/jquery-3.6.3.min.js"
    integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
    crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js" integrity="sha512-OFs3W4DIZ5ZkrDhBFtsCP6JXtMEDGmhl0QPlmWYBJay40TT1n3gt2Xuw8Pf/iezgW9CdabjkNChRqozl/YADmg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css" integrity="sha512-6ZCLMiYwTeli2rVh3XAPxy3YoR5fVxGdH/pz+KMCzRY2M65Emgkw00Yqmhh8qLGeYQ3LbVZGdmOX9KUjSKr0TA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css" integrity="sha512-mQ77VzAakzdpWdgfL/lM1ksNy89uFgibRQANsNneSTMD/bj0Y/8+94XMwYhnbzx8eki2hrbPpDm0vD0CiT2lcg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.min.js" type="text/javascript"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>


    <script src="./js/leaflet.geometryutil.js"></script>
    <script src="./js/leaflet-arrowheads.js"></script>


    <base target="_top">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MeshTastic Italia Live Map</title>
    <link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

	<style>
		html, body {
			height: 100%;
			margin: 0;
		}
		.leaflet-container {
			height: 100%;
			width: 100%;
			max-width: 100%;
			max-height: 100%;
		}
                .tbodyDiv {
			//max-height: clamp(250px);
			overflow: auto;
		}
	</style>

</head>
<body>

<div class="container h-100">
	<div class="row h-100">
		<div class="col-md-12">
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
				</button> <a class="navbar-brand" href="#">Meshtastic Italia Live Map</a>
<ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="https://t.me/meshtastic_italia">Join telegram</a>
      </li>
</ul>
			</nav>
			<div class="h-75 row">
				<div class="col-md-8" style="min-height:350px">
					<div id='map'></div>
				</div>
				<div class="col-md-4">
                                        <div id="tableDiv" class="tbodyDiv">
					    <table class="table table-striped" id="nodeTable">
                                                <thead class="sticky-top bg-white"><tr><th scope="col">Name</th><th scope="col">ID</th><th scope="col">L.H.</th></tr></thead>
    					    </table>
                                        </div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<textarea class="form-control" id="updateBox" rows="5"></textarea>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

    const map = L.map('map').setView([42.5, 12.0], 6);

    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                     maxZoom: 19,
                     attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	             }).addTo(map);

    var geoJSONdata = L.geoJSON();
    var trackData = L.geoJSON();
    var markers = L.markerClusterGroup({maxClusterRadius: 15});

    const mapMarkers = [L.icon({iconUrl: "./meshtasticVectors/marker-satKO-red.svg",
                                iconSize: [46, 46],
                                iconAnchor: [15.5, 42],
                                popupAnchor: [0, -45] }),
                        L.icon({iconUrl: "./meshtasticVectors/marker-satOK-red.svg",
                                iconSize: [46, 46],
                                iconAnchor: [15.5, 42],
                                popupAnchor: [0, -45] }),
                        L.icon({iconUrl: "./meshtasticVectors/marker-satKO-green.svg",
                                iconSize: [46, 46],
                                iconAnchor: [15.5, 42],
                                popupAnchor: [0, -45] }),
                        L.icon({iconUrl: "./meshtasticVectors/marker-satOK-green.svg",
                                iconSize: [46, 46],
                                iconAnchor: [15.5, 42],
                                popupAnchor: [0, -45] }),
    ];

function trackNode(nodeID, duration) {
    console.log( "Tracking: "+nodeID );
    map.removeLayer(trackData);
    var myStyle = {
        "color": "blue",
        "weight": 2,
        "opacity": 0.65
        };
    var trackqr = $.getJSON( "track.php?nodeID="+nodeID+"&duration="+duration, function(mp) {
    trackData = L.geoJSON(mp, {
              arrowheads: { color: 'blue',
                            size: '5%',
                          },
              style: myStyle,
              })
    map.addLayer(trackData);
    });
}

function populateMap() {
    var jqxhr = $.getJSON( "nodes.php", function(mp) {
        //console.log( "success" );

    map.removeLayer(markers);
    map.removeLayer(geoJSONdata);
    markers = L.markerClusterGroup({maxClusterRadius: 15});

    geoJSONdata = L.geoJSON(mp, {
            pointToLayer: function (Feature, latlng) {
                    return L.marker(latlng, {icon: mapMarkers[Feature.properties.nodeStatus]});
            },
            onEachFeature: function (Feature, layer) {
                var temp = '';
                var hum = '';
                var press = '';
                if (Feature.properties.temperature) {temp = '<b>Temperature:</b>'+Feature.properties.temperature+' °C</br>'}
                if ((Feature.properties.humidity) && (Feature.properties.humidity > 0)) {hum = '<b>Humidity:</b>'+Feature.properties.humidity+' %</br>'}
                if (Feature.properties.pressure) {press = '<b>Pressure:</b>'+Feature.properties.seaLevelPressure+' mbar</br>'}
                layer.bindPopup('<h3>'+Feature.properties.longName+'</h3>'+
                                '<h8>'+Feature.properties.hardware+'</h8>'+
                                '<p><b>Node ID:</b> '+Feature.properties.nodeID+'<br/>'+
                                '<b>Short name:</b> '+Feature.properties.shortName+'<br/>'+
                                '<b>Position:</b> '+Feature.properties.latitude+'°, '+Feature.properties.longitude+'°, '+Feature.properties.altitude+'m<br/>'+
                                '<b>CH Util:</b> '+Feature.properties.chUtil+'%<br/>'+
                                '<b>Air Util:</b> '+Feature.properties.airUtil+'%<br/>'+
                                temp+
                                hum+
                                press+
                                '<b>Last Heard:</b> '+Feature.properties.lastHeard+'<br/>'+
                                '<b>Track:</b><a id="myLink" href="#6h" onclick="trackNode(\''+Feature.properties.nodeID+'\', 6);">6h</a>&nbsp;'+
                                '<a id="myLink" href="#12h" onclick="trackNode(\''+Feature.properties.nodeID+'\', 12);">12h</a>&nbsp;'+
                                '<a id="myLink" href="#24h" onclick="trackNode(\''+Feature.properties.nodeID+'\', 24);">1d</a>&nbsp;'+
                                '<a id="myLink" href="#48h" onclick="trackNode(\''+Feature.properties.nodeID+'\', 168);">2d</a>&nbsp;'+
                                '</p>');
        }}); //.addTo(map);
        markers.addLayer(geoJSONdata);
        map.addLayer(markers);
     })
     .done(function(mp) {
         //console.log( "second success" );
             $("#nodeTable tbody").remove();
             //$('#nodeTable').append('<thead><tr><th scope="col">Name</th><th scope="col">ID</th><th scope="col">L.H.</th></tr></thead><tbody>');
             $('#nodeTable').append('<tbody>');
             $.each(mp, function(i, Feature) {
                 $('#nodeTable').append('<tr><td scope="row">'+Feature.properties.longName+'</td>'+
                                        '<td>'+Feature.properties.nodeID+'</td>'+
                                        '<td>'+Feature.properties.lastHeard+'</td></tr>');
             });
             $('#nodeTable').append('</tbody>');

      })
      .fail(function() {
          //console.log( "error" );
      })
      .always(function() {
          //console.log( "complete" );
      });
}


    var mqtt;
    var reconnectTimeout = 2000;
    var host="<?=$wsHost?>";
    var port=<?=$wsPort?>;
    function onFailure(message) {
        //console.log("Connection Attempt to Host "+host+"Failed");
	setTimeout(MQTTconnect, reconnectTimeout);
    }

    function onMessageArrived(msg){
        out_msg="Message received "+msg.payloadString+"<br>";
	out_msg=out_msg+"Message received Topic "+msg.destinationName;
	//console.log(out_msg);
        $('#updateBox').append(msg.payloadString+"\n");
        $('#updateBox').scrollTop($('#updateBox')[0].scrollHeight);
//        populateMap();
    }

    function onConnect() {
        console.log("Connected ");
        mqtt.subscribe("msh/2/stat/updates");
        populateMap();
    }

    function MQTTconnect() {
        console.log("connecting to "+ host +" "+ port);
	var x=Math.floor(Math.random() * 10000); 
	var cname="orderform-"+x;
	mqtt = new Paho.MQTT.Client(host,port,cname);
	var options = { timeout: 3,
			onSuccess: onConnect,
			onFailure: onFailure,
                        useSSL: <?=$wsSecure?>,
	 };
	mqtt.onMessageArrived = onMessageArrived
	mqtt.connect(options); //connect
    }

    var mapmargin = 250;
    var tablemargin = 15;
    $('#map').css("height", ($(window).height() - mapmargin));
    $(window).on("resize", resize);
    function resize(){
        if($(window).width()>=980){
            $('#map').css("height", ($(window).height() - mapmargin));
            $('#tableDiv').css("height", ($(window).height() - mapmargin + tablemargin));
            $('#map').css("margin-top",15);
        }else{
            $('#map').css("height", ($(window).height() - (mapmargin+12)));
            $('#map').css("margin-top",15);
        }
    }

    MQTTconnect();
    resize();

    const interval = setInterval(function() {
       populateMap();
    }, 60000);
</script>

<script type = "text/javascript" language = "javascript">
	  </script>

</body>
</html>
