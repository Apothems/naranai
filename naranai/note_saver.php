<?php

	require_once('hibbity/dbinfo.php');
	

	
	$x 			= mysql_real_escape_string($_POST['x']);
	$y 			= mysql_real_escape_string($_POST['y']);
	$width 		= mysql_real_escape_string($_POST['width']);
	$height 	= mysql_real_escape_string($_POST['height']);
	$text 		= mysql_real_escape_string($_POST['text']);
	$new 		= mysql_real_escape_string($_POST['new']);
	$id 		= mysql_real_escape_string($_POST['id']);
	$user_id	= mysql_real_escape_string($_POST['user_id']);
	$image_id	= mysql_real_escape_string($_POST['image_id']);
	
	if($new == 'true')
	{
		$sql = "INSERT INTO `notes`
							(
							 	image_id,
							  	user_id,
							  	x,
							  	y,
							  	width,
							 	height,
								note
							)
							
							VALUES
							(
							 	" . $image_id . ",
							 	" . $user_id . ",
								" . $x . ",
								" . $y . ",
								" . $width . ",
								" . $height . ",
								'" . $text . "'
							)";
							 	
								  
	}
	else
	{
		$sql = "UPDATE `notes` SET
							user_id = " . $user_id . ",
							x = " . $x . ",
							y = " . $y . ",
							width = " . $width . ",
							height = " . $height . ",
							note = '" . $text . "'
							
						WHERE `id` = " . $id;
	}
	
	mysql_query($sql);
	
	echo $text;

?>