<?php
/*
 * api feed
 * 
 * /json or /xml (default)

-- to do
test-driven, unit tests
create a callback feature, including a gallery browser, e.g.,
<div id="mygallery">...</div>
<script src="http://avant.net/artwork/generate?id=mygallery" />
-- see
-- https://stackoverflow.com/questions/8578617/inject-a-script-tag-with-remote-src-and-wait-for-it-to-execute

which will produce well-formed HTML inside div#mygallery, 
which needs a nice CSS, e.g., document.createStyleSheet('style.css');

first, let's make an example in x.html
...

 *
 */

if (!file_exists('cache/META.xml')) {

  ## create META.xml
  $RAW = `cat */META.xml | grep -v '<?xml'`;
  $META = new SimpleXMLElement('<projects>'.$RAW.'</projects>');
  $dom = dom_import_simplexml($META)->ownerDocument;
  $dom->formatOutput = true;
  file_put_contents("cache/META.xml", $dom->saveXML());
 
  ## create META.json
  $META = simplexml_load_file('cache/META.xml');
  file_put_contents('cache/raw.json', json_encode($META)); 
  ## create assoc array (from xml)
  $art = array();
  $json = json_decode( json_encode($META), true);
  foreach( $json['exhibit'] as $ex ) {
    $artwork = array();
    foreach( $ex['artwork'] as $a ) {
      $artwork[ $a['filename'] ] = $a;
    }
    $ex['artwork'] = $artwork;
    $art[ $ex['name'] ] = $ex;
  }
  file_put_contents('cache/META.json', json_encode($art)); 
}

if ($_REQUEST['view'] == 'json') {
  header('Content-Type: application/json');
  print( file_get_contents('cache/META.json'));
} else {
  print( file_get_contents('cache/META.xml'));
}
?>
