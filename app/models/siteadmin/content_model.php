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
    along with MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	
class content_model extends requestHandler{
	public $userid;
	//for User class
	public function init()
    {
       	$query="SELECT user FROM content WHERE id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$_GET['article'])); 		
		while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
			$this->userid = $row["user"];			
		}
    }
	function getCreatorId(){
		return $this->userid;
	}

	public function insert(){		
		$date = new DateTime('now');
		$timestamp = $date->format('Y-m-d H:i:s');
		$query='INSERT INTO content (
		articlename,
		published,
		menu,
		type,
		menutype,
		content,
		date,
		lastupdate
		)
		VALUES
		(?,?,?,?,?,?,?,?)';			
		
		$array=array(
			$_POST['articlename'],
			isset($_POST['published']) ? 1 : 0,
			$_POST['menu'],
			$_POST['type'],		
			$_POST['menutype'],	
			$_POST['content'],
			$timestamp,
			$timestamp
			);				
			
			$stmt=$this->pdo->prepare($query);
			$stmt->execute($array);			
			return $this->pdo->lastInsertId('id');
	}
	public function update(){			
	    $query="SELECT user FROM content WHERE lastupdate = :lastupdate";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':lastupdate'=>$_POST['lastupdate'])); 		
		if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
			$count = 1;
		}
		
		$query='UPDATE content SET 
		articlename=:articlename,
		published=:published,
		menu=:menu,
		type=:type,
		menutype=:menutype,
		content=:content,
		date=:date';
		if ($count == 0) {	
			$query.=", lastupdate=:lastupdate";
		}
		$query.=' WHERE id=:id';
		
		$array=array(
				':articlename'=>$_POST['articlename'],
				':published'=>isset($_POST['published']) ? 1 : 0,
				':menu'=>$_POST['menu'],
				':type'=>$_POST['type'],		
				':menutype'=>$_POST['menutype'],	
				':content'=>$_POST['content'],
				':date'=>$_POST['date']
			);				
			if($count == 0){
				$array[':lastupdate']=$_POST['lastupdate'];
			}
			$array[':id']=$_POST['id'];
			
			$stmt=$this->pdo->prepare($query);
			$stmt->execute($array);	
			
			//Handle Page Caching
			$this->deleteCacheByArticleId($_POST['id']);		
	}
		
	public function getAllById($table,$id){
       	$query="SELECT * FROM content WHERE id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$id)); 		
		if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
			$value = $row["content"];				
			$value = str_replace("&", "&amp;" ,$value);
	        $value = str_replace("<", "&lt;",$value);
			$row['content'] = $value;    				
		}
		
		return $row;
	}
	public function selectDistinct($table,$col){
		//not necesarily being used
		$stmt=$this->pdo->prepare("SELECT DISTINCT $col FROM $table");
		$stmt->execute(array());
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	return $rows;
	}	
	
	
	
	public function countAll($table){
	
		$stmt=$this->pdo->prepare("SELECT count(*) AS count FROM $table");
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['count'];
	}
	//------
	
	public function getPagesByArticleId($id){
		$sql="SELECT * FROM pages
		
		WHERE FIND_IN_SET(?, pages.articleids) OR pages.articleids=?";	
	
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute(array($id,$id));
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function delete($id){
		$message='';
			//Join on match or find in set
		$rows = $this->getPagesByArticleId($id);
		if(count($rows)!=0){
				$message='Article removed from the following pages:';
			foreach ($rows as $row){
			
				$assignedviewpositions=unserialize($row['positions']);			
				
				foreach($assignedviewpositions as $key => $position){
					if($position['id']==$id ){
						unset($assignedviewpositions[$key]);
					}
				}
				
				$assignedviewpositions=serialize($assignedviewpositions);

				$articleids=explode(',',$row['articleids']);
				foreach (array_keys($articleids, $id) as $key) {
					unset($articleids[$key]);
				}
				$articleids=implode(',',$articleids);	
	
				$this->update_page($row['id'],$articleids,$assignedviewpositions);
				//Delete this page's cache
				$this->deletePageCache($row['categoryname'],$row['page']);	
				
				$message.=' '.$row['page'];
				
			}
		}
		$message.=$this->delete_article($id);
		

		return $message;
	
	}	
	function delete_article($id){		
			if ($this->pdo->exec("DELETE FROM content WHERE id = $id")) {
				return 'Article id:'.$id.' deleted';
			}
		}
		
	function update_page($id,$articleids, $positions){

			$date = new DateTime('now');
			$timestamp = $date->format('Y-m-d H:i:s');
			$query='UPDATE pages SET 		
			articleids=:articleids,
			positions=:positions,
			lastupdate=:lastupdate
			
			WHERE id=:id';
			
			$stmt=$this->pdo->prepare($query);
			$stmt->execute(array(
			':articleids'=>$articleids,	
			':positions'=>$positions,
			':lastupdate'=>$timestamp,
			':id'=>$id));
		}

	//Delete Caching functions
	public function deleteCacheByArticleId($id){
		$rows = $this->getPagesByArticleId($id);
		//Delete cache for every page that contains this article
		if(count($rows)!=0){			
			foreach ($rows as $row){
				$this->deletePageCache($row['categoryname'],$row['page']);
			}			
		}
	}
	//Delete pages'	caches on update or delete
	public function deletePageCache($categoryname,$page){
		$path=($categoryname==''?'':$categoryname.'/').$page;
		$break = Explode('/', $path);
		$file = implode('-^-', $break);
		$cachefile = $this->doc_root.'cached/cached-'.$file;
		if (file_exists($cachefile)){
			unlink($cachefile);
		}		
	}	

	
}
