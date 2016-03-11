<h2 class="header">
	<img alt="" src="/userfiles/1/image/carl/siteraiserwizard-xmas.png" style="float: left; margin: 5px; width: 251px; " class="responsive-img"></h2>
<h2 class="header">
The Blog</h2>
<p class="grey-text text-darken-3 lighten-3">
	Parallax is an effect where the background content or image in this case, is moved at a different speed than the foreground content while scrolling. Oh how very nice an auto article</p></body>
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
		<div class="nav-wrapper right valign-wrapper">
      <form action="/search" method="get">
        <div class="input-field valign">
          <input name="search" id="search" type="search" required>
          <label for="search"><i class="material-icons">search</i></label>
         <!-- <i class="material-icons">close</i> -->
        </div>
      </form>
    </div>		</nav><!-- #site-navigation -->		

	<div class="clear"></div>
</div>