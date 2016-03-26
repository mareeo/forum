<?php
/**
 * This is something
 */
namespace Forum;

use Models\Post;
use DateTime;


/**
 * This class contains all the methods and logic for posts.
 */
class PostService {
    
    /**
     * Get an individual post by ID
     *
     * @param int $id ID of the post
     * @return Models/Post|null The post if found, null if not.
     */
    public static function getPostByID($id) {
        
        // Find post.  Redirect if not found or error.
        try {
            $post = Post::find($id);
        } catch (\Exception $e) {
            return null;
        }
        return $post;
    }
    
    /**
     * Get posts for a page
     *`
     * @return Model\Post[] An array of posts
     */
    public static function getPosts($afterPost = null) {
        
        if($afterPost !== null) {
            $afterPost = (int)$afterPost;
            
            $conditions = array (
                'id < ? AND thread LIKE concat(id, "/")',
                $afterPost
            );
            
        } else {
            $conditions = array (
                'thread LIKE concat(id, "/")'
            );
        }
        
        
        
        // Get the 50 newest threads
        $posts = Post::all(
                array(
                        'conditions' => $conditions,
                        'order' => 'timestamp DESC',
                        'include' => 'images',
                        'limit' => 20
                )
        );
        
        return $posts;
    }
    
    /**
     * Create a new post
     *
     * @param array $request The POST data for the request.
     * @param String $token The user's edit token.
     * @param String $ip The user's IP address.
     * @return int The ID of the newly created post
     */
    public static function createPost($request, $token, $ip) {
        
        $author = trim($request['pumpkin']);
        $subject = trim($request['subject']);
        
        
        
        // Sanitize input
        $author = str_replace("\n","",$author);
        $author = strip_tags($author);
        $author = substr($author, 0, 60);

        $subject = str_replace("\n","",$subject);
        $subject = htmlspecialchars($subject);
        $subject = substr($subject, 0, 160);
        
        // If they didn't enter an author (pumpkin) and subject, exit
        if(strlen($author) == 0 || strlen($subject) == 0) {
           return null;
        }
        
        $allowedTags = '<b><i><u><s>';
        
        $message = htmlspecialchars($request['message']);
        $message = substr($message, 0, 20000);
        
        
        // Create the new post using the sanitized data
        $post = new Post();
        $post->author = $author;
        $post->subject = $subject;
        $post->post = $message;
        $post->timestamp = new DateTime();
        $post->ip = $ip;
        $post->token = $token;
        
        // If this is a reply to the post, set the parent_id
        if(isset($request['replyTo'])) {
            
           $parent = Post::find($request['replyTo']);
           $post->thread = $parent->thread;
           
           
           if($request['subject'] == '!delete') {
              $parent->delete();
              
              // Redirect back to main page
              header("Location: index.php");
              exit;
           }
           
           if($request['subject'] == '!remove') {
              $parent->author = '[removed]';
              $parent->subject = '[removed]';
              $parent->post = '';
              
              $parent->save();
              
              // Redirect back to main page
              header("Location: index.php");
              exit;
           }
        } else {
            $post->thread = '';
        }

//        var_dump($post);exit;
        
        // Save it (insert in to database)
        $post->save();
        
        $post->thread = $post->thread . $post->id . '/';
        
        $post->save();
        
        return $post->id;
    }
    
    /**
     * Process images submitted for the post
     * @param int $post_id The ID of the post for which we're processing images.
     * 
     */
    public static function processImages($post_id) {
        
        if(!array_key_exists("image", $_FILES))
            return;
        
        // Now we need to handle the upload images.  First figure out how many images
        $numFiles = count($_FILES["image"]["name"]);
        
        // If they submitted files
        if($numFiles) {
           
           // Get a PDO database handle
           $dbh = \ActiveRecord\Connection::instance();
           
           // Get the next image id
           $results = $dbh->query("SELECT MAX(id) FROM image");
           $id = $results->fetchColumn() + 1;
           
           // Limit to 5 files
           if($numFiles > 5)
              $numFiles = 5;
              
           // Loop that many times
           for($i = 0; $i < $numFiles; $i++) {
              
              // Skip if no file field was blank
              if(strlen($_FILES["image"]['name'][$i]) < 4)
                 continue;
              
              // Validate the file's extension.  Skip if not valid
              $extension = getExtension($_FILES["image"]['name'][$i]);
              
              
              if(!preg_match("/((jpe?g)|(png)|(gif))$/i", $extension))
                 continue;
              
              // Construct the new files names
              $filename = "$id.$extension";
              $destination = FS_IMG_DIR.$filename;
              $thumbfilename = "$id.jpg";
              $thumb = FS_THUMB_DIR.$thumbfilename;
              
              // Copy the file to the image directory
              copy($_FILES['image']['tmp_name'][$i], $destination);
              
              // Now for thumbnail creation
              
              // Get dimensions of image
              $size = getimagesize($destination);
                       
              // If the image is larger than 7500*7500 we won't even try to generate a thumbnail
              if( ($size[0] * $size[1] < 56250000)) {
                 makeThumb($destination, $thumb, $extension);						
              } else {
                 $thumbfilename = null;
              }
              
              // Create and save the new image record
              $image = new \Models\Image();
              $image->post_id = $post_id;
              $image->image = $filename;
              $image->thumbnail = $thumbfilename;
              $image->save();
              
              $id++;
           }
           
        }
    }
    
