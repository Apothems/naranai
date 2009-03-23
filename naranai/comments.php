<?php
	require_once('hibbity/dbinfo.php');
	require_once(SITE_DIR . '/lib/functions.php');

	$page_type = 'comments';
	$title     = 'all comments';
	$pagenum   = isset($_GET['pagenum']) ? abs($_GET['pagenum'] - 1) : 0;
	$pics      = 10;
	$limit     = $pics * $pagenum;
	$comments  = array();
	$ids       = array();

	$sql = "SELECT image_id FROM `comments` GROUP BY image_id ORDER BY posted DESC LIMIT " . $limit . ", " . $pics;

	$get = mysql_query($sql);
	$sql = "";

	while( $run = mysql_fetch_assoc($get) ) $ids[] = $run['image_id'];
	$pages = ceil(mysql_found_rows() / $pics);

	$sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.hash, i.posted, i.rating, group_concat(t.tag separator ',') AS tags, group_concat(t.count separator ',') AS counts, group_concat(t.type separator ',') AS types, u.name FROM `images` i LEFT OUTER JOIN `image_tags` s ON i.id = s.image_id LEFT OUTER JOIN `tags` t ON s.tag_id = t.id LEFT OUTER JOIN `users` u ON i.owner_id = u.id WHERE i.id IN('" . implode("', '", $ids) . "') GROUP BY i.id ORDER BY i.id DESC";

	$get = mysql_query($sql);
	$sql = '';
	while( $run = mysql_fetch_assoc($get) ) {
		$comments[] = $run;
		$tags .= $run['tags'] . ',';
		$counts .= $run['counts'] . ',';
		$types .= $run['types'] . ',';
	}

	$tags = explode(",", $tags);
	$counts = explode(",", $counts);
	$types = explode(",", $types);
	for($i = 0; $i < count($counts); $i++)
	{
		$counts_proper[] .=  $counts[$i] . ':' . $types[$i];
	}
	$counts = "";
	$tags = array_combine($tags, $counts_proper);
	 
	array_pop($tags);
	arsort($tags, SORT_NUMERIC);
	$tags = array_slice($tags, 0, 15);
	array_pop($tags);


	$page_title = "Viewing " . $title . " - " . SITE_NAME;
	require_once("header.php");
?>

