<?php



function fuzzy_time( $time ) {

  if ( ( $time = strtotime( $time ) ) == false ) {
    return 'an unknown time';
  }
  define( 'NOW',        time() );
  define( 'ONE_MINUTE', 60 );
  define( 'ONE_HOUR',   3600 );
  define( 'ONE_DAY',    86400 );
  define( 'ONE_WEEK',   ONE_DAY*7 );
  define( 'ONE_MONTH',  ONE_WEEK*4 );
  define( 'ONE_YEAR',   ONE_MONTH*12 );
 
  // sod = start of day :)
  $sod = mktime( 0, 0, 0, date( 'm', $time ), date( 'd', $time ), date( 'Y', $time ) );
  $sod_now = mktime( 0, 0, 0, date( 'm', NOW ), date( 'd', NOW ), date( 'Y', NOW ) );
 
  // used to convert numbers to strings
  $convert = array( 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven' );
 
  // today
  if ( $sod_now == $sod ) {
    if ( $time > NOW-(ONE_MINUTE*3) ) {
      return 'just a moment ago';
    } else if ( $time > NOW-(ONE_MINUTE*7) ) {
      return 'a few minutes ago';
    } else if ( $time > NOW-(ONE_HOUR) ) {
      return 'less than an hour ago';
    }
    return 'today at ' . date( 'g:ia', $time );
  }
 
  // yesterday
  if ( ($sod_now-$sod) <= ONE_DAY ) {
    if ( date( 'i', $time ) > (ONE_MINUTE+30) ) {
      $time += ONE_HOUR/2;
    }
    return 'yesterday around ' . date( 'ga', $time );
  }
 
  // within the last 5 days
  if ( ($sod_now-$sod) <= (ONE_DAY*5) ) {
    $str = date( 'l', $time );
    $hour = date( 'G', $time );
    if ( $hour < 12 ) {
      $str .= ' morning';
    } else if ( $hour < 17 ) {
      $str .= ' afternoon';
    } else if ( $hour < 20 ) {
      $str .= ' evening';
    } else {
      $str .= ' night';
    }
    return strtolower($str);
  }
 
  // number of weeks (between 1 and 3)...
  if ( ($sod_now-$sod) < (ONE_WEEK*3.5) ) {
    if ( ($sod_now-$sod) < (ONE_WEEK*1.5) ) {
      return 'about a week ago';
    } else if ( ($sod_now-$sod) < (ONE_DAY*2.5) ) {
      return 'about two weeks ago';
    } else {
      return 'about three weeks ago';
    }
  }
 
  // number of months (between 1 and 11)...
  if ( ($sod_now-$sod) < (ONE_MONTH*11.5) ) {
    for ( $i = (ONE_WEEK*3.5), $m=0; $i < ONE_YEAR; $i += ONE_MONTH, $m++ ) {
      if ( ($sod_now-$sod) <= $i ) {
        return 'about ' . $convert[$m] . ' month' . (($m>1)?'s':'') . ' ago';
      }
    }
  }
 
  // number of years...
  for ( $i = (ONE_MONTH*11.5), $y=0; $i < (ONE_YEAR*10); $i += ONE_YEAR, $y++ ) {
    if ( ($sod_now-$sod) <= $i ) {
      return 'about ' . $convert[$y] . ' year' . (($y>1)?'s':'') . ' ago';
    }
  }
 
  // more than ten years...
  return 'more than ten years ago';
}

function send_file($image, $type) {
		
		switch($image->ext)
		{
			case 'png':
				$mime = "image/png";
				break;
			case 'gif':
				$mime = "image/gif";
				break;
			case 'jpg':
				$mime = "image/jpeg";
				break;
			default:
				$mime = "image/jpeg";
				break;
		}
		
		if(!is_null($image)) {
			$hash = $image->hash;
			$ab = substr($hash, 0, 2);
			if($type == "thumb") {
				header('Content-type: image/jpeg');
				$file = '/home/digiwombat/iki/img/thumbs/' . $ab . '/' . $hash;
			}
			else {
				header('Content-type: ' . $mime);
				$file = '/home/digiwombat/iki/img/images/' . $ab . '/' . $hash;
			}
		
			echo file_get_contents($file);

			if(isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
				$if_modified_since = preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]);
			}
			else {
				$if_modified_since = "";
			}
			$gmdate_mod = gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT';

			// FIXME: should be $page->blah
			if($if_modified_since == $gmdate_mod) {
				header("HTTP/1.0 304 Not Modified");
			}
			else {
				header("Last-Modified: $gmdate_mod");
				header("Expires: Fri, 2 Sep 2101 12:42:42 GMT"); // War was beginning
			}
		}
		else {
			header("Location: /post/list");
			exit();
		}
	}

?>