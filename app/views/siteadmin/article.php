<?php /*	Copyright © 2016 
	
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
    along with MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<div class="container-fluid">

<div style="<?php  echo ($messages =='' ? 'display:none;':'');?>" id="form-errors" class="alert alert-danger col-sm-12"><?php echo $messages;?> </div>

    <div class="span10 offset1">
    	<div class="row">
		    <h3><?php echo $title;?></h3>
			<div class="pull-right">
				<?php echo($add === false ? '<a class="btn btn-success" href="'.$this->base_url.'siteadmin/content/add">Add</a>' : '' );?>
			</div>
		</div>
		<form action="<?php echo($add === true ? $this->base_url.'siteadmin/content/edit' : '' );?>" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
	
<hr>


<input type="hidden" name="id" value="<?php echo $article['id']; ?>">
<input type="hidden" name="update" value="<?php echo($add == true ? '0':'1' )?>">

Name: <input type="text" name="articlename" value="<?php echo $article['articlename']?>"><br>

Menu:<select name="menu" id="menu" class="form-control">
		 <?php
			echo $this->selectedOption($this->getSelectFields($menus,'name'),$article['menu']);
		?>
		</select><br>
Menu Type: <input type="text" name="menutype" size="50" value="<?php echo $article['menutype']?>">

<br>
Type: <select name="type" id="type" class="form-control">
		 <?php
			echo $this->selectedOption(array("default", "header", "footer", "menu","blog","noindex","article","auto p false","html"),$article['type']);
		?>
		</select>

<br>

		<p id="editor">
			
			<textarea cols="80" id="content" name="content" rows="10"><?php echo $article['content']?></textarea>
			<?php if($article['type']!=='html'){
			?>
			<script type="text/javascript">
			<?php //<![CDATA[
				// This call can be placed at any point after the
				// <textarea>, or inside a <head><script> in a
				// window.onload event handler.

				// Replace the <textarea id="editor"> with an CKEditor
				// instance, using default configurations.?>
				CKEDITOR.replace( 'content',
                { <?php /*width: 750,   */ ?> resize_minWidth: 150,<?php echo($article['type'] == 'auto p false'?'enterMode: CKEDITOR.ENTER_BR, autoParagraph:false,':'')?>
                    filebrowserBrowseUrl :'<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/browser/default/browser.html?Connector=<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
                    filebrowserImageBrowseUrl : '<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/browser/default/browser.html?Type=Image&Connector=<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
                    filebrowserFlashBrowseUrl :'<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/browser/default/browser.html?Type=Flash&Connector=<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
					filebrowserUploadUrl  :'<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=File',
					filebrowserImageUploadUrl : '<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=Image',
					filebrowserFlashUploadUrl : '<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=Flash'
				});

			<?php //]]> ?>
			</script>
		<?php } ?>	
		<?php /* in case of php executable contents, prepare for output:	
				$value = $row["content"];				
				$value = str_replace("&", "&amp;" ,$value);
	            $value = str_replace("<", "&lt;",$value);
	           $row['content'] = $value;  */?>
		</p>


		
	
		<br>
		
Date: <input type="text" name="date" value="<?php echo $article['date'];?>">	<br>
Last Update: <input type="text" name="lastupdate" value="<?php echo $article['lastupdate'];?>">	<br>


Published: <input type="checkbox" name="published" <?php echo (@$article['published'] == 1?'checked':'')?> value="<?php echo @$article['published']?>"> 

	
			<div class="form-actions">	

			<?php if($article['type']=='html'){ ?>
				<button id="submit" name="submit" class="btn success" value="submit">Save</button>					
			<?php } ?>			
				<a class="btn" href="/siteadmin/content<?php echo $back_link?>">Back to Table</a>
			</div>
		</form>
		<form action="/siteadmin/content<?php echo $back_link?>" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="indexSelected[]" value="<?php echo $article['id']; ?>">
			<button type="submit" class="btn btn-warning" name="submit" value="Delete Record" onclick="return confirm('Are you sure you want to remove this record?')">Delete</button>
		</form>
	<br>
	<hr>
	<div id="displaypages">
	</div>
	<div id="paging">
	</div>
	</div>
</div>	


<script>

function getQueryParam(param) {
    location.search.substr(1)
        .split("&")
        .some(function(item) { // returns first occurence and stops
            return item.split("=")[0] == param && (param = item.split("=")[1])
        })
    return param
}	
$(document).ready(function(){
	loadContent(1);	
});
	
$(function() {	
	$(document).on('click', 'ul.pagination a', function(e){
		e.preventDefault();		

		href = $(this).attr("href");
		if(href != undefined){
			var id=href.split("?")[1];		
			id=id.split("&")[0];
			id=id.split("=")[1];
			loadContent(id);			
		}
	});
	
});
function loadContent(id){	
	var nocache = new Date().getTime();
	var extra='?nocache=' + nocache;			
	/*USES JQUERY TO LOAD THE CONTENT*/
	$.getJSON("<?php echo$this->base_url;?>siteadmin/ajaxpaging" + extra, {pid: id, aid:<?php echo $article['id']; ?>, format: 'json' , cache:false}, function(json) {

		/* THIS LOOP PUTS ALL THE CONTENT INTO THE RIGHT PLACES*/
		$.each(json, function(key, value){			
			if(key == 'pages_assigned_to'){
				$('#displaypages').html(value);
			}
			if(key == 'currentpage'){
				$('#paging').html(value);
			}
		});			
	});	
}
	
	
	
	
	</script>
 <style>
 img {max-height:150px;}
 </style>
 

<?php

 //echo '<pre>'.htmlspecialchars(print_r($rows, true)).'</pre>';
 
 ?>
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
		<hr />
		<p>
			CKEditor - The text editor for Internet - <a href="http://ckeditor.com/">http://ckeditor.com</a>
		</p>
		<p id="copy">
			Copyright &copy; 2003-2010, <a href="http://cksource.com/">CKSource</a> - Frederico
			Knabben. All rights reserved.
		</p>
	</div>
