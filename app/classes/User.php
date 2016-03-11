<?php 
//User class - based on an example from Larry Ullman's PHP Advanced and Object-Oriented Programming Third Edition

class User{

	//All attributes correspond to db columns

	protected $id = null;
	protected $userType = null;
	protected $username = null;
	protected $email = null;
	protected $pass = null;
	protected $urls = null;
	protected $image = null;
	protected $dateAdded = null;
	protected $status = null;
	
	//Method returns userId
	function getId(){
		return $this->id;
	}
	function getUsername(){
		return $this->username;
	}
	function status(){
		return  ($this->status == 1);
	}
	//Method returns a Bool if user is admn.
	function isAdmin(){
		return ($this->userType == 'admin');
	}
	//Method returns a Bool if user is admn.
	function isAuthor(){
		return ($this->userType == 'author');
	}	
		
	//Method returns a Bool indicating if user is the original author
	function canEditArticle(content_model $p){
		return (($this->id == $p->getCreatorId()));
	}

	//Method returns a Bool indicating if user is an admin or original author
	function canCreatePage(){
		return ($this->isAdmin() || ($this->userType == 'author'));
	}
}
?>