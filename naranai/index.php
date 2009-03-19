<?php
	require_once('hibbity/dbinfo.php');

	# All the predefined shit goes here.
	$page_type  = "post";
	$pics       = 20;
	$title      = "all posts";
	$search_tag = "";
	$counts     = array();
	$tags       = array();
	$types      = array();
	$id['id']   = array();
	$id['tags'] = array();
	$id['hash'] = array();
	$search_tag = isset($_GET['q']) ? $_GET['q'] : '';
	$pagenum    = isset($_GET["pagenum"]) ? abs($_GET['pagenum'] - 1) : 0;
	$limit      = $pics * $pagenum;

	if( !empty($search_tag) )
	{
		$search_tags = explode(" ", $search_tag);
		sort($search_tags);

		# Remove unneeded, duplicate values and set $title to the correct values.
		$search_tags = array_unique($search_tags);
		$title       = htmlspecialchars(implode(' ', $search_tags), ENT_QUOTES);

		# Sanitize string.
		$search_tags = array_map('mysql_real_escape_string', $search_tags);

		# Imploding it all in one is 5x faster.
		$search_tag = "HAVING LOCATE('" . implode("', tags) > 0 AND LOCATE('", $search_tags) . "', tags) > 0 ";
	}

	# Setup page title.
	$page_title = "Viewing " . $title . " - " . SITE_NAME;

	# INNER JOIN checks for a match while LEFT JOIN (AKA LEFT OUTER JOIN) doesn't really.
	# Reference: http://www.w3schools.com/Sql/sql_join_left.asp
	$sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.hash, group_concat(t.tag separator ' ') as tag, group_concat(t.count separator ' ') as count, group_concat(t.type separator ' ') as type FROM `images` i LEFT OUTER JOIN `image_tags` s ON i.id = s.image_id LEFT OUTER JOIN `tags` t ON s.tag_id = t.id GROUP BY i.id " . $search_tag . "ORDER BY i.id DESC LIMIT " . $limit . ", " . $pics;
	$get = mysql_query($sql);
	$sql = "";

	while( $run = mysql_fetch_assoc($get) )
	{
		$id['id'][]   = $run['id'];
		$id['hash'][] = $run['hash'];
		$tags[]       = $run['tag'];
		$counts[]     = $run['count'];
		$types[]      = $run['type'];
	}
	$id['tags'] = $tags;

	if( mysql_num_rows($get) ) {
		$counts_proper = array();
		$size          = sizeof($counts);
		for($i = 0; $i < $size; ++$i) $counts_proper[] = array($counts[$i], $types[$i]);
		$counts = "";
		$types  = "";
		$tags = array_combine($tags, $counts_proper);
		arsort($tags, SORT_NUMERIC);
		$tags = array_slice($tags, 0, 15);
	}

	require_once("header.php");
	$pages = ceil(mysql_found_rows() / $pics);
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
						echo '<a href="', BASE_URL, '/post/list/' , $run['tag'] , '" class="' . $run['type'] . '">' , $run['tag'] , '</a> ' , $run['count'] , '<br />';
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
						$stags = explode(' ', $tag);
						$count[0] = explode(' ', $count[0]);
						$count[1] = explode(' ', $count[1]);

						foreach($stags as $i => $s) {
							echo '<a href="', BASE_URL , '/post/list/' . $s . '" class="' . $count[1][$i] . '">' . $s . '</a> ' . $count[0][$i] . '<br />';
						}
					}
				?>
            </div>
        </div>
        
    </div>
	
    <div id="content">
    	<div id="page_title">
        	Viewing <?php echo $title ?>
        </div>
        <div id="alert" style="display:block;">
    		Tag pages are up. Get jiggy with 'em. I'll get aliases and stuff working proper later.
	    </div>
        <div class="spacer"></div>
    		<?php
				$size = sizeof($id['id']);
				for($i = 0; $i < $size; ++$i)
				{
					$imgtags = $id['tags'][$i];
					$class = "";
					if (ereg('tagme', $imgtags) || $imgtags == "") 
					{
						$class = ' class="tagme"';
					}
					echo '
							<span class="list_image">
								<a href="', BASE_URL , '/post/view/' , $id['id'][$i] , '">
									<img src="',  BASE_URL , '/thumbs/' , $id['hash'][$i] , '.jpg" alt="' , $imgtags , '" title="' , $imgtags , '"' , $class , ' />
								</a>
							</span>';
				}
			?>
		
        <div id="pages">
        	
				<?php
					$search_tag = '';
					if( isset($_GET["q"]) ) $search_tag = '/' . $_GET["q"];

					if($pagenum > 0)
					{
						echo '<span><a href="', BASE_URL , '/post/list' , $search_tag , '/' , ($pagenum) , '">&laquo; Previous</a></span>';

						if($pagenum == 0) $this_page = ' class="current_page"';
						else $this_page = '';
						echo '<span><a href="', BASE_URL , '/post/list' , $search_tag , '/1"' , $this_page , '>1</a>';
						$this_page = '';
						
						if($pages < 10)
						{
							for($i = 2; $i <= $pages; $i++)
							{
								if($i == $pagenum+1) $this_page = ' class="current_page"';
								else $this_page = "";
								echo '<a href="', BASE_URL , '/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
							}
						}
						elseif($pagenum > ($pages - 10))
						{
							echo '...';
							for($i = ($pages - 9); $i < ($pages); $i++)
							{
								if($i == $pagenum+1) $this_page = ' class="current_page"';
								else $this_page = "";
								echo '<a href="', BASE_URL , '/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
							}   
						}
						elseif($pagenum > 7)
						{
							echo '...';
							for($i = ($pagenum - 3); $i <= ($pagenum + 5); $i++)
							{
								if($i == $pagenum+1) $this_page = ' class="current_page"';
								else $this_page = "";
								echo '<a href="', BASE_URL , '/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
							}
							echo '...';
						}
						else
						{
							for($i = 2; $i <= 9; $i++)
							{
								if($i == $pagenum+1) $this_page = ' class="current_page"';
								else $this_page = "";
								echo '<a href="', BASE_URL , '/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
							}
							echo '...';
						}
						
						if($pages >= 10)
						{
							if($pages == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = '';	
							echo '<a href="', BASE_URL , '/post/list' . $search_tag . '/' . $pages . '"' . $this_page . '>' . $pages . '</a>';
						}
						echo "</span>";
						
						if($pages != $pagenum+1)
						{
							echo '<span><a href="', BASE_URL , '/post/list' . $search_tag . '/' . ($pagenum + 2) . '">Next &raquo;</a></span>';
						}
					}
                ?>
            </span>
        </div>
        
    </div>    
    
</div>
<?php
	require_once("footer.php");
?>