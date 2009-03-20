<?php
	require_once('hibbity/dbinfo.php');
	require_once(SITE_DIR . '/lib/functions.php');
	
	if( !isset($_COOKIE["user_id"]) ) {
		$sql      = "SELECT COUNT(*) as uploads FROM `images` WHERE `posted` >= DATE_SUB(CURDATE(),INTERVAL 1 DAY) AND `owner_ip` = '" . $_SERVER['REMOTE_ADDR'] . "'";
		$uploaded = intval(mysql_result(mysql_query($sql), 0));
		$sep      = "&";
	} else {
		$uploaded = 0;
		$sep      = "?";
	}

	$page_type = "post";

	$head = array(
		'js' => array(
			'load' => array('/lib/Swiff.Uploader.js',
							'/lib/Fx.ProgressBar.js',
							'/lib/FancyUpload2.js',
							'/lib/moocombo.js'),
			'out' => "						window.addEvent('domready', function() {
	
							var swiffy = new FancyUpload2($('upload_status'), $('file_list'), {
								url: $('upload_form').action,
								fieldName: 'photoupload',
								path: '" . BASE_URL . "/lib/Swiff.Uploader.swf',
								limitSize: 2 * 1024 * 1024,
								limitFiles: 10,
								onLoad: function() {
									$('upload_status').removeClass('hide');
									$('form_fallback').destroy();
								},
								onAllComplete: function() {
									$('progress_holder').innerHTML = '<h1>All Uploads Complete.</h1><br /><a href=\"" . BASE_URL . "/post/upload/tag\" style=\"font-size:20px;font-weight:bold;\">Tag Uploads &raquo;</a>';
								},
								// The changed parts!
								debug: true, // enable logs, uses console.log
								target: 'file_browse' // the element for the overlay (Flash 10 only)
							});
						 		
							swiffy.options.typeFilter = {'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'};

							
							$('file_browse').addEvent('click', function() {	
								swiffy.browse();
								return false;
							});
						 
						 
							$('file_clear').addEvent('click', function() {
								swiffy.removeFile();
								return false;
							});
						 
							$('file_upload').addEvent('click', function() {
								swiffy.setOptions({url: $('upload_form').action + '/' + encodeURIComponent($('group_field').value)});
								swiffy.upload();
								return false;
							});
						 
							
						});"
		),

		'css' => array(
			'load' => array('/styles/formcheck.css',
							'/styles/upload.css'
			)
		)
	);

	$page_title = "Image Upload - " . SITE_NAME;

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
        	Image Upload
        </div>
        <div id="alert">
    		
	    </div>
        <div class="spacer"></div>
    	
        <?php
			if( $uploaded < 10 )
			{
		?>
	    <form class="registration" id="upload_form" action="<?php echo BASE_URL; ?>/uploader/<?php echo $_COOKIE["user_id"] ?>" method="post">
	    
        <h4>Posting Guidelines</h4>
        
        <ul>
        	<li>
            	Try not to upload shitting pictures.
            </li>
            <li>
            	Nothing illegal. That means no CP.
            </li>
            <li>
            	Please properly tag the stuff you upload. If you don't know anything about the picture, use the tagme tag.
            </li>
            <li>
            	After the images upload, please follow the link to tag images
            </li>
             <li>
            	You can upload 10 images at a time.
            </li>
            <li>
            	Anonymous users can only upload 10 files per day.
            </li>
            <li>
            	<strong>If you don't see the javascript uploader below</strong>, you are a tool who should die and you can only upload one image at a time.
            </li>
		</ul>
            
        <fieldset id="form_fallback">
            <legend>File Upload</legend>
            <p>
                Select a photo to upload.<br />
            </p>
            <label for="photoupload">
                Upload Photos:
                <input type="file" name="photoupload" id="reg_photoupload" />
                <input type="hidden" name="fail" value="true" />
            </label>
        </fieldset>
    
        <div id="upload_status" class="hide">
    
            <div id="progress_holder" class="left">
                <p>
                    <a href="#" id="file_browse">Browse Files</a> |
                    <a href="#" id="file_clear">Clear List</a> |
                    <a href="#" id="file_upload">Upload</a>
                </p>
                <div>
        
                    <strong class="overall-title">Overall progress</strong><br />
                    <img src="<?php echo BASE_URL; ?>/lib/assets/progress-bar/bar.gif" class="progress overall-progress" />
                </div>
                <div>
                    <strong class="current-title">File Progress</strong><br />
                    <img src="<?php echo BASE_URL; ?>/lib/assets/progress-bar/bar.gif" class="progress current-progress" />
                </div>
                <div>
                <strong class="overall-title">Add Uploads to the Following Group</strong><br />
                <span style="position:relative;">
                <select name="group_field" id="group_field" class="combo_box" style="position:relative;top:4px;left:4px;">
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
                <div class="current-text"></div>
    		</div>
			
            <div id="list_holder" class="left">
            	<ul id="file_list"></ul>
            </div>            
            
            <div class="clear"></div>
        </div>
        
        <div class="clear"></div>
            
        </form>
        <?php
			}
			else
			{
		?>
        	<div id="upload_status">
            	<h1>You have reached your upload quota for the day.</h1>
            </div>
        <?php
			}
		?>
    </div>    
    
</div>
<?php
	require_once("footer.php");
?>