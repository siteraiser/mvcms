<?php 
class routes_model extends requestHandler{

	public function getRoutes(){
		return file_get_contents($this->doc_root.'app/system/config/routing.php');
	}
	public function putRoutes(){
		file_put_contents($this->doc_root.'app/system/config/routing.php',$_POST['routes'],LOCK_EX);
	}
}