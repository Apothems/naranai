<?php
	
	require_once('hibbity/dbinfo.php');
	
	
	$page_type 	= "post";
	
	$sql = "SELECT id FROM notes ORDER BY id DESC LIMIT 1";
	$get = mysql_query($sql);
	
	$run = mysql_fetch_assoc($get);
	$note_count = $run['id'] + 0;
	//mysql_free_result($run);
	
	$pic = $_GET["picture_id"];
	$sql = "SELECT i.id, i.filename, i.source, i.height, i.width, i.hash, i.ext, i.posted, i.numeric_score, i.rating, group_concat(t.tag separator ',') AS tags, group_concat(t.count separator ',') AS counts, group_concat(t.type separator ',') AS types FROM `images` i LEFT OUTER JOIN `image_tags` s ON  i.id = s.image_id LEFT OUTER JOIN `tags` t ON s.tag_id = t.id WHERE i.id = " . $pic . " GROUP BY i.id";
	$get = mysql_query($sql);
	
	$run = mysql_fetch_assoc($get);
	
	$head 		= ' <script src="/lib/textboxlist.js" type="text/javascript"></script>
					<script src="/lib/facebooklist.js" type="text/javascript"></script>
					<script src="/lib/formcheck.js" type="text/javascript"></script>
					<script src="/lib/moocombo.js" type="text/javascript"></script>
					<script type="text/javascript">
						var orig_width =  ' . $run['width'] . ';
						var note_id = ' . $note_count . ' ;
					</script>
					<script src="/lib/view.js" type="text/javascript"></script>
					<style type="text/css">
						@import url(\'/styles/facelist.css\');
						@import url(\'/styles/comments.css\');
						@import url(\'/styles/formcheck.css\');
					</style>
					';
	
	$page_title = "Post " . $run['id'] . " - img.dasaku";
	
	$source = "None";
	if($run['source'] != "")
	{
		$source = '<a href="' . $run['source'] . '">' . $run['source'] . '</a>';
	}
	
	$form_tags = str_replace(",", " ", $run['tags']);
	$tags = explode(",", $run['tags']);
	$counts = explode(",", $run['counts']);
	$types = explode(",", $run['types']);
	
	$sql_user = "SELECT u.name FROM `images` i LEFT OUTER JOIN `users` u ON i.owner_id = u.id WHERE i.id = " . $pic;
	$get_user = mysql_query($sql_user);
	$run_user = mysql_fetch_assoc($get_user);
	
	$sql_group = "SELECT g.id, g.group_name FROM `image_groups` i LEFT OUTER JOIN `groups` g ON i.group_id = g.id WHERE i.image_id = " . $pic;
	$get_group = mysql_query($sql_group);
	$run_group = mysql_fetch_assoc($get_group);
	
	if(mysql_num_rows($get_group) > 0)
	{
		$group_id 	= $run_group['id'];
		$group		= '<a href="/group/view/' . $run_group['id'] . '">' . $run_group['group_name'] . '</a>';
	}
	else
	{
		$group = "None";	
	}
	
	for($i = 0; $i < count($counts); $i++)
	{
		$counts_proper[] .=	$counts[$i] . ':' . $types[$i];
	}
	$counts = "";
	
	$tags = array_combine($tags, $counts_proper);
	arsort($tags, SORT_NUMERIC);
	array_slice($tags, 0, 15);
	
	
	switch($run['rating'])
	{
		case 0:
			$unrated = ' checked="checked"';
			$rating = 'unrated';
			break;
		case 1:
			$explicit = ' checked="checked"';
			$rating = 'explicit';
			break;
		case 2:
			$questionable = ' checked="checked"';
			$rating = 'questionable';
			break;
		case 3:
			$safe = ' checked="checked"';
			$rating = 'safe';
			break;
		default:
			$unrated = ' checked="checked"';
			$rating = 'unrated';
			break;
	}
	
	require_once("header.php");
?>


<div id="main">
	
    <div id="sidebar">
    
    	<?php
			echo $search_box;
		?>
        
        <div id="file_info">
        	<div class="block_title">
            	File Info
            </div>
            <div class="block_content">
            	<strong>Resolution:</strong> <?php echo $run['width'] . 'x' . $run['height'] ?><br />
                <strong>Rating:</strong> <?php echo $rating; ?><br />
                <strong>Score:</strong> <?php echo $run['numeric_score'] ?>
            </div>
        </div>
        
        <div id="tag_list">
        	<div class="block_title">
            	Image Tags
            </div>
            <div class="block_content">
            	<?php
					foreach($tags as $tag => $count)
					{
						$count = explode(":", $count);
						echo '<a href="/post/list/' . $tag . '" class="' . $count[1] . '">' . $tag . '</a> ' . $count[0] . '<br />';
					}
				?>
            </div>
        </div>
        
        <div id="pop_list">
        	<div class="block_title">
            	Popular Tags
            </div>
            <div class="block_content">
            	<?php
					$sql_block = "SELECT tag, count, type FROM tags ORDER BY count DESC LIMIT 15";
					$get_block = mysql_query($sql_block);
					while($run_block = mysql_fetch_assoc($get_block))
					{
						echo '<a href="/post/list/' . $run_block['tag'] . '" class="' . $run_block['type'] . '">' . $run_block['tag'] . '</a> ' . $run_block['count'] . '<br />';
					}
				?>
            </div>
        </div>

        <div id="img_admin">
        	<div class="block_title">
            	Image Admin
            </div>
            <div class="block_content">
            	<a id="add_note">Add Translation</a><br />
            	<?php
					if($_COOKIE["user_name"] == "randall")
					{
						echo '<a href="/admin/delete/' . $pic . '">Remove Image</a><br />';
						
					}
				?>
            </div>
        </div>

    </div>
	
    <div id="content">
    	<div id="page_title">
        	Viewing post <?php echo $pic; ?>
        </div>
        <div id="alert">
    		
	    </div>
        <div class="spacer"></div>
        
        <div id="note-holder" style="display:none;">
        	<textarea id="note_text"></textarea><br />
            <input type="button" value="Save" id="note_save" /><input type="button" value="Cancel" id="note_cancel" />
            <input type="hidden" id="note_id" value="new" />
            <input type="hidden" id="note_new" value="true" />
            <input type="hidden" id="note_image_id" value="<?php echo $pic; ?>" />
            <input type="hidden" id="note_user_id" value="<?php echo $_COOKIE['user_id'] + 0; ?>" />
        </div>
        
        <div id="image_holder">

    		<?php
				$sql_notes = "SELECT * FROM `notes` WHERE `image_id` = " . $pic;
				$get_notes = mysql_query($sql_notes);
				$pic_note_count = mysql_num_rows($get_notes);
				while($run_notes = mysql_fetch_assoc($get_notes))
				{
					echo '<div id="note_' . $run_notes['id'] . '" class="image_note" style="position: absolute; left: ' . $run_notes['x'] . 'px; top: ' . $run_notes['y'] . 'px; width: ' . $run_notes['width'] . 'px; height: ' . $run_notes['height'] . 'px;cursor: default;" onmouseover="this.getElement(\'.tip\').show();" onmouseout="this.getElement(\'.tip\').hide();">
						<div id="drag_' . $run_notes['id'] . '" class="drag">
							
						</div>
						<div class="tip_space"></div>
						<div id="tip_' . $run_notes['id'] . '" class="tip" style="display: none;cursor: default;">
							' . nl2br(stripslashes($run_notes['note'])) . '
						</div>
					</div>';
				}
				echo '<img id="main_image" src="http://img.dasaku.net/images/' . $run['hash'] . '.' . $run['ext'] . '" alt="" />';
				
			?>

        </div>
        
		<div id="main_info">
        	
            <div>
            	<span>
                    <strong>Uploader:</strong> <?php echo $run_user['name']; ?>
                </span>
                <span>
                    <strong>Posted:</strong> <?php echo fuzzy_time($run['posted']); ?>
                </span>
                <span>
	                <strong>Original Name:</strong> <?php echo $run['filename'] ?>
				</span>
                <span>
                    <strong>Source:</strong> <?php echo $source; ?>
                </span>
                <span>
                    <strong>Group:</strong> <?php echo $group; ?>
                </span>
			</div>
            
            <div>
                <span>
                    <a id="editclick">Edit</a>
                </span>
                <span>
                    <a>History</a>
                </span>
			</div>
            
        </div>
            
		<div id="edit">
        	<form id="tagform" action="/save" method="post">
            
        	<div>
                <span class="edit_title">
                    Source
                </span>
                <span class="edit_form">
                    <input type="text" name="source_field" id="source_field" value="<?php echo $run['source']; ?>" style="width:350px;" class="validate['url']" />
                </span>
            </div>
            
            <div>
                <span class="edit_title">
                    Group
                </span>
                <span class="edit_form" style="position:relative">
                	
                    <select name="group_field" id="group_field" class="combo_box">
                    	<option value="None">None</option>
                    	<?php
							$sql_group = "SELECT id, group_name FROM `groups`";
							$get_group = mysql_query($sql_group);
							
							while($run_group = mysql_fetch_assoc($get_group))
							{
								$select = "";
								if($group_id == $run_group['id'])
								{
									$select = ' selected="selected"';
								}
								if($run_group["group_name"] != "")
								{
						?>
                        	
								<option value="<?php echo $run_group["group_name"]; ?>"<?php echo $select ?>><?php echo $run_group["group_name"]; ?></option>
                            
                        <?php
								}
							}
						?>
                    </select>
                </span>
            </div>
            
            <div>
                <span class="edit_title">
                    Rating
                </span>
                <span class="edit_form">
	                <label class="radio">
                        <input id="rating_unrated" type="radio" value="0" name="rating"<?php echo $unrated; ?> />
                        Unrated
                    </label>
                    <label class="radio">
                        <input id="rating_explicit" type="radio" value="1" name="rating"<?php echo $explicit; ?> />
                        Explicit
                    </label>
                    <label class="radio">
                        <input id="rating_questionable" type="radio" value="2" name="rating"<?php echo $questionable; ?> />
                        Questionable
                   </label>
                   <label class="radio">
                        <input id="rating_safe" type="radio" value="3" name="rating"<?php echo $safe; ?> />
                        Safe
                   </label>
				</span>
			</div>
            
            <div>
                <span class="edit_title">
                    Tags
                </span>
                <span class="edit_form">
                    <input type="text" name="tag_field" id="img_tags" value="<?php echo $form_tags; ?>" />
                    <div id="taglist">
                    	<div class="default">
                        	Type for delicious tag search. Need a new tag? Type it and hit space.
						</div>
                    	<?php
								
								echo "<ul>";
								if(!$form_tags)
								{
									echo "<li>tagme</li>";	
								}
								else
								{
									$form_tag_loop = explode(" ", $form_tags);
									foreach($form_tag_loop as $form_tag)
									{
										echo "<li>" . $form_tag . "</li>";	
									}
								}
								echo "</ul>";
						?>
                    </div>
                </span>
            </div>
            
            <div>
                <span class="edit_title">
                    &nbsp;
                </span>
                <span class="edit_form">
                	<input type="hidden" name="picture_id" value="<?php echo $pic; ?>" />
                    <input type="hidden" name="old_tags" value="<?php echo $form_tags; ?>" />
                    <input type="submit" name="submit" value="Save Changes" />
                </span>
            </div>
            
            </form>
			
        </div>
        
        <div id="comments">
        	<ol class="comment">
            <?php
					$sql_block = "SELECT c.id, u.name, c.posted, c.comment FROM `comments` c LEFT OUTER JOIN `users` u ON c.owner_id = u.id WHERE c.image_id = " . $pic . " ORDER BY c.id";
					$get_block = mysql_query($sql_block);
					while($run_block = mysql_fetch_assoc($get_block))
					{
			?>
                        	<li id="comment-<?php echo $run_block['id'] ?>">
								<span class="info">
									<span class="poster">
										<?php echo $run_block['name'] ?>
									</span>
									<span class="time">
										<abbr class="time" title="<?php echo date('D M j h:m:s', strtotime($run_block['posted'])); ?>"><?php echo fuzzy_time($run_block['posted']) ?></abbr>
									</span>
								</span>
					
								<div class="content">
									<p>
										<?php echo nl2br($run_block['comment']) ?>
									</p>
								</div>
								<div class="actions">
									<span class="self">
									</span>
									<span class="pointquote">
									</span>
								</div>
								</li>
			<?php
					}
			?>
            	
            </ol>
        </div>
        
        <div id="response">
        	
           <form id="comment_form" action="/comment" method="post">
    
           		<div>
    	            <span class="edit_title">
            	        Comment
        	        </span>
                	<span class="edit_form">
						<textarea name="comment" id="comment_box" value="<?php echo $run['source']; ?>" class="validate['required']"></textarea>
	                </span>
	            </div>
                
                <div>
	                <span class="edit_title">
    	                &nbsp;
        	        </span>
            	    <span class="edit_form">
                		<input type="hidden" name="picture_id" value="<?php echo $pic; ?>" />
                    	<input type="hidden" name="user_id" value="<?php echo $_COOKIE["user_id"]; ?>" />
	                    <input type="submit" name="submit" value="Post Comment" />
    	            </span>
        	    </div>
    
           </form>
            
        </div>
        
    </div>    
    
</div>

<script type="text/javascript">
img = $('main_image');
holder = $('image_holder');
if(img) 
{
 img.onclick = function() {scale(img);};
 scale(img);
}

<?php
	if($pic_note_count > 0)
	{
			echo "scale($('main_image'), 1);";
	}
?>

</script>
<?php
	require_once("footer.php");
?>
