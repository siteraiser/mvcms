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
class pages_model extends requestHandler{
	public function getCategoryById($id=''){
		if($id !=''){
			$query="SELECT name FROM categories WHERE id = '$id'"; 
			if ($stmt=$this->pdo->query($query)) {			
				if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
					return $row['name'];
				}	
			}
		}
	}
	public function getAllById($table,$id){
       	$query="SELECT * FROM pages WHERE id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$id)); 		
		
			if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
				return $row;
			}
	
	}
	//Find matching menu links and replace with updated values	
	public $oldlink='';
	public $newlink='';
	public $count=0;
	public function makelist(&$objects)
	{

		foreach($objects as $object){				
			if($object->url ==$this->oldlink){
				$object->url=$this->newlink;
			}			
			if(isset($object->children)){			
				$this->makelist($object->children);					
			}
		}
	}
	public function updateMenu($data,$orig,$new){	
	
		//$getList = new Menu();//move to check menu and pass in for speed
		$this->oldlink=$orig;
		$this->newlink=$new;
		$array=unserialize($data);
		$this->makelist($array);
		
		return serialize($array);
	}
	
	public function checkMenu($changes){
	
		foreach($changes as $orig => $new){
			//$orig = '/products-and-services/web-design/design-demos';
		
			echo$query="SELECT * FROM menus WHERE data LIKE '%:\"".$orig."\";s:%'";
			$stmt=$this->pdo->prepare($query);// AND type like '%:\"".'linked'."\";s:%'
			$stmt->execute(array());
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($rows as $row){
				$new_data = $this->updateMenu($row['data'],$orig,$new);
				
				$q='UPDATE menus SET 
				data=:data			
				WHERE id=:id';				
				$stmt=$this->pdo->prepare($q);
				$stmt->execute(array(
				':data'=>$new_data,	
				':id'=>$row['id']));				
				
				//Delete caches containing this menu id
				$this->handleCaching($row['id']);

			}

		}
	}	
	public function findDifferences($original,$new){		
		$i = 0;
		foreach($original as $orig){
			if($new[$i] != $orig){
				$changes['/'.$orig] = '/'.$new[$i];
			}
			$i++;
		}
		$this->checkMenu($changes);
	}
		
	public function updateSelected(){

		$orig_links = $this->deleteCaches();//get links to update menu
		
		$date = new DateTime('now');
		$timestamp = $date->format('Y-m-d H:i:s');
		foreach($_POST["indexSelected"] as $key => $value){
			$new_links[] = ($_POST["categoryname"][$value]==''?'':$_POST["categoryname"][$value].'/').$_POST["page"][$value];
			
			
			$query='UPDATE pages SET 
			categoryname=:categoryname,			
			page=:page,
			controller=:controller,
			priority=:priority,
			published=:published,
			lastupdate=:lastupdate
			
			WHERE id=:id';
			
			$stmt=$this->pdo->prepare($query);
			$stmt->execute(array(
			':categoryname'=>($_POST["categoryname"][$value]==''?NULL:$_POST["categoryname"][$value]),	
			':page'=>$_POST["page"][$value],
			':controller'=>$_POST["controller"][$value],
			':priority'=>$_POST["priority"][$value]>1?1.00:$_POST["priority"][$value],
			':published'=>isset($_POST['published'][$value]) ? 1 : 0,
			':lastupdate'=>$timestamp,
			':id'=>$value));			
		}		
		//Send changes to menu updater
		$this->findDifferences($orig_links,$new_links);		
	}
	
	public function delPage(){
//--------delete-page----
		foreach(@$_REQUEST['indexSelected'] as $key => $value){
			$this->deleteCache($value);
			if ($this->pdo->exec("DELETE FROM pages WHERE id = $value")) {
				echo$value.' deleted';
			}
		}
	}

	public function insert(){	
	
		$i=0;
		$date = new DateTime('now');
		$timestamp = $date->format('Y-m-d H:i:s');
		$stringarray=[];
		foreach($_POST["articleid"] as $value){
			$stringarray[$i]['id']=$value;
			$stringarray[$i]['view']=$_POST["view"][$i];
			$stringarray[$i]['aggregate']=$_POST["aggregate"][$i];
			++$i;
		}

		$assignedviewpositions=serialize($stringarray);
		
		$articleids=implode(',',$_POST["articleid"]);

			$query='INSERT INTO pages (
			page,
			pagetype,
			template,
			headline,
			categoryname,			
			articleids,
			positions,
			controller,
			meta,
			priority,
			published,
			cache,
			minify
			)
			VALUES
			(?,?,?,?,?,?,?,?,?,?,?,?,?)';	
			
			$stmt=$this->pdo->prepare($query);
			$stmt->execute(array(
			$_POST["page"],			
			$_POST["pagetype"],
			$_POST["template"],
			$_POST["headline"],			
			($_POST["categoryname"]==''?NULL:$_POST["categoryname"]),
			$articleids,	
			$assignedviewpositions,
			$_POST["controller"],
			$_POST["meta"],
			$_POST["priority"]>1?1.00:$_POST["priority"],
			isset($_POST['published']) ? 1 : 0,
			isset($_POST['cache']) ? 1 : 0,
			isset($_POST['minify']) ? 1 : 0
			));	
			$lastInsert = $this->pdo->lastInsertId('id');
			
			//Fix the date
			$query="SELECT lastupdate FROM pages WHERE id = :id";
			$stmt=$this->pdo->prepare($query);
			$stmt->execute(array(':id'=>$lastInsert)); 		
		
			if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
					
				$query="UPDATE pages SET date=:date WHERE id = :id";
				 $stmt=$this->pdo->prepare($query);
				$stmt->execute(array(':date'=>$row['lastupdate'],':id'=>$lastInsert)); 	
			}
			
		return $lastInsert;
	}
	
	
	
