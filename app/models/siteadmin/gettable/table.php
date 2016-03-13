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
class table extends requestHandler{
public $head='';
public $rows='';
public $tableindex='';
function includeFields($fields, $res){
	
	if(is_array($fields) || $fields[0] !=false){		
		foreach($res as $row => $value){
			$save=false;
			unset($cols);
			foreach($value as $key => $value2){	
				if(in_array($key, $fields) ){
					$save=true;	
					$cols[$key]=$value2;				
				}
			}
			if($save==true){
				$rows[]=$cols;
			}
		}
	}else{
		$rows=$res;
	}
	return $rows;
}

//automatically rearange rows to match fields	array
function matchCols($fields,$rows){
	$i=0;
	foreach($fields as $field){
		$orderer[$field]=$i++;
	}

	foreach($rows as $key => $value){
		foreach($value as $key2 => $value2){
			$nrow[$orderer[$key2]] = array($key2 => $value2);
		}
		$nrows[]=$nrow;
	}

	foreach($nrows as $key => &$value){
		ksort($value);	
	}
	unset($value);//important!!

	foreach($nrows as $key => $value){
		foreach($value as $key2 => $value2){
			foreach($value2 as $key3 => $value3){
				$temp[$key3]=$value3;
			}		
		}
		$tablerows[$key] =$temp;
	}
	return $tablerows;
}

function truncDBOut($value,$fix=1,$len=150){
	if(strlen($value) > $len){
		$value=substr($value,0,$len).'...';
	}
	if($fix){
		$value = str_replace("&", "&amp;" ,$value);
		$value = str_replace("<", "&lt;",$value);
	}
	return $value;
}

//Decide if there are one or more params for query, return ? or &
 function reqChar($pageNumber,$link){	
	return ($pageNumber != '' || $link != '' ? $pageNumber.$link.'&':'?');
 }
 

function getHeadArr($getParams,$fields,$pageNumber){  
	$array=[];

	foreach($fields as $field){  
		
		$out='<a class="';
		if(@$_GET['sortfield'] == $field){
			$out.="selectedsort";
		}
		$out.='" href="';
		$out.=$this->reqChar($pageNumber ,($_GET['sortfield'] == $field?$getParams['sortfieldrevord']:$getParams['sortfield']));
		$out.='sortfield='.$field.'">';
		$out.=strtoupper($field).'</a>';
		$array[]=$out;
	}

	return $array;
}  
function getRowsArr($tablerows){  
//output rows
	$rows=array();

	foreach($tablerows as $key => $value){
		
		$dout=[];
		foreach($value as $key2 => $value2){			
			//$dout='<textarea name="'.$key2.'[]">';
			$dout[$key2]=$this->truncDBOut($value2);	
			//$dout.='</textarea>';			
		}
		$rows[]=$dout;		
	}
	return $rows;
}  

function setHead($headarray,$html){
	
	foreach($headarray as $value){
		$html.="<th>$value</th>";		
	}
	$this->head="<tr>$html</tr>";
}

function addRow($str,$rowarray){
	$html="$str";
	if(is_array($rowarray)){
		foreach($rowarray as $value){
			$html.="<td>$value</td>";		
		}
	}else{
		$html='<td></td>';
	}
	$this->rows.="<tr>$html</tr>";
}

function render($attr=''){
	$table='<table '.$attr.'><thead>';	
	$table.=$this->head.'</thead><tbody>';	
	$table.=$this->rows;
	return $table.="</tbody></table>";
}


}



/*

function outputHead($getParams,$fields,$pageNumber){  
	$allOut='<th></th>';
	
	foreach($fields as $field){  
		$out='<th>';
		$out.='<a class="';
		if(@$_GET['sortfield'] == $field){
			$out.="selectedsort";
		}
		$out.='" href="';
		$out.=$this->reqChar($pageNumber ,($_GET['sortfield'] == $field?$getParams['sortfieldrevord']:$getParams['sortfield']));
		$out.='sortfield='.$field.'">';
		$out.=strtoupper($field).'</a>';
		$out.='</th>'; 
		$allOut.=$out;
	}
	$out='<tr>'.$allOut.'</tr>';
	return $out;
}  

function outputRows($tableindex,$tablerows){

	//output rows
	$allOut='';

	foreach($tablerows as $key => $value){
		$rout="<tr>
		";		
		$rout.="<td><input type=\"checkbox\" name=\"indexSelected[]\" value=\"".$value[$tableindex]."\"></td>";

		foreach($value as $key2 => $value2){
			$dout='<td class="'.($tableindex == $key2 ? 'primary-index' :'').'"><textarea name="'.$key2.'[]">';
			$dout.=$this->truncDBOut($value2);	
			$dout.='</textarea></td>';
			$rout.=$dout;
		}
		
		$rout.= "</tr>
		"; 
		$allOut.=$rout;
	}
	return $allOut;
}
*/




