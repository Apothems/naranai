<?php
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

	$post_active = $tags_active = $account_active = $groups_active = $post = '';
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
		$page_title = SITE_NAME;	
	}

include_once('tag_search.php');

// Load the JS
if( !isset($head['js']['load']) ) $head['js']['load'] = array();
array_unshift($head['js']['load'], '/lib/mootools.js',
								   '/lib/mootoolsmore.js',
								   '/lib/observer.js',
								   '/lib/autocompleter.js',
								   '/lib/autocompleter.local.js');

$head['js']['out'] =  "
	window.addEvent('domready', function() {
			tags = " . $tag_search . ";
			new Autocompleter.Local('searchbox', tags, {
												'minLength': 1, // We need at least 1 character
												'selectMode': 'type-ahead', // Instant completion
												'separator': ' ', // NOT DEFAULT NO MORE BITCHES.
												'multiple': true // Tag support, by default comma separated
											});	
			
			 
	});" . (isset($head['js']['out']) ? $head['js']['out'] : '' );

// Load the CSS
if( !isset($head['css']['load']) ) $head['css']['load'] = array();
array_unshift($head['css']['load'], '/styles/style.css', '/styles/autocompleter.css');
$head['css']['out']    =  '
<!--[if lt IE 8]>
.list_image {
    display: inline;
}
<![endif]-->' . (isset($head['css']['out']) ? $head['css']['out'] : '' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>
<?php
	$tab  = "\n";
	$tab2 = $tab . '	';

	# Start with the JS
	$data =& $head['js'];
	echo $tab . '<script src="' , BASE_URL , implode('" type="text/javascript"></script>' . $tab . '<script src="' . BASE_URL, $data['load']) , '" type="text/javascript"></script>';

	if( isset($data['var']) ) {
		echo $tab , '<script type="text/javascript">';
		foreach($data['var'] as $var => $value) {
			echo $tab2 , 'var ' , $var , ' = ' , is_numeric($value) ? $value : "'" . $value . "'" , ';';
		}
		echo $tab , '</script>';
	}

	echo $tab , '<script type="text/javascript">';
	echo $data['out'];
	echo $tab , '</script>';

	# Finally, do the CSS
	$data =& $head['css'];
	echo $tab, '<style type="text/css">';
	echo $tab2, "@import url('", BASE_URL , implode("');" . $tab2 . "@import url('" . BASE_URL, $data['load']) , "');";
	echo $tab, '</style>';

	echo $tab , '<style type="text/css">';
	echo $data['out'];
	echo $tab , '</style>';

	$head = null;
?>

</head>

<body>

<div id="header">
	
    <div id="site_name">
    	<?php echo SITE_NAME; ?>
    </div>

    <div id="main_menu">
    	<span<?php echo $post_active; ?>>
        	<a href="<?php echo BASE_URL; ?>/post/list">
            	Posts
			</a>
        </span>
        <span<?php echo $tags_active; ?>>
        	<a href="<?php echo BASE_URL; ?>/tags/list">
            	Tags
			</a>
        </span>
        <span<?php echo $groups_active; ?>>
        	<a href="<?php echo BASE_URL; ?>/group/list">
            	Groups
			</a>
        </span>
        <span<?php echo $account_active; ?>>
        	<a href="<?php echo BASE_URL; ?>#/account">
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
				        	<a href="', BASE_URL, $link , '">
            					' , $name , '
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
                <a href="<?php echo BASE_URL; ?>/logout">
                    Logout
                </a>
            </span>
            <?php
			}
			else
			{
			?>
            <span id="login">
                <a href="<?php echo BASE_URL; ?>/login">
                    Login
                </a>
            </span>
            <span id="register">
                <a href="<?php echo BASE_URL; ?>/register">
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