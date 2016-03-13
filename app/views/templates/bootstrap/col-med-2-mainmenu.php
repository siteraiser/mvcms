 
<!-- start main menu -->
 <div class="col-md-2">

	  
	  <?php echo$content;?><?php echo $menu;?>
	  				
      <ul class="nav nav-pills nav-stacked">
				<li <?php $length=strlen($url=$this->base_url.'admin/categories'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Categories</a></li>				
				<li <?php $length=strlen($url=$this->base_url.'admin/paintings'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Add Paintings</a></li>
									
				<li><a href="<?php echo $this->base_url; ?>">Site Home</a></li>
      </ul>
</div>
<!-- end main menu -->
