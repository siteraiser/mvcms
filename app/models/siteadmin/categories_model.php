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
class categories_model extends requestHandler{
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
	public function getCategories(){

		$query="SELECT id,name FROM categories"; 
		if ($stmt=$this->pdo->query($query)) {			
			$results=[];
			while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
				$results[$row['id']] = $row['name'];
			}	
		}
		return $results;		
	}
	
	function addCategory(){// if in category then add to name
		if($_REQUEST['categoryName'] != ''){
		$date = microtime(true);

		$count="SELECT * FROM categories"; 
		$result=$this->pdo->query($count);
		$idnum=$result->rowCount();

		if(isset($_REQUEST['selectedCategory'][0])){
		$name=$_REQUEST['selectedCategory'][0].'/';
		}

		$type=$_REQUEST['type'];
		$name.=$_REQUEST['categoryName'];

		$sql = "INSERT INTO categories (id,date,type,name)
			VALUES
			( ?, ?, ?, ?)
			   ";

			$stmt=$this->pdo->prepare($sql);
			$r=$stmt->execute(array($idnum +1,$date,$type,$name));

			if (!$r) {
				echo 'Database prepare error';
				exit;
			}
		}
	}



	//-----------------update------------
	function updateCategory(){

	//----first update type
		$origArray=@$_REQUEST['updateCategoryOrig'];	
		$newType=@$_REQUEST['updateType'];
		foreach($origArray as $key => $value){
		//call database
		
			$this->pdo->quote($newType[$key]);	$this->pdo->quote($value);
			$query="UPDATE categories SET type='$newType[$key]' WHERE name='$value'";

			if ($this->pdo->exec($query) === TRUE) {
			
			}else{  /*print_r($this->pdo->errorInfo());*/}
		}
		unset($value);
	//----now categories
	 
	$upArray=@$_REQUEST['updateCategory'];
	$origArray=@$_REQUEST['updateCategoryOrig'];
	if(isset($origArray)){
	foreach (@$origArray as $key => $value){
		$parts[] = explode('/', $value);
		$test=count($parts[$key]);
		if($parts[$key][$test-1]!==$upArray[$key]){
		$chopped=$this->chopper($value);
		$changes[$test-1][$value]=$upArray[$key];
		}
	}
	unset($value);
	}
	//call database
	$query = "SELECT name,type FROM categories";
		
	/* prepare statement */
	if ($stmt=$this->pdo->query($query)) {			
			/* fetch values */
		while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
			$fullname[$row['name']]=$row['name'];
		}
	}

	if(isset($changes)){
	foreach (@$changes as $key => $value){
	foreach (@$changes[$key] as $keyb => $valueb){
	$keyb.='/';
		$lenth=strlen($keyb);

		foreach ($fullname as $key2 => &$value2){$key2.='/';
		$test=substr($key2, 0, $lenth); 
			if($keyb==$test){
			
			$partsAll = explode('/', $value2);
	$partsAll[$key]=$valueb;
			
	$value2=implode('/', $partsAll);

			}
		}
	}}
	}
	unset($value);unset($value2);unset($valueb);
	foreach ($fullname as $key => $value){

		if($key !==$value){	$this->pdo->quote($key);	$this->pdo->quote($value);
			$query="UPDATE categories SET name='$value' WHERE name='$key'";

			if ($this->pdo->exec($query) === TRUE) {

			}else{  /* print_r($this->pdo->errorInfo());*/}
		}
	}
		//Cache deletion		
		$this->findDifferences($fullname);//Update menu changes
		$this->delete($changes);//Delete caches in these categories
		
	}
	
	//Find matching menu links and replace with updated values
	public $newcategory='';
	public $category='';
	public function makelistcats(&$objects)
	{

		foreach($objects as $object){
			$compare=$this->category.'/';
			$cat_length = strlen($compare);
			$teststem=substr($object->url, 0, $cat_length); //includes
			if($compare == $teststem){			
				$object->url = $this->newcategory .'/' . end((explode('/', $object->url)));
			}			
			if(isset($object->children)){				
				$this->makelistcats($object->children);				
			}
		}
	}	
	public function findDifferences($fullnames){
		
		foreach($fullnames as $orig => $new){
			if($new != $orig){
				$changes['/'.$orig] = '/'.$new;
			}		
		}	

		$changes = array_reverse($changes, true);
		$this->checkMenu($changes);
	}
	
	public function updateMenu($data,$orig,$new){	
	
		//$getList = new Menu();//move to check menu and pass in for speed
		$this->category=$orig;
		$this->newcategory=$new;
		$array=unserialize($data);
		$this->makelistcats($array);
		
		return serialize($array);
	}
	public function checkMenu($changes){
	
		foreach($changes as $orig => $new){
		
			$query="SELECT * FROM menus WHERE data LIKE '%:\"".$orig."/%'";
			$stmt=$this->pdo->prepare($query);
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
				//make dependent apps list for cache deletion
				if(!in_array($row['dependencies'],$apps)){
					$apps[]=$row['dependencies'];
				}
			}	
		}
		$this->deleteAppCaches($apps);
	}		
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
	//delete page caching
	function delete($changes){	
		$dir = new DirectoryIterator($this->doc_root."/cached");
		foreach ($dir as $fileinfo) {
		if($fileinfo != '.' && $fileinfo != '..')
			$caches[]= pathinfo($fileinfo->getFilename(), PATHINFO_FILENAME);
		}
		
		foreach ($changes as $value) {
		
			$break = Explode('/', key($value));
			$file = implode('-^-', $break);
			$compare='cached-'.$file.'-^-';
			$del_length = strlen('cached-'.$file.'-^-');
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
	
	
	
		



	//-----------------
	function delCategory(){
		$delArray=$_REQUEST['selectedCategory'];


		$query = "SELECT name,type FROM categories";
			
			if ($stmt=$this->pdo->query($query)) {
				while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
					$fullname[]=$row['name'];
				}		
			}
		foreach ($delArray as $key => $value){

			$value.='/';
			$lenth=strlen($value);
			foreach ($fullname as $key1 => $value1){
			
				$test=substr($value1, 0, $lenth); 
				if($value==$test){
					$delArrayChildren[]=$value1;			
				}			
			}
		}


		unset($value);unset($value2);
		foreach ($delArrayChildren as $key => $value){
			$delArray[]=$value;
		}
		unset($value);
		foreach ($delArray as $key => $value){

			$this->pdo->quote($value);
			if ($this->pdo->exec("DELETE FROM categories WHERE name = '$value'")) {
				//echo'Returned True';
			}
				
		}
		/////////////Re-Number ids on delete

		$result3 = $this->pdo->exec('set @a=0; update categories set id=(@a:=@a+1);');
	}
	
	
	public function chopper($value){$i2=0;$incat2='';
		$incat=explode('/', $value, -1);
		$c=count($incat);while($i2<$c){if($incat2=='')$incat2.=$incat[$i2++];else$incat2.='/'.$incat[$i2++];}
		return $incat2;
	} 


	public function showCategory(){
		if($this->url_segments[1] == 'categories'){
			$id_or_type = 'type';
		}else if($this->url_segments[1] == 'pages'){
			$id_or_type = 'id';
		}
		
		$output='';
		$query = "SELECT name,$id_or_type FROM categories ORDER BY name ASC";
			/* prepare statement */
		$stmt=$this->pdo->prepare($query);
		$r=$stmt->execute(); 
		if ($r) {
			/* fetch values */ 
			$output='';
		
			while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
			$full[$row['name']]=$row[$id_or_type];
				
			}/* close statement */

		

			if($this->url_segments[1] == 'categories'){
				foreach ($full as $key => $value){ 
					$keyA=$key;$keyB=$key;
					$parts = explode('/', $keyB);$last=count($parts);$ename=$parts[$last-1];
					$base=$this->chopper($keyA);if($base!='')$base.='/';
					$output.='
					<div style="float:left;height:24px;width:24px;"> <input type="checkbox" name="selectedCategory[]" value="'.$key.'" /></div>
					<div style="height:24px;width:500px;float:left;">
					<input type="hidden" name="updateCategoryOrig[]" value="'.$key.'"/><span>'.$base.'</span><input type="textbox" name="updateCategory[]" value="'.$ename.'"/></div>
					<div style="height:24px;width:100px;float:left;"><input type="textbox" name="updateType[]" value="'.$value.'"/></div><br><br>';
				}
			}	
			else if($this->url_segments[1] == 'pages'){
				$output='';
				$results[0]='';
				foreach ($full as $name => $id){ 
				$results[$id] = $name;
					//$output.='<div style="border-bottom:dotted 1px;"><div style="height:16px;float:left;">'.$name.'</div><div style="height:16px;float:right;margin-left:16px;">'.$id.'</div><br></div>';
				}
				return $results;
			}
		}
		return $output;
	}	
	

	
}
