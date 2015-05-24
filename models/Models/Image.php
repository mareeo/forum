<?php
/**
 * This class is the php-activerecord class for the image table.
 *
 * The image table represents any image uploaded to the forum.  Each
 * image is linked to a post using the post_id column of this table.
 */
namespace Models;

class Image extends \ActiveRecord\Model {

   /**
    * Basic variables for php-activerecord
    */
   static $table_name = 'image';
   static $primary_key = 'id';
   static $before_destroy = array('deleteFiles');
   
   /**
    * Method to delete the image files assoicated with this image record.
    * This will be called automatically when you use the delete() method
    * for an image.
    * 
    * @return void
    */
   function deleteFiles() {
      unlink(FS_THUMB_DIR.$this->thumbnail);			
      unlink(FS_IMG_DIR.$this->image);
   }
   
}
