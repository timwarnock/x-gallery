<?php
if (!file_exists('cache/META.xml')) {
  $RAW = `cat */META.xml | grep -v '<?xml'`;
  $META = new SimpleXMLElement('<projects>'.$RAW.'</projects>');
  $dom = dom_import_simplexml($META)->ownerDocument;
  $dom->formatOutput = true;
  file_put_contents("cache/META.xml", $dom->saveXML());
}

print( file_get_contents('cache/META.xml'));
?>
