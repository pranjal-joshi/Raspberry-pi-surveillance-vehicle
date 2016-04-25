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
	//$streamURL = "http://192.168.0.7:8081";
	$streamURL = "http://proxy70.yoics.net:38642"
?>

	<head>
	<link rel="stylesheet" href="style.css">
		<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyD3zA_dGObzN9XY2Xdcb_lnsdcDUw8pzkY"></script>
		<script>
			var map;
			var markerCnt;
			var waypoints = [];
			var dirDisp;
			var dirService = new google.maps.DirectionsService();
			var markerData;
			var wpaddr = [];
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
				var url = "ordinates.php/?wp="+ markerCnt + "&lat=" + loc.lat().toString() + "&lon=" + loc.lng().toString();
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
		</script>
	</head>

	<body>
	<center><h2 style="color: red; font-weight: bold;">GPS guided survillance vehicle</h2></center>
	<table>
	<tr>
		<td style="border-width:2px; border-color:Black ; border-style :groove ;" width="60">
			<div id="googleMap" style="width:900px;height:550px;"></div>
		</td>
		<td style="border-width:2px; border-color:Black ; border-style :groove ;" width="40">
			<img src=<?php echo $streamURL; ?>/>
		</td>
	</tr>

	</table>
	<body>

	<form action="ordinates.php" method="GET">
		<input type="submit" value="clearWaypoints" name="clearWP" id="clearWP">
	</form>
	<button onclick="routePath()">Route path</button>

</html>
