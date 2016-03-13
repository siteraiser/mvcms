<?php /*
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
    along with PHP-MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	
class user_model extends requestHandler{
public function getAdmins(){
       	$query="SELECT * FROM users WHERE userType = 'admin'";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array());
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getUsers(){
       	$query="SELECT * FROM users";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array());
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}		
	public function getUserById($id){
       	$query="SELECT * FROM users WHERE id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$id)); 		
		
		$count=$stmt->rowCount($result);
		if ($count > 0) {
					
			if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
				return $row;   
			}
		}	
		
	}	
	
	public function addUser(){		
		if($_POST['username']!='' && $_POST['email']!='' &&  $_POST['email']!=''){
	  	$query='INSERT INTO users (
		userType,
		username,
		email,
		pass,
		status
		)
		VALUES
		(?,?,?,?,?)';	
			
		$array=array(
			$_POST['userType'],
			$_POST['username'],
			$_POST['email'],
			$_POST['pass'],
			$_POST['status']=isset($_POST['status']) ? 1 : 0
			);				
			
		$stmt=$this->pdo->prepare($query);
		$stmt->execute($array);			
		return $this->pdo->lastInsertId('id');
		}else{
			return 'Fill all required fields';
		}
	}

	
	public function updateUser($id){
	
		$query='UPDATE users SET 
		userType=:userType,			
		username=:username,
		email=:email,
		pass=:pass,
		status=:status		
		WHERE id=:id';
			
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(
		':userType'=>($_POST["userType"]==''?NULL:$_POST["userType"]),	
		':username'=>$_POST["username"],
		':email'=>$_POST["email"],
		':pass'=>$_POST["pass"],
		':status'=>isset($_POST['status']) ? 1 : 0,
		':id'=>$id));			
	}	
	public function updateSelected(){

		foreach($_POST["indexSelected"] as $key => $value){
					
			$query='UPDATE users SET 
			userType=:userType,			
			username=:username,
			email=:email,
			pass=:pass,
			status=:status
			
			WHERE id=:id';
			
			$stmt=$this->pdo->prepare($query);
			$stmt->execute(array(
			':userType'=>($_POST["userType"][$value]==''?NULL:$_POST["userType"][$value]),	
			':username'=>$_POST["username"][$value],
			':email'=>$_POST["email"][$value],
			':pass'=>$_POST["pass"][$value],
			':status'=>isset($_POST['status'][$value]) ? 1 : 0,
			':id'=>$value));			
		}		
	}
	public function getArticleIdsByUserId($user){
       	$query="SELECT id FROM content WHERE user = :user";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':user'=>$user)); 		
		while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
			$articleIds[] = $row["id"];		
		}
		
		return $articleIds;
	}

	public function delUser(){
//--------delete-user----
		$articleIds=array();
		foreach(@$_REQUEST['indexSelected'] as $key => $user_id){
			
			if ($this->pdo->exec("DELETE FROM users WHERE id = $user_id")) {
				echo'User with id: '.$user_id.' deleted';
			}
			$path = $this->doc_root.'userfiles/'.$user_id;
			$this->deleteDir($path);
			$this->deleteCachedLinks($user_id);//delete the cached links and sub pages
			
			$articleIds = array_merge($articleIds, $this->getArticleIdsByUserId($user_id));//ids to delete using content_model

			return $articleIds;
		}
	}

	public function deleteCachedLinks($user){
		$apps=[];
       	$query="SELECT link FROM content WHERE user = :user";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':user'=>$user)); 		
		while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
			$linksegs = explode('/',$row["link"]);
			if(!in_array($linksegs[0],$apps)){
				$apps[] = $linksegs[0];
			}
		}
		//var_dump($apps);
		$this->deleteAppCaches($apps);
	}
	
	
	
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
	function deleteDir($path){
		return !empty($path) && is_file($path) ?
			@unlink($path) :
			(array_reduce(glob($path.'/*'), function ($r, $i) { return $r && $this->deleteDir($i); }, TRUE)) && @rmdir($path);
	}
}	
