<?php

	require_once('hibbity/dbinfo.php');
	
	
	$page_type = "post";
	$head 		= ' <script src="/lib/textboxlist.js" type="text/javascript"></script>
					<script src="/lib/facebooklist.js" type="text/javascript"></script>
					<script src="/lib/formcheck.js" type="text/javascript"></script>
					<style type="text/css">
						@import url(\'/styles/facelist.css\');
						@import url(\'/styles/comments.css\');
						@import url(\'/styles/formcheck.css\');
					</style>
					<script type="text/javascript">
						window.addEvent(\'domready\', function(){
							var elements = $$(\'.edit form\');
							var id_list;
							$(\'formsender\').addEvent(\'click\', function(){
								elements.each(function(e){
									id = e.id.replace(\'form_\', \'\');
									completer = \'completer_\' + id;
									eval(completer + ".update();");
									e.send();
									if(e == elements.getLast())
									{
										$(\'main_area\').innerHTML = \'<h1>Thanks for tagging your shit.</h1><br /><a href="/post/list" style=\"font-size:20px;font-weight:bold;">Post List &raquo;</a>\'
									}
								});
							});
							
							elements.each(function(e){
								id = e.id.replace(\'form_\', \'\');
								holdy = $(\'taglist_\' + id);
								boxy  = $(\'img_tags_\' + id);
								completer = \'completer_\' + id;
								eval(completer + " = new FacebookList(boxy, holdy)");
								new Request.JSON({\'url\': \'/tag_list.php\', \'onComplete\': function(j) {
									eval("j.each(" + completer + ".autoFeed, " + completer + ");");
								}}).send();

							});
							
							
							  
							
							 //$(\'tagform\').addEvent(\'submit\', function(){
							//	tlist2.update();
							//	this.action = "/save";
							//});
														
						});
					</script>
					<style type="text/css">
						.edit .holder .bit-input .maininput
						{
							width: 80px;
						}
					</style>
					';
	
	$page_title = "Tag your images - img.dasaku";
	
	if(isset($_COOKIE['user_id']))
	{
		$where = '`owner_id` = ' . $_COOKIE['user_id'];
	}
	else
	{
		$where = "`owner_ip` = '" . $_SERVER['REMOTE_ADDR'] . "'";	
	}
	
	$sql = "SELECT id FROM `images` WHERE " . $where . " AND (SELECT id FROM `image_tags` WHERE `image_id` = id GROUP BY `image_id`) IS NULL AND `posted` >= DATE_SUB(NOW(),INTERVAL 5 MINUTE) ORDER BY id DESC LIMIT 10";
	$get = mysql_query($sql);
	

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
        	Tag Your Uploads
        </div>
        <div id="alert" style="display:block;">
    		This is the point where you tag all the shit you just uploaded.
	    </div>
        <div class="spacer"></div>
        <div id="main_area">
    		<?php
			if(mysql_num_rows($get) > 0)
			{
				while($id = mysql_fetch_assoc($get))
				{	
					?>
					<span class="list_image">
								<a href="/post/view/<?php echo $id['id']; ?>">
									<img src="http://img.dasaku.net/thumb/<?php echo $id['id']; ?>.jpg" alt="" title="" />
								</a>
                                <div class="edit">
                                    <form id="form_<?php echo $id['id']; ?>" action="/save" method="post">
                                    
                                    <div>
                                        
                                        <div class="edit_form for_tagging">
                                            <input type="text" name="tag_field" id="img_tags_<?php echo $id['id']; ?>" />
                                            <input type="hidden" name="picture_id" value="<?php echo $id['id']; ?>" />
                                            <div id="taglist_<?php echo $id['id']; ?>" class="taglist">
                                                <div class="default">
                                                    Type for delicious tag search. Need a new tag? Type it and hit space.
                                                </div>
                                                <?php
                                                        
                                                        echo "<ul>";
                                                            echo "<li>tagme</li>";	
                                                        echo "</ul>";
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    </form>
                                    
                                </div>
							</span>
                   <?php
				}
				
				?>
                <div id="formsender">
		        	<h1>Save All Tags</h1>
			    </div> 
             <?php
			}
			else
			{
				echo "<h1>No recent posts.</h1>";
			}
			?>
		
    
        </div>
    </div>  
    
     
    
</div>
<?php
	require_once("footer.php");
?>