/* Single page editing classes */

	public function getArticleList(){
		/* select assigned "positions" from pages instead */	
	
		$stmt=$this->pdo->prepare("SELECT id,articlename FROM content");
		$stmt->execute(array());
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
	
		if(count($rows) > 0)
		{
			foreach($rows as $key => $row){
				$article_list[] = array('value' => $row->articlename, 'data' => $row->id, 'views' => $row->views);
			}
			return $article_list;
		}
	}	
	public function getIncludedList($id){
		$sql="SELECT *, content.id AS articleid FROM content
		JOIN pages ON (pages.articleids=content.id AND pages.id = ? 
		) OR ( FIND_IN_SET(content.id, pages.articleids) AND pages.id = ?";

		$sql.=")";
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute(array($id,$id));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$sorted=[];
		$artOrder=explode( ',', $rows[0]['articleids']);

		$usedKeys=[];
		foreach($artOrder as $id){
			foreach($rows as $row){			
				if($row['articleid'] == $id){
					$positionsA = unserialize($row['positions']);
					foreach($positionsA as $key => $value){					
						if(!in_array($key,$usedKeys[$id]) && $id == $value['id']){
							$positions[]=array('id'=>$value['id'],'name'=>$row['articlename'],'views'=>$value['view'],'aggregate'=>$value['aggregate']);
							$usedKeys[$id][]=$key;
							break;
						}
					}				
				}		
			}
		}

		return $positions;
		
	}	
	
	
	public function updateSingle($id){

		$orig_links[] = $this->deleteCache($id);//only one link in an array...

		$date = new DateTime('now');
		$timestamp = $date->format('Y-m-d H:i:s');
		$stringarray=[];$i=0;
		foreach($_POST["articleid"] as $value){
			$stringarray[$i]['id']=$value;
			$stringarray[$i]['view']=$_POST["view"][$i];
			$stringarray[$i]['aggregate']=$_POST["aggregate"][$i];
			++$i;
		}
		
		$assignedviewpositions=serialize($stringarray);
		
		$articleids=implode(',',$_POST["articleid"]);

			$query='UPDATE pages SET 
			page=:page,			
			pagetype=:pagetype,
			template=:template,
			headline=:headline,			
			categoryname=:categoryname,			
			articleids=:articleids,
			positions=:positions,
			controller=:controller,
			meta=:meta,
			priority=:priority,
			published=:published,
			lastupdate=:lastupdate,
			cache=:cache,
			minify=:minify
			
			WHERE id=:id';
			
			$stmt=$this->pdo->prepare($query);
			$stmt->execute(array(
			':page'=>$_POST["page"],
			':pagetype'=>$_POST["pagetype"],
			':template'=>$_POST["template"],
			':headline'=>$_POST["headline"],			
			':categoryname'=>($_POST["categoryname"]==''?NULL:$_POST["categoryname"]),
			':articleids'=>$articleids,	
			':positions'=>$assignedviewpositions,
			':controller'=>$_POST["controller"],
			':meta'=>$_POST["meta"],
			':priority'=>$_POST["priority"]>1?1.00:$_POST["priority"],				
			':published'=>isset($_POST['published']) ? 1 : 0,
			':lastupdate'=>$timestamp,
			':cache'=>isset($_POST['cache']) ? 1 : 0,
			':minify'=>isset($_POST['minify']) ? 1 : 0,
			':id'=>$id));
			
			$new_links[] = ($_POST["categoryname"]==''?'':$_POST["categoryname"].'/').$_POST["page"];
			$this->findDifferences($orig_links,$new_links);
			
	}	
	
	//Un-Caching
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
		return $path;
	}	
	public function deleteCaches(){
		$links=[];
		foreach($_POST["indexSelected"] as $key => $value){
			$links[]=$this->deleteCache($value);
		}
		return $links;
	}
	
	
	//Delete the caches in which the menus reside
	public function handleCaching($id){
		//Get pages
	   	$query="SELECT * FROM menus 
		JOIN content ON (content.menu=menus.name) 
		JOIN pages ON FIND_IN_SET(content.id, pages.articleids) OR pages.articleids=content.id
		WHERE menus.id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$id)); 	
		

		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($rows as $row){
				$pageids[] = $row['id'];
			}
		
		$this->deleteCachesById($pageids);
	}
	
	public function deleteCachesById($ids){
		foreach($ids as $value){
			$this->deleteCache($value);
		}
	}
	

	
}
