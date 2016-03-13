<?php /*
Copyright © 2016 
	
	This file is part of PHP-MVCMS.

    PHP-MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PHP-MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PHP-MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.9.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="<?php echo $this->base_url;?>assets/js/jquery.autocomplete.js"></script>
<script src="<?php echo $this->base_url;?>assets/js/jquery.ui.touch-punch.min.js"></script>

<?php 
/* Select template first */
if(!isset($page['template']) && $this->url_segments[2] == 'add'){ ?>
<form class="form-inline" method="post" action="<?php echo $this->base_url.'siteadmin/pages/add';?>" role="form">
Select a Template: <select name="template">
 <?php
 echo $this->selectedOption($templates,'');
?>
</select>
<button id="submit" name="submit" class="btn success" value="selectedTemplate">Submit</button>		
</form>
<?php 
exit;
} 
?>


<!-- sortable code -->
<style>
.custom-combobox {
	position: relative;
	display: inline-block;
}
.custom-combobox-toggle {
	position: absolute;
	top: 0;
	bottom: 0;
	margin-left: -1px;
	padding: 0;
	/* support: IE7 */
	*height: 1.7em;
	*top: 0.1em;
}
.custom-combobox-input {
	margin: 0;
	padding: 0.3em;
}
.autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; z-index:1}
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }	
</style>
<script>
(function( $ ) {
$.widget( "custom.combobox", {
_create: function() {
this.wrapper = $( "<span>" )
.addClass( "custom-combobox" )
.insertAfter( this.element );
this.element.hide();
this._createAutocomplete();
this._createShowAllButton();
},
_createAutocomplete: function() {
var selected = this.element.children( ":selected" ),
value = selected.val() ? selected.text() : "";
this.input = $( "<input>" )
.appendTo( this.wrapper )
.val( value )
.attr( "title", "" )
.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
.autocomplete({
delay: 0,
minLength: 0,
source: $.proxy( this, "_source" )
})
.tooltip({
tooltipClass: "ui-state-highlight"
});
this._on( this.input, {
autocompleteselect: function( event, ui ) {
ui.item.option.selected = true;
this._trigger( "select", event, {
item: ui.item.option
});
},
autocompletechange: "_removeIfInvalid"
});
},
_createShowAllButton: function() {
var input = this.input,
wasOpen = false;
$( "<a>" )
.attr( "tabIndex", -1 )
.attr( "title", "Show All Items" )
.tooltip()
.appendTo( this.wrapper )
.button({
icons: {
primary: "ui-icon-triangle-1-s"
},
text: false
})
.removeClass( "ui-corner-all" )
.addClass( "custom-combobox-toggle ui-corner-right" )
.mousedown(function() {
wasOpen = input.autocomplete( "widget" ).is( ":visible" );
})
.click(function() {
input.focus();
// Close if already visible
if ( wasOpen ) {
return;
}
// Pass empty string as value to search for, displaying all results
input.autocomplete( "search", "" );
});
},
_source: function( request, response ) {
var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
response( this.element.children( "option" ).map(function() {
var text = $( this ).text();
if ( this.value && ( !request.term || matcher.test(text) ) )
return {
label: text,
value: text,
option: this
};
}) );
},
_removeIfInvalid: function( event, ui ) {
// Selected an item, nothing to do
if ( ui.item ) {
return;
}
// Search for a match (case-insensitive)
var value = this.input.val(),
valueLowerCase = value.toLowerCase(),
valid = false;
this.element.children( "option" ).each(function() {
if ( $( this ).text().toLowerCase() === valueLowerCase ) {
this.selected = valid = true;
return false;
}
});
// Found a match, nothing to do
if ( valid ) {
return;
}
// Remove invalid value
this.input
.val( "" )
.attr( "title", value + " didn't match any item" )
.tooltip( "open" );
this.element.val( "" );
this._delay(function() {
this.input.tooltip( "close" ).attr( "title", "" );
}, 2500 );
this.input.data( "ui-autocomplete" ).term = "";
},
_destroy: function() {
this.wrapper.remove();
this.element.show();
}
});
})( jQuery );
$(function() {
$( "#combobox-test" ).combobox();
$( "#toggle" ).click(function() {
$( "#combobox" ).toggle();
});
});


</script>

<!--autocomplete-->
<script>
<?php /* Make a Global variable to be used for adding to the list on the bottom */?>
var currentSuggestion={};


$(document).ready(function(){
	var result = <?php echo json_encode($article_list); ?>;
	var arr = [];
	var len = result.length;
	for (var i = 0; i < len; i++) {
		var dataobj = {
			value: String(result[i].value),
			data: String(result[i].data),
			views: String(result[i].views),
		};
		arr.push(dataobj);
	}
	
	jQuery('#autocomplete').autocomplete({
		lookup: arr,
		onSelect: function (suggestion) {
			currentSuggestion = suggestion;
		}
	});
	
})

</script>  
<!-- sortable code -->	

  <script>
  $(function() {
    $( "#included" ).sortable();
  /*  $( "#included" ).disableSelection();*/
  });
  </script>
	

  <div class="container-fluid">

