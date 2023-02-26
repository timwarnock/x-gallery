<?
################################################################################
## photos/index.php
##   by Tim Warnock (c) 2006
##
##   Easy Photo Gallery - OLD OLD OLD
##
################################################################################

## GLOBALS
$album = $_REQUEST["album"];
$show = $_REQUEST["show"];



## FUNCTIONS

################################################################################
## ls(dir,pattern) return file list in "dir" folder matching "pattern"
## ls("path","module.php?") search into "path" folder for module.php3, module.php4, ...
## ls("images/","*.jpg") search into "images" folder for JPG images
################################################################################
function ls($__dir="./",$__pattern="*.*") {
  settype($__dir,"string");
  settype($__pattern,"string");

  $__ls=array();
  $__regexp=preg_quote($__pattern,"/");
  $__regexp=preg_replace("/[\\x5C][\x2A]/",".*",$__regexp);
  $__regexp=preg_replace("/[\\x5C][\x3F]/",".", $__regexp);

  if(is_dir($__dir))
    if(($__dir_h=@opendir($__dir))!==FALSE)
    {
      while(($__file=readdir($__dir_h))!==FALSE)
      if(preg_match("/^".$__regexp."$/",$__file))
        array_push($__ls,$__file);

      closedir($__dir_h);
      sort($__ls,SORT_STRING);
    }

  return $__ls;
}







################################################################################
## draw random preview image
################################################################################
if ( preg_match("/([^\/]+)\.jpg/i", $_SERVER["PATH_INFO"], $match) ) {
  $directory = $match[1];
  $image_array = ls("$directory/", "small_img_*.*");
  $previewImage = $image_array[ rand(0, count($image_array)-1 ) ];
  if (is_readable( "$directory/$previewImage" )) {
    $filesize = filesize( "$directory/$previewImage" );
    header("Content-type: image/jpg");
    header("Content-length: $filesize" );
    $image = file_get_contents( "$directory/$previewImage" );
    print $image;
  }
  exit;
}

















## derive real_image
if (isset($_REQUEST["album"]) && isset($_REQUEST["show"]) ) {
  if (preg_match("/^img_([\w\d\._\-]+)$/", $_REQUEST["show"], $match)) {
     $real_image = $match[1];
  }
  if (! is_readable( "$album/$real_image" ) ) {
    $real_image = $_REQUEST["show"];
  }
}
$img_icon = "<img src=\"/photos/jpg.gif\" alt=\"download\" title=\"download\" id=\"img_icon\" />";
$dltxt = "<span class=\"em\">(download)</span>";


