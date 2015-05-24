<?php $this->display('views/header.tpl.php') ?>
<body>
<div class="container">
   <div class="twelve columns">
      <h3>Edit Post</h3>

      <form method="post" enctype="multipart/form-data" >
         <input type="hidden" name="id" value="<?=$this->post->id?>" />
         <div class="row">
            <div class="offset-by-one one columns">
               Name
            </div>
            <div class="two columns">
               <input type="text" name="author" maxlength="64" style="display: none" />
               <input type="text" name="pumpkin" maxlength="64" style="width: 100%" value="<?=$this->post->author?>" />
            </div>
         </div>
         <div class="row">
            <div class="offset-by-one one columns">
               Subject
            </div>
            <div class="four columns">
               <input type="text" name="subject"  maxlength="160" style="width: 100%" value="<?=$this->post->subject?>" />
            </div>
         </div>
         <div class="row">
            <div class="offset-by-one one columns">
               Message
            </div>
            <div class="eight columns">
               <textarea name="message" maxlength="20000" style="width: 100%; min-height: 200px"><?=$this->post->post?></textarea>
            </div>
         </div>
         <div class="row">
            <div class="offset-by-one one columns">
               Images
            </div>
            <div class="eight columns">
               <b>Image editing coming soon...</b>
            </div>
         </div>
         <div class="row">
            <div class="offset-by-one one columns">
               Update
            </div>
            <div class="eight columns">
               <input type="submit" name="action" value="Update" class="button"/>
            </div>
         </div>
      </form>

      <form method="post" action="process.php" onsubmit="return confirm('Do you really want to delete this post?');" >
         <input type="hidden" name="id" value="<?=$this->post->id?>" />
         <div class="row">
            <div class="twelve columns">
               <h3>Delete Post</h3>
               <p>
                  Deleting this post will remove all content and images from the post.  Once you delete
                  a post, you will not be able to edit it. Replies to this post will not be affected.
               </p>
               <input type="submit" name="action" value="Delete" class="button"/>
            </div>
         </div>
      </form>

      <div class="back"><a href="<?=WEB_BASE_DIR?>">Back to the main page</a></div>
   </div>
</div>

<?php $this->display('views/footer.tpl.php'); ?>
</body>
</html>
