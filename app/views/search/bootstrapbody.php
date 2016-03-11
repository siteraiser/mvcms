 <div class="jumbotron">
      <div class="container">
      	<div class="panel panel-default">
			<div class="panel-heading clearfix">
				
			<a href="/">  <img class="pull-left img-responsive" alt="Logo" src="/images/siteraiserwizard.png"></a>
			
		
			</div>
	<div class="panel-body">
		<form action="" method="get"  class="form-inline">
	
		<div class="form-group">
        <p>			  
			<label for="search" class="control-label">Search Site</label><br>
			<input name="search" id="search" type="text" class="form-control" value="<?php echo$_GET['search'];?>">
			

			<input class="form-control btn btn-primary" type="submit">
			

			
		</p>
		</div>


</form>


<?php echo $totalrecords.' '.($totalrecords != 1 ? 'Results' : 'Result');

echo$results;

/*
foreach($results as $value){
echo'<div><a href="'.$value['categoryname'].'/'.$value['page'].'">'.$value['page'].'</a></div>';
}
*/
// $pagination->pageNumber :))
/* output pagination */

 echo @$currentpage; 
 	
?>

</div>
</div>
</div>
</div>