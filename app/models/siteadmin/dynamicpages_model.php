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
    along with PHP-MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	

class dynamicpages_model extends requestHandler{


	public function	getPage($path){
		$fullCount=count(explode('/', $path));

		if($fullCount==1){
			$page=$path;
			$category='';
		}else{
			//Find category and page- more than one url segment

			$incat=explode('/', $path, -1);
			$c=count($incat);
			
			$i2=0;$incat2='';
			while($i2<$c){
				if($incat2=='')$incat2.=$incat[$i2++];else$incat2.='/'.$incat[$i2++];
			}	
			//Find page, it's the last in the path
			$page = explode('/', $path);
			$page=$page[$c];

			$stmt=$this->pdo->prepare("SELECT * FROM categories 
			JOIN pages ON categories.name=pages.categoryname
			WHERE pages.page = ?");
			$stmt->execute(array($page));
			$num=$stmt->rowCount();

			if($num !=0){
				$category = $incat2;
			}else{
			//no category matched to page! -> not found();
				$category='';
				return false;
			}
		}

		//Join on match or find in set
		$sql="SELECT *, content.id AS articleid, content.type AS contenttype
		FROM content
		JOIN pages ON (pages.articleids=content.id AND pages.page = ? AND pages.published = 1 AND content.published = 1 AND pages.pagetype = 'page'"; 
		if($category ==''){
			$sql.=" AND pages.categoryname IS NULL";
		}
		$sql.=") OR ( FIND_IN_SET(content.id, pages.articleids) AND pages.page = ? AND pages.published = 1 AND content.published = 1 AND pages.pagetype = 'page'";
		if($category ==''){
			$sql.=" AND pages.categoryname IS NULL";
		}
		$sql.=")";

		//Finish query for pages with categories
		$addon='';
		$array=[];
		$array[]=$page;
		$array[]=$page;

		if($category !==''){
			$addon=' JOIN categories ON categories.name=pages.categoryname AND categories.name = ?';
			$array[]=$category;
		}
		//echo$sql;
		$addon.=" ORDER BY content.id ASC;";


		$stmt=$this->pdo->prepare($sql.$addon);
		$stmt->execute($array);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//return with controller/method to load
		if($rows[0]['controller'] != ''){
			return $rows[0]['controller'];
		}
		if(count($rows)==0){
			return false;
		}
		return $rows;
	}
}
