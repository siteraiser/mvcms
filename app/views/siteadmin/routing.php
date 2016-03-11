   <div class="container-fluid">

<div style="<?php  echo ($messages =='' ? 'display:none;':'');?>" id="form-errors" class="alert alert-danger col-sm-12"><?php echo $messages;?> </div>

    <div class="span10 offset1">
    	<div class="row">
		    <h3><?php echo $title;?></h3><?php echo(isset($create_link) ? '<a class="btn btn-success" href="'.@$create_link.'">Create</a>' : '' );?>
		</div>
    				
		<form action="" class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
	

<div class="row">
<?php
/*
echo "<hr><a class=\"pull-left\" href=\"/editimages$back_link&amp;article={$article['id']}\">Original<br><img src=\"/images/{$article['filename']}\"></a>";
echo "<a class=\"pull-left\" href=\"/editimages$back_link&amp;article={$article['id']}\">Display<br><img src=\"/images/th_{$article['filename']}?last_picture_update=" . filemtime($this->doc_root."images/th_{$article['filename']}")."\"></a>";
*/
?>
</div>
<hr>


		<p id="routes">
			
			<textarea cols="80" id="routes" name="routes" rows="10"><?php echo $routes;?></textarea>
		
		</p>
		<button type="submit" class="btn" name="Update" value="Update">Update</button>


		</form>
		
	</div>
</div>	


