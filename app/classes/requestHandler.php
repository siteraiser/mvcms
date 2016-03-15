<?php 
/*	Copyright © 2016 
	
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
    along with PHP-MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/
abstract class helpers{

	public function routes($rows,$path){

		foreach($rows as $key => $value){	
			$routes[]=['pattern' => $key,'controller' => $value];
		}
		unset($value);

		function match($pattern,$urlSegs){
			$i = 0;$match = false; $urlcount=count($urlSegs);
			foreach($pattern as $value){				
				if($value != '(:any)'){
					if($urlSegs[$i] == $value){
						$match = true;
					}else{
						return false;
					}
				}else{
					if(isset($urlSegs[$i])){
						$any[$i]=$urlSegs[$i];
					}
				}
				$i++;
				if($urlcount-- == 0){return false;}
			}
			if(!isset($any) && $match==true){
				$any=true;
			}
			return $any;
		}

		$i = 0;
		foreach($routes as $value){	
			$pattern=explode('/',$value['pattern']);
			$urlSegs = explode('/', $path,count($pattern));
			$results = match($pattern,$urlSegs);
			if($results){
				break;
			}
			$i++;
		}

		if($results !==true && !empty($results)){
			$k=1;
			foreach($results as $value){
				$trans[]=$value;
			}
		}
		
		$controller_method=[];
		if(isset($routes[$i]['controller'])){			
			$first = strpos($routes[$i]['controller'], '$');
			if($first==0){
			$first = strlen($routes[$i]['controller']);
			}
			$controller_method = substr($routes[$i]['controller'], 0, $first); 
			$controller_method = explode('/',$controller_method);
		}

		if(isset($trans)){
			
			$args = substr($routes[$i]['controller'],  $first, strlen($routes[$i]['controller']));
				$argsOrder = explode('/',$args);
				$i=0;
				foreach($argsOrder as $value){
					$combined[] = @$trans[substr($value,1) - 1]; 
				}
				//ksort($combined);
			$controller_method[2]=$combined;
		}
		return $controller_method;
	}
	public function get_include_contents($filename,$data) {
		foreach($data as $key => $value){
			$$key = $value;
		}
		if (is_file('app/views/'.$filename.'.php')) {
			ob_start();
	
			include 'app/views/'.$filename.'.php';
			return ob_get_clean();
		}
		echo $filename . ' is not a valid file!';
		return false;
	}	
	
	public function addView($view,$data) {	
		//sets output in controller's var
		if(!isset($this->output)){$this->output='';}
		$this->output.=$this->get_include_contents($view,$data);		
	}	
	public function loadDB() {

			//add pdo 
			include('app/system/config/db.inc.php');		
			if(!is_object($this->pdo) && $xml->database !=''){
				try {
					$this->pdo = new PDO('mysql:host=localhost;dbname='.$xml->database, $xml->username, $xml->password);    					
				} catch (PDOException $e) {
					print "Error!: " . $e->getMessage() . "<br/>";
					return'';
					//die();
				} 		
				
			}else{
				return'';
			}
		return $this->pdo;		
	}	
	public function loadModel($path) {
		$name =	end(explode('/',$path));
		$loadname = 'app/models/'.$path.'.php';
		try {

		if (!file_exists($loadname ))
		  throw new Exception ($loadname.' does not exist');
		else		
		  	include_once($loadname);
			$this->$name = new $name;
			$this->add_props($this->$name);		
		}
		catch(Exception $e) {    
		  echo "Message : " . $e->getMessage();
		  echo "Code : " . $e->getCode();
		}
		
	
	}	
	public function add_props($object){
	//controller or model
		$object->url_segments=$this->url_segments;	
		$object->doc_root=$this->doc_root;
		$object->path=$this->path;	
		$object->req_url=$this->req_url;
		$object->base_url=$this->base_url;
		$object->pdo=$this->pdo;//Remove to go back to model loading
	}
	public function doc_root() {
		return $this->doc_root;
	}
	public function url_segments() {//no need in controller or view or model
		return $this->url_segments;
	}

	
	/*****extras ***********/
	public function urlSlug ($string) {
		$string = utf8_encode($string);
		$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);   
		$string = preg_replace('/^a-z0-9\-\_]/i', '', $string);
		$string = str_replace(' ', '-', $string);
		$string = str_replace('(', '', $string);
		$string = str_replace(')', '', $string);
		$string = trim($string, '-');
		$string = strtolower($string);

		if (empty($string)) {
			return '';
		}

		return $string;
	}
	public function imgSlug ($string) {
		$string = str_replace('?', '', $string);
		$string = str_replace(' ', '_', $string);
		return $string;
	}
	
	public function selectedOption($fields,$selected=''){
		$allOut = "";	
		foreach($fields as $field){
			$out = '<option ';
				if(@$selected == $field){
					$out.="selected";
				}
			$out.=' value="'.$field.'">';
			$out.=$field . '</option>';  
			$allOut.= $out;			
		 }
		 return $allOut;
	}	
	
	public function getSelectFields($array,$col_name){
		foreach(@$array as $value){
			$ret[]=$value[$col_name];
		}		
		return $ret;
	}
	public function textAreaOut($value){
		$value = str_replace("&", "&amp;" ,$value);
		return $value = str_replace("<", "&lt;",$value);
	}
	public function removeSpaces($string){
	 	$search = array('~>\s*\n\s*<~','/(\s)+/s');	 
	$replace = array('><','\\1'); 
		
		return preg_replace($search, $replace, $string);
	}

	public function minimize($string){

	//	$string = preg_replace('@(?<![http|https]:)//.+?(?=\n|\r|$)@', '', $string);//Singleline JS comments. Maybe cause issues.
		$string = preg_replace('/<!--(?!<!)[^\[>].*?-->/s', '', $string);
		$string = preg_replace('!/\*.*?\*/!s', '', $string); // removes /* comments */
		$string = $this->removeSpaces($string);
		$order = array("\r\n", "\n", "\r");
		$replace = '';
		$string = str_replace($order, $replace, $string);
		return  $string;
	}

	function browserCache($cached){
		//css tricks intelligent browser caching system
		$lastModified=filemtime($cached);
		$etagFile = md5_file($cached);

		//$ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
		$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
		header("Etag: $etagFile");
		header('Cache-Control: public');

		//check if page has changed. If not, send 304 and exit
		if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified || $etagHeader == $etagFile)
		{
			   header("HTTP/1.1 304 Not Modified");
			   exit;
		}
	}

	public function checkCache(){
			
		$break = Explode('/', $this->path);
		$file = implode('-^-', $break);
		$this->cachefile = $this->doc_root.'cached/cached-'.$file;
		$cachetime = 31536000;//currently set to one year 86400 1 day in secs

		// Serve from the cache if it is younger than $cachetime
		if (file_exists($this->cachefile) && time() - $cachetime < filemtime($this->cachefile)) {
		   // echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
		   $this->browserCache($this->cachefile);
			include($this->cachefile);
			exit;
		}
	}
	public function cacheOutput(){
		if($this->cache == true){
			$cachetime = 31536000;//currently set to one year, 86400 is 1 day in secs
			$cached = fopen($this->cachefile, 'w');
			fwrite($cached, $this->output);
			fclose($cached);
			$this->browserCache($this->cachefile);
		}
	}
}

