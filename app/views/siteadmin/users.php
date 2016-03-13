<div class="container">
<div style="<?php  echo ($messages =='' ? 'display:none;':'');?>" id="form-errors" class="alert alert-danger col-sm-12"><?php echo $messages;?> </div>
<br>
<form action="">

 <h3 style="display:inline">Search for</h3>
 <select name="searchfield">
 <option value=""></option>
 <?php
 echo $this->selectedOption($fields, @$_GET['searchfield']);
?>
</select> 

<h3 style="display:inline">That</h3>
<select name="modifier">
	<option value="" <?php echo(@$_GET['modifier'] == ''?'selected':'');?>>is equal to</option>
	<option value="like" <?php echo(@$_GET['modifier'] == 'like'?'selected':'');?>>is like</option>
	<option value="starts" <?php echo(@$_GET['modifier'] == 'starts'?'selected':'');?>>starts w/</option>
	<option value="ends" <?php echo(@$_GET['modifier'] == 'ends'?'selected':'');?>>ends w/</option>
</select>

<input type="text" name="search" value="<?php echo(isset($_GET['search'])?$_GET['search']:'')?>"/>

Results Per Page
<input type="text" name="resultsppg" style="width:25px" value="<?php echo(isset($_GET['resultsppg'])?$_GET['resultsppg']:'')?>"/>

<br> 
<h3 style="display:inline">Date Column</h3>
<select name="datefield">
<?php 
 echo $this->selectedOption( $datefields, @$_GET['datefield']);
?>
</select>

<span>
	<label class="desc-text" for="datepicker"> Start Date: </label><input name="startdate" id="datepicker" type="text" value="<?php echo @$_GET['startdate'];?>" size="10" placeholder="<?php echo '0000'?>">
	<label class="desc-text" for="datepicker2"> End Date: </label> <input name="enddate" id="datepicker2" type="text" value="<?php echo @$_GET['enddate'];?>" size="10" placeholder="<?php echo date('Y');?>">
</span>

<?php
	echo $hiddeninputs;
?>	
<input type="submit" class="btn"/>
</form>
<a class="btn btn-success pull-right" href="<?php echo$this->base_url;?>siteadmin/users/add">Add</a>

  <h3><?php echo ($totalrecords ==1? $totalrecords.' Result' : $totalrecords.' Results' );?> Sorted <?php echo$_GET['sort'];?> By <?php echo$_GET['sortfield'];?></h3>

<form action="" method="post">
<?php
/* output table */
echo $tableHTML;
// $pagination->pageNumber :))
/* output pagination */

 echo $currentpage; 
 	
?>
<br><input type="submit" name="submit" value="Update Checked"><input type="submit" name="submit" value="Delete Checked">
</form>
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
