<?php 
$xml=simplexml_load_file($_SERVER['DOCUMENT_ROOT']."/app/system/config/sitevars.xml") or die("Error: Cannot create object");
define('SESSION_PREFIX',$xml->sessionprefix);
?>