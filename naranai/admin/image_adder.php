<?php

	require_once('hibbity/dbinfo.php');
	require_once('hibbity/config.php');
	
	
	$urls 	  = $_GET["url"];
	$urls 	  = array_unique($urls);
	$username = "Anonymous";
	if(isset($_GET["user"]))
	{
		$username = urlencode(mysql_real_escape_string(trim($_GET["user"])));
		$username = str_replace("%0320", "", $username);
	}
	
	
	foreach($urls as $url)
	{
		$sql = "select id from images where source = '" . $url . "'";
		$run = mysql_num_rows(mysql_query($sql));
		if($run > 0)
		{
			echo "File exists:" . $url;
		}
		else
		{
			
			$filename = explode("/", $url);
			$filename = array_pop($filename);
			$ext = explode(".", $filename);
			$ext = array_pop($ext);
			
			if($ext == "png" || $ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "GIF" || $ext == "JPG" || $ext == "JPEG" || $ext == "PNG")
			{
				$hash = md5($url.microtime());
				$ab = substr($hash, 0, 2);
				$max_width = '192';
				$thumb_name = $site_dir . "/thumbs/" . $ab . "/" . $hash;
				$image_name = $site_dir . "/images/" . $ab . "/" . $hash;
				
				if(eregi("pixiv.net", $url))
				{
					$refer = "-e 'http://www.pixiv.net/member_illust.php'";	
				}
				$command = "curl " . $refer . " -A 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5' " . $url . " > " . $image_name;
				exec($command);			
				
				$size = @getimagesize($image_name);

				if($size[0] < 25 || $size[1] < 25)
				{
					unlink($image_name);
					die("File too small: " . $size[0] . "x" . $size[1]);
				}
				
				$current_img_width  = $size[0];
				$current_img_height = $size[1];
				
				$too_big_diff_ratio = $current_img_width/$max_width;
				$new_img_width = $max_width;
				$new_img_height = round($current_img_height/$too_big_diff_ratio);
				if($new_img_height > $max_width)
				{
					$too_big_diff_ratio = $current_img_height/$max_width;
					$new_img_height = $max_width;
					$new_img_width = round($current_img_width/$too_big_diff_ratio);
				}
				
				$thumb = imagecreatetruecolor($new_img_width, $new_img_height) or die("Bad dimensions.");
				switch($ext)
				{
					case 'jpg':
						$source = imagecreatefromjpeg($image_name) or die("Bad file.");
						break;
					case 'jpeg':
						$source = imagecreatefromjpeg($image_name) or die("Bad file.");
						break;
					case 'gif':
						$source = imagecreatefromgif($image_name) or die("Bad file.");
						break;
					case 'png':
						$source = imagecreatefrompng($image_name) or die("Bad file.");
						break;
					case 'GIF':
						$source = imagecreatefromgif($image_name) or die("Bad file.");
						break;
					case 'PNG':
						$source = imagecreatefrompng($image_name) or die("Bad file.");
						break;
					default:
						$source = imagecreatefromjpeg($image_name) or die("Bad file.");
						break;
				}
				
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_img_width, $new_img_height, $current_img_width, $current_img_height) or die("Bad reample?.");
				imagejpeg($thumb, $thumb_name, 90) or die("Create thumb file failed.");
				
				$ip  = "255.255.255.255";
				$filesize = filesize($image_name);
				$note = "Linked in IRC by " . $username;
				switch($username)
				{
					case "digiwombat":
						$user = 2;
						break;
					case "Alkenshel":
						$user = 16;
						break;
					case "Ci":
						$user = 4;
						break;
					case "Mastersage":
						$user = 5;
						break;
					case "Zeroblade-":
						$user = 10;
						break;
					case "iluna":
						$user = 17;
						break;
					default:
						$sql_user = "select id from users where name = '" . $username . "' LIMIT 1";
						$run_user = mysql_fetch_assoc(mysql_query($sql_user));
						$user = $run_user["id"];
						break;
				}
				if(!$user) $user = 1;
				
				$sql_add = "INSERT INTO images(
											   owner_id,
											   owner_ip,
											   filename,
											   filesize,
											   hash,
											   ext,
											   width,
											   height,
											   source,
											   note,
											   posted
											  )
										VALUES(
											   " . $user . ",
											   '" . $ip . "',
											   '" . $filename . "',
											   " . $filesize . ",
											   '" . $hash . "',
											   '" . $ext . "',
											   " . $size[0] . ",
											   " . $size[1] . ",
											   '" . mysql_real_escape_string($url) . "',
											   '" . $note . "',
											   '" . date('Y-m-d H:i:s') . "'
											  )";
				mysql_query($sql_add) or die("Fucked up.");
				
				echo "Uploaded: " . $url;
			}
		}
	}
?>