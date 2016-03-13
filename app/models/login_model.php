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
//Login class

class login_model extends requestHandler{


	function getEmail(){
		
		$email =filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			return filter_var($email, FILTER_VALIDATE_EMAIL);
		}else{
			return false;   
		} 
	}
	function getPass(){
		
		return trim($_POST['pass']);
	}

	public function check(){

		$formSubmitted=0;
		$form='<form action="" method="post">';
		 $form.='<p>Email: <input type="text" name="email" /></p>';
		  $form.='<p>Password: <input type="password" name="pass" /></p>';
		  $form.='<p><input type="submit" value="Login"/></p>';
		 $form.='</form>';
		 
		 if($_SERVER['REQUEST_METHOD'] == 'POST'){ //handle submission form
			$formSubmitted=1;
			
			 $q = 'SELECT * FROM users WHERE email=:email AND pass=:pass';
			 $stmt = $this->pdo->prepare($q);
			 $r = $stmt->execute(array(':email' => $this->getEmail(), ':pass' => $this->getPass() ));

		 }

		 //try fto fetch the results
		 if($r){
			$stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
			$user = $stmt->fetch();
		}

		//Store user in session, redirect...
		if($user && ($user->status() || $user->isAdmin())){

			//store session
			$_SESSION[SESSION_PREFIX]['user'] = $user;
			//echo "<script language='javascript'>\n";
			//echo "  location.href=\"http://www.siteraiser.com/blog\";\n";
			//echo "</script>\n";
			if( $this->path == 'siteadmin' && $user->isAdmin()){
				//redirect
				header("Location: ".$this->base_url."siteadmin/content");
				exit;
			}else{
				return true;
			}
		}

		//Show login html
		if($formSubmitted && $this->getEmail() !=''){
			$form.='Our records don\'t match. <a href="'.$this->base_url.'sendpassword?email='.$this->getEmail().'">Email</a> password to: '.$this->getEmail().'?';
		}
		return $form;
	}
}
