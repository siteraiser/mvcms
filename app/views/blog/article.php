<div class="row">
        <div class="col s12 m6">
          <div class="card blue-grey darken-1">
            <div class="card-content white-text">
              <span class="card-title"><?php echo$article['articlename'];?></span>
              <p><?php echo$article['content'];?></p>
            </div>
            <div class="card-action">
              <a href="<?php echo$this->base_url.'blog/'.$article['category'];?>">See <?php echo $article['category'];?> Category</a>
            </div>
          </div>
        </div>
      </div>