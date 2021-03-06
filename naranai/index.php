<?php
	require_once('hibbity/dbinfo.php');
	require_once(SITE_DIR . '/lib/functions.php');

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
		$search_tags = array_unique($search_tags);
		$title       = htmlspecialchars(implode(' ', $search_tags), ENT_QUOTES);
		$search_tags = array_map('mysql_real_escape_string', $search_tags);
		$find_colors = "WHERE 1=1";
		$count = count($search_tags);
		for($i = 0; $i < $count; $i++)
		{
			if(iscolor($search_tags[$i]))
			{
				$find_colors .= " AND (i.primary_color = '" . $search_tags[$i] . "' OR i.secondary_color = '" . $search_tags[$i] . "' OR i.tertiary_color = '" . $search_tags[$i] . "')";
				unset($search_tags[$i]);
			}
		}
		if(!empty($search_tags))
		{
			$search_tag = "HAVING LOCATE('" . implode("', tags) > 0 AND LOCATE('", $search_tags) . "', tags) > 0 ";
		}
		else
		{
			$search_tag = "";
		}
	}

	$page_title = "Viewing " . $title . " - " . SITE_NAME;

	$sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.hash, group_concat(t.tag " . $tags_id . " separator ',') AS tags, group_concat(t.count separator ',') AS counts, group_concat(t.type separator ',') AS types FROM `images` i LEFT OUTER JOIN `image_tags` s ON i.id = s.image_id LEFT OUTER JOIN `tags` t ON s.tag_id = t.id " . $find_colors . " GROUP BY i.id " . $search_tag . "ORDER BY i.id DESC LIMIT " . $limit . ", " . $pics;
	$get = mysql_query($sql);
	$sql = "";

	while( $run = mysql_fetch_assoc($get) )
	{
		$id['id'][] .= $run['id'];
		$id['tags'][] .= $run['tags'];
		$id['hash'][] .= $run['hash'];
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
        	Viewing <?php echo $title ?>
        </div>
        <div id="alert" style="display:block;">
    		Tag pages are up. Get jiggy with 'em. I'll get aliases and stuff working proper later.
	    </div>
        <div class="spacer"></div>
    		<?php
				$size       = sizeof($id['id']);
				for($i = 0; $i < $size; ++$i)
				{
					$imgtags = str_replace(",", " ", $id['tags'][$i]);
			        $class = "";
					if(ereg('tagme', $imgtags)) 
					{
						$class = ' class="tagme"';
					}
					elseif($imgtags == "") 
					{
						$class = ' class="tagless"';
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
        </div>
        
    </div>    
    
</div>
<?php
	require_once("footer.php");
?>