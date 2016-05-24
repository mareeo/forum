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
            <?php \Forum\Forum\Util::recursiveDisplay($post) ?>
         </div>
      <?php endforeach; ?>
      <?php $lastPost = end($posts); ?>
      <?php if($lastPost !== false): ?>
      <a href="?after=<?=$lastPost->id?>">Next Page</a>
      <br><br>
      <?php endif; ?>
      <div class="button" id="changeTheme" style="font-size:10px">Change Theme</div>
   </div>


</div>