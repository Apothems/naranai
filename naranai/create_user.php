<?php
require_once('hibbity/dbinfo.php');

if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["agree"]))
	{
		$sql = "SELECT COUNT(*) as users FROM `users` WHERE `name` = '" . $_POST["username"] . "' OR `email` = '" . $_POST["email"] . "'";
		$get = mysql_query($sql);
		$run = mysql_fetch_assoc($get);
		
		if($run['users'] > 0)
		{
			header("Location: " . BASE_URL . "/register/err/422");
			exit();
		}
		
		if(($_POST["password"] == $_POST["password2"]) && ($_POST["email"] == $_POST["email2"]))
		{
			$username 	= mysql_real_escape_string($_POST["username"]);
			$password 	= md5($username . mysql_real_escape_string($_POST["password"]));
			$email		= mysql_real_escape_string($_POST["email"]);
			
			$sql = "INSERT INTO `users`(
								   name,
								   pass,
								   joindate,
								   email
								  )
						VALUES	  (
								   '" . $username . "',
								   '" . $password . "',
								   '" . date('Y-m-d H:i:s') . "',
								   '" . $email . "'
								   );";
			mysql_query($sql);
			
			header("Location: " . BASE_URL . "/login");
			exit();
		}
		else
		{
			header("Location: " . BASE_URL . "/register/err/412");
			exit();
		}
	}
else
{
	header("Location: " . BASE_URL . "/register/err/415");
	exit();
}
?>