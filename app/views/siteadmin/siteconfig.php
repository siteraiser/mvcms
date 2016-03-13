<?php /*	Copyright © 2016 
	
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
?>
<div class="container-fluid">

<div style="<?php  echo ($messages =='' ? 'display:none;':'');?>" id="form-errors" class="alert alert-danger col-sm-12"><?php echo $messages;?> </div>

    <div class="span10 offset1">
    	<div class="row">
		    <h3><?php echo $title;?></h3>
			<div class="pull-right">
				<?php echo($add === false ? '<a class="btn btn-success" href="'.$this->base_url.'siteadmin/users/add">Add</a>' : '' );?>
			</div>
		</div>
		<form action="" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
	
<hr>


<!--
User Type: <select name="userType" id="userType" class="form-control">
		 <?php
			echo $this->selectedOption(array('admin'=>'admin','author'=>'author','public'=>'public'),$site['userType']);
		?>
		</select><br>
-->
Base Url: <input type="text" name="baseurl" placeholder="http://example.com/" value="<?php echo $site->baseurl;?>"><br>
Session Prefix: <input type="text" name="sessionprefix" value="<?php echo $site->sessionprefix;?>"><br>
DB Username: <input type="text" name="username" value="<?php echo $db->username;?>"><br>
DB Password: <input type="text" name="password" value="<?php echo $db->password;?>"><br>
Database: <input type="text" name="database" value="<?php echo $db->database;?>"><br>
		<!--
Active: <input type="checkbox" name="status" <?php echo (@$site['status'] == 1?'checked':'')?> value="<?php echo @$site['status']?>"> 	
			-->
			<div class="form-actions">	
				<button id="submit" name="submit" class="btn success" value="Update">Update</button>			
			</div>
		</form>
	
	<br>
	</div>
</div>	



 <style>
 img {max-height:150px;}
 </style>
 

<?php

 //echo '<pre>'.htmlspecialchars(print_r($rows, true)).'</pre>';
 
 ?>
	<div id="footer">
	  PHP-MVCMS - Copyright &copy; 2016 - Carl Turechek
  	
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
	
	</div>
