<?php
/*
Copyright © 2016 
	
	This file is part of MVCMS.

    MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	


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