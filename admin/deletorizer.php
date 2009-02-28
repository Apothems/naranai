<?php
	
	if($_COOKIE["user_name"] != "randall")
	{
		header("Location: http://img.dasaku.net");	
		exit();
	}
	
	require_once("/home/digiwombat/hosteeconn.php");
	mysql_select_db("iki_image");
	
	$id  = $_GET["picture_id_number"];
	
	$sql = "select * from images where id = " . $id;
	$run =mysql_query($sql);
	
	$hash = $run['hash'];
	$ab = substr($hash, 0, 2);
	$thumb_name = "/home/digiwombat/iki/img/thumbs/" . $ab . "/" . $hash;
	$image_name = "/home/digiwombat/iki/img/images/" . $ab . "/" . $hash;
	
	unlink($thumb_name);
	unlink($image_name);
	$sql = "DELETE FROM images WHERE id = " . $id . " LIMIT 1";
	mysql_query($sql);
	
	header("Location: http://img.dasaku.net");
?>