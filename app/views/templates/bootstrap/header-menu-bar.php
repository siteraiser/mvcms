<div class="panel panel-default">
		
	<div class="panel-body">
	<h1 class="pull-left"><?php if(isset($content))echo$content;?></h1><div class="pull-left droptop"><?php if(isset($menu))echo$menu;?></div>
	<div class="pull-right">		
		<form action="/search" method="get"  class="form-inline">
	
		<div class="form-group">
        <p>			  
			<label for="search" class="control-label">Search SiteRaiser.com</label><br>
			<input name="search" id="search" type="text" class="form-control" value="">
			<input class="form-control btn btn-primary" type="submit">			
		</p>
	</div>
</form>	
</div>

</div>
</div>