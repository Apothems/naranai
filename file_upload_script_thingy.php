<?php
 
	require_once("/home/digiwombat/hosteeconn.php");
	mysql_select_db("iki_image");
 	
 	
 	
$result = array();
 
if (isset($_FILES['photoupload']) )
{
	$name = $_FILES['photoupload']['name'];
	$file = $_FILES['photoupload']['tmp_name'];
	$filesize = $_FILES['photoupload']['size'];
	$error = false;
	$size = false;
	$group = mysql_real_escape_string(urldecode($_GET["group"]));

	if (!is_uploaded_file($file) || ($_FILES['photoupload']['size'] > 2 * 1024 * 1024) )
	{
		$error = 'Please upload only files smaller than 2Mb!';
	}
	if (!$error && !($size = @getimagesize($file) ) )
	{
		$error = 'Please upload only images, no other files are supported.';
	}
	if (!$error && !in_array($size[2], array(1, 2, 3, 7, 8) ) )
	{
		$error = 'Please upload only images of type JPEG, PNG, or GIF.';
	}
	if (!$error && ($size[0] < 25) || ($size[1] < 25))
	{
		$error = 'Please upload an image bigger than 25px.';
	}
	//CREATE MD% SUMMING SHIT
 
	if ($error)
	{
		$result['result'] = 'failed';
		$result['error'] = $error;
	}
	else
	{
			$hash = md5($name.$filesize.$file.microtime());
			$ab = substr($hash, 0, 2);
			$ext = explode(".", $name);
			$ext = array_pop($ext);
			$user = 1;
			if($_GET["user_id"])
			{
				$user = mysql_real_escape_string($_GET["user_id"]);;
			}
			$ip = $_SERVER['REMOTE_ADDR'];
					
			$max_width = '192';

			// Get the current info on the file
			$current_img_width = $size[0];
			$current_img_height = $size[1];

			$thumb_name = "/home/digiwombat/iki/img/thumbs/" . $ab . "/" . $hash;
			$image_name = "/home/digiwombat/iki/img/images/" . $ab . "/" . $hash;
 		    
			$too_big_diff_ratio = $current_img_width/$max_width;
			$new_img_width = $max_width;
			$new_img_height = round($current_img_height/$too_big_diff_ratio);
			if($new_img_height > $max_width)
			{
				$too_big_diff_ratio = $current_img_height/$max_width;
				$new_img_height = $max_width;
				$new_img_width = round($current_img_width/$too_big_diff_ratio);
			}
			// Convert the file
			move_uploaded_file($file, $image_name) or die("Error: Couldn't move file.");
			
			$thumb = imagecreatetruecolor($new_img_width, $new_img_height);
			switch($ext)
			{
				case 'jpg':
					$source = imagecreatefromjpeg($image_name);
					break;
				case 'jpeg':
					$source = imagecreatefromjpeg($image_name);
					break;
				case 'gif':
					$source = imagecreatefromgif($image_name);
					break;
				case 'png':
					$source = imagecreatefrompng($image_name);
					break;
				default:
					$source = imagecreatefromjpeg($image_name);
					break;
			}
			
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_img_width, $new_img_height, $current_img_width, $current_img_height);
			imagejpeg($thumb, $thumb_name, 90);
			
			//$make_magick = system("convert -format jpeg -quality 85 -compress JPEG -thumbnail $new_img_width x $new_img_height $image_name $thumb_name", $retval);
			// Did it work?
			if (!($retval)) {
					
			}
			else {
				$result['result'] = 'error';
				$result['error'] = 'Thumb creation failed.';
			}

			
			
			$sql = "INSERT INTO images(
									   owner_id,
									   owner_ip,
									   filename,
									   filesize,
									   hash,
									   ext,
									   width,
									   height,
									   posted
									  )
								VALUES(
									   " . $user . ",
									   '" . $ip . "',
									   '" . $name . "',
									   " . $filesize . ",
									   '" . $hash . "',
									   '" . $ext . "',
									   " . $size[0] . ",
									   " . $size[1] . ",
									   '" . date('Y-m-d H:i:s') . "'
									  )";
			mysql_query($sql);
			$id = mysql_insert_id();
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
									   
			$result['result'] = 'success';
			$result['size'] = "Uploaded an image ({$size['mime']}) with  {$size[0]}px/{$size[1]}px.";
	}
 
}
else
{
	$result['result'] = 'error';
	$result['error'] = 'Missing file or internal error!';
}

if($_POST["fail"] == "true" && !headers_sent())
{
	header('Location: /post/list');
	exit();
}

if (!headers_sent() )
{
	header('Content-type: application/json');
}
 
echo json_encode($result);
 
?>