<div class="container h-100">
	<div class="row h-100">
		<div class="col-md-12">
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
				</button> <a class="navbar-brand" href="https://hub.iz1kga.it">Live Map</a>
<ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="https://t.me/meshtastic_italia">Join telegram</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="https://meshwiki.iz1kga.it">Docs</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="?page=packetRate">PacketeRate</a>
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

    //const map = L.map('map').setView([42.5, 12.0], 6);
    const map = L.map('map').setView([<?=$lat?>, <?=$lon?>], <?=$zoom?>);


    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                     maxZoom: 19,
                     attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	             }).addTo(map);

    var geoJSONdata = L.geoJSON();
    var trackData = L.geoJSON();
    var markers = L.markerClusterGroup({maxClusterRadius: function (zoom) {
                                            return (zoom <= 10) ? 80 : 1; // radius in pixels
                                            },
                                        });

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

    map.addControl(new L.Control.Fullscreen({
        title: {
            'false': 'View Fullscreen',
            'true': 'Exit Fullscreen'
        }
    }));


function localize(t)
{
  var d=new Date(t+" UTC");
  var dateString = ("0" + d.getHours()).slice(-2) + ":" +
                   ("0" + d.getMinutes()).slice(-2) + ":" +
                   ("0" + d.getSeconds()).slice(-2);
  return dateString;
}


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

function centerNode(lat, lon) {
    map.setView([lat, lon], 18);
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
                var batt = '';
                var envVoltage = '';
                var envCurrent = '';
                if (Feature.properties.temperature) {temp = '<b>Temperature:</b>'+Feature.properties.temperature+' °C</br>'}
                if ((Feature.properties.humidity) && (Feature.properties.humidity > 0)) {hum = '<b>Humidity:</b>'+Feature.properties.humidity+' %</br>'}
                if (Feature.properties.pressure) {press = '<b>Pressure:</b>'+Feature.properties.seaLevelPressure+' mbar</br>'}
                if ((Feature.properties.envVoltage)  && (Feature.properties.envVoltage != 0)) {envVoltage = '<b>Voltage Sensor:</b>'+Feature.properties.envVoltage+' V</br>'}
                if ((Feature.properties.envCurrent)  && (Feature.properties.envCurrent != 0)) {envCurrent = '<b>Current Sensor:</b>'+Feature.properties.envCurrent+' mA</br>'}
                if (Feature.properties.batteryLevel) {
                    batt = "mdi-battery-0";
                    if (Feature.properties.batteryLevel >= 5)
                        batt = "mdi-battery-10";
                    if (Feature.properties.batteryLevel >= 15)
                        batt = "mdi-battery-20";
                    if (Feature.properties.batteryLevel >= 25)
                        batt = "mdi-battery-30";
                    if (Feature.properties.batteryLevel >= 35)
                        batt = "mdi-battery-40";
                    if (Feature.properties.batteryLevel >= 45)
                        batt = "mdi-battery-50";
                    if (Feature.properties.pessure >= 55)
                        batt = "mdi-battery-60";
                    if (Feature.properties.batteryLevel >= 65)
                        batt = "mdi-battery-70";
                    if (Feature.properties.batteryLevel >= 75)
                        batt = "mdi-battery-80";
                    if (Feature.properties.batteryLevel >= 85)
                        batt = "mdi-battery-90";
                    if (Feature.properties.batteryLevel >= 95)
                        batt = "mdi-battery";
                }
                layer.bindPopup('<h3>'+Feature.properties.longName+'</h3>'+
                                '<h8>'+Feature.properties.hardware+'</h8>&nbsp;<span class="iconify" data-icon="' + batt + '"></span>'+
                                '<p><b>Node ID:</b> '+Feature.properties.nodeID+'<br/>'+
                                '<b>Short name:</b> '+Feature.properties.shortName+'<br/>'+
                                '<b>Position:</b> '+Feature.properties.latitude+'°, '+Feature.properties.longitude+'°, '+Feature.properties.altitude+'m<br/>'+
                                '<b>CH Util:</b> '+Feature.properties.chUtil+'%<br/>'+
                                '<b>Air Util:</b> '+Feature.properties.airUtil+'%<br/>'+
                                temp+
                                hum+
                                press+
                                envVoltage+
                                envCurrent+
                                '<b>Last Heard:</b> '+Feature.properties.lastHeard+'<br/>'+
                                '<b>Track:</b><a id="myLink" href="#" onclick="trackNode(\''+Feature.properties.nodeID+'\', 6);">6h</a>&nbsp;'+
                                '<a id="myLink" href="#" onclick="trackNode(\''+Feature.properties.nodeID+'\', 12);">12h</a>&nbsp;'+
                                '<a id="myLink" href="#" onclick="trackNode(\''+Feature.properties.nodeID+'\', 24);">1d</a>&nbsp;'+
                                '<a id="myLink" href="#" onclick="trackNode(\''+Feature.properties.nodeID+'\', 168);">2d</a>&nbsp;'+
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
                 $('#nodeTable').append('<tr><td scope="row"><a href="#" onclick="centerNode('+Feature.properties.latitude+', '+Feature.properties.longitude+')">'+Feature.properties.longName+'</a></td>'+
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
    var reconnectTimeout = 10000;
    var host="<?=$wsHost?>";
    var port=<?=$wsPort?>;
    function onFailure(message) {
        //console.log("Connection Attempt to Host "+host+"Failed");
	setTimeout(MQTTconnect, reconnectTimeout);
    }

    function onMessageArrived(msg){
        const msgObj = JSON.parse(msg.payloadString);
        console.log(msg.payloadString);
        //out_msg="Message received "+msg.payloadString+"<br>";
	//out_msg=out_msg+"Message received Topic "+msg.destinationName;
	//console.log(out_msg);
        if((msgObj.type != "text") || (<?= $textEnabled; ?>)) {
            $('#updateBox').append(localize(msgObj.timestamp)+" ["+msgObj.id+"]: "+msgObj.message+" reported by ["+msgObj.reporter+"]\n");
            $('#updateBox').scrollTop($('#updateBox')[0].scrollHeight);
        }
//        populateMap();
    }

    function onConnect() {
        console.log("Connected ");
        mqtt.subscribe("msh/2/stat/updates");
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
    populateMap();

    const interval = setInterval(function() {
       populateMap();
    }, 60000);
</script>

<script type = "text/javascript" language = "javascript">
	  </script>
