<?php

require_once('hibbity/dbinfo.php');
mysql_select_db("iki_image");
require_once("lib/functions.php");
	
	$pic = $_GET["q"];
	$pic = explode("/", $pic);
	$type = $pic[0];
	$pic = explode(".", $pic[1]);
	$sql = "SELECT * FROM `images` WHERE id = " . $pic[0] . "";
	$get = mysql_query($sql);
	
	$run = mysql_fetch_object($get);
	

	send_file($run, $type);

?>