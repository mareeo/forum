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

<?php
$this->layout('base.plates', [
    'forum' => $forum,
    'title' => $title
]);
?>
<div class="container">
      <div class="twelve columns">
         <h2><a href=".">Posts</a></h2>
         <a href="<?=WEB_BASE_DIR?>new">New Post</a>
   </div>
   <div class="twelve columns">
      <?php foreach ($posts as $post): ?>
         <div class="thread">
            <?php recursiveDisplay($post) ?>
         </div>
      <?php endforeach; ?>
      <?php $lastPost = end($posts);?>
      <a href="?after=<?=$lastPost->id?>">Next Page</a>
   </div>
</div>