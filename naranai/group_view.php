<?php
	require_once('hibbity/dbinfo.php');
	require_once(SITE_DIR . '/lib/functions.php');
	
	$group      = isset($_GET['group']) ? abs($_GET["group"]) : '';
	if( empty($group) ) {
		header("Location: " . BASE_URL . "/group/list");
		exit();
	}

	$page_type  = "groups";
	$pagenum    = 0;
	$pics       = 20;
	$search_tag = "";
	$title      = "all posts";
	$pagenum    = isset($_GET["pagenum"]) ? abs($_GET["pagenum"] - 1) : 0;
	$limit      = $pics * $pagenum;
	$id['id']   = array();
	$id['hash'] = array();
	$tags       = array();
	$counts     = array();
	$types      = array();
	
	$sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.hash, group_concat(t.tag separator ' ') AS tags, group_concat(t.count separator ' ') AS counts, group_concat(t.type separator ' ') AS types FROM `images` i LEFT OUTER JOIN `image_tags` s ON  i.id = s.image_id LEFT OUTER JOIN `tags` t ON s.tag_id = t.id LEFT OUTER JOIN `image_groups` g ON i.id = g.image_id  WHERE group_id = " . $group . " GROUP BY i.id ORDER BY i.id DESC LIMIT " . $limit . ", " . $pics;
	$get = mysql_query($sql);
	
	while( $run = mysql_fetch_assoc($get) )
	{
		$id['id'][]   = $run['id'];
		$id['hash'][] = $run['hash'];
		$tags[]       = $run['tags'];
		$counts[]     = $run['counts'];
		$types[]      = $run['types'];
	}

	$id['tags'] = $tags;
	$sql        = "";

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
	
	$sql   = "SELECT group_name FROM groups WHERE id = " . $group . " LIMIT 1";
	$title = mysql_result(mysql_query($sql), 0);
	$page_title = "Viewing Group: " . $title . " - " . SITE_NAME;

	$pages = ceil(mysql_found_rows() / $pics);

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
					$sql = "SELECT REPLACE(tag, '_', ' ') as tag, count, type FROM tags ORDER BY count DESC LIMIT 15";
					$get = mysql_query($sql);
					while($run = mysql_fetch_assoc($get))
					{
						echo '<a href="', BASE_URL , '/post/list/' , $run['tag'] , '" class="' , $run['type'] , '">' , $run['tag'] , '</a> ' , $run['count'] , '<br />';
					}
				?>
            </div>
        </div>
        
        <div id="tag_list">
        	<div class="block_title">
            	Group Tags
            </div>
            <div class="block_content">
            	<?php
					foreach($tags as $tag => $count)
					{
						$stags = explode(' ', $tag);
						$count[0] = explode(' ', $count[0]);
						$count[1] = explode(' ', $count[1]);

						foreach($stags as $i => $s) {
							echo '<a href="', BASE_URL , '/post/list/' . $s . '" class="' . $count[1][$i] . '">' , str_replace('_', ' ', $s) , '</a> ' . $count[0][$i] . '<br />';
						}
					}
				?>
            </div>
        </div>
        
    </div>
	
    <div id="content">
    	<div id="page_title">
        	Viewing Group: <?php echo $title ?>
        </div>
        <div id="alert" style="display:block;">
    		Coding in progress. This site isn't running danbooru or shimmie2. Just saying, it's a bit incomplete.
	    </div>
        <div class="spacer"></div>
    		<?php
				$id['tags'] = str_replace('_', ' ', $id['tags']);
				$size       = sizeof($id['id']);
				for($i = 0; $i < $size; ++$i)
				{	
					$imgtags = $id['tags'][$i];
					$class = "";
					if( ereg('tagme', $imgtags) ) 
					{
						$class = ' class="tagme"';
					}
					echo '
				<span class="list_image">
					<a href="', BASE_URL ,'/post/view/' , $id['id'][$i] , '">
						<img src="' , BASE_URL , '/thumbs/' , $id['hash'][$i] , '.jpg" alt="' , $imgtags , '" title="' , str_replace('_', ' ', $imgtags) , '"' , $class , ' />
					</a>
				</span>';
				}
			?>
		
        <div id="pages">
			<?php
				$search_tag = '';
				if( isset($_GET["q"]) ) $search_tag = '/' . $_GET["q"];

				if($pages > 1)
				{
					if( $pagenum )
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