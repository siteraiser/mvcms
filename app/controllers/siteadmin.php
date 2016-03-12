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
class siteAdmin extends requestHandler{

	public function index()
	{//no sub matches goes here
		$this->loadModel('login_model');		
		$data['content']=$this->login_model->check();
		$this->addView('siteadmin/header');
		$this->addView('default',$data);
		$this->addView('siteadmin/footer');
	}
	
	public function logout()
	{
		$this->loadModel('logout_model');		
		$data['content']=$this->logout_model->logout();
		$this->addView('siteadmin/header');
		$this->addView('default',$data);
		$this->addView('siteadmin/footer');
	}
	private function noCache(){
		header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
	}
	public function userCheckIfAdmin(){
		$user=(isset($_SESSION[SESSION_PREFIX]['user'])) ? $_SESSION[SESSION_PREFIX]['user'] : null;
		if($user && ($user->isAdmin() /*|| $user->canEditArticle($this->content_model)*/)){	
		}else{
			header("Location: ".$this->base_url."siteadmin");
			exit;
		}	
	}	
	public function content(){
		$data['title']='Manage Articles';		
		$this->noCache();
		//check for user
		$this->userCheckIfAdmin();


		//Load editarticle controller
		if(@$this->url_segments[2] == 'edit' || @$this->url_segments[2] == 'add'  ){
			$this->editarticle($user);
			return;
		}
		
		
		$this->loadModel('siteadmin/content_model');	
		$this->loadModel('search_model');//Update search index for site search		
		//Delete records from pages or editpages
		if($_POST["submit"] == "Delete Checked" || $_POST["submit"] == "Delete Record"){
			foreach(@$_REQUEST['indexSelected'] as $key => $value){
				$data['messages'].=$this->content_model->delete($value);
				$data['messages'].='<br>';				
			}
		
			$this->search_model->updateSearch();
		}	
	
		//Sorting and pagination--
		$this->loadModel('pagination_model');
		$this->loadModel('siteadmin/gettable/table');
		$this->loadModel('siteadmin/gettable/sort');
		$this->loadModel('siteadmin/gettable/database');


		/*******************************/
		/* Set Program Variables Here: */
		/*******************************/

		$numresults=10;//Default number of records per page 
		$numpagelinks='3';//pagination numbered links ahead and behind, not total clickable.
		$get_var='page';
		$firstlast=true;
		$prevnext=true;

		/*****************************/ 
		/**** Instantiate classes ****/
		/*****************************/

		$table = $this->table;
		$sort = $this->sort;
		$database = $this->database;

		$database->dbtable = 'content';//Default db table
		$sort->sortfield = 'id';//Default sort field
		$sort->sort='desc';
		$sort->datefield = 'date';//Default

		//--Table Fields -- used to include and order the db table fields in the html table

		//Set fields to false (gets all db fields) or define array to specify table columns
		$fields=false;//array("id","article","date","date2","date3","content");///false;///array("id","date","name","categoryIds","type");
		if($fields == false){
			$returnfields='*';//Specify database return fields
		}else{
			$returnfields=implode(',',$fields);
		}

		//Used to dynamically add to url params, for new inputs
		$sortFields = array('sort', 'sortfield', 'search', 'searchfield','resultsppg','dbtable','startdate','enddate','datefield','modifier');// %like etc..

		$requested_uri = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);//pagination class

		if(isset($_GET['dbtable'])){
			$database->dbtable=$_GET['dbtable'];//maybe add security check here!!!
		}

		//use defaults if page number and sortfield are the same as the last request's and there are no new GET values.
		$sort->init();

		//Pager---------
		$pageNumber='';
			if(isset($_GET[$get_var]) ){
				$pageNumber="?$get_var=".$_GET[$get_var];		
			}

		//Creates the query string for the links, makes a new array variable used to exclude each var from 'search' url string
		//Get parameters minus page, and build link_params also sets $sort->get_params for the hidden form inputs.
		$link_params = $sort->getParameters($sortFields,$link_params='',$pageNumber);

