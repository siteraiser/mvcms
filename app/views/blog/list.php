<?php 	

	foreach($results as $record){
?>
	<div class="row">
        <div class="col s12 m6">
          <div class="card blue-grey darken-1">
            <div class="card-content white-text">
              <span class="card-title"><?php echo$record['articlename'];?></span>
              <p><?php echo$record['description'];?></p>
            </div>
            <div class="card-action">
              <a href="<?php echo$this->base_url.$record['link'];?>">This is a link</a>
              <a href="#">This is a link</a>
            </div>
          </div>
        </div>
      </div>
<?php 
	}
	echo $currentpage;
	
	/*
		echo '<pre>';
		print_r($content);
		echo  '</pre>';
		echo '<pre>';
		print_r($article);
		echo  '</pre>';
		echo '<pre>';
		print_r($articles);
		echo  '</pre>';
		
		*/
		?>				
<br>