    /**
     * Edit an already existing post
     * @param  array  $request Array of POST data
     * @param  String $token   The editing token of the user
     * @param  String $ip      The IP address of the user
     * @return bool            True on success, false on failure
     */   
    public static function editPost($request, $token, $ip) {
        
        $post = self::getPostByID($request['id']);
        
        if($post === null)
            return false;
        
        if($post->token !== $token)
            return false;
            
        $author = trim($request['pumpkin']);
        $subject = trim($request['subject']);
        
        // Sanitize input
        $author = str_replace("\n","",$author);
        $author = strip_tags($author);
        $author = substr($author, 0, 60);
        
        $subject = str_replace("\n","",$subject);
        $subject = strip_tags($subject);
        $subject = substr($subject, 0, 160);
        
        // If they didn't enter an author (pumpkin) and subject, exit
        if(strlen($author) == 0 || strlen($subject) == 0) {
           return false;
        }
        
        $allowedTags = '<b><i><u><s>';
        
        $message = strip_tags($request['message'], $allowedTags);
        $message = substr($message, 0, 20000);
        
        $post->author = $author;
        $post->subject = $subject;
        $post->post = $message;
        $post->ip = $ip;
        $post->save();
        
        return true;
    }
    
    /**
     * Delete a post
     * @param  array $request  Array of POST data
     * @param  String $token   The editing token of the user
     * @return bool            True on success, false on failure
     */
    public static function deletePost($request, $token) {
        
        $post = self::getPostByID($request['id']);
        
        if($post === null)
            return false;
        
        if($post->token !== $token)
            return false;
            
        // Delete the content of the post
        $post->author = '[deleted]';
        $post->subject = '[deleted]';
        $post->post = '';
        $post->token = uniqid(); //Set the token to some junk value so it can't be edited again
        $post->save();
        
        // Get images for this post
        $images = $post->getImages();
        
        // Delete the images for this post
        foreach($images as $image) {
           $image->delete();
        }

        return true;
    }
    
    /**
     * Generates the tree of all child posts for the given post
     * @param  Models\Post $post The post to generate the tree for
     * @return stdClass          The post's tree
     */
    public static function generateTree(Post $post) {
        
        global $pdo;
        
        $query = $pdo->prepare(
        <<<SQL
        SELECT * FROM post WHERE thread LIKE ? ORDER BY id
SQL
        );
        
        $search = $post->id . "/%";
                
        $query->execute(array($search));
        
        $results = $query->fetchAll(\PDO::FETCH_CLASS);
        
        foreach($results as $key =>$result) {
            if($result->id . '/' == $result->thread) {
                $root = $result;
                unset($results[$key]);
                break;
            }
        }
        
        self::getPostImages($root);
        
        self::treeThing($root, $results);
        
        return $root;
    }
    
    /**
     * Recursive function to generate a post tree
     * @param  stdClass $root  The root post 
     * @param  Array $posts    An array of child posts not yet in the tree
     */
    public static function treeThing($root, &$posts) {
        
        $children = [];
        
        foreach($posts as $key => $post) {
            $pattern = "/^" . str_replace('/', '\/', $root->thread) . "\d+\/$/";
            if(preg_match($pattern, $post->thread) === 1) {
                self::getPostImages($post);
                $children[] = $post;
                unset($posts[$key]);
            }
        }
        
        foreach($children as $child) {
            self::treeThing($child, $posts);
        }
        
        $root->children = $children;
        
    }
    
    /**
     * Get the images for a post and assign them to the ->images property.
     * @param  stdClass $post The post for which to get images
     */
    public static function getPostImages(&$post) {
        
        global $pdo;
        
        $query = $pdo->prepare(<<<SQL
        SELECT * FROM image WHERE post_id = ?
SQL
        );
                
        $query->execute(array($post->id));
        
        $results = $query->fetchAll(\PDO::FETCH_CLASS);
        
        $post->images = $results;
        
    }
}
