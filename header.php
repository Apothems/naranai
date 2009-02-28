<?php
	require_once("lib/functions.php");
	
	
	$post_menu =	array
					(
						'List' 	 		=> '/post/list',
						'Favourites'	=> '#/post/favourites',
						'Upload'		=> '/post/upload',
						'Help'			=> '#/help'
					);
	$tags_menu =	array
					(
					 	'List'	 	 	=> '/tags/list',
						'Normal' 	 	=> '/tags/list/normal',
						'Series' 	 	=> '/tags/list/series',
						'Characters'	=> '/tags/list/character',
						'Artists'		=> '/tags/list/artist',
						'Add Tag'		=> '/tags/add'
					);
	$groups_menu =	array
					(
						'List' 	 		=> '/group/list',
						'Manage Groups'	=> '#/group/manage'					
					);
	$account_menu =	array
					(
						'Manage Account'	=> '/account/manage'
					);
					
	$search_box = '<div id="search">
						<div class="block_title">
							Search
						</div>
						<div class="block_content">
							<form method="post" action="/search.php">
							<input type="text" name="q" id="searchbox" /><br />
							<input type="submit" name="submit" value="Search" />
							</form>
						</div>
					</div>';
					
	switch($page_type)
	{
		case "post":
			$menu = $post_menu;
			$post_active = ' class="active"';
			break;
		case "tags":
			$menu = $tags_menu;
			$tags_active = ' class="active"';
			break;
		case "account":
			$menu = $account_menu;
			$account_active = ' class="active"';
			break;
		case "groups":
			$menu = $groups_menu;
			$groups_active = ' class="active"';
			break;
		default:
			$menu = $post_menu;
			$post = ' class="active"';
			break;
	}

	if($page_title == "")
	{
		$page_title = "img.dasaku";	
	}

include_once('tag_search.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>

<script src="/lib/mootools.js" type="text/javascript"></script>
<script src="/lib/mootoolsmore.js" type="text/javascript"></script>
<script type="text/javascript" src="/lib/observer.js"></script>
<script type="text/javascript" src="/lib/autocompleter.js"></script>
<script type="text/javascript" src="/lib/autocompleter.local.js"></script>
<script type="text/javascript">

	window.addEvent('domready', function() {
			tags = <?php echo $tag_search ?>;
			new Autocompleter.Local('searchbox', tags, {
												'minLength': 1, // We need at least 1 character
												'selectMode': 'type-ahead', // Instant completion
												'separator': ' ', // NOT DEFAULT NO MORE BITCHES.
												'multiple': true // Tag support, by default comma separated
											});	
			
			 
	});
						
</script>

<style type="text/css">
	@import url('/styles/style.css');
	@import url('/styles/autocompleter.css');
</style>
<!--[if lt IE 8]><style type="text/css">
.list_image {
    display: inline;
}
</style><![endif]-->

<?php echo $head; ?>

</head>

<body>

<div id="header">
	
    <div id="site_name">
    	img.dasaku
    </div>

    <div id="main_menu">
    	<span<?php echo $post_active; ?>>
        	<a href="/post/list">
            	Posts
			</a>
        </span>
        <span<?php echo $tags_active; ?>>
        	<a href="/tags/list">
            	Tags
			</a>
        </span>
        <span<?php echo $groups_active; ?>>
        	<a href="/group/list">
            	Groups
			</a>
        </span>
        <span<?php echo $account_active; ?>>
        	<a href="#/account">
            	Account
			</a>
        </span>
    </div>

    <div id="sub_menu">
    	<div class="left">
    	<?php
			foreach($menu as $name => $link)
			{
				echo '	<span>
				        	<a href="' . $link . '">
            					' . $name . '
							</a>
				        </span>';	
			}
			
		?>
        </div>
        
		<div id="log_menu" class="right">
            <?php
			if(isset($_COOKIE["user_name"]))
			{
			?>
            <span>
            	<span>
	            	Hello, <?php echo $_COOKIE["user_name"]; ?>.
                </span>
            </span>
			<span>
                <a href="/logout">
                    Logout
                </a>
            </span>
            <?php
			}
			else
			{
			?>
            <span id="login">
                <a href="/login">
                    Login
                </a>
            </span>
            <span id="register">
                <a href="/register">
                    Register
                </a>
            </span>
            <?php
			}
			?>
        </div>
        
        <span class="floatfix">
        </span>
        
    </div>
    

</div>