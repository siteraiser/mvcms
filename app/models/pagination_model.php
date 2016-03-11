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
class pagination_model extends requestHandler{
	
	private $pagination_open='<ul class="pagination">';
	private $pagination_close='</ul>';
	private $open="<li>";
	private $close="</li>";
	private $active_open="<li class='disabled'><li class='active' ><a>";
	private $active_close="</a></li></li>";//<span class='sr-only'></span></a></li></li>
	private $next_open="<li>";
	private $next_close="</li>";
	private $prev_open="<li>";
	private $prev_close="</li>";
	private $disabled_left='';	
	private $disabled_right='';		
	
	private $first_open="<li>";
	private $first_close="</li>";	
	private $last_open="<li>";
	private $last_close="</li>";	
	private $prev_chevron_left='&lt;';
	private $next_chevron_right='&gt;';	

	
	/* Material Design
	 <ul class="pagination">
    <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
    <li class="active"><a href="#!">1</a></li>
    <li class="waves-effect"><a href="#!">2</a></li>
    <li class="waves-effect"><a href="#!">3</a></li>
    <li class="waves-effect"><a href="#!">4</a></li>
    <li class="waves-effect"><a href="#!">5</a></li>
    <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
  </ul>

  	private $pagination_open='<ul class="pagination">';
	private $pagination_close='</ul>';
	private $open='<li class="waves-effect">';
	private $close='</li>';
	private $active_open='<li class="active"><a>';
	private $active_close='</a></li>';
	private $next_open='<li class="waves-effect">';
	private $next_close='</li>';
	private $prev_open="<li class="waves-effect">";
	private $prev_close='</li>';
	private $disabled_left='';	
	private $disabled_right='';		
	private $prev_chevron_left=' <li class="disabled"><a><i class="material-icons">chevron_left</i></a></li>';
	private $next_chevron_right=' <li class="disabled"><a><i class="material-icons">chevron_right</i></a></li>';	
	
	private $first_open="<li>";
	private $first_close="</li>";	
	private $last_open="<li>";
	private $last_close="</li>";	
	

	*/

	private $query;
	private $query_array=[];
	private $link_url; 
	private $link_params=''; 
	private $num_results=6;
	private $links = 3;//ahead and behind
	private $get_var='page'; 
	private $prevnext=true;
	private $firstlast=true;
	
	public $total_records='';//if supplied skip	
	public $results;
	public $page_links;
	
	public function init($config = []){
		//Set class config vars
		foreach($config as $key => $value){
			$this->$key = $value;		
		}
	}
	public function paginate($config = []){

	$this->init($config);
	
	//Count Records if total isn't arleady supplied in config, if it is supplied, skip count and query addon later
	$selecting_by_ids=true;
	if($this->total_records == ''){ 
		$selecting_by_ids=false;		
		if($this->query_array !==''){
			$stmt=$this->pdo->prepare($this->query);
			$res = $stmt->execute($this->query_array);
		}else{
			$res = $this->pdo->query($this->query);
		}
		if(is_object($stmt)){
		$this->total_records = $stmt->rowCount($res);		
		}else{
		$this->total_records = 0;
		}	
	}
	
	$total=ceil($this->total_records/$this->num_results);  

	$page_num=intval($_GET[$this->get_var]);
	if($page_num==''){
		$page_num=1;
		$startingrecord=0;
	}else{
		$startingrecord=($page_num*$this->num_results)-$this->num_results;//for db call
	}


//control number of pages / results
	$start = 1;
	//$this->links is half of the total clickable links. 3 links ahead and 3 behind for eg.
	if($page_num > $this->num_links){
		$start = $page_num - $this->num_links;		
		if($start <= 0 ){
			$start =1;
		}	
	}
	$end = $page_num + $this->num_links ;
		
	$currentpage="";	
//Number and check for current page
	for($i=$start; $i<=$end && $i<=$total; $i++){
		if($i==$page_num && $i==$total){
			$currentpage.=$this->active_open.$i.$this->active_close;
		}else if($i==$page_num){
			$currentpage.=$this->active_open.$i.$this->active_close;
		}else{		
			$currentpage.=$this->open."<a href='".$this->link_url."?".$this->get_var."=$i".$this->link_params."'>$i</a>".$this->close;
		}
	}

//Work out prev and next links
	if($total>1 && $this->prevnext != false ){
		if($page_num=="1"){
			$page=$page_num + 1;
			$next=$this->next_open."<a rel='next' href='".$this->link_url."?".$this->get_var."=$page".$this->link_params."' >".$this->next_chevron_right."</a>".$this->next_close;
			$prev=$this->disabled_left;//bootstrap is ''
		}else if($page_num==$total){
			$page=$page_num - 1;
			$next=$this->disabled_right;//bootstrap is ''
			$prev=$this->prev_open."<a rel='prev' href='".$this->link_url."?".$this->get_var."=$page".$this->link_params."' >".$this->prev_chevron_left."</a>".$this->prev_close;			
		}else{
			$next=$this->next_open."<a rel='next' href='".$this->link_url."?".$this->get_var."=". ($page_num + 1) .$this->link_params."' >".$this->next_chevron_right."</a>".$this->next_close;
			$prev=$this->prev_open."<a rel='prev' href='".$this->link_url."?".$this->get_var."=". ($page_num - 1) .$this->link_params."' >".$this->prev_chevron_left."</a>".$this->prev_close;	
		}
	}else{
		$next=$this->disabled_left;//bootstrap is ''
		$prev=$this->disabled_right;//bootstrap is ''
	}
	
//Work out first and last links
	if($total>1 && $this->firstlast != false ){
		$firstlink='';
		$lastlink='';
		$lastlink=$this->last_open."<a href='".$this->link_url."?".$this->get_var."=$total".$this->link_params."'>Last</a>".$this->last_close;
		$lastlink.=$next;
		$firstlink=$prev.$this->first_open."<a href='".$this->link_url."?".$this->get_var."=1".$this->link_params."'>First</a>".$this->first_close;
	}

//Additional behavior for prev / next links - hide or disable prev / next buttons
	if($page_num < ($this->num_links +2) ){
		$firstlink=$prev;
	}
	if($page_num + $this->num_links >= $total){
		$lastlink=$next;
	}
	if($total < $this->num_links + 1 ){
		$firstlink=$prev;
		$lastlink=$next;
	}

//Button Options
	if($this->firstlast==true && $this->prevnext==true){
		$currentpage=$firstlink.$currentpage.$lastlink;	
	}else if($this->firstlast==false && $this->prevnext==true){
		$currentpage=$prev.$currentpage.$next;	
	}else if($this->firstlast==true && $this->prevnext==false){
		$currentpage=$firstlink.$currentpage.$lastlink;
	}	
		
//Perform database query - use computed limit or select by ids in case of custom external ordering
	if($selecting_by_ids==false){		
		$this->query.=" LIMIT $startingrecord,$this->num_results;";
	}
	if($this->query_array !==''){
		$stmt=$this->pdo->prepare($this->query);
		$stmt->execute($this->query_array);	
		$this->results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	$this->page_links=$this->pagination_open.$currentpage.$this->pagination_close;
}

}