<?php
	
	
	
	if($_COOKIE["user_name"] != "randall")
	{
		header("Location: " . $base_url);	
		exit();
	}
	
	require_once('hibbity/dbinfo.php');
	require_once('hibbity/config.php');
	
	
	$id  = $_GET["picture_id_number"];
	
	$sql = "select * from images where id = " . $id;
	$run =mysql_query($sql);
	
	$hash = $run['hash'];
	$ab = substr($hash, 0, 2);
	$thumb_name = $site_dir . "/thumbs/" . $ab . "/" . $hash;
	$image_name = $site_dir . "/images/" . $ab . "/" . $hash;
	
	unlink($thumb_name);
	unlink($image_name);
	$sql = "DELETE FROM images WHERE id = " . $id . " LIMIT 1";
	mysql_query($sql);
	
	header("Location: ' . $base_url . '");
?>