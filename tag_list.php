<?php

require_once("/home/digiwombat/hosteeconn.php");
mysql_select_db("iki_image");

if(isset($use_small))
{
	
}
else
{
	$sql = mysql_query("select * from tags");
	echo '["';
	while($run = mysql_fetch_assoc($sql))
	{
		echo $run["tag"] . '", "';
	}
	echo '"]';
}

?>