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
class blog extends requestHandler{
	public function index($link){
		$data['title']='Blog';
		$data['meta']='<meta name="description" content="Find the latest news here.">';
		$this->loadModel('blog/blog_model');	
	//	$data['content'] = $this->blog_model->getAllByType();

		$this->loadModel('pagination_model');
		//Define pagination vars 
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
					
		//Get current page assignments		
		$pageconfig['query']="SELECT * FROM content WHERE (type='blog' OR type='code') AND published = 1 ORDER BY date DESC";
		$pageconfig['query_array'];
		$pageconfig['link_url']= $link;
		$pageconfig['link_params']=''; 
		$pageconfig['num_results']=2;//default set to 10
		$pageconfig['num_links'] = 3;//ahead and behind
		$pageconfig['get_var']='page'; 
		$pageconfig['prevnext']=true;
		$pageconfig['firstlast']=true;
				/*Paginate!*/
		$this->pagination_model->paginate($pageconfig);
		if($this->pagination_model->total_records == 0){
			return;
		}
		$data['results']=$this->pagination_model->results;
		$data['currentpage']=$this->pagination_model->page_links;
		$data['totalrecords']=$this->pagination_model->total_records;

		$this->loadModel('templates/materialize_model');
		$data['sidemenu'] = $this->materialize_model->getMenu('main menu','sidemenu');
		$data['dropmenu'] = $this->materialize_model->getMenu('main menu','dropmenu');		
				
		$this->addView('templates/materialize/header',$data);
		$this->addView('blog/nav',$data);
		$this->addView('blog/list',$data);
		$this->addView('templates/materialize/footer');
	}
	public function article($link,$category='',$article=''){
	//	= ' IN Blog';
	
		$this->loadModel('blog/blog_model');	
		$data['article'] = $this->blog_model->getArticle($link);
		if($data['article'] == ''){
			return;
		}
		$data['title']=$data['article']['articlename'];
		$data['meta']='<meta name="description" content="'.$data['article']['description'].'">';
		//Let system know page output wants to be cached
		//if($result['cache'] == 1){
			$this->cache = true;
		//}
		//if($result['minify'] == 1){
			$this->minify = true;
		//}	
		
		$this->loadModel('templates/materialize_model');
		$data['sidemenu'] = $this->materialize_model->getMenu('main menu','sidemenu');
		$data['dropmenu'] = $this->materialize_model->getMenu('main menu','dropmenu');	



		
		$this->addView('/templates/materialize/header',$data);
		$this->addView('blog/nav',$data);
		$this->addView('blog/article',$data);
		$this->addView('/templates/materialize/footer');
	}
	public function category($link,$category){
		$this->loadModel('blog/blog_model');	
	//	$data['content'] = $this->blog_model->getAllByType();

	
		
		$data['title']=ucfirst ($data['content'][0]['category']);
		$data['meta']='<meta name="description" content="'.ucfirst ($data['content'][0]['category']).' category">';
		
		$this->loadModel('pagination_model');
		//Define pagination vars 
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
			
		//Get current page assignments		
		$pageconfig['query']="SELECT * FROM content WHERE (type='blog' OR type='code') AND category =? AND published = 1 ORDER BY date DESC";
		$pageconfig['query_array']=array($category);
		$pageconfig['link_url']= $this->base_url.$link;
		$pageconfig['link_params']=''; 
		$pageconfig['num_results']=2;//default set to 10
		$pageconfig['num_links'] = 3;//ahead and behind
		$pageconfig['get_var']='page'; 
		$pageconfig['prevnext']=true;
		$pageconfig['firstlast']=true;
				/*Paginate!*/
		$this->pagination_model->paginate($pageconfig);
		if(empty($data['content'])){
			return;
		}
		$data['results']=$this->pagination_model->results;
		$data['currentpage']=$this->pagination_model->page_links;
		$data['totalrecords']=$this->pagination_model->total_records;

		$this->loadModel('templates/materialize_model');
		$data['sidemenu'] = $this->materialize_model->getMenu('main menu','sidemenu');
		$data['dropmenu'] = $this->materialize_model->getMenu('main menu','dropmenu');		
		
		$this->addView('/templates/materialize/header',$data);
		$this->addView('blog/nav',$data);
		$this->addView('blog/list',$data);
		$this->addView('/templates/materialize/footer');
	}
	
