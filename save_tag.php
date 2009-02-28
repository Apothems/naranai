<?php
	
	require_once('hibbity/dbinfo.php');
	mysql_select_db("iki_image");
	
	$id = mysql_real_escape_string($_POST["tag_id"]);
	$tag_name = strtolower(mysql_real_escape_string($_POST["name_field"]));
	$tag_type = mysql_real_escape_string($_POST["tag_type"]);
	if(!$id)
	{
		$sql = "INSERT INTO `tags`(tag, type) VALUES('" . $tag_name . "', '" . $tag_type . "'";
		mysql_query($sql);
	}
	else
	{
		$sql = "UPDATE tags SET tag = '" . $tag_name . "', type = '" . $tag_type . "' WHERE id = " . $id;
		mysql_query($sql);
	}
	
	header("Location: /tags/list");

?>