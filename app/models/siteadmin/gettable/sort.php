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
class sort extends requestHandler{
public $sort='';
public $sortfield='';
public $datefield='';
public $datefield2='';//maybe not needed
public $get_params='';
public $get_var='';
public $sqladdon='';


function init($sqladdon=''){
	if(isset($_GET['datefield'])){
		$this->datefield=$_GET['datefield'];//maybe add security check here!!!
	}
	//set fake default GET vars so sorting works, GET vars are used later to toggle sort etc.
	//if the sorting field and the page number are the same as last time, then set GET to the default vars. 
	if( $this->sameAsLast($this->get_var) ){
		if(!isset($_GET['sort']) && isset($_GET['sortfield'])){
			$_GET['sort']=$this->sort;//default sort direction
		}	
	}

	if(isset($_GET['sortfield'])){
		$this->sortfield = $_GET['sortfield'];
	}	
	//Set both to defaults
	if( $this->sameAsLast($this->get_var) || !isset($_GET['sortfield'])){
		if(!isset($_GET['sort']) && !isset($_GET['sortfield'])){
			$_GET['sort']=$this->sort;
			$_GET['sortfield']=$this->sortfield;
		}			
	}
	
	if($sqladdon!=''){
		$this->sqladdon=$sqladdon;//maybe add security check here!!!
	}
}


function getParameters($sortFields,$extralink,$pageNumber){

	$reqChar='?';
	if(isset($_GET[$this->get_var]) || $this->sortfield){$reqChar='&';}

	foreach($sortFields as $value){
		if(isset($_GET[$value])){	
			$extralink .= $reqChar.$value.'='.$_GET[$value];
			$getparams[$value] = '1';		
			$reqChar='&';	
			if($value == 'sortfield'){
				$getparams[$value.'revord'] = '1';	
			}
		}	
	}

	foreach($sortFields as $value){
		$getparams[$value] = $this->combine($sortFields,$value,$pageNumber);	
		if(isset($getparams[$value.'revord'])&&$getparams[$value.'revord'] !=null ){
			$getparams[$value.'revord'] =$this->combine($sortFields,$value,$pageNumber,$reverse=true);
		}	
	}
	
	$this->get_params=$getparams;
	return $extralink;//for pagination
}
//if is asc return desc and visa versa	
function swapSort($sort){ 

		if($sort == strtolower('asc')){
			$sort='desc';
		}else if($sort == strtolower('desc')){
			$sort='asc';
		}

	return $sort;
}

//Check if the last request str had the same sort field and pageno's
 function sameAsLast($get_var){
  
  $last='';
	$query_str = parse_url(urldecode(@$_SERVER['HTTP_REFERER']), PHP_URL_QUERY);
	parse_str($query_str, $last);
  
	if(isset($last['sortfield']) ){
	  
		if($last['sortfield'] == @$_GET['sortfield'] && @$last[$get_var] == @$_GET[$get_var]){
	
			return 1;
		 }else{
			return 0;
		 }
	 } else{
		return 0;	
	 }
 }
 
//Combine without the current field, use reversed sort direction for alternate url param str
public function combine($sortFields,$current,$pageNumber,$reverse=false){
	$params='';
	$reqChar='?';
	if($pageNumber ){$reqChar='&';}
	foreach($sortFields as $value){
		if($value != $current && isset($_GET[$value])){
			if($reverse == false){
				$params .= $reqChar.$value.'='.$_GET[$value];
			}else{
				$params .= $reqChar.$value.'='.$this->swapSort($_GET[$value]);
			}
			$reqChar='&';
		}		
	}
	return $params;
}

function buildQuery($table,$returnfields){
//Start building query
	if(isset($_GET['modifier'])){
		
		switch ($_GET['modifier']) {
		   case '':
				 $modifier='';
				 $modifier2='';
				 $operator ="=";
				 break;
				 
		   case 'like':
				$modifier='%';
				$modifier2='%';
				$operator ="LIKE";
				 break;
				 
		   case 'starts':
				$modifier='';
				$modifier2='%';
				$operator ="LIKE";
				 break;
				 
		   case 'ends':
				$modifier='%';
				$modifier2='';
				$operator ="LIKE";
				 break;
		}	
	}
	
	
	
	$Sql="SELECT $returnfields FROM $table";	
	$SqlForDateFields=$Sql;	
	$SqlArray = array();
	
	if(!empty($_GET['search'])){
		if(!empty($_GET['searchfield'])){
			$Sql.=" WHERE {$_GET['searchfield']} $operator ?";
			$search_string=$_GET['search'];
			$SqlArray = array("$modifier{$_GET['search']}$modifier2");
		}
	}
	
	
	//$start_date='0000';
	//$end_date=date('Y');
	$start_date=date('Y-m-d','0000-00-00');
	$end_date=date("Y-m-d");
	if(isset($_GET['startdate'])&& $_GET['startdate']!=''){
	
		//$start_date=$_GET['startdate'];
		$myDateTime = DateTime::createFromFormat('m-d-Y', str_replace("/", "-",str_replace('%2F','/',$_GET['startdate'])));
		if ($myDateTime instanceof DateTime) {
			$start_date = $myDateTime->format('Y-m-d');
		}	
		/**/
	}	
	if(isset($_GET['enddate'])&& $_GET['enddate']!=''){
		
		$myDateTime = DateTime::createFromFormat('m-d-Y', str_replace("/", "-",str_replace('%2F','/',$_GET['enddate'])));
		if ($myDateTime instanceof DateTime) {
			$end_date = $myDateTime->format('Y-m-d');
		}	
		/**/
	//	$end_date = $_GET['enddate'];
		
	}
	
	
	$start_date.=' 00:00:00';
	$CHKDATE=date('Y-m-d', strtotime("+1 day", strtotime("$end_date")));
	$end_date=$CHKDATE.' 00:00:00';
	if(!empty($_GET['search']) && !empty($_GET['searchfield'])){
		$Sql.=" AND";
	}else{
		$Sql.= " WHERE";
	}
if($this->datefield != 'range' ){


	$Sql.=" $this->datefield BETWEEN ? AND ?";
	
	$SqlArray[]=$start_date;
	$SqlArray[]=$end_date;
}else{




	$Sql.=" ((date BETWEEN ? AND ? OR lastupdate BETWEEN ? AND ?) OR (? BETWEEN date AND lastupdate OR ? BETWEEN date AND lastupdate))";
	
	$SqlArray[]=$start_date;
	$SqlArray[]=$end_date;
	
	$SqlArray[]=$start_date;
	$SqlArray[]=$end_date;
	
	$SqlArray[]=$start_date;
	$SqlArray[]=$end_date;
}

//Query addon passed into init 
if($this->sqladdon !=''){
$Sql.=' AND '.$this->sqladdon;
}
	
	if($_GET['sort']==strtolower('asc')){
		$this->sort=' ORDER BY '. $this->sortfield ." ASC ";
	}else if($_GET['sort']==strtolower('desc')){
		$this->sort=' ORDER BY '. $this->sortfield ." DESC ";
	}else{
		$this->sort=' ORDER BY '. $this->sortfield ." ". $this->sort." ";
	}
	
	$Sql.=$this->sort;

	return array("sql"=>$Sql,"sql_array"=>$SqlArray);
}


function hiddenInputs($fields,$pageNumber,$get_var){
//exclude unesessary hidden fields which are submitted by the form, maybe make an exclude array to check for custom filtering
		//$key != 'search' && $key != 'searchfield' && $key != 'resultsppg' && $key != 'startdate' && $key != 'enddate'
	$hidden_inputs='';
	parse_str(parse_url($pageNumber.$this->get_params['search'], PHP_URL_QUERY),$q_arry);
	
	foreach( $q_arry as $key => $value){
		
		if(in_array($key,$fields) || $key == $get_var || $key == 'sort' || $key == 'dbtable' || $key == 'sortfield'){
			$hidden_inputs.='<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
		}
	}

	return $hidden_inputs;	
}



/*
function addAttributes($attributes){
	
	$out='';
	foreach($attributes as $key => $value){
		$out.=' '.$key.'="'.$value.'"';
	 
	}
	return $out;
}

*/
}
