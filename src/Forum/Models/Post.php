<?php

/**
 * This class is the php-activerecord class for the post table.
 *
 * The post table represents a post of the forum.  It contaisn basic information
 * about a post.  A post may be considered a reply (or child) to another post. If
 * this is the case, you will find the id of the original (or parent) post in the
 * parent_id field.  If this field is null, this means the post is a 'root' post,
 * meaning it is the start of a new thread.
 */
namespace Forum\Forum\Models;

use DateTime;

class Post extends \ActiveRecord\Model
{
   /**
    * Basic variables for php-activerecord
    */
   static $table_name = 'post';
   static $primary_key = 'id';
   static $before_create = array('rateLimit');
	static $before_destroy = array('cleanDelete');
	
   private $children;
   
   /**
    * @var int The number of seconds for the rate limiting threshold
    * @access public
    */
   const RATE_LIMIT = 10;
	
   /**
    * Set up the php-activerecord association between posts and images.
    * Doing this just allows the activerecord library to fetch all images
    * associated with a post easily.
    */
	static $has_many = array(
	    array(
			'images',
			'readonly' => true,
			'foreign_key' => "post_id",
			'primary_key' => 'id'
		)
	);
   
   /**
    * Gets the children of the current post
    * @return Post[] An array of Post objects, all of which are children of this post
    */
   public function getChildren() {
      
      
      // Get all posts with the parent_id of this post
      $conditions = array (
         'thread LIKE ?', $this->thread . '%/'
      );
       
       $children = Post::all(array('conditions' => $conditions));
       
       return $children;
   }
   
   /**
    * Find the root post of a post
    * @return Post The Post object for the root post of this thread
    */
   public function find_root() {
      
      $rootID = explode('/', $this->thread)[0];
      
      if($rootID == $this->id)
	 return $this;
      
      // Find the parent of this thread
      $root = Post::find($rootID);
      
      // Finally, return the parent
      return $root;
   }
   
   /**
    * Rate limiting for posting.  Dies if posting too quickly.
    *
    * Finds the most recent post by this IP address. If is within the
    * set threshold, the program will just die. This method will be
    * automatically called when you use the save() method of a Post.
    * 
    * @return bool True if okay.  Dies if not okay
    */
   public function rateLimit() {
      
      // Get the timestamp of the most recent post from this ip
      $conditions = array (
         'token' => $this->token
      );
      
      $mostRecent = Post::find(
         array(
            'select' => 'timestamp',
            'conditions' => $conditions,
            'order' => 'timestamp DESC',
            'limit' => '1'
         )
      );
      
      // No previous posts from this person.  It's good.
      if($mostRecent === null)
         return true;
      
      // Find seconds between this post and the current time
      $mostRecent->timestamp;
      $now = new DateTime();
      $interval = $now->getTimestamp() - $mostRecent->timestamp->getTimestamp();
      
      // If it's below our threshold, die.
      if($interval <= Post::RATE_LIMIT) {
         die("<h2>You're posting too quickly</h2>");
      }
      
   }
      
   /**
    * Get the image objects associated with a post
    * 
    * @return Image[] An array of image objects
    */
   public function getImages() {
      
      $conditions = array(
         'post_id' => $this->id
      );
      
      $images = Image::all(array('conditions' => $conditions));
      return $images;
   }
	
   /**
    * Recursive method to delete a post's images, and all
    * of the child posts beneath it.  This method will be automatically
    * when you use the delete() method of a Post.
    * 
    * @return Image[] An array of image objects
    */
   function cleanDelete() {
      
      $children = $this->getChildren();
      
      // Recursively call this method for any children
       foreach($children as $child) {
	  $child->delete();
       }
      
      
      // Get images for this post
      $images = $this->getImages();
      
      // Delete the image records
      foreach($images as $image) {
	      $image->delete();
      }
   }
}
