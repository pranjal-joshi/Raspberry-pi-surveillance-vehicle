<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);

	$car = $_GET["car"];
	$stop = $_GET["stop"];

	$con = mysqli_connect("localhost","root","linux","gsv");
	$lock = "lock tables motor";
	$query = "update motor set id=1, dir=$car, stop=$stop where id=1";

	mysqli_query($con, $query);

	exit();
?>
