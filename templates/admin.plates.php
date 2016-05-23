<?php
$this->layout('base.plates', [
    'forum' => $forum,
    'title' => $title
]);
?>
<div class="container">
   <?php if($loggedOut): ?>
      <div class="row">
         <div class="twelve columns">
            <h2>Logged out</h2>
         </div>
      </div>
   <?php endif; ?>
   <div class="row">
      <div class="twelve columns">
         <h2><a href=".">Admin</a></h2>
      </div>
   </div>
   <form method="post" enctype="multipart/form-data">
      <div class="row">
         <div class="offset-by-one eleven columns">
            Password<br>
            <input type="password" name="password" /><br>
            <input type="submit" name="action" value="Post" class="button"/>
         </div>
      </div>
   </form>
</div>