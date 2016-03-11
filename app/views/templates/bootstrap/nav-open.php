<div class="navWrap" style="height:50px">

<nav id="myNavbar" class="navbar navbar-default" role="navigation">

	<div class="container innerNav">

		<div class="navbar-header">

            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">

                <span class="sr-only">Toggle navigation</span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

            </button>

            <a class="navbar-brand" href="<?php echo $this->base_url; ?>"><div style="margin-top:-10px;width:120px;"><?php if(isset($content))echo$content;?></div><?php /*<img style="margin-top:-10px;width:135px;height:45px;" alt="Dealer Dirt logo" src="/images/hder.svg" onerror="this.src='/images/hder.png'">*/?></a>

        </div>

 

        <div class="collapse navbar-collapse" id="navbarCollapse">

          <?php /*  <ul class="nav navbar-nav vcenter">

				<li <?php echo( $this->path ==  $this->base_url ? 'class="active"' : '')?>><a href="<?php echo $this->base_url; ?>">Home</a>	</li>	

				<li <?php echo( $this->path ==  $this->base_url.'read-review' ? 'class="active"' : '')?>><a href="<?php echo $this->base_url; ?>read-review/">Read Review</a></li>

				<li <?php echo( $this->path ==  $this->base_url.'write-reviews' ? 'class="active"' : '')?>><a href="<?php echo $this->base_url; ?>write-reviews/">Write a Review</a></li>

				<li <?php echo( $this->path ==  $this->base_url.'about-us' ? 'class="active"' : '')?>><a href="<?php echo  $this->base_url; ?>about-us/">About Us</a></li>			

				<li <?php echo( $this->path ==  $this->base_url.'login' ? 'class="active"' : '')?>><a href="<?php echo  $this->base_url; ?>login/">Dealer Login</a></li>
				</ul>
				*/?>


<?php if(isset($menu))echo$menu;?>
		

			

      