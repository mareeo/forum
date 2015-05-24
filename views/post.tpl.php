<?php $this->display('views/header.tpl.php') ?>

<div class="container">
   <div class="twelve columns">
      <h5 class="post-title"><?= $this->post->subject ?></h5>

      <div class="sub-title">
         Posted by <?= $this->post->author ?> on the <?= $this->post->timestamp->format('jS')?> of <?= $this->post->timestamp->format('F') ?> at <?= $this->post->timestamp->format('g:i A') ?>
         <?php if(isset($_COOKIE['token']) && $_COOKIE['token'] === $this->post->token): ?>
            : <a href="<?=WEB_BASE_DIR?>edit/<?=$this->post->id?>">Edit this post</a>
         <?php endif; ?>
      </div>

   </div>

   <div class="twelve columns">
      <div class="message">
         <?= $this->message ?>
      </div>
   </div>
   <div class="twelve columns">
      <?php if(count($this->images) > 0): ?>
         <div class="images">
            <?php foreach($this->images as $image):
               if($image->thumbnail !== null)
                  $thumb = THUMB_DIR.$image->thumbnail;
               else
                  $thumb = IMG_DIR.$image->image;
               ?>
               <a href="<?=IMG_DIR.$image->image?>">
                  <img src="<?=$thumb?>" />
               </a>
            <?php endforeach; ?>
         </div>
      <?php endif; ?>
   </div>

      <div class="twelve columns" style="text-align: center;">

         <a href="<?=WEB_BASE_DIR?>">Back to the main page</a>
      </div>
   <div class="twelve columns">
      <h4>Thread</h4>
      <div class="thread">
         <?php
         $tree = Forum\PostService::generateTree($this->root);
         recursiveDisplay($tree);
         ?>
      </div>
   </div>
   <div class="twelve columns">
      <h4>Reply</h4>
   </div>
   <?php $this->display('views/form.tpl.php'); ?>
   <?php $this->display('views/footer.tpl.php'); ?>



</div>

</body>
