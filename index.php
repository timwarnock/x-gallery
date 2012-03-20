<?php
/*
 * api feed
 * 
 * /json or /xml (default)
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
  file_put_contents('cache/META.json', json_encode($META)); 
}

if ($_REQUEST['view'] == 'json') {
	print( file_get_contents('cache/META.json'));
} else {
	print( file_get_contents('cache/META.xml'));
}
?>
