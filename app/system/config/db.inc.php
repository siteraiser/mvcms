<?php
//determine if working on local or live server
$host = substr($_SERVER['HTTP_HOST'], 0, 5);
if (in_array($host, array('local', '127.0', '192.1'))){
$local = TRUE;
} else {
$local = FALSE;
}

//Determine location of files and the url of the site:
//Allow for development on different servers
if ($local) {//Always debug when running locally
//error_reporting(0);
} else {
//error_reporting(0);
}

$xml=simplexml_load_file($_SERVER['DOCUMENT_ROOT']."/app/system/config/dbvars.xml") or die("Error: Cannot create object");

?>