<div style="<?php  echo ($messages =='' ? 'display:none;':'');?>" id="form-errors" class="alert alert-danger col-sm-12"><?php echo $messages;?> </div>

    <div class="span10 offset1">
    	<div class="row">
		    <h3><?php echo $title;?></h3>
			<div class="pull-right">
				<?php echo($add === false ? '<a class="btn btn-success" href="'.$this->base_url.'siteadmin/pages/add">Add</a>' : '' );?>
			</div>
		</div>
    				
		<form action="" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">

				<div class="panel panel-default">				
				<h2 class="panel-heading">Manage Page:</h2>
					
					<form class="form-inline" role="form">					
						<span class="form-group">
							<label for="search">Add Articles</label>
										<input type="text" name="search" id="autocomplete" style="width:98%;padding:4px 5px" placeholder="Name of article" value="" />
								<div class="autocomplete-suggestions" id="suggestions-container" style="position: relative; width: 400px; margin: 10px;"></div>
						</span>
						<button id="add" type="submit" class="btn default">Add</button>
					</form> 
				</div><!-- end included panel -->	
					<form class="form-inline" method="post" action="<?php echo($add === true ? $this->base_url.'siteadmin/pages/edit' : '' );?>" role="form">	
						<h3>Included articles</h3>
						<div class="well">
							<ul id="included" >
							</ul>
						</div>
							
<hr>
<input type="hidden" name="id" value="<?php echo $page['id']; ?>">

Name: <input type="text" name="page" value="<?php echo $page['page']?>"><br>
Template: <select name="template">
 <?php
 echo $this->selectedOption($templates,$page['template']);
?>
		</select>

Type: <select name="pagetype">
		 <?php
			echo $this->selectedOption(array("page", "blog"),$page['pagetype']);
		?>
		</select>

<br>
Headline: <input type="text" name="headline" value="<?php echo $page['headline']?>"><br>
Category: 
<select name="categoryname">
<?php
echo $this->selectedOption($categories,$page['categoryname']);
?>
</select>


Controller: <input type="text" name="controller" value="<?php echo $page['controller']?>"><br>
Meta: <br><textarea name="meta"><?php echo $this->textAreaOut($page['meta']);?></textarea><br>
Priority: <input type="text" name="priority" value="<?php echo $page['priority']?>"><br>

<br>
		
Date: <input type="text" name="date" value="<?php echo $page['date'];?>">	<br>
Last Update: <input type="text" name="lastupdate" value="<?php echo $page['lastupdate'];?>">	<br>
Published: <input type="checkbox" name="published" <?php echo (@$page['published'] == 1?'checked':'')?> value="<?php echo @$page['published']?>"> 
Cache: <input type="checkbox" name="cache" <?php echo (@$page['cache'] == 1?'checked':'')?> value="<?php echo @$page['cache']?>"> 
Minify: <input type="checkbox" name="minify" <?php echo (@$page['minify'] == 1?'checked':'')?> value="<?php echo @$page['minify']?>"> 


		
			<div class="form-actions">		
				<button id="submit" name="submit" class="btn success" value="<?php echo($add !== true ? 'Update':'Create' )?>"><?php echo($add !== true ? 'Update':'Create' )?></button>		
				<a class="btn" href="/siteadmin/pages<?php echo $back_link?>">Back to Table</a>
			</div>
		</form>
		<form action="/siteadmin/pages<?php echo $back_link?>" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="indexSelected[]" value="<?php echo $page['id']; ?>">
			<button type="submit" class="btn btn-warning" name="submit" value="Delete Record" onclick="return confirm('Are you sure you want to remove this record?')">Delete</button>
		</form>
	</div>
</div>	



<script>
var i=0;
$(document).ready(function(){
	var articles = <?php echo json_encode($included_list); ?>; //array_values
if( articles != null){

	articles.forEach(function(article) {
		$('ul#included').append('<li class="ui-state-default remove"><a class="btn btn-danger">' + article.name + '</a><a class="btn btn-default" href="/siteadmin/content/edit?article=' + article.id + '">view</a><input type="hidden" name="articleid[]" value="' + article.id + '"><select id="selectview' + ++i +'" name="view[]"> <?php foreach($views as $value)echo"<option value=".$value.">$value</option>"?></select><select id="selectagg' + i +'" name="aggregate[]"><option value="separate">separate</option><option value="aggregate">aggregate</option><option value="agg-pos">agg-pos</option></select></li>');
$("ul#included select#selectview"+ i).find('option[value="' + article.views + '"]').attr("selected", "selected");
$("ul#included select#selectagg"+ i).find('option[value="' + article.aggregate + '"]').attr("selected", "selected");
	});
}	
 
	
});

$('body').on('click','button#add',function(event){
 event.preventDefault(); 
 <?php /* event.defaultPrevented; */ ?>
        var element = $(this);
	
	if(currentSuggestion.value != '' && typeof currentSuggestion.value !== "undefined"){
		var arr = [];
		$('ul#included li a').each(function( i ) {
				arr.push($( this ).text() );
		  });
		  
		element.parent().find('input[name="search"]').val("");
		
	//	if( $.inArray( currentSuggestion.value, arr) == -1 ){	 
			$('ul#included').append('<li class="ui-state-default remove"><a class="btn btn-danger">' + currentSuggestion.value + '</a><a class="btn btn-default" href="/siteadmin/content/edit?article=' + currentSuggestion.data + '">view</a><input type="hidden" name="articleid[]" value="' + currentSuggestion.data + '"><select id="selectview' + ++i +'" name="view[]"> <?php foreach($views as $value)echo"<option value=".$value.">$value</option>"?></select><select id="selectagg' + i +'" name="aggregate[]"><option value="separate">separate</option><option value="aggregate">aggregate</option><option value="agg-pos">agg-pos</option></select></li>');
		//}
	}
	//console.log(name);
}); 

 $('body').on('click','li.remove a.btn-danger',function(event){
	event.preventDefault(); 
	var element = $(this);	
	element.parent().remove();
	//console.log(name);
}); 



</script>

	<div id="footer">
	  PHP-MVCMS - Copyright &copy; 2016 - Carl Turechek
  	
	 PHP-MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PHP-MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PHP-MVCMS.  If not, see http://www.gnu.org/licenses/.
	
	</div>
