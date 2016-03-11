<?php 
//User class

class logout_model extends requestHandler{
	function logout(){
		$user=(isset($_SESSION[SESSION_PREFIX]['user'])) ? $_SESSION[SESSION_PREFIX]['user'] : null;
		if($user){

		$user = null;

		$_SESSION = array();

		//Clear the cookie:
		setcookie(session_name(), false, time()-3600);

		//destroy session data
		session_destroy();

		}
	//	var_dump($_SESSION);
		return'you are logged out <a href="/">home</a>';
	}
}
