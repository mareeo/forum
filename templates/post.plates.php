<?php $this->layout('base.plates', [
   'forum' => $forum,
   'title' => $title
]); ?>

<div class="container">
   <div class="twelve columns">
      <h5 class="post-title"><?= $post->subject ?></h5>

      <div class="sub-title">
         Posted by <?= $post->author ?> on the <?= $post->timestamp->format('jS')?> of <?= $post->timestamp->format('F') ?> at <?= $post->timestamp->format('g:i A') ?>
         <?php if(isset($_COOKIE['token']) && $_COOKIE['token'] === $post->token): ?>
            : <a href="<?=WEB_BASE_DIR?>edit/<?=$post->id?>">Edit this post</a>
         <?php endif; ?>
      </div>

   </div>

   <div class="twelve columns">
      <div class="message">
         <?= $message ?>
      </div>
   </div>
   <div class="twelve columns">
      <?php if(count($images) > 0): ?>
         <div class="images">
            <?php foreach($images as $image):
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
         <?php \Forum\Forum\Util::displayTree($root); ?>
      </div>
   </div>
   <div class="twelve columns">
      <h4>Reply</h4>
   </div>
   <?php $this->insert('form.plates', ['name' => $name, 'post' => $post]); ?>
</div>
