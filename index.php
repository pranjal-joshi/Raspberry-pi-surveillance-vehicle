<!--
	Name: 	GSV control panel main page.
	Author:	Pranjal Joshi.
	Date:	7-1-2014
-->

<!DOCTYPE html>
<html>
<title>GSV control panel</title>
<?php
	$lat = 18.51037736775892;
	$lon = 73.81354451179504;
	//$streamURL = "http://cyberfox.dlinkddns.com:8081";
	$urlFile = '/home/pi/stream.txt';
	$handle = fopen($urlFile, 'r');
	$streamURL = fread($handle, filesize($urlFile));
	fclose($handle);
	$controlFile = '/home/pi/control.txt';
	$handle2 = fopen($controlFile, 'r');
	$controlURL = fread($handle2, filesize($controlFile));
	fclose($handle2);
?>

	<head>
	<link rel="stylesheet" href="style.css">
		<script src="https://maps.googleapis.com/maps/api/js?key=ENTER YOUR GOOGLE MAPS API KEY HERE"></script>
		<script>
			var map;
			var markerCnt;
			var waypoints = [];
			var dirDisp;
			var dirService = new google.maps.DirectionsService();
			var markerData;
			var wpaddr = [];

			var pulseMarker = new google.maps.MarkerImage(
							'http://plebeosaur.us/etc/map/bluedot_retina.png',
							null, // size
							null, // origin
							new google.maps.Point( 8, 8 ), // anchor (move to center of marker)
							new google.maps.Size( 17, 17 )
			);

			function init()
			{
				dirDisp = new google.maps.DirectionsRenderer();
				markerCnt = 1;
				var latlng = {lat: <?php echo $lat;?>, lng: <?php echo $lon;?>};

				var mapProp = {
					center: latlng,
					zoom:15,
					mapTypeId:google.maps.MapTypeId.ROADMAP
				};

				 map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
				dirDisp.setMap(map);

				google.maps.event.addListener(map, 'click', function(event){
					placeMarker(event.latLng);
				});
			}

			google.maps.event.addDomListener(window, 'load', init);

			function placeMarker(loc) {
				var mark = new google.maps.Marker({
					position: loc,
					map: map,
					animation: google.maps.Animation.DROP
				});
				var markerString = markerCnt.toString();
				var info = new google.maps.InfoWindow({
					content: 'Waypoint: ' + markerString
				});

				waypoints[markerCnt*2] = loc.lat();
				waypoints[markerCnt*2+1] = loc.lng();
				markerData = new google.maps.LatLng(waypoints[markerCnt*2], waypoints[markerCnt*2+1]);
				info.open(map,mark);

				var xmlhttp = new XMLHttpRequest();
				var url = "ordinates_bkp.php/?wp="+ markerCnt + "&lat=" + loc.lat().toString() + "&lon=" + loc.lng().toString();
				xmlhttp.open("GET", url, true);
				xmlhttp.send();
				getAddr();

				markerCnt = markerCnt + 1;
			}

			function routePath()
			{
			if(markerCnt > 1)
			{
				var start = new google.maps.LatLng(waypoints[2], waypoints[3]);
				var end = new google.maps.LatLng(waypoints[markerCnt*2], waypoints[markerCnt*2+1]);
				var wps = [];
				for(var i=1;i<wpaddr.length;i++){
					var adr = wpaddr[i];
					console.log("adr: "+adr);
					wps.push({
						location: adr,
						stopover: true
					});
				}
				var x = wpaddr[1];
				var y = wpaddr[markerCnt-1];
				var req = {
					origin: x,
					destination: y,
					waypoints: wps,
					optimizeWaypoints: true,
					travelMode: google.maps.DirectionsTravelMode.WALKING
				};
				console.log("start: "+x);
				console.log("end: "+y);
				console.log(wps);
				console.log(req);
				dirService.route(req, function(result, status){
					if(status == google.maps.DirectionsStatus.OK){
						dirDisp.setDirections(result);
					}
					else{
						alert("Failed to get directions: " + status);
					}
				});
			}
			}
			/*
				Discarded function: Geocoder is not used. only LatLng object is copied to array.
				Don't delete this function. Its called while placing every marker!!!
			*/
			function getAddr(){
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({'latLng': markerData}, function(result, status) {
					if(status == google.maps.GeocoderStatus.OK){
						if(result[1]){
							//wpaddr[markerCnt-1] = result[1].formatted_address;
							wpaddr[markerCnt-1] = markerData;
							console.log(wpaddr[markerCnt-1]);
						}
					}
				});
			}

			var timeout = setInterval(locationUpdate, 1500);
			function locationUpdate(){
				$locURL = "location.php/?update=1";
				var resp = httpGet($locURL);
				console.log(resp);
			}

			var lastkey;
			function httpGet(theUrl)
			{
				var xmlHttp = new XMLHttpRequest();
				xmlHttp.open("GET", theUrl,true ); // false for synchronous request
				xmlHttp.send();
				return xmlHttp.responseText;
			}

			window.onkeydown=function checkKeyDown(e) {
			if(lastkey && lastkey.keyCode == e.keyCode)
			{
				return;
			}
			lastkey=e;
			document.getElementById("demo1").innerHTML = "The Unicode value is: " + e.keyCode;
			if(e.keyCode == "87") {
				httpGet("<?php echo $controlURL; ?>/?dir?servo?up");
			}
			if (e.keyCode == "83") {
				httpGet("<?php echo $controlURL; ?>/?dir?servo?down");
			}
			if (e.keyCode == "65") {
				httpGet("<?php echo $controlURL; ?>/?dir?servo?left");
			}
			if (e.keyCode == "68") {
				httpGet("<?php echo $controlURL; ?>/?dir?servo?right");
			}

			if (e.keyCode == "38") {
				httpGet("<?php echo $controlURL; ?>/?car=1&stop=0");
			}
			if (e.keyCode == "40") {
				httpGet("<?php echo $controlURL; ?>/?car=2&stop=0");
			}
			if (e.keyCode == "37") {
				httpGet("<?php echo $controlURL; ?>/?car=3&stop=0");
			}
			if (e.keyCode == "39") {
				httpGet("<?php echo $controlURL; ?>/?car=4&stop=0");
			}
		}

		window.addEventListener("keyup",checkKeyUp);
		function checkKeyUp(e) {
			document.getElementById("demo2").innerHTML = "The Unicode value is: " + e.keyCode;
			if (e.keyCode == "87") {
				httpGet("<?php echo $controlURL; ?>/?stop?servo?up");
			}
			if (e.keyCode == "83") {
				httpGet("<?php echo $controlURL; ?>/?stop?servo?down");
			}
			if (e.keyCode == "65") {
				httpGet("<?php echo $controlURL; ?>/?stop?servo?left");
			}
			if (e.keyCode == "68") {
				httpGet("<?php echo $controlURL; ?>/?stop?servo?right");
			}

			if (e.keyCode == "38") {
				httpGet("<?php echo $controlURL; ?>/?stop=1&car=0");
			}
			if (e.keyCode == "40") {
				httpGet("<?php echo $controlURL; ?>/?stop=1&car=0");
			}
			if (e.keyCode == "37") {
				httpGet("<?php echo $controlURL; ?>/?stop=1&car=0");
			}
			if (e.keyCode == "39") {
				httpGet("<?php echo $controlURL; ?>/?stop=1&car=0");
			}
		}

		</script>
	</head>

	<body id="body">
	<center><h2 style="color: red; font-weight: bold;">GPS guided survillance vehicle</h2></center>
	<table>
	<tr>
		<td style="border-width:2px; border-color:Black ; border-style :groove ;" width="60">
			<div id="googleMap" style="width:900px;height:550px;"></div>
			<form action="stream.php" id="controlTextbox" method="GET">
				Enter Control URL: <input type="text" name="data" id="controlUrlBox">
				<input type="submit" value="Submit" id="controlTextboxButton">
				<br>
			</form>
		</td>
		<td style="border-width:2px; border-color:Black ; border-style :groove ;" width="40">
			<img src=<?php echo $streamURL; ?>/>
			<form action="stream.php" id="streamTextbox" method="GET">
				Enter stream URL: <input type="text" name="url" id="urlBox">
				<input type="submit" value="Submit" id="streamTextboxButton">
				<br>
			</form>

		</td>
	</tr>

	</table>
	<body>

	<form action="ordinates_bkp.php" method="GET">
		<input type="submit" value="clearWaypoints" name="clearWP" id="clearWP">
	</form>
	<button onclick="routePath()">Route path</button>

<h1></h1>
<h1 id="demo1"></h1>
<h2></h2>
<h2 id="demo2"></h2>
</html>
