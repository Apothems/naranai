<?php
	require_once('hibbity/config.php');
	setcookie("user_id", '', time() - 31556926);
	setcookie("user_name", '', time() - 31556926);
	setcookie("user_email", '', time() - 31556926);
	header("Location: " . $base_url . "/post/list");
	exit();
?>