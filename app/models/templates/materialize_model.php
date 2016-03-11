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
class materialize_model extends requestHandler{
	public function respImgs($html){
		if(strlen($html)!=0){

		$html='<div class="dynamic-content">'.$html.'</div>';//

		$doc = new DOMDocument();
		@$doc->loadHTML($html);

		$doc->removeChild($doc->doctype); 
		$doc->replaceChild($doc->firstChild->firstChild->firstChild, $doc->firstChild);

		//fix images
		$imgs = $doc->getElementsByTagName('img');	
		for ($i = $imgs->length; --$i >= 0; ) {		  
			$img = $imgs->item($i);
			$style = $img->getAttribute('style');
			$pattern = "/height:\s*\d*\s*(px|%);*/" ;
			$style = preg_replace($pattern,"", $style) ;
			$img->setAttribute('style',$style);
			$img->setAttribute('class','responsive-img');		
		}
		
		//fix videos, all Iframes are targeted, may need to add a check for url...
		$frames = $doc->getElementsByTagName('iframe');	
		$d=$doc->createElement('div');
		$d->setAttribute('class','video-container no-controls'); 

		foreach ($frames AS $frame) {
			$div = $d->cloneNode();
			$frame->parentNode->replaceChild($div,$frame);
			$div->appendChild($frame);
		}
			
		$html = $doc->saveHTML();
		$html =substr($html, 29, -7); 
		return $html;

		}
	}
	
	
		
	public $testurl='';
	public $urls=[];
	public $checklist='';
	public $out='';
	//For drop menu
	public $i=0;
	public function getMenuByName($name){
       	$query="SELECT * FROM menus WHERE name = :name";
		$stmt=$this->pdo->prepare($query);
		$stmt->execute(array(':name'=>$name)); 					
		if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {	
			return $row;	
		}
	}	
	public function getMenu($menu,$type){
		$this->out='';
		//$phpArray=json_decode('[{"url":"/","name":"home"},{"url":"/testpage","name":"products <br>and services","children":[{"url":"/products-and-services/design","name":"design","children":[{"url":"/products-and-services/design/graphics","name":"graphics"},{"url":"/products-and-services/design/web","name":"web"}]},{"url":"/products-and-services/seo","name":"seo"}]}]');
		$res = $this->getMenuByName($menu);
		$phpArray = unserialize($res['data']);
		
		$phpArray=$this->labelObjects($phpArray);
		
		$this->testurl=$this->path;//'/testpage';
		$this->testurl=ltrim($this->testurl,'/');
		$testurls=explode('/',$this->testurl);

		
		$this->checked($phpArray);

		$urlcount = count($testurls);
		$i=0;$urlcheck='';
		while($i < ($urlcount)){
			$urlcheck.='/'.$testurls[$i];
			foreach($this->urls as $id => $url){
				if(str_replace("-", " ", strtolower($url))==str_replace("-", " ", strtolower($urlcheck))){
					$this->checklist[]=$url;		
				}
			}
		$i++;
		}
		if($type=='sidemenu'){
			$this->sidemenu($phpArray);	
			return "<ol class='tree'>".$this->out."</ol>";	
		}else
		if($type=='dropmenu'){
			$this->dropmenu($phpArray);				
			return "<ul id='nav'>".$this->out."</ul>";	
		}
	}	
	
	public function labelObjects($objects){
		foreach($objects as $object){
			$object->skip = 1;			
		}
		return $objects;
	}
	function checked($objects){
		foreach($objects as $object){
			$this->urls[] = $object->url;
			if(isset($object->children)){            
				$this->checked($object->children);            
			}
		}
	}
	function checkedurl($url,$str='checked'){
		if(is_array($this->checklist)){
			if(in_array($url,$this->checklist)){
				return $str;
			}
		}
		return'';
	}		
	public function sidemenu($objects)
	{		
		foreach($objects as $object){

			$href="";$href_close="";
			if($object->url != ''){
				$href="<a href='".$object->url."' ".($object->url == '/'.$this->testurl? 'class="select"':'').">";
				$href_close='</a>';
			}	
			if(isset($object->children)){
				$this->out.= "<li class='waves-effect waves-light'><label for='".$object->name."'>$href".strip_tags($object->name,'')."$href_close</label> <input type='checkbox' ".$this->checkedurl($object->url)." id='".$object->name."' />";
			}else{
				$linsert = '';$label='';$label_close='';
				if(!$object->skip){
					$linsert = " class='file'";
				}else{
					$label="<label for='".$object->name."'>";
					$label_close="</label><input type='checkbox' id='".$object->name."' />";					
				}
				
				$this->out.= "<li class='waves-effect waves-light'".$linsert.">".$label.$href.strip_tags($object->name,'')."$href_close$label_close";	
			}
			if(isset($object->children)){
				 $this->out.= '<ol>';
				$this->sidemenu($object->children);
				$this->out.= '</ol>';
			}
			$this->out.= '</li>';
		}
	}
	

	public function dropmenu($objects)
	{

		foreach($objects as $object){
			$href='';
			if($object->url != ''){
				$href='<a class="waves-effect" href="'.$object->url.'">'.$object->name.'</a>';
			}else{
				$href='<span class="waves-effect">'.$object->name.'</span>';
			}	
			$this->out.= '<li '.$this->checkedurl($object->url, 'class="active"').'>'.$href.(isset($object->children) && !$object->skip?'<img alt="more" height="16" class="arrow" src="/images/arrow.png"/>':'');

			if(isset($object->children)){
				$this->out.= '<ul>';
				$this->dropmenu($object->children);
				$this->out.= '</ul>';
			}			
			$this->out.= '</li>';
		}
	}

	
	
}
