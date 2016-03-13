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
    along with MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/


class dynamicPages extends requestHandler{

public function index($path){

$this->loadModel('siteadmin/dynamicpages_model');
$results=$this->dynamicpages_model->getPage($path);
if($results != false && !is_array($results)){
	$this->loadcontroller=$results;
	return;
}else if($results == false){
	return;
}

//Let system know page output wants to be cached
if($results[0]['cache'] == 1){
	$this->cache = true;
}
if($results[0]['minify'] == 1){
	$this->minify = true;
}

//If there is a matched controllerless page, continue...
//set page header for article type "header" contents
$head['meta']=$results[0]['meta'];
$head['title']=$results[0]['headline'];

//load template model
$templatemodel=$results[0]['template'].'_model';
$this->loadModel('templates/'.$templatemodel);

//sort
$sorted=[];
$artOrder=explode( ',', $results[0]['articleids']);
foreach($artOrder as $id){
	foreach($results as $row){
		if($row['articleid'] == $id){
			$sorted[] = $row;
		}		
	}
}
$articleAssViews=unserialize( $results[0]['positions']);

$used=[];
foreach($sorted as $rowkey => $article){
	foreach($articleAssViews as $key => $page){
		if($article['articleid'] == $page['id']){
			$aggregate='';
			if($page['aggregate'] == 'aggregate'){					
				$aggregate='aggregate';			
			}else{
				$aggregate='single';
			}
				
			if($article['contenttype'] == 'header'){ 
				$loadViews[$rowkey][$page['view']]=array('meta'=>$head['meta'],'title'=>$head['title'],'content'=>$article['content']);	
			}else if($article['contenttype'] == 'menu'){ 
				$loadViews[$rowkey][$page['view']]=array('menu'=>$article['menu'],'menutype'=>$article['menutype'],'content'=>$article['content'],'type'=>$aggregate);
			}else{
				$loadViews[$rowkey][$page['view']]['type']=$aggregate;
				$loadViews[$rowkey][$page['view']]['content']=$article['content'];
			}
			unset($articleAssViews[$key]);break;
		}	
	}
}

//aggregate
$temp='';$menutemp='';
foreach($loadViews as $key => $value){
	foreach($value as $key2 => $value2){

		if($value2['type'] == 'aggregate' AND $loadViews[$key + 1][$key2]['type'] == 'aggregate' AND key($value) == key($loadViews[$key + 1])){
			$temp.= $value2['content'];
			if(isset($value2['menu'])){ $menutemp.=  $this->$templatemodel->getMenu($value2['menu'],$value2['menutype']); }
			
		}else if($value2['type'] == 'aggregate' AND $loadViews[$key - 1][$key2]['type'] == 'aggregate' AND key($value) == key($loadViews[$key - 1])		
		AND $value2['type'] == 'aggregate' AND ($loadViews[$key + 1][$key2]['type'] !== 'aggregate' OR key($value) !== key($loadViews[$key + 1]))){
		
			$content['content']=$this->$templatemodel->respImgs($temp.$value2['content']);
			if(isset($value2['meta'])){	$content['meta'] = $value2['meta'];	}
			if(isset($value2['title'])){ $content['title'] = $value2['title']; }
			
			if(isset($value2['menu']) || $menutemp !='' ){$content['menu'] = $menutemp.$this->$templatemodel->getMenu($value2['menu'],$value2['menutype']); }//getMenu returns null if no menu is assigned			
			
			$this->addView('templates/'.key($value),$content);
			$temp;$menutemp='';//Done aggregating this view, this occurance
		}else{
			$content['content'] = $this->$templatemodel->respImgs($value2['content']);	
			if(isset($value2['meta'])){	$content['meta'] = $value2['meta'];	}
			if(isset($value2['title'])){ $content['title'] = $value2['title']; }
			
			if(isset($value2['menu'])){ $content['menu'] = $this->$templatemodel->getMenu($value2['menu'],$value2['menutype']); }else{$content['menu'] = '';}
			$this->addView('templates/'.key($value),$content);	
		}

	}
}

}
}
