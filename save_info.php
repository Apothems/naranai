<?php
	
	require_once("/home/digiwombat/hosteeconn.php");
	mysql_select_db("iki_image");
	
	$id = mysql_real_escape_string($_POST["picture_id"]);
	
	if(!$id)
	{
		header("Location: /post/list");
		exit();
	}
	
	$user_id = 1;
	if(isset($_COOKIE['user_id']))
	{
		$user_id = $_COOKIE['user_id'];
	}
	$old_tags 	= mysql_real_escape_string($_POST["old_tags"]);
	$tags 		= strtolower(mysql_real_escape_string($_POST["tag_field"]));
	$tags 		= str_replace("/", "-", $tags);
	$source 	= mysql_real_escape_string($_POST["source_field"]);
	$rating 	= mysql_real_escape_string($_POST["rating"]);
	$group 		= mysql_real_escape_string($_POST["group_field"]);
	
	
	$urlregex =  "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
	
	if (!eregi($urlregex, $source)) 
	{
		$source = "";
	}
	
	if($tags != $old_tags)
	{
		$sql = "INSERT INTO `tag_histories`(id, 
											image_id, 
											tags, 
											user_id, 
											date_set, 
											user_ip) 
					VALUES(NULL, 
						   '" . $id . "', 
						   '" . $tags . "', 
						   '" . $user_id . "', 
						   '" . date('Y-m-d H:i:s') . "', 
						   '" . $_SERVER['REMOTE_ADDR'] . "')";
		mysql_query($sql);
	}
	$tag_search = str_replace(" ", "', '", $tags);
	$tags 		= explode(" ", $tags);
	foreach($tags as $tag)
	{
		$sql = "INSERT IGNORE INTO `tags`(tag) VALUES('" . $tag . "')";
		mysql_query($sql);
	}
	
	if($group != "None" && $group != "")
	{
		$sql = "INSERT IGNORE INTO `groups`(group_name) VALUES('" . $group . "')";
		mysql_query($sql);
		$sql = "SELECT id FROM `groups` WHERE `group_name` = '" . $group . "'";
		$get = mysql_query($sql);
		$run = mysql_fetch_assoc($get);
		$group_id = $run['id'];
		$sql = "INSERT IGNORE INTO `image_groups`(image_id, group_id) VALUES(" . $id . ", " . $group_id . ")";
		mysql_query($sql);
	}

	$old_tags 	= explode(" ", $old_tags);
	foreach($old_tags as $old_tag)
	{
		if(!in_array($old_tag, $tags))
		{
			$remove[] .= $old_tag;
		}	
	}
	$old_tag_search = implode("', '", $remove);
	
	$sql = "SELECT id FROM `tags` WHERE `tag` IN ('" . $tag_search . "')";
	$get = mysql_query($sql);
	while($run = mysql_fetch_assoc($get))
	{
		$sql_tag = "INSERT IGNORE INTO `image_tags`(image_id, tag_id) VALUES('" . $id . "', '" . $run['id'] . "')";
		mysql_query($sql_tag);
		if(mysql_affected_rows() > 0)
		{
			$sql_tag = "UPDATE `tags` SET `count` = `count` + 1 WHERE `id` = '" . $run['id'] . "'";
			mysql_query($sql_tag);
		}
	}
	
	$sql = "SELECT id FROM `tags` WHERE `tag` IN ('" . $old_tag_search . "')";
	$get = mysql_query($sql);
	while($run = mysql_fetch_assoc($get))
	{
		$sql = "DELETE FROM `image_tags` WHERE `image_id` = '" . $id . "' AND `tag_id` = '" . $run['id'] . "' LIMIT 1";
		mysql_query($sql);
		if(mysql_affected_rows() > 0)
		{
			$sql_tag = "UPDATE `tags` SET `count` = `count` - 1 WHERE `id` = '" . $run['id'] . "'";
			mysql_query($sql_tag);
		}
	}
	
	$sql = "UPDATE `images` SET `source` = '" . $source . "', `rating` = " . $rating . " WHERE `id` = " . $id;
	mysql_query($sql);

	header("Location: /post/view/" . $id);

?>