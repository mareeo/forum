<?php

function displayPost($post) {

   if($post->id . "/" == $post->thread)
      $root = true;
   else
      $root = false;
   // If the post is a root post, we want to put it in a special span tag (for styling purposes)
   if($root) {
      echo "<span class='root'>";
   }

   $numImages = count($post->images);

   // Print the link to view the post
   echo "<a href=\"".WEB_BASE_DIR."view/$post->id\">$post->subject</a> ";

   echo '<span class="postDetails">';

   // Print (nt) if there is no message
   if($post->post == '')
      echo "(nt) ";

   // Print (image) if there are images for this post
   if($numImages == 1)
      echo "(image) ";
   elseif($numImages > 1)
      echo "($numImages images) ";

   $postDate = new DateTime($post->timestamp);
   $postDate = $postDate->format('F jS g:i A');

   // Print the author and the timestamp
   echo "By <span class='author'>$post->author</span> on <span class='timestamp'>".$postDate."</span>\n";

   // If a root post, close the span tag
   if($root) {
      echo "</span>";
   }

   echo '</span>';
}

function recursiveDisplay($root) {
   // Get all children of this post

   // Display this post
   echo "<div class='post'>\n";
   displayPost($root);

   // For each child post, do this same process
   foreach($root->children as $child) {
      recursiveDisplay($child);
   }
   echo "</div>\n";
}
?>

<?php $this->layout('base.plates') ?>

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
         <?php
         $tree = \Forum\Forum\PostService::generateTree($root);
         recursiveDisplay($tree);
         ?>
      </div>
   </div>
   <div class="twelve columns">
      <h4>Reply</h4>
   </div>
   <?php $this->insert('form.plates', ['name' => $name]); ?>
</div>
