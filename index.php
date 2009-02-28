<?php

	require_once('hibbity/dbinfo.php');
	
	
	$page_type = "post";
	
	$pagenum = 0;
	$pics = 20;
	$search_tag = "";
	$title = "all posts";
	if($_GET["q"])
	{
		$search_tag = $_GET["q"];
		$title = $_GET["q"];
		
	}
	
	$page_title = "Viewing " . $title . " - img.dasaku";
	
	if($_GET["pagenum"]) $pagenum = $_GET["pagenum"] - 1;
	$limit = $pics * $pagenum;
	if($search_tag != "")
	{
		$search_tags = explode(" ", $search_tag);
		sort($search_tags);
		foreach($search_tags as $gah)
		{
			$string .= "LOCATE('" . $gah . "', tags) > 0 AND ";
		}
		$string = substr($string, 0, -4);
		$search_tag = "HAVING " . $string;
	}
	$sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.hash, group_concat(t.tag " . $tags_id . " separator ',') AS tags, group_concat(t.count separator ',') AS counts, group_concat(t.type separator ',') AS types FROM `images` i LEFT OUTER JOIN `image_tags` s ON  i.id = s.image_id LEFT OUTER JOIN `tags` t ON s.tag_id = t.id " . $tag_ids . "GROUP BY i.id " . $search_tag . "ORDER BY i.id DESC LIMIT " . $limit . ", " . $pics;
	$get = mysql_query($sql);
	
	while($run = mysql_fetch_assoc($get))
	{
		$id['id'][] .= $run['id'];
		$id['tags'][] .= $run['tags'];
		$id['hash'][] .= $run['hash'];
		$tags .= $run['tags'] . ',';
		$counts .= $run['counts'] . ',';
		$types .= $run['types'] . ',';
	}
	
	$sql = "";
	$tags = explode(",", $tags);
	$counts = explode(",", $counts);
	$types = explode(",", $types);
	for($i = 0; $i < count($counts); $i++)
	{
		$counts_proper[] .=	$counts[$i] . ':' . $types[$i];
	}
	$counts = "";
	$tags = array_combine($tags, $counts_proper);

	array_pop($tags);
	arsort($tags, SORT_NUMERIC);
	$tags = array_slice($tags, 0, 15);
	
	$total = mysql_fetch_row(mysql_query("SELECT FOUND_ROWS()"));
	$pages = ceil($total[0] / $pics);
	

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
					$sql = "SELECT tag, count, type FROM tags ORDER BY count DESC LIMIT 15";
					$get = mysql_query($sql);
					while($run = mysql_fetch_assoc($get))
					{
						echo '<a href="/post/list/' . $run['tag'] . '" class="' . $run['type'] . '">' . $run['tag'] . '</a> ' . $run['count'] . '<br />';
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
						echo '<a href="/post/list/' . $tag . '" class="' . $count[1] . '">' . $tag . '</a> ' . $count[0] . '<br />';
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
				for($i = 0; $i < count($id['id']); $i++)
				{	
					$imgtags = str_replace(",", " ", $id['tags'][$i]);
					$class = "";
					if (ereg('tagme', $imgtags) || $imgtags == "") 
					{
						$class = ' class="tagme"';
					}
					echo '
							<span class="list_image">
								<a href="/post/view/' . $id['id'][$i] . '">
									<img src="http://img.dasaku.net/thumbs/' . $id['hash'][$i] . '.jpg" alt="' . $imgtags . '" title="' . $imgtags . '"' . $class . ' />
								</a>
							</span>';
				}
			?>
		
        <div id="pages">
        	
				<?php
					$search_tag = '';
					if($_GET["q"])
					{
						$search_tag = '/' . $_GET["q"];
					}
                    
					if($pagenum > 0)
					{
						echo '<span><a href="/post/list' . $search_tag . '/' . ($pagenum) . '">&laquo; Previous</a></span>';
					}
					else
					{
						echo '<span>&laquo; Previous</span>';	
					}
					
					if($pagenum == 0) $this_page = ' class="current_page"';
					else $this_page = '';
					echo '<span><a href="/post/list' . $search_tag . '/1"' . $this_page . '>1</a>';
					$this_page = '';
                    
					if($pages < 10)
					{
						for($i = 2; $i <= $pages; $i++)
                        {
                            if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
                            echo '<a href="/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
                        }
					}
					elseif($pagenum > ($pages - 10))
                    {
                        echo '...';
                        for($i = ($pages - 9); $i < ($pages); $i++)
                        {
                            if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
                            echo '<a href="/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
                        }   
                    }
					elseif($pagenum > 7)
                    {
                        echo '...';
                        for($i = ($pagenum - 3); $i <= ($pagenum + 5); $i++)
                        {
                            if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
                            echo '<a href="/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
                        }
                        echo '...';
                    }
                    else
                    {
                        for($i = 2; $i <= 9; $i++)
                        {
                            if($i == $pagenum+1) $this_page = ' class="current_page"';
							else $this_page = "";
                            echo '<a href="/post/list' . $search_tag . '/' . $i . '"' . $this_page . '>' . $i . '</a>';
                        }
                        echo '...';
                    }
					
					if($pages >= 10)
					{
						if($pages == $pagenum+1) $this_page = ' class="current_page"';
						else $this_page = '';	
						echo '<a href="/post/list' . $search_tag . '/' . $pages . '"' . $this_page . '>' . $pages . '</a>';
					}
					echo "</span>";
					
					if($pages != $pagenum+1)
					{
						echo '<span><a href="/post/list' . $search_tag . '/' . ($pagenum + 2) . '">Next &raquo;</a></span>';
					}
					else
					{
						echo '<span>Next &raquo;</span>';
					}	
                ?>
            </span>
        </div>
        
    </div>    
    
</div>
<?php
	require_once("footer.php");
?>