		$query=$sort->buildQuery($database->dbtable,$returnfields);

		
		//Define pagination vars 
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
		
		
		$pageconfig['query']=$query['sql'];
		$pageconfig['query_array']=$query['sql_array'];
		$pageconfig['link_url']=$requested_uri; 
		$pageconfig['link_params']=$link_params; 
		$pageconfig['num_results']=(isset($_GET['resultsppg'])&&$_GET['resultsppg']!=''? intval($_GET['resultsppg']):10);//default set to 10
		$pageconfig['num_links'] = $numpagelinks;//ahead and behind
		$pageconfig['get_var']=$get_var; 
		$pageconfig['prevnext']=$prevnext;
		$pageconfig['firstlast']=$firstlast;
				
		/*Paginate!*/
		$this->pagination_model->paginate($pageconfig);

		$res=$this->pagination_model->results;
		$data['currentpage']=$this->pagination_model->page_links;
		$data['totalrecords']=$this->pagination_model->total_records;
		//end of pagination

		$rows=$table->includeFields($fields, $res);
		//if fields var is not specified by being set to false
		foreach($rows as $row => $value){
			$fields=array_keys($value);
		}
		if($fields === false && count($res) < 1 ){
			$fields = $database->getCols($database->dbtable);
		}
		$data['fields'] = $fields;
		$data['datefields'] = $database->getDateCols($database->dbtable);
		
			
		//Extra table columns, get table index and send to table for later use in retrieving index id
		$table->tableindex = $database->getIndex();


		$headArray=$table->getHeadArr($sort->get_params,$fields,$pageNumber);	
		//Set head and provide any additional headers at beginning
		$table->setHead($headArray,$html='<th></th><th>action</th>');
		
		$rowsArray=$table->getRowsArr($table->matchCols($fields,$rows));	
		

		//Provide xtra columns to beginning of each row here... Set row id by using the table's index column name to find it within the rows. Add the rest of the rows.
		foreach($rowsArray as $row){
		$str="<td><input type=\"checkbox\" name=\"indexSelected[]\" value=\"".$row[$table->tableindex]."\"></td>
		
		<td><a href=\"".$this->base_url."siteadmin/content/edit?".$get_var.'='.$_GET[$get_var].$link_params."&article=".$row[$table->tableindex]."\">Edit</a></td>		
		";
			$table->addRow($str,$row);
		}
		
		$data['hiddeninputs'] = $sort->hiddenInputs($fields,$pageNumber,$get_var);		
		$data['tableHTML'] = $table->render($attr='class="table table-condensed table-bordered table-striped"');


