<?php $this->layout('base.plates', [
   'forum' => $forum,
   'title' => $title
]); ?>
<div class="container">
   <div class="twelve columns">
      <h3>New Post</h3>
   </div>

   <?php $this->insert('form.plates', ['name' => $name]); ?>
   <div class="twelve columns">
      <div class="back"><a href="<?=WEB_BASE_DIR?>">Back to the main page</a></div>
   </div>
</div>
