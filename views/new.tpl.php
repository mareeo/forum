<?php $this->display('views/header.tpl.php') ?>
<body>
<div class="container">
   <div class="twelve columns">
      <h3>New Post</h3>
   </div>

   <?php $this->display('views/form.tpl.php'); ?>
   <div class="twelve columns">
      <div class="back"><a href="<?=WEB_BASE_DIR?>">Back to the main page</a></div>
   </div>

   <?php $this->display('views/footer.tpl.php'); ?>
</div>
</body>
</html