	public function admin($link,$any1){
		$user = $this->userCheck();
		//route to create and edit functions, pass in user
		if($any1=='create'){
			$this->create($user);
			return;
		}
		if($any1=='edit'){
			$this->edit($user);
			return;
		}
		$this->loadModel('blog/blog_model');
		$this->loadModel('search_model');			
		//Delete records from pages or editpages
		if($_POST["submit"] == "Delete Checked" || $_POST["submit"] == "Delete Record"){
			$this->blog_model->delArticles();
			$this->search_model->updateSearch();
		//Update selected pages
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
		$fields=array("id","articlename","published","type","date","lastupdate","link","content","image","category");///false;///array("id","date","name","categoryIds","type");//$fields=false;
		
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
		$sort->init("(type='blog' OR type='code') AND user='".$user->getId()."'");//pass in the query addon to specify types of articles. The first AND is added automatically.

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
		
		<td><a href=\"".$this->base_url."blog/admin/edit?".$get_var.'='.$_GET[$get_var].$link_params."&article=".$row[$table->tableindex]."\">Edit</a></td>		
		";
			$table->addRow($str,$row);
		}
		
		$data['hiddeninputs'] = $sort->hiddenInputs($fields,$pageNumber,$get_var);		
		$data['tableHTML'] = $table->render($attr='class="table table-condensed table-bordered table-striped"');

	
		
		
		$this->addView('blog/admin/header');
		$this->addView('blog/admin/blog_list',$data);
		$this->addView('blog/admin/footer');
	}

	public function create($user){

		if($user && ($user->canCreatePage())){
		}else{
			header("Location: ".$this->base_url."blog/login");
			exit;
		}	
		$this->loadModel('blog/blog_model');
		$this->loadModel('search_model');
		if($_POST["update"] == "0"){
			$articleid=$this->blog_model->insertArticle($user);	
			if($articleid > 0){
				$this->search_model->updateSearch(); 	
				error_reporting(E_ALL | E_WARNING | E_NOTICE);
ini_set('display_errors', TRUE);


flush();	
				header("Location:".$this->base_url."blog/admin/edit?article=".$articleid);
				exit;
			}
		}
		
		$data['categories'] = $this->blog_model->getDistinct();		
		$data["add"]=true;
		$data["title"]="Add Pages";

		
		$this->addView('blog/admin/header');
		$this->addView('blog/admin/blog_article',$data);
		$this->addView('blog/admin/footer');
	}		
	
	public function edit($user){
		$this->loadModel('siteadmin/content_model');		
		$this->content_model->init();
		if($user && ($user->isAdmin() || $user->canEditArticle($this->content_model))){	///have to be an admin to get this far, && canEdit might be of use
		}else{
			header("Location: ".$this->base_url."blog/admin");
			exit;
		}
		
		$this->loadModel('blog/blog_model');	
		$this->loadModel('search_model');		
		if(!empty($_POST['id'])){
			$data["articleid"] = $_POST['id'];
		}else if(!empty($_GET['article'])){
			$data["articleid"] = $_GET['article'];		
		}



		if($_POST["update"] == "1"){
			$data['messages'] ='update id:'.$_POST["id"] .'<br>';
			$data['messages'].= $this->blog_model->updateArticle();
			$this->search_model->updateSearch();	
		}
		
		$data["title"]="Update Pages";		
		$data["add"]=false;		
		$data['categories'] = $this->blog_model->getDistinct();		
		$data['article']=$this->content_model->getAllById('content',$data["articleid"]);
		
		$this->addView('blog/admin/header');
		$this->addView('blog/admin/blog_article',$data);
		$this->addView('blog/admin/footer');
	}	
	
	
	//login functions
	
		public function login($link,$any1){
		$this->loadModel('login_model');
		$data['content']=$this->login_model->check();		
		if($data['content']===true){
			header("Location: ".$this->base_url."blog/admin");
			exit;		
		}
		$this->addView('blog/admin/header');
		$this->addView('default',$data);
		$this->addView('blog/admin/footer');
	}
	public function logout()
	{
		$this->loadModel('logout_model');		
		$data['content']=$this->logout_model->logout();
		$this->addView('blog/admin/header');
		$this->addView('default',$data);
		$this->addView('blog/admin/footer');
	}	
	public function userCheck(){
		$user=(isset($_SESSION[SESSION_PREFIX]['user'])) ? $_SESSION[SESSION_PREFIX]['user'] : null;
		if($user && ($user->isAdmin() || $user->isAuthor() /*|| $user->canEditArticle($this->content_model)*/)){	
			return $user;
		}else{
			header("Location: ".$this->base_url."blog/login");
			exit;
		}	
	}	
}
