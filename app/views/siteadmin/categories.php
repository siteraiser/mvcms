   <div class="container-fluid">

<div style="<?php  echo ($messages =='' ? 'display:none;':'');?>" id="form-errors" class="alert alert-danger col-sm-12"><?php echo $messages;?> </div>

    <div class="span10 offset1">
    	<div class="row">
		    <h3><?php echo $title;?></h3><?php echo(isset($create_link) ? '<a class="btn btn-success" href="'.@$create_link.'">Create</a>' : '' );?>
		</div>


	<form action="" method="post" > 
		<fieldset><legend>Category Name: </legend><input type="text" name="categoryName" value=""></fieldset><br>
		<fieldset><legend>Category Type: </legend><input type="text" name="type" value=""></fieldset><br>
<hr>
<h3>Showing Categories</h3><div style="height:20px;overflow:hidden;width:10%;float:left;margin-left:20px;"> Name: </div><div style="height:20px;overflow:hidden;width:10%;float:left;"> Type:</div><br><div></div>

<?php echo $categories;?>


<br><input type="submit" name="submit" value="Add Category"><input type="submit" name="submit" value="Delete Checked">
	</form>
		
	</div>
</div>	
<div id="footer">
	  MVCMS - Copyright &copy; 2016 - Carl Turechek
  	
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
</div>


