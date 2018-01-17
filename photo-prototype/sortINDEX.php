#!/usr/bin/php
<?

# date dater title titler
$sort = 'dater';


##
##   get index
$ALBUMS = array();
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
## write file
##
$FOUT = "";
foreach ( $ALBUMS as $dir=>$alb ) {
$subject = $alb[subject];
$date   = $alb[date];
$body    = $alb[body];
$FOUT .= <<<ENDFOUT
--- $dir
subject: $subject
date: $date

$body
ENDFOUT;
}
file_put_contents("INDEX_ALL",$FOUT);


?>
