<?php /* Copyright © 2016 
	
	This file is part of PHP-MVCMS.
    PHP-MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    PHP-MVCMS is distributed in the hope that it will be useful,
    You should have received a copy of the GNU General Public License
     but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
   along with PHP-MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/
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
