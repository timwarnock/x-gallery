<?php
/*
 * api feed
 * 
 * /json or /xml (default)

TODO 
test-driven, unit tests
create a callback feature, including a gallery browser, e.g.,
<div id="mygallery">...</div>
<script src="http://avant.net/artwork/generate?galleryid=mygallery" />

which will produce well-formed HTML inside div#mygallery, 
which needs a nice CSS, e.g., document.createStyleSheet('style.css');

album browser
<div class="album_browse">
<div class="album" id="albumname"><img...><span class="album_desc">...</span></div>
</div>

image browser
<div class="image_browse">
<img...>
</div>

image view
cover, click to close

!!
square preview images
inspiration: IMAGE SEARCH (google, duckduckgo)
.. when clicking on an album, the grayed-out preview becomes a title ABOVE the image gallery
.. closing the image-gallery restores the view of the album browser
.. possibly in layers from albums -> images -> full-image
MOBILE first, and responsive




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
