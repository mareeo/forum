<form method="post" enctype="multipart/form-data" action="<?=WEB_BASE_DIR?>new">
    <?php if(isset($post)): ?>
        <input type="hidden" name="replyTo" value="<?= $post->id ?>" />
    <?php endif; ?>
   <div class="row">
      <div class="offset-by-one one column">
         Name
      </div>
      <div class="two columns">
         <input type="text" name="author" maxlength="64" style="display: none" />
         <input type="text" name="pumpkin" maxlength="64" style="width: 100%" value="<?=$name?>" />
      </div>
   </div>
   <div class="row">
      <div class="offset-by-one one column">
         Subject
      </div>
      <div class="four columns">
         <input type="text" name="subject"  maxlength="160" style="width: 100%" />
      </div>
   </div>
   <div class="row">
      <div class="offset-by-one one column">
         Message
      </div>
      <div class="eight columns">
         <textarea name="message" maxlength="20000" style="width: 100%; min-height: 200px"></textarea>
      </div>
   </div>

   <div class="row">
      <div class="offset-by-one one column">
         Images
      </div>
      <div class="eight columns">
         <div id="pickers"></div>
         <div href="#" id="addImage" class="button">Add image</div>
      </div>
   </div>
   <div class="row">
      <div class="offset-by-one one column">
         Post
      </div>
      <div class="eight columns">
         <input type="submit" name="action" value="Post" class="button"/>
      </div>
   </div>

   <div class="row">
      <div class="offset-by-one one column">
         Preview
      </div>
      <div class="eight columns">
         <div id="messagePreview"></div>
      </div>
   </div>
</form>
