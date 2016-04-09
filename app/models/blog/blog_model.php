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
class blog_model extends requestHandler{
	public $userid;
	//for User class
	public function init()
    {
       	$query="SELECT user FROM content WHERE id = :id";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':id'=>$_GET['article'])); 							
		if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
			$this->userid = $row["user"];			
		}
    }
	function getCreatorId(){
		return $this->userid;
	}


/*	for loading pages and articles of a certain type, not being used in this demo. Site map will have to be managed separately with article only apps.
	public function getAllByType($table,$type='blog'){
       	$query="SELECT *, content.id AS articleid FROM content
		JOIN pages ON (pages.articleids=content.id AND pages.pagetype = :type)";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':type'=>$type)); 
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);		
	
		return 	$rows;	
	}
*/
	public function getAllByType($table){
       	$query="SELECT * FROM content WHERE type='blog' OR type='code'";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(); 
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);		
	
		return 	$rows;	
	}
	public function getArticle($link){
		$query="SELECT * FROM content WHERE link = ? AND published = 1";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array($link)); 
		return $stmt->fetch(PDO::FETCH_ASSOC);		
	}
	public function getCategory($category){
		$query="SELECT * FROM content WHERE category = ? AND published = 1";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array($category)); 
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	
	public function getDistinct(){
		$stmt=$this->pdo->prepare("SELECT DISTINCT category FROM content WHERE (type = 'blog' OR type = 'code') AND (category != '' AND category IS NOT NULL) ");
		if ($stmt->execute()) {
			return $stmt->fetchAll(PDO::FETCH_ASSOC) ;		
		}
	}


	public function insertArticle($user) {

		 if(  isset($_POST['articlename']) && $_POST['articlename'] !=''){
			if($_POST['category'] ==''){
				$_POST['category'] = 'uncategorized';
			}
			 $date = new DateTime('now');
			$timestamp = $date->format('Y-m-d H:i:s');
			$link = $this->urlSlug('blog/'.$_POST['category'].'/'.$_POST['articlename']);
			$link = $this->uniqueSlug($link,'content');
			$sqlArray = array($_POST['type'],$user->getId(),$link,$_POST['articlename'],$_POST['description'],$_POST['content'],$_POST['category'],isset($_POST['published']) ? 1 : 0,$timestamp,$timestamp);
			$query="INSERT INTO content (`type`,`user` ,`link` ,`articlename`,`description`,`content`,`category`,`published`,`date`,`lastupdate` ) VALUES ( ?,?,?,?,?,?,?,?,?,? )";
			$stmt=$this->pdo->prepare($query);
			$stmt->execute($sqlArray);			 
			return $this->pdo->lastInsertId('id');
		}
	}
	public function updateArticle() {

		$old_slug = $this->getSlugById($_POST['id']);				
		
		$new_slug = $this->urlSlug('blog/'.$_POST['category'].'/'.$_POST['slug']);			
		
		if($new_slug !== $old_slug){
			$new_slug = $this->uniqueSlug($new_slug,'content','id',$_POST['id']);			
		}
		
			
		 if(  isset($_POST['articlename'])){ 
		 	$this->deleteCache($_POST['id']);
			$query='UPDATE content SET 
			type=:type,
			link=:link,
			articlename=:articlename,
			description=:description,
			content=:content,
			category=:category,
			published=:published
			WHERE id=:id';
			
			$array=array(
				':type'=>$_POST['type'],
				':link'=>$new_slug,
				':articlename'=>$_POST['articlename'],	
				':description'=>$_POST['description'],
				':content'=>$_POST['content'],
				':category'=>$_POST['category'],	
				':published'=>isset($_POST['published']) ? 1 : 0,
				':id'=>$_POST['id']
			);				
		
			$stmt=$this->pdo->prepare($query);
			$stmt->execute($array);	
			
		}
	}


	public function uniqueSlug($slug,$table,$col='',$id=0) {

		if ($this->checkSlug($slug,$table,$col,$id)) {
			$elements = explode('-',$slug);
			$last_element = end($elements);
			if(is_numeric (strrev("$last_element")) && is_numeric ($last_element)){								
				array_pop($elements);									
				++$last_element;	
				array_push($elements,$last_element);	
				$slug=implode('-',$elements);
				return $this->uniqueSlug($slug,$table,$col,$id);
			}else{
				return $this->uniqueSlug($slug.'-2',$table,$col,$id);
			}
		}
		return $slug;
	}
	
	function checkSlug($slug,$table,$col,$id=0)//if update provide id & id column
	 {
	 
		$sql="SELECT link FROM $table WHERE link = ?";
		$array[]=$slug;		
		if($id!==0){
			$array[]=$id;		
			$sql.=" AND $col != ?"; 
		}	
		$sql.="	LIMIT 1";
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute($array); 
	  if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) 
	   {
		 return 1;
	   }
	   else
	   {
		 return false;
	   }
	 }	
	 
	 
	public function getSlugById($id){
       	$query="SELECT link FROM content WHERE id = ?";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array($id)); 
	
		if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
			return $row["link"];			
		}		
	}
	

	public function delArticles(){
		foreach(@$_REQUEST['indexSelected'] as $key => $id){
			$this->deleteCache($id);
			if ($this->pdo->exec("DELETE FROM content WHERE id = $id")) {
				echo$id.' deleted';
			}
		}
	}
	public function deleteCache($id){
		$stmt=$this->pdo->prepare("SELECT link FROM content WHERE id=?");
		$stmt->execute(array($id));
		$row = $stmt->fetch(PDO::FETCH_OBJ);	
		$path=$row->link;
		$break = Explode('/', $path);
		$file = implode('-^-', $break);
		$cachefile = $this->doc_root.'cached/cached-'.$file;
		if (file_exists($cachefile)){
			unlink($cachefile);
		}	
		return $path;
	}		
}	 
/* Delete caches for blog-home, category and page...not yet needed since only the articles are currently cached
	public function deleteRelatedCaches($link){
	$break = Explode('/', '/'.$link);
	array_shift($break);
	//var_dump($break);
	$break2 = $break;
	//
	
	$file = implode('-^-', $break);
	$cachefile = BASE_URI.'/cached/cached-'.$file;
	unlink($cachefile);
	
	$break2 = array_pop($break2);	
	
	$file2 = '-^-'.$break[1];
	$cachefile2 = BASE_URI.'/cached/cached-blog'.$file2;
	unlink($cachefile2);
	
	
	$path = BASE_URI.'/cached/';
if ($handle = opendir($path)) {

    while (false !== ($file = readdir($handle))) { 
        if(stristr($file, 'cached-blog'.$file2) == TRUE && is_file($path . $file))
        {
           unlink($path . $file);echo$path . $file.' unlinked';
        }

    }

    closedir($handle); 
}
		
	unlink(BASE_URI.'/cached/cached-blog');
}
*/
