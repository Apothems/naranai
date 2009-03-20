<?php
	require_once('hibbity/config.php');
	if($_POST["q"])
	{
		header("Location: " . BASE_URL . "/post/list/" . $_POST["q"]);
	}
	else
	{
		header("Location: " . BASE_URL . "/post/list");
	}

?>