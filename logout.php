<?php
	setcookie("user_id", '', time() - 31556926);
	setcookie("user_name", '', time() - 31556926);
	setcookie("user_email", '', time() - 31556926);
	header("Location: /post/list");
	exit();
?>