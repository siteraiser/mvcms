<?php /*
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
class menu_model extends requestHandler{

	public function getMenus(){
       	$query="SELECT id,name,dependencies FROM menus";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array());
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}		
	public function getMenuById($id){
       	$query="SELECT * FROM menus WHERE id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$id)); 							
		if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
			return $row;   
		}		
	}	
	public function addMenu(){		
		if($_POST['name']!=''){
	  	$query='INSERT INTO menus (
		name,
		data,
		dependencies
		)
		VALUES
		(?,?,?)';	
			
		$array=array(
			$_POST['name'],
			serialize(json_decode($_POST['data'])),
			$_POST['dependencies']
			);				
			
		$stmt=$this->pdo->prepare($query);
		$stmt->execute($array);			
		return $this->pdo->lastInsertId('id');
		}
	}

	
	public function updateMenu($id){
	
		$query='UPDATE menus SET 
		name=:name,			
		data=:data,	
		dependencies=:dependencies	
		WHERE id=:id';
			
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(
		':name'=>$_POST["name"],
		':data'=>serialize(json_decode($_POST["data"])),
		':dependencies'=>$_POST["dependencies"],
		':id'=>$id));	
		
		$this->handleCaching($id);		
	}	
	
	public function delMenu($id){
//--------delete-menu----
		$this->handleCaching($id);
		if ($this->pdo->exec("DELETE FROM menus WHERE id = $id")) {
			echo$id.' deleted';
		}		
	}
	
	
	public function handleCaching($id){
		//Get pages
	   	$query="SELECT * FROM menus 
		JOIN content ON (content.menu=menus.name) 
		JOIN pages ON FIND_IN_SET(content.id, pages.articleids) OR pages.articleids=content.id
		WHERE menus.id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$id)); 	
		
		$count=$stmt->rowCount($result);
		if ($count > 0) {
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($rows as $row){
				$pageids[] = $row['id'];
			}
		}
		$this->deleteCaches($pageids);
		//Now handle dependent app caches	
		$query="SELECT dependencies FROM menus
		WHERE menus.id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$id)); 	
		
		$count=$stmt->rowCount($result);
		if ($count > 0) {
			$row=$stmt->fetch(PDO::FETCH_ASSOC);			
			$apps = explode(',',$row['dependencies']);
		}
		$this->deleteAppCaches($apps);		
	}

		//Un-Caching
		
		//Dependent apps cache deletion. "blog" will delete: blog, blog/anycache etc.
	function deleteAppCaches($apps){	
		$dir = new DirectoryIterator($this->doc_root."/cached");
		foreach ($dir as $fileinfo) {
		if($fileinfo != '.' && $fileinfo != '..')
			$caches[]= pathinfo($fileinfo->getFilename(), PATHINFO_FILENAME);
		}
		
		foreach ($apps as $app) {
		//delete app base cache
			if (file_exists($this->doc_root.'cached/'.$app)){
				unlink($this->doc_root.'cached/'.$app);
			}	
			//delete nested caches
			$compare='cached-'.$app.'-^-';
			$del_length = strlen('cached-'.$app.'-^-');
			foreach($caches as $cachefile){
			
				$teststem=substr($cachefile, 0, $del_length); 
				if($compare == $teststem){
					if (file_exists($this->doc_root.'cached/'.$cachefile)){
						unlink($this->doc_root.'cached/'.$cachefile);
					}		
				}
			}
		}
	
	}
		
		
		//pages
	public function deleteCache($id){
		$stmt=$this->pdo->prepare("SELECT categoryname,page FROM pages WHERE id=?");
		$stmt->execute(array($id));
		$row = $stmt->fetch(PDO::FETCH_OBJ);	
		$path=($row->categoryname==''?'':$row->categoryname.'/').$row->page;
		$break = Explode('/', $path);
		$file = implode('-^-', $break);
		$cachefile = $this->doc_root.'cached/cached-'.$file;
		if (file_exists($cachefile)){
			unlink($cachefile);
		}		
	}	
	public function deleteCaches($ids){
		foreach($ids as $value){
			$this->deleteCache($value);
		}
	}
}