class requestHandler extends helpers{

	public $base_url;
	public $url_segments;
	public $req_url;
	public $path;	
	public $controllername;
	public $pdo;	
	public $doc_root;
	public $notfound=false;
	
	public function getContent(){

		if(!isset($_SESSION)) {
			session_start();
		}
		include_once('app/system/config/config.inc.php');		
		$this->base_url    = $xml->baseurl;
		$this->doc_root    = $_SERVER['DOCUMENT_ROOT'].'/';		
		$this->req_url     = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
		$this->output="";	//set default out html		
		$this->path = trim($this->req_url, '/');    // Trim leading slash(es)
		$this->cache=false;
		$this->minify=false;
		$this->cachefile='';
		$this->notfound=true;
		$method='';
		
		if($this->path == null || $this->path == 'index.php'){   // No path url_segments means home
			$this->path='home';
			$this->url_segments[0] = 'home';//home controller
		
		}else
		{
			$this->url_segments = explode('/', urldecode($this->path));			
			$this->path=parse_url($this->path, PHP_URL_PATH);	//echo path here		
		}
		
		/* check for cache here? */
		$this->checkCache();
		
		$this->controllername=$this->url_segments[0];	
		if(isset($this->url_segments[1])){
			$method=$this->url_segments[1];
		}
		//Routing		
		include_once('app/system/config/routing.php');	//can be managed in the admin system
	
		$controller_method=$this->routes($rows,$this->path);
		unset($rows);
		if(isset($controller_method[0])){
			$this->controllername = $controller_method[0];			
		}
		
		if(isset($controller_method[1])){
			$method = $controller_method[1];
		}
		$params =[];
		if(isset($controller_method[2])){	
			$params = $controller_method[2];
		}
	
		array_unshift($params, $this->path);
		///Routed --

			$controllerpath='app/controllers/'.$this->controllername.'.php';	
			$this->pdo=$this->loadDB();
			
		if($this->pdo !=''){
			include_once('app/controllers/dynamicPages.php');
			$controller = new dynamicPages();	
			$this->add_props($controller);
			call_user_func_array(array($controller,'index'), $params);
			
			$this->output=$controller->output;
			if(isset($controller->loadcontroller)){
				
				$cname=explode('/',$controller->loadcontroller);
				if(isset($cname[1])){
					$method = $cname[1];
				}
				$this->controllername=$cname[0];
				$controllerpath='app/controllers/'.$this->controllername.'.php';			
				unset($controller);
				$this->notfound=true;
			}else if($this->output != ''){			
				$this->output=$controller->output;
				$this->cache=$controller->cache;	
				$this->minify=$controller->minify;						
				$this->notfound=false;
			}else{
				unset($controller);
				$this->notfound=true;			
			}		
		}

		//Regular MVC
		if( $this->notfound ===true){
			$this->notfound=false;		

			if(file_exists($controllerpath)){
				include_once($controllerpath);
				$controller = new $this->controllername();	
				if($method !='' AND method_exists($controller, $method)){
					//call function set view data
					$this->add_props($controller);				
					call_user_func_array(array($controller,$method), $params);				
				} else if(method_exists($controller, 'index')){
					//call function set view data
					$this->add_props($controller);
					call_user_func_array(array($controller,'index'), $params);
				}		
			}
			if(	isset($controller->output) ){
				$this->output=$controller->output;
				$this->cache=$controller->cache;	
				$this->minify=$controller->minify;		
			}else{
				$this->notfound=true;
				unset($controller);
			}		
		}
		
		
		if( $this->notfound===true){$this->notFound();}
		
		if($this->minify == true){
			$this->output = $this->minimize($this->output);
		}
		$this->cacheOutput();		
	}	
	
	public function notFound(){
		header('HTTP/1.1 404 Not Found');
		print' Not Found! <a href="/">Home</a>';exit();
	}

}
