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
class search extends requestHandler{

	public function index(){

		$this->loadModel('search_model');		
		$this->loadModel('pagination_model');
		
		$ids=$this->search_model->search($_GET['search']);
			

		$numresults=5;//Default number of records per page 
		$numpagelinks='3';//pagination numbered links ahead and behind, not total clickable.
		$get_var='page';
		$firstlast=true;
		$prevnext=true;	

		$total_records = count($ids);	
			
		$page_num=intval($_GET[$get_var]);
		if($page_num==''){
			$page_num=1;
			$startingrecord=0;
		}else{
			$startingrecord=($page_num*$numresults)-$numresults;//for db call
		}

		
			
		$ids=array_splice($ids, $startingrecord,$numresults); 
		
	
//Split types
		$pages=[];
		$articles=[];
		foreach($ids as $key => $record){
			if( $record['type']=='page'){
				$pages[] = $record['id'];
			}else if( $record['type']=='article'){
				$articles[] = $record['id'];
			}			
		}
				
			
		//Pages
		$in  = str_repeat('?,', count($pages) - 1) . '?';
		$sql = "SELECT *,pages.id as pageid FROM pages
		JOIN content ON (pages.articleids=content.id AND pages.published = 1 AND content.published = 1
		) OR ( FIND_IN_SET(content.id, pages.articleids) AND pages.published = 1 AND content.published = 1)
		 WHERE pages.id IN ($in)";
		 
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute($pages);	
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		
		
		//Content
		$in  = str_repeat('?,', count($articles) - 1) . '?';
		$sql = "SELECT * FROM content
		 WHERE id IN ($in)";
		 
	
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute($articles);	
		$articleresults = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		
		
		foreach($results as $result){
			$allresults[] = $result;
		}
		foreach($articleresults as $result){
			$allresults[] = $result;
		}		
		$results=$allresults;



/* Material Design */
		$pageconfig['pagination_open']='<ul class="pagination">';
		$pageconfig['pagination_close']='</ul>';
		$pageconfig['open']='<li class="waves-effect">';
		$pageconfig['close']='</li>';
		$pageconfig['active_open']='<li class="active"><a>';
		$pageconfig['active_close']='</a></li>';
		$pageconfig['next_open']='<li class="waves-effect">';
		$pageconfig['next_close']='</li>';
		$pageconfig['prev_open']='<li class="waves-effect">';
		$pageconfig['prev_close']='</li>';
		$pageconfig['disabled_left']='<li class="disabled"><a><i class="material-icons">chevron_left</i></a></li>';	
		$pageconfig['disabled_right']='<li class="disabled"><a><i class="material-icons">chevron_right</i></a></li>';		
		$pageconfig['prev_chevron_left']='<i class="material-icons">chevron_left</i>';
		$pageconfig['next_chevron_right']='<i class="material-icons">chevron_right</i>';		
		$pageconfig['first_open']="<li>";
		$pageconfig['first_close']="</li>";	
		$pageconfig['last_open']="<li>";
		$pageconfig['last_close']="</li>";	


/* Bootstrap

		$pageconfig['pagination_open']='<ul class="pagination">';
		$pageconfig['pagination_close']='</ul>';
		$pageconfig['open']="<li>";
		$pageconfig['close']="</li>";
		$pageconfig['active_open']="<li class='disabled'><li class='active' ><a>";
		$pageconfig['active_close']="</a></li></li>";//<span class='sr-only'></span></a></li></li>
		$pageconfig['next_open']="<li>";
		$pageconfig['next_close']="</li>";
		$pageconfig['prev_open']="<li>";
		$pageconfig['prev_close']="</li>";	
		$pageconfig['first_open']="<li>";
		$pageconfig['first_close']="</li>";	
		$pageconfig['last_open']="<li>";
		$pageconfig['last_close']="</li>";	
*/
		//public $pdo=$this->pdo;
		
		$pageconfig['query']='';//done externally
		$pageconfig['query_array']='';
		$pageconfig['link_url']=$requested_uri; 
		$pageconfig['link_params']='&amp;search='.$_GET['search'];
		$pageconfig['num_results']=intval($numresults);
		$pageconfig['num_links'] = $numpagelinks;//ahead and behind
		$pageconfig['get_var']=$get_var; 
		$pageconfig['prevnext']=$prevnext;
		$pageconfig['firstlast']=$firstlast;
		$pageconfig['total_records']=$total_records;
		
		/*Paginate!*/
		$this->pagination_model->paginate($pageconfig);

		//$results=$this->pagination_model->results;
		$data['currentpage']=$this->pagination_model->page_links;
		$data['totalrecords']=$this->pagination_model->total_records;

	
			
	function getMatches($content,$searchArray){


	foreach($searchArray as $value){
		$regexs[] = array('regx' => "/\b$value\b/i", "term"=>$value);
	}


	$match=false;
	foreach($regexs as $key => $value){
		if ( @preg_match_all($value['regx'], $content, $matches, PREG_OFFSET_CAPTURE)){
			$match = true;
			$term = $value['term'];
			break;
		}	
	}
	
	if($match == true){	
		return array('matches' => $matches, "matched"=>$term);
	}else{
		return array('matches' => false);
	}
}



function highlight($search,$content,$searchArray){
$content = ' '.$content;
	$results = getMatches($content, $searchArray);	
	$matches = $results['matches'];
	
	if (is_array($matches) and $sl=strlen($search)>1){
				//echo'can\'t find';
				
		$pos= $matches[0][0][1];//count matched for weighted search here! strpos(strtolower($content1), strtolower($search));
		$lenth=100;
		$start = 5;//105 total
		if($pos<$start){
		$start = $pos;
	
		}

		$result.= substr($content, ($pos -$start), $start).''.substr($content, $pos, (strlen ($results['matched']))).''.substr($content, ($pos + (strlen ($results['matched']))),(strlen ($results['matched']))+ $lenth -strlen ($results['matched']) );
		if(($a=($pos)+ 2 + (strlen ($results['matched']))) < $b = (strlen ($content)) AND $b - $a > $lenth){ $result .='...';} else { $result .='';}
		if($pos>=4){
		$result = '...'.$result;
		}
		return $result;

	}
	if(strlen ($content) > 100){
	$content = substr($content,0,100);
	$content.="...";
	}
return $content	;
}


$searchArray[] = trim($_GET['search']);
$searchwords = explode(' ',trim($_GET['search']));
foreach($searchwords as $key => $value){
$searchArray[]=$value;
}	
			
		
		$pages=array();
		foreach($results as $row)
		{	
	
			$content=$this->search_model->stripHTML($row['content']);
			if( $content != ''){
				if(isset($pages[$row['pageid']])){					
					$pages[$row['pageid']]['content']= $content.'...' .$pages[$row['pageid']]['content']; 
				}else if(isset($row['pageid'])){
					$pages[$row['pageid']]['pageid']=$row['pageid'];
					$pages[$row['pageid']]['page']=$row['page'];
					$pages[$row['pageid']]['categoryname']=$row['categoryname'];
					$pages[$row['pageid']]['content'] =  $content;
					$pages[$row['pageid']]['link'] =  $row['link'];
					$pages[$row['pageid']]['type'] =  $row['pagetype'];//not used because if there is a link, it uses that
				}else{
				//is an article
					$pages[$row['id']]['articleid'] =  $row['id'];
					$pages[$row['id']]['articlename'] =  $row['articlename'];
					$pages[$row['id']]['content']= $content;
					$pages[$row['id']]['link'] =  $row['link'];	
					$pages[$row['id']]['type'] =  $row['type'];					
				}
			}
		}
			
			

		/* output table */
		foreach($ids as $value){
		//echo$value;
			foreach($pages as $value2){
			//.' - '.$value2['id'].'<br>';
				if($value['id'] == $value2['pageid']){//add type check here
				$data['results'].='<div><a href="'.($value2['link']!=''?$value2['link']:$value2['categoryname']).'/'.(strtolower($value2['page']) == 'home'?'':$value2['page']).'">'.$value2['page'].'</a><br>'.highlight(trim($_GET['search']),$value2['content'],$searchArray).'</div><br>';
				}else if($value['id'] == $value2['articleid']){
				
				$data['results'].='<div><a href="'.$value2['link'].'">'.$value2['articlename'].'</a><br>'.highlight(trim($_GET['search']),$value2['content'],$searchArray).'</div><br>';
				}
			}
		}

		
		$this->loadModel('templates/materialize_model');
		$data['sidemenu'] = $this->materialize_model->getMenu('main menu','sidemenu');
		$data['dropmenu'] = $this->materialize_model->getMenu('main menu','dropmenu');		
				
		$this->addView('templates/materialize/header');
		$this->addView('search/materializebody',$data);
		$this->addView('templates/materialize/footer');
		
		/*
		$this->addView('search/bootstrapheader');
		$this->addView('search/bootstrapbody',$data);
		$this->addView('siteadmin/footer');
		*/
	}
	public function buildindex(){

		$this->loadModel('search_model');		
		
		$this->search_model->updateSearch();
	}

}
