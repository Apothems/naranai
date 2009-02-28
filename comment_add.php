<?php
	
	require_once('hibbity/dbinfo.php');
	
	
	$id 		= mysql_real_escape_string($_POST["picture_id"]);
	$comment 	= mysql_real_escape_string($_POST["comment"]);
	$user 		= 1;
	if(isset($_COOKIE["user_id"]))
	{
		$user = mysql_real_escape_string($_COOKIE["user_id"]);
	}
	
	if(!$id || !$comment)
	{
		header("Location: /post/list");
		exit();
	}
	
	$sql = "INSERT INTO `comments`(
								   image_id,
								   owner_id,
								   owner_ip,
								   posted,
								   comment
								  )
						VALUES	  (
								   " . $id . ",
								   '" . $user . "',
								   '" . $_SERVER['REMOTE_ADDR'] . "',
								   '" . date('Y-m-d H:i:s') . "',
								   '" . $comment . "'
								   );";
	mysql_query($sql);
	header("Location: /post/view/" . $id);
	exit();
	
?>