<!DOCTYPE html>
<html>
        <head>
                <title><?php if(isset($title))echo$title;?></title><!--Load style.css file, which store in css folder.-->

  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link href='//fonts.googleapis.com/css?family=Source+Sans+Pro|Open+Sans+Condensed:300|Raleway' rel='stylesheet' type='text/css'>

</head>
<body>
		


	<div class="container-fluid">
  <div class="row">
   
    
    <div class="col-md-2">
	<?php /*	if($this->session->userdata('admin_logged_in')): */?>
				
      <ul class="nav nav-pills nav-stacked">
  				<li <?php $length=strlen($url=$this->base_url); echo(substr($this->base_url.$this->path,0,$length) == $url ? 'class="active"' : '');?>><a href="<?php echo $url;?>">Home</a></li>	
			
      </ul>
	  <a style="pull-right" href="/siteadmin/logout">Logout</a>
	 <?php /*endif */?>
    </div>

 <div class="offset1 col-md-10">