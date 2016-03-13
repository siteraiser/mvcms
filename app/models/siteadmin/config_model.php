<?php
/*
Copyright © 2016 
	
	This file is part of PHP-MVCMS.
    PHP-MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    PHP-MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	
class config_model extends requestHandler{

	public function	getSiteXML(){
		$xml = simplexml_load_file("app/system/config/sitevars.xml") or die("Error: Cannot create object");		
		return $xml;
	}
	public function	getDBXML(){
		$xml = simplexml_load_file("app/system/config/dbvars.xml") or die("Error: Cannot create object");		
		return $xml;
	}
	public function	setXML($xml,$file){
		$xml->asXml('app/system/config/'.$file.'.xml');
		// Save only the modified node
		//$root->a->asXml('only-a.xml');
	}	
	public function	updateSiteXML($xml){
		if(!empty($_POST['baseurl'])){
			$xml->baseurl=$_POST['baseurl'];
		}
		if(!empty($_POST['sessionprefix'])){
			$xml->sessionprefix=$_POST['sessionprefix'];
		}
		$this->setXML($xml,'sitevars');
		return $xml;
	}
	public function	updateDBXML($xml){
		if(!empty($_POST['username'])){
			$xml->username=$_POST['username'];
		}
		if(!empty($_POST['password'])){
			$xml->password=$_POST['password'];
		}
		if(!empty($_POST['database'])){
			$xml->database=$_POST['database'];
		}		
		$this->setXML($xml,'dbvars');
		return $xml;
	}		
}
