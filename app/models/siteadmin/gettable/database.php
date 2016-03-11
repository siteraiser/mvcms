<?php 
class database extends requestHandler{

public $dbtable ='';
//public $primaryIndex =''; !!!might be of use

function getIndex(){
	$stmt = $this->pdo->prepare("show index from $this->dbtable where `Key_name` = 'PRIMARY'");
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return  $res[0]['Column_name'];
} 

function getCols($dbtable){
	$stmt = $this->pdo->prepare("SHOW COLUMNS FROM $dbtable");
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);	
	foreach ($res as $row => $value){
		$cols[]=$value['Field'];
	}
	return $cols;
}
function getDateCols($dbtable){

	$stmt = $this->pdo->prepare("SHOW FIELDS FROM $dbtable");
	$stmt->execute();
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);		

	foreach ($res as $row => $value){
		if($value['Type'] == "timestamp" ||
		$value['Type'] == "date" || //0000-00-00
		$value['Type'] == "datetime" ||  //||0000-00-00 00:00:00
		//$value['Type'] == "time" ||//
		$value['Type'] == "year(4)" //0000 
		){
			$datefields[]=$value['Field'];			
		}		
	}	
	
	
	return $datefields;	
}	

function getImage($id){
	$stmt = $this->pdo->prepare("select filename from $this->dbtable where id = ?");//public $primaryIndex =''; !!!might be of use
	$stmt->execute(array($id));
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return  $res[0]['filename'];
} 
function getSlug($id){
	$stmt = $this->pdo->prepare("select slug from $this->dbtable where id = ?");//public $primaryIndex =''; !!!might be of use
	$stmt->execute(array($id));
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return  $res[0]['slug'];
} 
	/*
	if(count($datefields) != 0)
	
	return $datefields;
	
	else
	
	
	//no timestamp columns found, search for valid dates instead
	$stmt=$pdo->prepare("SELECT * FROM $dbtable LIMIT 100");//check first 100
	$stmt->execute(array());	
	$daterows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//test database column fields for any valid dates
	foreach($daterows as $row => $value){
		foreach($value as $key => $value1){
			if($this->isDate($value1)){
				if(!@in_array($key,$datefields)){
					$datefields[]=$key;
				}
			}
		}
	}
	
function isDate($date){	
	$myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
	if ($myDateTime instanceof DateTime) {
		return 1;
	}
 }
 
 
 
 */	
	/*	echo '<pre>';
    print_r($res);
    echo  '</pre>';*/
	
	
	

	
	



}








