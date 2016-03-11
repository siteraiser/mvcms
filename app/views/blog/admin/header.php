<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
        <head>
                <title><?php if(isset($title))echo$title;?></title><!--Load style.css file, which store in css folder.-->

  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link href='//fonts.googleapis.com/css?family=Source+Sans+Pro|Open+Sans+Condensed:300|Raleway' rel='stylesheet' type='text/css'>
<?php if($this->path == 'blog/admin/edit' || $this->path == 'blog/admin/create'){ ?>
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
  				<li <?php $url=$this->base_url.'blog/admin'; echo($this->base_url.$this->path == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">List</a></li>	
				<li <?php $length=strlen($url=$this->base_url.'blog/admin/user'); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">User</a></li>	
									
      </ul>
	  <a style="pull-right" href="/blog/logout">Logout</a>
	 <?php /*endif */?>
    </div>

 <div class="offset1 col-md-10">