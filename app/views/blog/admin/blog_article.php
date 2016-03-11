   <div class="container-fluid">

<div style="<?php  echo ($messages =='' ? 'display:none;':'');?>" id="form-errors" class="alert alert-danger col-sm-12"><?php echo $messages;?> </div>

    <div class="span10 offset1">
    	<div class="row">
		    <h3><?php echo $title;?></h3>
			<div class="pull-right">
				<?php echo($add === false ? '<a class="btn btn-success" href="'.$this->base_url.'siteadmin/content/add">Add</a>' : '' );?>
			</div>
		</div>
		<form action="" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
	


<hr>


<input type="hidden" name="id" value="<?php echo $article['id']; ?>">
<input type="hidden" name="update" value="<?php echo($add == true ? '0':'1' )?>">
<label for="headline-text"> Headline</label>
<input type="text" id="headline-text" name="articlename" value="<?php echo @$article['articlename']?>" >
<label for="slug"> Slug</label>
<?php 
	$elements = explode('/',@$article['link']);
	$last_element = end($elements);
?>		
<input type="text" id="slug" name="slug" value="<?php echo @$last_element;?>" >
<br>
<?php /*
Views: 
<select name="views">
<?php
echo $this->selectedOption($views,$article['views']);
?>
</select>
*/?>
<label for="type-select"> Select Type</label>
			<select name="type" id="type-select" class="form-control">
			<?php
			echo $this->selectedOption(array("blog", "code"),$article['type']);
			?>
			</select>

			<label for="category-select"> Category Select</label>
		 	<input type="text" list="datalist" id="category-select" name="category" value="<?php echo@$article['category'];?>">
                  <datalist id="datalist" >
				  
                        <?php echo $this->selectedOption($this->getSelectFields($categories,'category'),@$article['category']);?>
						
				</datalist>


			
			<label for="description"> Description</label>
			<input type="text" id="description" name="description" value="<?php echo @$article['description']?>" >		
			
			
			<?php /* or edit image! */?>
			<label for="PostArticleImage"> Upoad Image</label>
			<input type="file" name="PostArticleImage" id="PostArticleImage"  />
               
 <?php /*
Enter genre for picture: 
<input list="genre" name="ud_genre" size="25" placeholder="P. Genre / V.A. Artform" value="">
<datalist id="genre">

// echo $this->selectedOption($genres,$article['genre']);

</datalist>
*/	
?>


<br>

		<p id="editor">
			
			<textarea cols="80" id="content" name="content" rows="10"><?php echo $article['content']?></textarea>
			<script type="text/javascript">
			<?php //<![CDATA[

				// This call can be placed at any point after the
				// <textarea>, or inside a <head><script> in a
				// window.onload event handler.

				// Replace the <textarea id="editor"> with an CKEditor
				// instance, using default configurations.?>
				CKEDITOR.replace( 'content',
                { <?php /*width: 750,   */ ?> resize_minWidth: 150,
                    filebrowserBrowseUrl :'<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/browser/default/browser.html?Connector=<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
                    filebrowserImageBrowseUrl : '<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/browser/default/browser.html?Type=Image&Connector=<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
                    filebrowserFlashBrowseUrl :'<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/browser/default/browser.html?Type=Flash&Connector=<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/connector.php',
					filebrowserUploadUrl  :'<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=File',
					filebrowserImageUploadUrl : '<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=Image',
					filebrowserFlashUploadUrl : '<?php echo $this->base_url; ?>filemanager_in_ckeditor/js/ckeditor/filemanager/connectors/php/upload.php?Type=Flash'
				});

			<?php //]]> ?>
			</script>
		</p>


		
	
		<br>
		
Date: <input type="text" name="date" value="<?php echo $article['date'];?>">	<br>
Last Update: <input type="text" name="lastupdate" value="<?php echo $article['lastupdate'];?>">	<br>



Published: <input type="checkbox" name="published" <?php echo (@$article['published'] == 1?'checked':'')?> value="<?php echo @$article['published']?>">



		
			<div class="form-actions">			
				<a class="btn" href="/blog/admin<?php echo $back_link?>">Back to Table</a>
			</div>
		</form>
		<form action="/blog/admin<?php echo $back_link?>" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="indexSelected[]" value="<?php echo $article['id']; ?>">
			<button type="submit" class="btn btn-warning" name="submit" value="Delete Record" onclick="return confirm('Are you sure you want to remove this record?')">Delete</button>
		</form>
	</div>
</div>	


<script>
/*
var select = document.getElementById("type");
var editor = document.getElementById("editor1");
var submit = document.getElementById("submit");

function check(){
	var selectedString = select.options[select.selectedIndex].value;
    if(selectedString == 'static'){editor.style.display = 'none';submit.style.display = 'block';}else{editor.style.display = 'block';submit.style.display = 'none';}
}
check();
select.onchange = function(){
check();
}
	*/
	</script>
 <style>
 img {max-height:150px;}
 </style>
<?php




 //echo '<pre>'.htmlspecialchars(print_r($rows, true)).'</pre>';
 
 ?>