################################################################################
## Draw single image FULL
################################################################################
  if (isset($_REQUEST["album"]) && isset($_REQUEST["show"]) && isset($_REQUEST["redirect"])) {
    header("Location: http://$_SERVER[HTTP_HOST]/photos/$album$real_image");
    exit;



################################################################################
## Draw single image FULL
################################################################################
  } elseif (isset($_REQUEST["album"]) && isset($_REQUEST["show"]) && isset($_REQUEST["full"])) {
     $index_file = file_get_contents("$album/INDEX");
      if (preg_match("/^date: (.*)$/m", $index_file, $match)) {
        $date = $match[1];
      }
      if (preg_match("/^subject: (.*)$/m", $index_file, $match)) {
        $subject = $match[1];
      }
      if (preg_match("/\n\n(.*)$/ms", $index_file, $match)) {
        $body = $match[1];
      }
      header("Content-Type: text/xml");
      print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    print <<< ENDHTML
      <gallery>
       <base>http://$_SERVER[HTTP_HOST]/photos/</base>
       <album>$album</album>
       <subject>$subject</subject>
       <date>$date</date>
       <body>$body</body>
       <image>$real_image</image>
       <image_icon>$img_icon</image_icon>
      </gallery>
ENDHTML;



################################################################################
## Draw single image PREVIEW
################################################################################
   } elseif (isset($_REQUEST["album"]) && isset($_REQUEST["show"])) {
     $image_array = ls("$album", "img_*.*");
     $num_images = count($image_array);
     $current_key = array_search($show, $image_array);
     $current_key += $num_images;
     $prev = ($current_key-1) % $num_images;
     $next = ($current_key+1) % $num_images;
     $big_img_target = "<img src=$album/$show border=0 id=\"main_image\" />";
     $index_file = file_get_contents("$album/INDEX");
      if (preg_match("/^date: (.*)$/m", $index_file, $match)) {
        $date = $match[1];
      }
      if (preg_match("/^subject: (.*)$/m", $index_file, $match)) {
        $subject = $match[1];
      }
      if (preg_match("/\n\n(.*)$/ms", $index_file, $match)) {
        $body = $match[1];
      }
      header("Content-Type: text/xml");
      print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    print <<< ENDHTML
      <gallery>
       <base>http://$_SERVER[HTTP_HOST]/photos/</base>
       <album>$album</album>
       <subject>$subject</subject>
       <date>$date</date>
       <body>$body</body>
       <image>$real_image</image>
       <image_icon>$img_icon</image_icon>
       <image_next>$image_array[$next]</image_next>
       <image_prev>$image_array[$prev]</image_prev>
      </gallery>
ENDHTML;



################################################################################
## Draw album gallery view
################################################################################
   } elseif (isset($_REQUEST["album"])) {
     $image_array = ls("$album", "img_*.*");
     $num_images = count($image_array);
     $index_file = file_get_contents("$album/INDEX");
      if (preg_match("/^date: (.*)$/m", $index_file, $match)) {
        $date = $match[1];
      }
      if (preg_match("/^subject: (.*)$/m", $index_file, $match)) {
        $subject = $match[1];
      }
      if (preg_match("/\n\n(.*)$/ms", $index_file, $match)) {
        $body = $match[1];
      }
      header("Content-Type: text/xml");
      print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    print <<< ENDHTML
      <gallery>
       <base>http://$_SERVER[HTTP_HOST]/photos/</base>
       <album>$album</album>
       <subject>$subject</subject>
       <date>$date</date>
       <body>$body</body>
ENDHTML;
     for ($i=0; $i<$num_images; $i++) {
       $image = $image_array[$i];
       print "<image>$image</image>";
     }
     print <<< ENDHTML
      </gallery>
ENDHTML;



################################################################################
## Draw Album Browser
################################################################################
  } else {
    $sort = $_REQUEST["sort"];
    if ( !isset($_REQUEST["sort"]) ) { $sort = "date"; }

    ##
    ## $ALBUMS[ loc ][ date ][ subject ] = body
    ##   get index
    $indeces = file_get_contents("INDEX_ALL");
    $directories = explode("---", $indeces);
    foreach ( $directories as $index_file ) {
      if (preg_match("/^ (.*\/)$/m", $index_file, $match)) {
        $dir = $match[1];
      }
      if (preg_match("/^date: (.*)$/m", $index_file, $match)) {
        $date = $match[1];
      }
      if (preg_match("/^subject: (.*)$/m", $index_file, $match)) {
        $subject = $match[1];
      }
      if (preg_match("/\n\n(.*)$/ms", $index_file, $match)) {
        $body = $match[1];
      }
      if (isset($dir) && isset($subject)) {
        $ALBUMS[ $dir ][ date ] = $date;
        $ALBUMS[ $dir ][ subject ] = $subject;
        $ALBUMS[ $dir ][ body ] = $body;
      }
    }
    ##
    ## SORT
    ##
    if ( $sort == "date" ) {
      function cmp($a, $b) {
        return strcmp($b[date], $a[date]);
      }
      uasort( $ALBUMS, "cmp" );
    } elseif ( $sort == "dater" ) {
      function cmp($a, $b) {
        return strcmp($a[date], $b[date]);
      }
      uasort( $ALBUMS, "cmp" );
    } elseif ( $sort == "titler" ) {
      function cmp($a, $b) {
        return strcmp($b[subject], $a[subject]);
      }
      uasort( $ALBUMS, "cmp" );
    } else {
      function cmp($a, $b) {
        return strcmp($a[subject], $b[subject]);
      }
      uasort( $ALBUMS, "cmp" );
    }

    ##
    ## DRAW
    ##
    header("Content-Type: text/xml");
    print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    print "<galleries>";
    print "<base>http://$_SERVER[HTTP_HOST]/photos/</base>";
    foreach ($ALBUMS as $loc => $this_album) {
      $date = $this_album[date];
      $title = $this_album[subject];
      $description = $this_album[body];
      print <<< ENDXML
        <gallery>
          <album>$loc</album>
          <subject>$title</subject>
          <date>$date</date>
          <description>$description</description>
        </gallery>
ENDXML;
    }
    print "</galleries>";

  }



?>
