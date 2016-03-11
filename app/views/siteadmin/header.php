<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
        <head>
                <title><?php if(isset($title))echo$title;?></title><!--Load style.css file, which store in css folder.-->

  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link href='//fonts.googleapis.com/css?family=Source+Sans+Pro|Open+Sans+Condensed:300|Raleway' rel='stylesheet' type='text/css'>
<?php if($this->path == 'siteadmin/content/edit' || $this->path == 'siteadmin/content/add'){ ?>
<script type="text/javascript" src="<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/ckeditor.js"></script>
	<script src="<?php echo $this->base_url; ?>filemanager_in_ckeditor/sample.js" type="text/javascript"></script>
	<link href="<?php echo $this->base_url; ?>filemanager_in_ckeditor/sample.css" rel="stylesheet" type="text/css" />
<?php 
}
?>
</head>
<body>
		


	<div class="container-fluid">
  <div class="row">
   
    
    <div class="col-md-2">
	<?php /*	if($this->session->userdata('admin_logged_in')): */?>
				
      <ul class="nav nav-pills nav-stacked">
  				<li <?php $length=strlen($url=$this->base_url.'siteadmin/routing'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Routing</a></li>	
				<li <?php $length=strlen($url=$this->base_url.'siteadmin/categories'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Categories</a></li>	
				<li <?php $length=strlen($url=$this->base_url.'siteadmin/pages'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Pages</a></li>					
				<li <?php $length=strlen($url=$this->base_url.'siteadmin/content'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Content</a></li>
				<li <?php $length=strlen($url=$this->base_url.'siteadmin/menueditor'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Menu Editor</a></li>	
				<li <?php $length=strlen($url=$this->base_url.'siteadmin/users'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Users</a></li>	
				<li <?php $length=strlen($url=$this->base_url.'siteadmin/config'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Site Configuation</a></li>							
				<li><a href="<?php echo $this->base_url; ?>">Site Home</a></li>
      </ul>
	  <a style="pull-right" href="/siteadmin/logout">Logout</a>
	 <?php /*endif */?>
    </div>

 <div class="offset1 col-md-10">