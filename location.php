<?php
	$url = $_GET['update'];
	if($url == '1')
	{
		header('location update requested',200);
	}
	die();
?>
