<?php

	require_once('hibbity/dbinfo.php');
	
	
	$page_type = "tags";
	$head 		= " <script src=\"/lib/formcheck.js\" type=\"text/javascript\"></script>
					<style type=\"text/css\">
						@import url('/styles/formcheck.css');
					</style>
					";
	$thing = "Add Tag";
	$page_title = "Add Tag - " . $site_name;
	if(isset($_GET["tag"]))
	{
		$sql = "SELECT tag, type FROM `tags` WHERE `id` = " . $_GET["tag"];
		$get = mysql_query($sql);
		$run = mysql_fetch_assoc($get);
		$thing = "Edit Tag";
		switch($run["type"])
		{
			case "normal":
				$normal = ' selected="selected"';
				break;
			case "character":
				$character = ' selected="selected"';
				break;
			case "artist":
				$artist = ' selected="selected"';
				break;
			case "series":
				$series = ' selected="selected"';
				break;
		}
		$page_title = "Edit Tag: " . $run["tag"] . " - " . $site_name;
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
        	<?php echo $thing ?> <?php echo $run["tag"] ?>
        </div>
        <div id="alert">
    		
	    </div>
        <div class="spacer"></div>
    	
        <div id="edit">
	    <form class="registration" id="edit_form" action="/tags/edit/commit" method="post">
	                
            
            <div>
                <span class="edit_title">
                    Name
                </span>
                <span class="edit_form">
                    <input type="text" name="name_field" id="name_field" value="<?php echo $run["tag"] ?>" style="width:250px;" class="validate['required']" />
                </span>
            </div>
            <div>
                <span class="edit_title">
					Type
                </span>
                <span class="edit_form">
                   	<select id="tag_type" name="tag_type">
	                    <option value="normal"<?php echo $normal ?>>Normal</option>
                        <option value="character"<?php echo $character ?>>Character</option>
                        <option value="artist"<?php echo $artist ?>>Artist</option>
                        <option value="series"<?php echo $series ?>>Series</option>
                    </select>
                </span>
            </div>
            <div>
                <span class="edit_title">
                    &nbsp;
                </span>
                <span class="edit_form">
                	<input type="hidden" name="tag_id" value="<?php echo $_GET["tag"]; ?>" />
                    <input type="submit" name="submit" value="<?php echo $thing ?>" />
                </span>
            </div>

    
        </form>
        </div>
    </div>    
    
</div>
<?php
	require_once("footer.php");
?>