<!DOCTYPE html>
<html>
    <head>
    <title><?php if(isset($title))echo$title;?></title>
	<?php if(isset($meta))echo$meta;?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/dropmenu.css">
	<link rel="stylesheet" href="/assets/css/sidemenu.css">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>

var foo1 = (('ontouchstart' in window) || (window.DocumentTouch && document instanceof DocumentTouch)) ? 'touchstart' : 'click';
if(foo1 != 'click'){
$(document).on('click touchstart',function(event){
    if (!$(event.target).is('ul#nav a')){
        $('ul#nav ul').hide();
    }else{
         $('ul#nav ul').show();  
    }
}); 
}
</script>
<style>
.responsive{height:auto; max-width:100%;}

/*** side menu ***/
li input + ol{margin:-1.05em 0 0 -44px}
li input:checked + ol {
	margin:-1.44em 0 0 -44px;
}   
li .file{ margin-left: -1px !important;}
ol.tree label{ /* bs override */
	margin-bottom:0px;
}

ol.tree li input{top:7px;}

ol.tree li label a{padding:0px}
ol.tree li label{
	position:relative;
}
li input + ol > li {
	margin-left: -14px !important; 
	padding-left: 1px; 
}	
li.file a{
	padding:0 5px 0 5px;
}	
li.file{margin-left: auto !important;}

@media only screen and (max-width: 1240px)  {
ol.tree{font-size:150%;}
li input:checked + ol{background-size: 24px 24px;background-position: 35px 7px;}
li input + ol{background-size: 24px 24px;background-position: 35px 0px;}

}	

.tree li{
padding:5px;	
}	
/* color styles */	
.tree li a,.tree li label{
	color: #fff;
}
		
ol li a.select{color:#aac !important;}	

.tree li{
	

/* IE10 Consumer Preview */ 
background-image: -ms-linear-gradient(top left, #11d 0%, #111 53%);
/* Mozilla Firefox */ 
background-image: -moz-linear-gradient(top left, #222 0%, #111 53%);
/* Opera */ 
background-image: -o-linear-gradient(top left, #222 0%, #111 53%);
/* Webkit (Safari/Chrome 10) */ 
background-image: -webkit-gradient(linear, left top, right bottom, color-stop(0, #222), color-stop(.53, #111));
/* Webkit (Chrome 11+) */ 
background-image: -webkit-linear-gradient(top left, #222 0%, #111 53%);
/* W3C Markup, IE10 Release Preview */ 
background-image: linear-gradient(to bottom right, #222 0%, #111 53%);
}
	
/* Typography */				
	
	
	div#navbarCollapse ul#nav{display:none;}
	/* Both menus */
	@media only screen and (min-width: 768px)  {
	div#navbarCollapse .tree li{display:none;}
	div#navbarCollapse ul#nav{display:block;}
	}

	
/*** Drop Menu ***/


/* mini menu */
#nav{padding:0px;font-size:14px;}
ul#nav a, ul#nav span {height:40px; padding: 4px 10px;}
div#navbarCollapse ul#nav{height:50px;
/*height:88px;*/
}
ul#nav li, ul#nav li a, ul#nav li span{ /* bs override */
	box-sizing: content-box; 
}	
#nav ul li a, #nav ul li span{
    padding: 6px 12px;font-size:14px;
}
.arrow{margin-top:-7px;}	






/* Big menu
div#navbarCollapse ul#nav{
height:88px;
}
ul#nav li{margin-left:0px;margin-right:0px;}
ul#nav li, ul#nav li a, ul#nav li span{ 
	box-sizing: content-box; 
}	
#nav ul li a, #nav ul li span{
    padding: 6px 12px;
}
*/	
/* color styles */	
#nav ul li {
	background: #223;
}
/* main level link */
#nav a, #nav span {
	color: #45f; font-family: 'Leckerli One', 'Trebuchet MS', Tahoma, cursive;
}
#nav a:hover {

	/*color: #fff;*/
}

/* main level link hover */
ul li a.select, #nav li:hover > a {
	color: #aad;
}

/* main level link hover */
#nav li:active > a {	
	color: #444
}

/* sub levels link hover */
#nav ul li:hover a, #nav li:hover li a {
	color: #999;
}
#nav ul a:hover {
background: #004 !important;
}
#nav li:hover{
	background: #004;
}


/* other */
.search-bar-inpt,.search-bar-grp {
float:right;
}


.search-bar-inpt:focus{
    min-width: 210px;
}
.search-box{

	margin-top: 7px;
	margin-bottom: 7px;
	
}

</style>
</head>
<body>
<?php if(isset($content))echo$content;?>
  
  <!-- end of header -->


 
