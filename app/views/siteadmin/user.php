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
		<form action="<?php echo($add === true ? $this->base_url.'siteadmin/users/edit' : '' );?>" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
	
<hr>


<input type="hidden" name="id" value="<?php echo @$user['id']; ?>">
<input type="hidden" name="update" value="<?php echo(@$add == true ? '0':'1' )?>">


User Type: <select name="userType" id="userType" class="form-control">
		 <?php
			echo $this->selectedOption(array('admin'=>'admin','author'=>'author','public'=>'public'),@$user['userType']);
		?>
		</select><br>

Name: <input type="text" name="username" value="<?php echo @$user['username']?>"><br>
Email: <input type="text" name="email" value="<?php echo @$user['email']?>"><br>
Password: <input type="text" name="pass" value="<?php echo @$user['pass']?>"><br>		
<?php echo($add !== true ? 'Date Added: '.@$user['dateAdded']:'' )?>	<br>
Active: <input type="checkbox" name="status" <?php echo (@$user['status'] == 1?'checked':'')?> value="<?php echo @$user['status']?>"> 	
			
			<div class="form-actions">	
				<button id="submit" name="submit" class="btn success" value="<?php echo(@$add !== true ? 'Update':'Create' )?>"><?php echo(@$add !== true ? 'Update':'Create' )?></button>			
				<a class="btn" href="/siteadmin/users<?php echo @$back_link?>">Back to Table</a>
			</div>
		</form>
		<form action="/siteadmin/users<?php echo @$back_link?>" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="indexSelected[]" value="<?php echo @$user['id']; ?>">
			<button type="submit" class="btn btn-warning" name="submit" value="Delete Record" onclick="return confirm('Are you sure you want to remove this record?')">Delete</button>
		</form>
	<br>
	</div>
</div>	


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
