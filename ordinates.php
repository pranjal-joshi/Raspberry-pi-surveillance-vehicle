<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);

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
		$page = "<html><head><meta http-equiv=\"refresh\" content=\"0; url=http://192.168.0.7:1234/try.php\"></head></html>";
		die($page);
	}

	$query = "INSERT INTO gsv VALUES($wp, $lat, $lon)";
	mysqli_query($con, $query);
?>
