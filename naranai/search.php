<?php
	require_once('hibbity/config.php');
	if($_POST["q"])
	{
		header("Location: " . $base_url . "/post/list/" . $_POST["q"]);
	}
	else
	{
		header("Location: " . $base_url . "/post/list");
	}

?>