		$this->addView('siteadmin/header');
		$this->addView('siteadmin/content',$data);
		$this->addView('siteadmin/footer');
	}
	
	
	
	public function editarticle($user){
	
		$this->noCache();
		$this->loadModel('pagination_model');
		$this->loadModel('siteadmin/content_model');	
		$this->loadModel('search_model');//Update search index for site search
		$this->loadModel('siteadmin/menu_model');	
		$user=(isset($_SESSION[SESSION_PREFIX]['user'])) ? $_SESSION[SESSION_PREFIX]['user'] : null;
		$this->content_model->init();
		if($user && ($user->isAdmin() || $user->canEditArticle($this->content_model))){	///have to be an admin to get this far, && canEdit might be of use
		}else{
			header("Location: ".$this->base_url."siteadmin");
			exit;
		}

		/////////////
		
		
		
		if(!empty($_POST['id'])){
			$data["articleid"] = $_POST['id'];
		}else if(!empty($_GET['article'])){
			$data["articleid"] = $_GET['article'];		
		}
		if (isset($this->url_segments[2]) && $this->url_segments[2] == 'add'){			
			$data["add"]=true;
			$data["title"]="Add Articles";
		}

		if(isset($this->url_segments[2]) && $this->url_segments[2] == 'edit'){	
		$data["add"]=false;	

			if($_POST["update"] == "0"){
				$data["articleid"]=$this->content_model->insert();	
				$this->search_model->updateSearch();				
			}else if($_POST["update"] == "1"){
				$data["title"]="Update Articles";
				$data['messages'] ='update id:'.$_POST["id"] .'<br>';
				$data['messages'].= $this->content_model->update();
				$this->search_model->updateSearch();
				$data["add"]=false;				
			}
		
		}
		

				
		$data['menus']=$this->menu_model->getMenus();
		$data['article']=$this->content_model->getAllById('content',$data["articleid"]);
		
		
		//Build get params for back to table button
		$data['back_link']='?';
		$amp='';
		foreach ($_GET as $key => $value){
			$getvars = array('page','sort','sortfield','search','searchfield','resultsppg','startdate','enddate','datefield','date','modifier');
			if(in_array($key,$getvars)){
				$data['back_link'].=$amp;
				$data['back_link'].=$key.'='.$value;//page=2&sort=desc&sortfield=id&search=&searchfield=id&resultsppg=&startdate=&enddate=&datefield=date&modifier=
				$amp='&amp;';
			}
		}
				
		$this->addView('siteadmin/header',$data);

		$this->addView('siteadmin/article',$data);

		$this->addView('siteadmin/footer');	
	}
	
	public function ajaxpaging(){//for content / article editpage
		$this->loadModel('pagination_model');
		//Define pagination vars 
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
		
		//Get current page assignments		
		$pageconfig['query']="SELECT * FROM pages WHERE FIND_IN_SET(?, pages.articleids) OR pages.articleids=?";
		$pageconfig['query_array']=array( $_GET['aid'], $_GET['aid']);
		$pageconfig['link_url']='ajaxpaging'; 
		$pageconfig['link_params']=''; 
		$pageconfig['num_results']=5;//default set to 10
		$pageconfig['num_links'] = 3;//ahead and behind
		$pageconfig['get_var']='pid'; 
		$pageconfig['prevnext']=true;
		$pageconfig['firstlast']=true;
				
		/*Paginate!*/
		$this->pagination_model->paginate($pageconfig);
		$rows=$this->pagination_model->results;
		//$data['totalrecords']=$this->pagination_model->total_records;
		if(count($rows)!=0){
				$links='Article assigned to the following pages:<hr>';
			foreach ($rows as $row){
				$path=($row['categoryname']==''?'':$row['categoryname'].'/').$row['page'];
				$links.='<a href="/siteadmin/pages/edit?record='.$row['id'].'">'.$path.'</a><br>';
			}
		}
		
		$obj = array(); 
		$obj['pages_assigned_to'] = $links;
		$obj['currentpage']=$this->pagination_model->page_links;
		
		$response = json_encode($obj); 
		print $response;
		exit;
	}
	
	
	
	
	
	
	public function routing(){	
		$this->userCheckIfAdmin();
		$this->noCache();
		$data['title']='Edit Routing File';
		$this->loadModel('siteadmin/routes_model');
		if($_POST['routes']){
			$this->routes_model->putRoutes();
		}	
		$data['routes']=$this->routes_model->getRoutes();	
	
		$this->addView('siteadmin/header',$data);
		$this->addView('siteadmin/routing',$data);
		$this->addView('siteadmin/footer');	
	}	

	public function categories(){	
		$this->userCheckIfAdmin();
		$this->noCache();	
		$data['title']='Edit Categories';
		
		$this->loadModel('siteadmin/categories_model');		
	
		if(@$_REQUEST['updateCategory']){
			$this->categories_model->updateCategory();
		}	
		if(@$_REQUEST['submit']=='Add Category'){$this->categories_model->addCategory();	}
		if(@$_REQUEST['submit']=='Delete Checked'){$this->categories_model->delCategory();	}
		
		$data['categories']=$this->categories_model->showCategory();		
		
	
		$this->addView('siteadmin/header',$data);
		$this->addView('siteadmin/categories',$data);
		$this->addView('siteadmin/footer');	
	}	

	
	public function pages(){	
		$this->noCache();	
		$data['title']='Edit Pages';		
		//load now to acquire db connection used in User class
		$this->loadModel('siteadmin/categories_model');	
		$this->loadModel('search_model');//Update search index for site search
		//check for user
		$this->userCheckIfAdmin();
		//Use edit or add controller instead
		if(@$this->url_segments[2] == 'edit' || @$this->url_segments[2] == 'add' ){
			$this->editpage($user);
			return;
		}		

		$this->loadModel('siteadmin/pages_model');	

		//Delete records from pages or editpages
		if($_POST["submit"] == "Delete Checked" || $_POST["submit"] == "Delete Record"){

			$this->pages_model->delPage();
			$this->search_model->updateSearch();
		//Update selected pages
		}else if($_POST["submit"] == "Update Checked"){
			$this->pages_model->updateSelected();
			$this->search_model->updateSearch();
		}		
		
		//Sorting and pagination--
		$this->loadModel('pagination_model');
		$this->loadModel('siteadmin/gettable/table');
		$this->loadModel('siteadmin/gettable/sort');
		$this->loadModel('siteadmin/gettable/database');

		/* Connect to Database */

		/*******************************/
		/* Set Program Variables Here: */
		/*******************************/

		$numresults=10;//Default number of records per page 
		$numpagelinks='3';//pagination numbered links ahead and behind, not total clickable.
		$get_var='page';
		$firstlast=true;
		$prevnext=true;

		/*****************************/ 
		/**** Instantiate classes ****/
		/*****************************/

		$table = $this->table;
		$sort = $this->sort;
		$database = $this->database;

		$database->dbtable = 'pages';//Default db table
		$sort->sortfield = 'id';//Default sort field
		$sort->sort='desc';
		$sort->datefield = 'date';//Default

		//--Table Fields -- used to include and order the db table fields in the html table

		//Set fields to false (gets all db fields) or define array to specify table columns
		$fields=false;//array("id","article","date","date2","date3","content");///false;///array("id","date","name","categoryIds","type");
		if($fields == false){
			$returnfields='*';//Specify database return fields
		}else{
			$returnfields=implode(',',$fields);
		}

		//Used to dynamically add to url params, for new inputs
		$sortFields = array('sort', 'sortfield', 'search', 'searchfield','resultsppg','dbtable','startdate','enddate','datefield','modifier');// %like etc..

		$requested_uri = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);//pagination class

		if(isset($_GET['dbtable'])){
			$database->dbtable=$_GET['dbtable'];//maybe add security check here!!!
		}


		//use defaults if page number and sortfield are the same as the last request's and there are no new GET values.
		$sort->init();

		//Pager---------
		$pageNumber='';
			if(isset($_GET[$get_var]) ){
				$pageNumber="?$get_var=".$_GET[$get_var];		
			}

		//Creates the query string for the links, makes a new array variable used to exclude each var from 'search' url string
		//Get parameters minus page, and build link_params also sets $sort->get_params for the hidden form inputs.
		$link_params = $sort->getParameters($sortFields,$link_params='',$pageNumber);

		$query=$sort->buildQuery($database->dbtable,$returnfields);

		
		
		
		
