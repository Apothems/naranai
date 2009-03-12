<?php

	require_once('hibbity/dbinfo.php');
	
	$page_type = "post";
	$head 		= ' <script src="/lib/formcheck.js" type="text/javascript"></script>
					<script type="text/javascript">

						window.addEvent(\'domready\', function(){
								new FormCheck(\'login_form\');
						});

					</script>
					<style type="text/css">
						@import url(\'/styles/formcheck.css\');
					</style>
					';
	$page_title = "Login - " . $site_name;
	
	
	
	if(isset($_POST["username"]) && isset($_POST["password"]))
	{
		$username = mysql_real_escape_string($_POST["username"]);
		$password = md5($username . mysql_real_escape_string($_POST["password"]));
		
		$sql = "SELECT id, name, pass, email FROM `users` WHERE name = '" . $username . "' AND pass = '" . $password . "'";
		$get = mysql_query($sql);
		if(mysql_num_rows($get) > 0)
		{
			while($run = mysql_fetch_assoc($get))
			{
				setcookie("user_id", $run['id'], time() + 31556926);
				setcookie("user_name", $run['name'], time() + 31556926);
				setcookie("user_email", $run['email'], time() + 31556926);
				header("Location: " . $base_url . "/post/list");
				exit();
			}
		}
	}
					
	require_once("header.php");
?>

<div id="main">
	
    <div id="sidebar">
    
    	<?php
			echo $search_box;
		?>
        
        
    </div>
        
	
    <div id="content">
    	<div id="page_title">
        	Login
        </div>
    	<div id="alert">
    		
	    </div>
        <div class="spacer"></div>
        <form class="registration" id="login_form" action="" method="post">
        <fieldset>
        <legend>Login Information</legend>
        
        <label for="username">
        	<span>
	        	Username
            </span>
            <input type="text" name="username" class="validate['required','length[4,16]','alphanum']" />
        <label>
        
        <label for="password">
        	<span>
	        	Password
            </span>
            <input type="password" name="password" class="validate['required','length[6,-1]']" />
        <label>
        
        
        <label for="password">
        	<input type="submit" name="submit" value="Login" />
            <span class="small light">
        	All logins are remembered for one year.
        	</span>
        </label>
        
        </fieldset>
        </form>
        
    </div>    
    
</div>
<?php
	require_once("footer.php");
?>