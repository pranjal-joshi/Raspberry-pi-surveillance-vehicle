<?php
	$con = mysqli_connect("localhost","root","linux","gsv");

	if(mysqli_connect_errno())
	{
		die("<p>Error commuincating with mySQL database.</p>");
	}
	$wp = $_GET['wp'];
	$lat = $_GET['lat'];
	$lon = $_GET['lon'];
	$clr = $_GET['clearWP'];

	if($clr == 'clearWaypoints')
	{
		$clrQuery = "truncate table gsv";
		mysqli_query($con, $clrQuery);
		$page = "<html><h1>Waypoints Cleared!  Go back to <a href=\"javascript:history.go(-1)\">Homepage</a>.</h1></html>";
		die($page);
	}

	$query = "INSERT INTO gsv VALUES($wp, $lat, $lon)";
	mysqli_query($con, $query);
?>
