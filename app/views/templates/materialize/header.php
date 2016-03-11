<!DOCTYPE html>
  <html>
    <head>	   
	<title><?php if(isset($title))echo$title;?></title><!--Load style.css file, which store in css folder.-->
	<?php if(isset($meta))echo$meta;?>      
	<!--Let browser know website is optimized for mobile-->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <!--Import materialize.css-->

  <!-- Compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
      <!--Import jQuery before materialize.js-->
      <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
       <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>
  <script type="text/javascript" src="/assets/js/materialize.js"></script>	 
	  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="/assets/css/dropmenu.css">
	  	<link rel="stylesheet" href="/assets/css/sidemenu.css">

<style>
html {
	font-family: 'roboto',sans-serif;
	-webkit-text-size-adjust: 100%;
	-ms-text-size-adjust:     100%;
}
.container {
	padding: 0 .5rem;
	margin: 0 auto;
	max-width: 1400px;
	width: 99%;
	
}


/* Small menu. */
.menu-toggle {
	display: none;
}
 
@media screen and (max-width: 600px) {
	.menu-toggle,
	.main-navigation.toggled .nav-menu {
		display: block;
	}
 
	.main-navigation ul #top-bar{
		display: none;
	}
}
 

.m-container nav {
  color: #fff;
 /* background-color: transparent;*/
  width: 100%;
  height: 56px;
  line-height: 56px;
  overflow: hidden;
  box-shadow: 0 0 0;
}

.site-branding {
  padding: 20px 30px;
}







/* extra styling */
div#content {
padding-top: 20px;
}


/* Header */
 
header#masthead {
background: #2196f3;
color: #fff;
}
 
header#masthead a {
	color: #fff;
}
 
/* Navigation */
 
.nav {
color: #fff;
background-color: #64b5f6;
width: 100%;
height: 56px;
line-height: 56px;
overflow: hidden;
}


 
nav ul li:hover, nav ul li.active {
background-color: #1976d2;
}

.parallax-container {
      height: auto;
    }	
.parallax-container.main-content{
      height: auto;
    }
.button-collapse{margin-top:10px;margin-left:5px;}
	
/*** drop nav ***/	
	
div.nav.custom-drop,nav .custom-drop{
overflow:visible;
height:88px;
}
nav.custom-drop{
height:88px;
overflow:visible;
}
#nav a, #nav span{
    padding: 6px 12px;
	font-size:1rem;
}
ul#nav li, ul#nav li a, ul#nav li span{ /* materialize override */
box-sizing: content-box; 
}
.arrow{margin-top:-2px;}
/* color styles */

.custom-drop{ 

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



/*** side nav ***/
ol.tree li.waves-effect{display:block;}
ol.tree li input + ol > li.waves-effect{display:none;}
ol.tree li input:checked + ol > li { display: block;}
ol.tree [type="checkbox"]:not(:checked),ol.tree  [type="checkbox"]:checked {
	left:0px;visibility:visible;
}

li input + ol{
    margin: -1.938em 0 0 -44px;
    height: 1.938em;
}

li input:checked + ol {
    margin: -2.25em 0 0 -44px;
    padding: 2.25em 0 0 80px;
}


ol.tree li label a{padding-left:15px;margin-left:-15px;}
ol.tree li label{
	position:relative;
	font-size:1rem;
	height: 64px;
	line-height: 64px;
	padding-left:37px;
}


ol.tree{font-size:150%;}
li input:checked + ol{background-size: 24px 24px;background-position: 38px 6px;}
li input + ol{background-size: 24px 24px;background-position: 38px 0px;}

ol.tree li input{top:20px;}

/* coloring styles*/
ol.tree li a.select{color:#aaf !important;}
ol.tree li a:hover {
    background-color: #ace;
} 
ol.tree li:hover { background-color: #eee;}
/* Typography */	
.tree li a,.tree li label{
	color: #000;
}

.tree li{
	padding:0px;
}



	
/*other styles*/
#primary,#secondary{
margin-top:20px;
	/*max-width:1000px;*/
	padding:10px 20px;
	background: rgba(40, 200, 255, 0.5);
	box-shadow: inset 0 6px  15px -4px rgba(31, 73, 125, 0.8), inset 0 -6px  8px -4px rgba(31, 73, 125, 0.8);
	border-radius: 5px;
}
/** Slideshow **/

.slider .slides li .caption.right-align {
left:45%;
width:45%;
}
.caption{
text-align:left;
}
@media screen and (min-width: 500px) {
	.slider .slides li .caption.right-align {
		left:50%;
		width:40%;
	}
}


	 </style>  

    </head>

    <body>
	
	