<div id="main">
	
    <div id="sidebar">
    
    	<?php
			echo $search_box;
		?>
        
        <div id="tag_list">
        	<div class="block_title">
            	Popular Tags
            </div>
            <div class="block_content">
            	<?php
					$sql = "SELECT tag, count, type FROM tags WHERE count > 0 ORDER BY count DESC LIMIT 15";
					$get = mysql_query($sql);
					while($run = mysql_fetch_assoc($get))
					{
						echo '<a href="', BASE_URL, '/post/list/' , $run['tag'] , '" class="' . $run['type'] . '">' , str_replace('_', ' ', $run['tag']) , '</a> ' , $run['count'] , '<br />';
					}
				?>
            </div>
        </div>
        
        <div id="tag_list">
        	<div class="block_title">
            	Current Page Tags
            </div>
            <div class="block_content">
            	<?php
					foreach($tags as $tag => $count)
					{
						$count = explode(":", $count);
						echo '<a href="'  . BASE_URL . '/post/list/' . $tag . '" class="' . $count[1] . '">' . $tag . '</a> ' . $count[0] . '<br />';
					}
				?>
            </div>
        </div>
        
    </div>
	
    <div id="content">
    	<div id="page_title">
        	Viewing <?php echo $title; ?>
        </div>
        <div id="alert" style="display:block;">
    		Comments Page up.
	    </div>
        <div class="spacer"></div>
    		<?php
				foreach($comments as $comment)
				{
					$imgtags = $comment['tags'];
			        $class = "";
					if(ereg('tagme', $imgtags)) 
					{
						$class = ' class="tagme"';
					}
					elseif($imgtags == "") 
					{
						$class = ' class="tagless"';
					}

					switch($comment['rating'])
					{
						case 0: default:
							$rating = 'Unrated';
							break;
						case 1:

							$rating = 'Explicit';
							break;
						case 2:
							$rating = 'Questionable';
							break;
						case 3:
							$rating = 'Safe';
							break;
					}

					$comment['tags'] = explode(',', $comment['tags']);
					$tags = array();
					foreach($comment['tags'] as $tag) {
						$tags[] = '<a href="' . BASE_URL . '/post/list/' . $ysh . '">' . $tag . '</a>';
					}

					$tags = '<span class="tags">' . implode('</span> <span class="tags">', $tags) . '</span>';
					echo '
			<div class="col">
				<a href="', BASE_URL , '/post/view/', $comment['id'] ,'"><img src="',  BASE_URL , '/thumbs/' , $comment['hash'] , '.jpg" alt="' , $imgtags , '" title="' , $imgtags , '"' , $class , ' /></a>
			</div>
			  <div class="col" id="comments-for-p', $comment['id'] ,'">
				<div class="header">
				  <div>
					<span class="info"><strong>Date</strong> ', fuzzy_time($comment['posted']) , '</span>
					<span class="info"><strong>User</strong> ', $comment['name'] ,'</a></span>
					<span class="info"><strong>Rating</strong> ', $rating , '</span>
				  </div>
				  <div>
					<strong>Tags</strong>
					
					  ', $tags , '
					
				  </div>
				</div>
			</div>
			<ol class="comment">';

					$sql_block = "SELECT c.id, u.name, c.posted, c.comment FROM `comments` c LEFT OUTER JOIN `users` u ON c.owner_id = u.id WHERE c.image_id = " . $comment['id'] . " ORDER BY c.id DESC LIMIT 5";
					$get_block = mysql_query($sql_block);
					while($run_block = mysql_fetch_assoc($get_block))
					{ ?>
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
				echo '</ol>';
				}
			?>
		
        <div id="pages">
			<?php

				if($pages > 1)
				{
					if( $pagenum )
						echo '<span><a href="', BASE_URL , '/comment/list/' , ($pagenum) , '">&laquo; Previous</a></span>';

					if($pagenum == 0) $this_page = ' class="current_page"';
					else $this_page = '';
					echo '<span><a href="', BASE_URL , '/comment/list/1"' , $this_page , '>1</a>';
					$this_page = '';
					
					if($pages < 10)
					{
						for($i = 2; $i <= $pages; $i++)
						{
							if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
							echo '<a href="', BASE_URL , '/comment/list/' . $i . '"' . $this_page . '>' . $i . '</a>';
						}
					}
					elseif($pagenum > ($pages - 10))
					{
						echo '...';
						for($i = ($pages - 9); $i < ($pages); $i++)
						{
							if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
							echo '<a href="', BASE_URL , '/comment/list/' . $i . '"' . $this_page . '>' . $i . '</a>';
						}   
					}
					elseif($pagenum > 7)
					{
						echo '...';
						for($i = ($pagenum - 3); $i <= ($pagenum + 5); $i++)
						{
							if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
							echo '<a href="', BASE_URL , '/comment/list/' . $i . '"' . $this_page . '>' . $i . '</a>';
						}
						echo '...';
					}
					else
					{
						for($i = 2; $i <= 9; $i++)
						{
							if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
							echo '<a href="', BASE_URL , '/comment/list/' . $i . '"' . $this_page . '>' . $i . '</a>';
						}
						echo '...';
					}
					
					if($pages >= 10)
					{
						if($pages == $pagenum+1) $this_page = ' class="current_page"';
						else $this_page = '';	
						echo '<a href="', BASE_URL , '/comment/list/' . $pages . '"' . $this_page . '>' . $pages . '</a>';
					}
					echo "</span>";
					
					if($pages != $pagenum+1)
					{
						echo '<span><a href="', BASE_URL , '/comment/list/' . ($pagenum + 2) . '">Next &raquo;</a></span>';
					}
				}
               ?>
        </div>
        
    </div>    
    
</div>
<?php
	require_once("footer.php");
?>