<?
################################################################################
##
##   prototype JSON svc for photo gallery
##
################################################################################

# todo use cache/ (MAKE_GALLERY.sh clears cache)



/*
  non-preg method for retrieving images
 */
function startswith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}
function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen, true) === 0;
}
function getImages($dir) {
  $images=array();
  if(is_dir($dir))
    if(($__dir_h=@opendir($dir))!==FALSE)
    {
      while(($__file=readdir($__dir_h))!==FALSE)
      if ( endswith($__file,'JPG') && !startswith($__file,'small_') && !startswith($__file,'img_') )
        array_push($images,$__file);

      closedir($__dir_h);
    }
  return $images;
}



/* ********************************************************************************
 * list all images for a given album

   photos/json/dir
   -- list all images for that dir [1.JPG, 2.JPG, ... ]


******************************************************************************** */
   if (isset($_REQUEST[album])) {
     $album = rtrim($_REQUEST[album], '/');
     $image_array = getImages($album);
     //$image_array = ls("$album", "img_*.*");
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
     header("Content-Type: application/json");
     print json_encode([
       'base' => '/photos',
       'prefix' => 'img_',
       'thumb_prefix' => 'small_img_',
       'dir' => $album, 
       'title' => $subject,
       'date' => $date,
       'desc' => $body,
       'images' => $image_array ]);



/* ********************************************************************************
 * album browser (show different galleries)

   photos/json
   -- list all galleres with [dir, title, date, desc]

******************************************************************************** */
  } else {

    $ALBUMS = array();
    $indeces = file_get_contents("INDEX_ALL");
    $directories = explode("---", $indeces);
    foreach ( $directories as $index_file ) {
      if (preg_match("/^ (.*)\/$/m", $index_file, $match)) {
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
      $alb = array();
      if (isset($dir) && isset($subject)) {
        $alb[ 'dir' ] = $dir;
        $alb[ 'date' ] = $date;
        $alb[ 'title' ] = $subject;
        $alb[ 'desc' ] = $body;
        $ALBUMS[] = $alb;
      }
    }
    header("Content-Type: application/json");
    print json_encode(array_reverse($ALBUMS));
  }



?>
