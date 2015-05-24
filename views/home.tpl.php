<?php $this->display('views/header.tpl.php') ?>
<body>

<div class="container">
      <div class="twelve columns">
         <h2><a href=".">Posts</a></h2>
         <a href="<?=WEB_BASE_DIR?>new">New Post</a>
   </div>
   <div class="twelve columns">
      <?php foreach ($this->posts as $post): ?>
         <div class="thread">
            <?php //$post->recursiveDisplayChildren(); ?>
            <?php recursiveDisplay($post) ?>
         </div>
      <?php endforeach; ?>
      <?php $lastPost = end($this->posts);?>
      <a href="?after=<?=$lastPost->id?>">Next Page</a>
      <?php $this->display('views/footer.tpl.php'); ?>
   </div>
</div>




</body>
</html>
