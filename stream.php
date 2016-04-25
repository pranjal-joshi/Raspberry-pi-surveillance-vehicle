<?php
	$url = $_GET['url'];
	$control = $_GET['data'];
	if(!empty($url))
	{
	$urlFile = "/home/pi/stream.txt";
	$handle = fopen($urlFile, 'w');
	fwrite($handle, $url);
	$page = "<html><h1>Stream URL saved!<a href=\"javascript:history.go(-1)\">Go Back to homepage</a></h1></html>";
	fclose($handle);
	die($page);
	}
	if(!empty($control))
	{
	$urlFile2 = "/home/pi/control.txt";
        $handle2 = fopen($urlFile2, 'w');
        fwrite($handle2, $control);
        $page2 = "<html><h1>Control URL saved!<a href=\"javascript:history.go(-1)\">Go Back to homepage</a></h1></html>";
        fclose($handle2);
        die($page2);
	}
?>
