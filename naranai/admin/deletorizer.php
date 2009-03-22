<?php
	require_once("../hibbity/dbinfo.php");
	require_once('../lib/functions.php');
	if(!isadmin($_COOKIE['user_id']))
	{
		header("Location: " . BASE_URL);	
		exit();
	}
	
	
	$id  = $_GET["picture_id_number"];
	
	$sql = "select * from images where id = " . $id;
	$run =mysql_query($sql);
	
	$hash = $run['hash'];
	$ab = substr($hash, 0, 2);
	$thumb_name = SITE_DIR . "/thumbs/" . $ab . "/" . $hash;
	$image_name = SITE_DIR . "/images/" . $ab . "/" . $hash;
	
	unlink($thumb_name);
	unlink($image_name);
	$sql = "DELETE FROM images WHERE id = " . $id . " LIMIT 1";
	mysql_query($sql);
	
	header("Location: " . BASE_URL);
?>