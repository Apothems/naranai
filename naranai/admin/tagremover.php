<?php

	require_once('../hibbity/dbinfo.php');
	/*$tag_to_delete = 'naruto';
	$id  = 94;
	
	echo "Removing all data for files tagged " . $tag_to_delete . "<br />";
	
	$sql = "SELECT i.hash, t.image_id FROM `image_tags` t LEFT OUTER JOIN `images` i ON t.image_id = i.id WHERE `tag_id` = " . $id;
	$get = mysql_query($sql);
	while($run = mysql_fetch_assoc($get))
	{
		$ab = substr($run['hash'], 0, 2);
		$thumb_name = "/home/digiwombat/iki/img/thumbs/" . $ab . "/" . $run['hash'];
		$image_name = "/home/digiwombat/iki/img/images/" . $ab . "/" . $run['hash'];
		unlink($thumb_name);
		echo "Removed thumb for " . $run['image_id'] . "<br />";
		unlink($image_name);
		echo "Removed image for " . $run['image_id'] . "<br />";
		$delsql = "DELETE FROM `images` WHERE `id` = " . $run['image_id'] . " LIMIT 1";
		mysql_query($delsql);
		echo "Removed db entry for " . $run['image_id'] . "<br />";
		$delsql = "DELETE FROM `image_tags` WHERE `image_id` =" . $run['image_id'];
		mysql_query($delsql);
		echo "Removed tags for " . $run['image_id'] . "<br />";
		$delsql = "DELETE FROM `image_groups` WHERE `image_id` =" . $run['image_id'];
		mysql_query($delsql);
		echo "Removed groups for " . $run['image_id'] . "<br />";
		$delsql = "DELETE FROM `tag_history` WHERE `image_id` =" . $run['image_id'];
		mysql_query($delsql);
		echo "Removed history for " . $run['image_id'] . "<br />";
		$delsql = "DELETE FROM `comments` WHERE `image_id` =" . $run['image_id'];
		mysql_query($delsql);
		echo "Removed comments for " . $run['image_id'] . "<br />";
		$delsql = "DELETE FROM `notes` WHERE `image_id` =" . $run['image_id'];
		mysql_query($delsql);
		echo "Removed notes for " . $run['image_id'] . "<br />";
	}
	*/
	$sql = "SELECT id, tag, count FROM `tags`";
	$get = mysql_query($sql);
	while($run = mysql_fetch_assoc($get))
	{	
		
		$countsql = "SELECT image_id FROM `image_tags` WHERE tag_id = " . $run['id'];
		$countget = mysql_query($countsql);
		$count 	  = mysql_num_rows($countget);
		echo "Checked tag count for " . $run['tag'] . "<br />";
		if($count != $run['count'])
		{
			$resetsql = "UPDATE `tags` SET `count` = " . $count . " WHERE id = " . $run['id'];
			mysql_query($resetsql);
			echo "Changed tag count for " . $run['tag'] . "<br />";
		}
	}
?>