/* Material Design
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
	
		
*/			
		
		
		
	/*Bootstrap*/	
		
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
		
		//public $pdo=$this->pdo;
		
		$pageconfig['query']=$query['sql'];
		$pageconfig['query_array']=$query['sql_array'];
		$pageconfig['link_url']=$requested_uri; 
		$pageconfig['link_params']=$link_params; 
		$pageconfig['num_results']=(isset($_GET['resultsppg'])&&$_GET['resultsppg']!=''? intval($_GET['resultsppg']):10);//default set to 10
		$pageconfig['num_links'] = $numpagelinks;//ahead and behind
		$pageconfig['get_var']=$get_var; 
		$pageconfig['prevnext']=$prevnext;
		$pageconfig['firstlast']=$firstlast;
				
		/*Paginate!*/
		$this->pagination_model->paginate($pageconfig);

		$res=$this->pagination_model->results;
		$data['currentpage']=$this->pagination_model->page_links;
		$data['totalrecords']=$this->pagination_model->total_records;
		

		$rows=$table->includeFields($fields, $res);
		//if fields var is not specified by being set to false
		foreach($rows as $row => $value){
			$fields=array_keys($value);
		}
		if($fields === false && count($res) < 1 ){
			$fields = $database->getCols($database->dbtable);
		}
		$data['fields'] = $fields;
		$data['datefields'] = $database->getDateCols($database->dbtable);
		
			
		//Extra table columns, get table index and send to table for later use in retrieving index id
		$table->tableindex = $database->getIndex();


		$headArray=$table->getHeadArr($sort->get_params,$fields,$pageNumber);	
		//Set head and provide any additional headers at beginning
		$table->setHead($headArray,$html='<th></th><th>action</th>');
		
		$rowsArray=$table->getRowsArr($table->matchCols($fields,$rows));	

		$categories = $this->categories_model->showCategory();

		//Provide xtra columns to beginning of each row here... Set row id by using the table's index column name to find it within the rows. Add the rest of the rows.
		foreach($rowsArray as $row){
			$str="<td><input type=\"checkbox\" name=\"indexSelected[]\" value=\"".$row[$table->tableindex]."\"></td>
			
			<td><a href=\"".$this->base_url."siteadmin/pages/edit?".$get_var.'='.$_GET[$get_var].$link_params."&record=".$row[$table->tableindex]."\">Edit</a></td>		
			";
			foreach($row as $key => $cell){
				if($key == 'categoryname'){
				
					$row[$key]='<select name="'.$key.'['.$row[$table->tableindex].']">';

					$row[$key].=$this->selectedOption($categories,$cell);
					$row[$key].='</select>';
					
				}else if($key == 'priority'){
					$row[$key]='<input type="text" style="max-width:50px;" name="'.$key.'['.$row[$table->tableindex].']" value="'.$cell.'">';
				}else if($key == 'page'){
					$row[$key]='<input type="text" name="'.$key.'['.$row[$table->tableindex].']" value="'.$cell.'">';
				}else if($key == 'controller'){
					$row[$key]='<input type="text" name="'.$key.'['.$row[$table->tableindex].']" value="'.$cell.'">';
				}else if($key == 'published'){
					$row[$key]="<input type=\"checkbox\" name=\"".$key."[".$row[$table->tableindex]."]\" ".($cell == 1?'checked':'')." value=\"".$cell."\">";
				}else if($key == 'positions'){
					$row[$key]=$table->truncDBOut($cell,1,20);
				}else if($key == 'meta'){
					$row[$key]=$table->truncDBOut($cell,0,20);
				}
				
			}

			$table->addRow($str,$row);
		}
		
		$data['hiddeninputs'] = $sort->hiddenInputs($fields,$pageNumber,$get_var);		
		$data['tableHTML'] = $table->render($attr='class="table table-condensed table-bordered table-striped"');

		
	
		$this->addView('siteadmin/header',$data);
		$this->addView('siteadmin/pages',$data);
		$this->addView('siteadmin/footer');	
	}	
	
	
	public function editpage($user){
		/** this controller is loaded from the pages controller */
		$this->noCache();
		$this->loadModel('siteadmin/pages_model');	
		$this->loadModel('siteadmin/content_model');
		$this->loadModel('search_model');//Update search index for site search
		
	//	$this->userCheckIfAdmin();
		
		
		if(!empty($_POST['id'])){
			$data["articleid"] = $_POST['id'];
		}else if(!empty($_GET['record'])){
			$data["articleid"] = $_GET['record'];		
		}
		
		
		
		if (isset($this->url_segments[2]) && $this->url_segments[2] == 'add'){			
			$data["add"]=true;
			$data["title"]="Add Pages";
			
			//Determine if a template has been selected
			if($_POST["submit"] == "selectedTemplate"){
				$data['page']['template'] = $_POST['template'];
			}
			
		}

		if(isset($this->url_segments[2]) && $this->url_segments[2] == 'edit'){	
		$data["add"]=false;	

			if($_POST["submit"] == "Create"){
				var_dump($_POST);
				$data["articleid"]=$this->pages_model->insert();	
				$this->search_model->updateSearch();				
			}else if($_POST["submit"] == "Update"){
				$data["title"]="Update Pages";
				$data['messages'].= $this->pages_model->updateSingle($data["articleid"]);
				$this->search_model->updateSearch();
				$data["add"]=false;				
			}
		
			//$data['messages'] ='update id:'.$_POST["id"] .'<br>';			
			$data["included_list"]=$this->pages_model->getIncludedList($data["articleid"]);		
			$data['page']=$this->pages_model->getAllById('pages',$data["articleid"]);
		}	
		
		
		$data['article_list']=$this->pages_model->getArticleList();

		/* 
		Template folder set here!!---------
		*/
		$dir = new DirectoryIterator($this->doc_root."/app/views/templates");
		foreach ($dir as $fileinfo) {
		if($fileinfo != '.' && $fileinfo != '..')
			$templates[]= pathinfo($fileinfo->getFilename(), PATHINFO_FILENAME);
		}
		
		$data['templates']=$templates;
		
		$template=$data['page']['template'];
		

		
		
		$dir = new DirectoryIterator($this->doc_root."/app/views/templates/$template");
		foreach ($dir as $fileinfo) {
		if($fileinfo != '.' && $fileinfo != '..')
			$views[]= $template.'/'. pathinfo($fileinfo->getFilename(), PATHINFO_FILENAME);
		}
		$data['views']=$views;//
		$data['categories'] = $this->categories_model->showCategory();
		
		
		//Back to table as it was left
		$data['back_link']='?';
		$amp='';
		foreach ($_GET as $key => $value){
			$getvars = array('page','sort','sortfield','search','searchfield','resultsppg','startdate','enddate','datefield','date','modifier');
			//echo $key;
			if(in_array($key,$getvars)){
				$data['back_link'].=$amp;
				$data['back_link'].=$key.'='.$value;//page=2&sort=desc&sortfield=id&search=&searchfield=id&resultsppg=&startdate=&enddate=&datefield=date&modifier=
				$amp='&amp;';
			}
		}
	
		
		$this->addView('siteadmin/header',$data);

		$this->addView('siteadmin/editpage',$data);

		$this->addView('siteadmin/footer');	
	}
	
	
	public function menueditor(){//$user
		$this->userCheckIfAdmin();
		$this->noCache();
		$this->loadModel('siteadmin/menu_model');	
			
		$data['title']='Edit Menu';
	
		if(!empty($_POST['menuid'])){
			$data["menuid"] = $_POST['menuid'];
		}else if(!empty($_GET['menuid'])){
			$data["menuid"] = $_GET['menuid'];		
		}
	
	
		if(@$_REQUEST['submit']=='Update Menu'){
			$articleIds=$this->menu_model->updateMenu($data["menuid"]);			
			//print '<pre>';
		//	print_r($articleIds);
		//	print '</pre>';
		}	
		if(@$_REQUEST['submit']=='Add Menu'){echo $this->menu_model->addMenu();	}
		if(@$_REQUEST['delete']=='Delete'){$this->menu_model->delMenu($data["menuid"]);	}
		
		
		$data['menu']=$this->menu_model->getMenuById($data["menuid"]);
		$data['menus']=$this->menu_model->getMenus();		
	
	
		$this->addView('siteadmin/header',$data);

		$this->addView('siteadmin/menueditor',$data);

		$this->addView('siteadmin/footer');	
	}
	
		
	public function users(){//$user
	
		$this->loadModel('siteadmin/user_model');	
		$admins=$this->user_model->getAdmins();		
		
		$user=(isset($_SESSION[SESSION_PREFIX]['user'])) ? $_SESSION[SESSION_PREFIX]['user'] : null;
		if($user && $user->isAdmin() || count($admins) ==0){	
		}else{
			header("Location: ".$this->base_url."siteadmin");
			exit;
		}
		//Use edit or add controller instead
		if(@$this->url_segments[2] == 'edit' || @$this->url_segments[2] == 'add' ){
			$this->edituser($user);
			return;
		}		
		$this->noCache();
		
			
		$data['title']='Edit users';
		//Delete records from pages or editpages
		if($_POST["submit"] == "Delete Checked" || $_POST["submit"] == "Delete Record"){

			$articleIds = $this->user_model->delUser();
			
			$this->loadModel('siteadmin/content_model');	
			$this->loadModel('search_model');//Update search index for site search

			foreach(@$articleIds as $key => $id){
				$data['messages'].=$this->content_model->delete($id);//deletes articles, removes from pages and clears those caches as well if any
				$data['messages'].='<br>';				
			}
		
			$this->search_model->updateSearch();
			
		//Update selected pages
		}else if($_POST["submit"] == "Update Checked"){
			$this->user_model->updateSelected();
		}		
		
		//Sorting and pagination--
		$this->loadModel('pagination_model');
		$this->loadModel('siteadmin/gettable/table');
		$this->loadModel('siteadmin/gettable/sort');
		$this->loadModel('siteadmin/gettable/database');

		/* Connect to Database */

		/*******************************/
		/* Set Program Variables Here: */
		/*******************************/

		$numresults=10;//Default number of records per page 
		$numpagelinks='3';//pagination numbered links ahead and behind, not total clickable.
		$get_var='page';
		$firstlast=true;
		$prevnext=true;

		/*****************************/ 
		/**** Instantiate classes ****/
		/*****************************/

		$table = $this->table;
		$sort = $this->sort;
		$database = $this->database;

		$database->dbtable = 'users';//Default db table
		$sort->sortfield = 'id';//Default sort field
		$sort->sort='desc';
		$sort->datefield = 'dateAdded';//Default

		//--Table Fields -- used to include and order the db table fields in the html table

		//Set fields to false (gets all db fields) or define array to specify table columns
		$fields=false;//array("id","article","date","date2","date3","content");///false;///array("id","date","name","categoryIds","type");
		if($fields == false){
			$returnfields='*';//Specify database return fields
		}else{
			$returnfields=implode(',',$fields);
		}

		//Used to dynamically add to url params, for new inputs
		$sortFields = array('sort', 'sortfield', 'search', 'searchfield','resultsppg','dbtable','startdate','enddate','datefield','modifier');// %like etc..

		$requested_uri = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);//pagination class

		if(isset($_GET['dbtable'])){
			$database->dbtable=$_GET['dbtable'];//maybe add security check here!!!
		}
		//use defaults if page number and sortfield are the same as the last request's and there are no new GET values.
		$sort->init();

		//Pager---------
		$pageNumber='';
			if(isset($_GET[$get_var]) ){
				$pageNumber="?$get_var=".$_GET[$get_var];		
			}

		//Creates the query string for the links, makes a new array variable used to exclude each var from 'search' url string
		//Get parameters minus page, and build link_params also sets $sort->get_params for the hidden form inputs.
		$link_params = $sort->getParameters($sortFields,$link_params='',$pageNumber);

		$query=$sort->buildQuery($database->dbtable,$returnfields);


	/*Bootstrap*/	
		
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

		$pageconfig['query']=$query['sql'];
		$pageconfig['query_array']=$query['sql_array'];
		$pageconfig['link_url']=$requested_uri; 
		$pageconfig['link_params']=$link_params; 
		$pageconfig['num_results']=(isset($_GET['resultsppg'])&&$_GET['resultsppg']!=''? intval($_GET['resultsppg']):10);//default set to 10
		$pageconfig['num_links'] = $numpagelinks;//ahead and behind
		$pageconfig['get_var']=$get_var; 
		$pageconfig['prevnext']=$prevnext;
		$pageconfig['firstlast']=$firstlast;
				
		/*Paginate!*/
		$this->pagination_model->paginate($pageconfig);

		$res=$this->pagination_model->results;
		$data['currentpage']=$this->pagination_model->page_links;
		$data['totalrecords']=$this->pagination_model->total_records;
		

		$rows=$table->includeFields($fields, $res);
		//if fields var is not specified by being set to false
		foreach($rows as $row => $value){
			$fields=array_keys($value);
		}
		if($fields === false && count($res) < 1 ){
			$fields = $database->getCols($database->dbtable);
		}
		$data['fields'] = $fields;
		$data['datefields'] = $database->getDateCols($database->dbtable);
		
			
		//Extra table columns, get table index and send to table for later use in retrieving index id
		$table->tableindex = $database->getIndex();


		$headArray=$table->getHeadArr($sort->get_params,$fields,$pageNumber);	
		//Set head and provide any additional headers at beginning
		$table->setHead($headArray,$html='<th></th><th>action</th>');
		
		$rowsArray=$table->getRowsArr($table->matchCols($fields,$rows));	


	/*
		<td><a href=\"".$this->base_url."admin/single?".$get_var.'='.$_GET[$get_var].$link_params."&article=".$row[$table->tableindex]."\"><img style=\"max-height:50px;width:auto;max-width:50px\" src=\"".$this->base_url."images/virtualimages/sm_".$row['filename']."\"/></a></td>
		*/
		//Provide xtra columns to beginning of each row here... Set row id by using the table's index column name to find it within the rows. Add the rest of the rows.
		foreach($rowsArray as $row){
			$str="<td><input type=\"checkbox\" name=\"indexSelected[]\" value=\"".$row[$table->tableindex]."\"></td>
			
			<td><a href=\"".$this->base_url."siteadmin/users/edit?".$get_var.'='.$_GET[$get_var].$link_params."&record=".$row[$table->tableindex]."\">Edit</a></td>		
			";
			foreach($row as $key => $cell){
				if($key == 'userType'){
				
					$row[$key]='<select name="'.$key.'['.$row[$table->tableindex].']">';

					$row[$key].=$this->selectedOption(array('admin'=>'admin','author'=>'author','public'=>'public'),$cell);
					$row[$key].='</select>';
					
				}else if($key == 'username'){
					$row[$key]='<input type="text" name="'.$key.'['.$row[$table->tableindex].']" value="'.$cell.'">';
				}else if($key == 'email'){
					$row[$key]='<input type="text" name="'.$key.'['.$row[$table->tableindex].']" value="'.$cell.'">';
				}else if($key == 'pass'){
					$row[$key]='<input type="text" name="'.$key.'['.$row[$table->tableindex].']" value="'.$cell.'">';
				}else if($key == 'password'){
					$row[$key]='<input type="text" name="'.$key.'['.$row[$table->tableindex].']" value="'.$cell.'">';
				}else if($key == 'status'){
					$row[$key]="<input type=\"checkbox\" name=\"".$key."[".$row[$table->tableindex]."]\" ".($cell == 1?'checked':'')." value=\"".$cell."\">";
				}else if($key == 'positions'){
					$row[$key]=$table->truncDBOut($cell,1,20);
				}else if($key == 'meta'){
					$row[$key]=$table->truncDBOut($cell,0,20);
				}
				
			}

			$table->addRow($str,$row);
		}
		
		$data['hiddeninputs'] = $sort->hiddenInputs($fields,$pageNumber,$get_var);		
		$data['tableHTML'] = $table->render($attr='class="table table-condensed table-bordered table-striped"');
	
		$this->addView('siteadmin/header',$data);
		$this->addView('siteadmin/users',$data);
		$this->addView('siteadmin/footer');	
	}
	
	public function edituser($user){
		/** this controller is loaded from the pages controller */
		$this->noCache();
		$this->loadModel('siteadmin/user_model');	
		
		if(!empty($_POST['id'])){
			$data["userid"] = $_POST['id'];
		}else if(!empty($_GET['record'])){
			$data["userid"] = $_GET['record'];		
		}
		
		
		
		if (isset($this->url_segments[2]) && $this->url_segments[2] == 'add'){			
			$data["add"]=true;
			$data["title"]="Add Users";			
		}

		if(isset($this->url_segments[2]) && $this->url_segments[2] == 'edit'){	
		$data["add"]=false;	

			if($_POST["submit"] == "Create"){
				//var_dump($_POST);
				$data["userid"]=$this->user_model->addUser();		
				
			}else if($_POST["submit"] == "Update"){
				$data["title"]="Update Users";
				$data['messages'].= $this->user_model->updateUser($data["userid"]);
				$data["add"]=false;				
			}
		
			$data['user']=$this->user_model->getUserById($data["userid"]);
		}	

		//Back to table as it was left
		$data['back_link']='?';
		$amp='';
		foreach ($_GET as $key => $value){
			$getvars = array('page','sort','sortfield','search','searchfield','resultsppg','startdate','enddate','datefield','date','modifier');
			if(in_array($key,$getvars)){
				$data['back_link'].=$amp;
				$data['back_link'].=$key.'='.$value;//page=2&sort=desc&sortfield=id&search=&searchfield=id&resultsppg=&startdate=&enddate=&datefield=date&modifier=
				$amp='&amp;';
			}
		}		
		$this->addView('siteadmin/header',$data);
		$this->addView('siteadmin/user',$data);
		$this->addView('siteadmin/footer');	
	}
	public function config(){
		if($this->pdo !==''){
				$stmt=$this->pdo->prepare("SHOW TABLES LIKE 'pages'");
				$stmt->execute(array()); 				
				$count=$stmt->rowCount($result);
				if($count !== 1){
					include_once('app/system/config/tablesetup.php');
					$this->pdo->exec($table_setup);
					echo 'Tables created, create an admin account.';
					//header("Location: ".$this->base_url."siteadmin/config");					
				//	exit;
				}
			
			$this->loadModel('siteadmin/user_model');	
			$admins=$this->user_model->getAdmins();	
		}
		$user=(isset($_SESSION[SESSION_PREFIX]['user'])) ? $_SESSION[SESSION_PREFIX]['user'] : null;
		if($user && $user->isAdmin() || count($admins) ==0){						
		}else{
			header("Location: ".$this->base_url."siteadmin");
			exit;
		}

		$this->loadModel('siteadmin/config_model');	
		$site=$this->config_model->getSiteXML();
		$db=$this->config_model->getDBXML();
		if($_POST["submit"] == "Update"){
			$this->config_model->updateSiteXML($site);
			$this->config_model->updateDBXML($db);
			header("Location: ".$site->baseurl."siteadmin/config");
			die;
		}
		$data['site'] = $site;
		$data['db'] = $db;	
		
		$this->addView('siteadmin/header',$data);
		$this->addView('siteadmin/siteconfig',$data);
		$this->addView('siteadmin/footer');	
	
	}
}
