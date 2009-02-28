<?php

	if($_POST["q"])
	{
		header("Location: /post/list/" . $_POST["q"]);
	}
	else
	{
		header("Location: /post/list");
	}

?>