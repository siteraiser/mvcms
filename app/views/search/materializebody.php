	<div class="m-container nav custom-drop">	
		<nav id="site-navigation" class="main-navigation custom-drop" role="navigation">	<a href="#" data-activates="mobile-nav" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
		<div id="mobile-nav" class="menu side-nav">

		<?php if(isset($sidemenu))echo$sidemenu;?>
		<!--
			<ul>
				<li class="mobile-header"><p><a href="#">Sass</a></p></li>
				<li class="mobile-header"><p><a href="#">Sass</a></p></li>
				<li class="mobile-header"><p><a href="#">Sass</a></p></li>
			</ul>-->
			<div class="clear"></div>
		</div>
		
		
		<div class="hide-on-med-and-down">
				<?php if(isset($dropmenu))echo$dropmenu;?>
			<!--<ul id="top-bar">
				<li class="menu-item"><a href="#">Sass</a></li>
				<li class="menu-item"><a href="#">Components</a></li>
				<li class="menu-item"><a href="#">JavaScript</a></li>
			</ul>
			-->
		</div>

	
	</nav><!-- #site-navigation -->		

	<div class="clear"></div>
</div>

<div class="container">	
	<div class="row">

      <form action="/search" method="get">
        <div class="input-field">
          <input name="search" id="search" type="search" value="<?php echo $_GET['search'];?>" required>
          <label for="search"><i class="material-icons">search</i></label>
        </div>
      </form>

	
	
<?php echo $totalrecords.' '.($totalrecords != 1 ? 'Results' : 'Result');

echo$results;


// $pagination->pageNumber :))
/* output pagination */

 echo @$currentpage; 
 	
?>
	  <div class="clear"></div>
 </div>
 </div>
