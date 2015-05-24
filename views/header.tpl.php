<?php

function displayPost($post) {
   
   if($post->id . "/" == $post->thread)
      $root = true;
   else
      $root = false;
   // If the post is a root post, we want to put it in a special span tag (for styling purposes)
   if($root) {
           echo "<span class='root'>";
   }
   
   $numImages = count($post->images);
   
   // Print the link to view the post
   echo "<a href=\"" . WEB_BASE_DIR . "view/$post->id\">$post->subject</a> ";

   echo '<span class="postDetails">';
   
   // Print (nt) if there is no message
   if($post->post == '')
           echo "(nt) ";
           
   // Print (image) if there are images for this post
   if($numImages == 1)
           echo "(image) ";
   elseif($numImages > 1)
           echo "($numImages images) ";
           
   $postDate = new DateTime($post->timestamp);
   $postDate = $postDate->format('F jS g:i A');
   
   // Print the author and the timestamp
   echo "By <span class='author'>$post->author</span> on <span class='timestamp'>".$postDate."</span>\n";
   
   // If a root post, close the span tag
   if($root) {
           echo "</span>";
   }

   echo '</span>';
}

function recursiveDisplay($root) {
   // Get all children of this post
   
   // Display this post
   echo "<div class='post'>\n";
   displayPost($root);
   
   // For each child post, do this same process
   foreach($root->children as $child) {
      recursiveDisplay($child);
   }
   echo "</div>\n";
}
?>

<!doctype html>

<!--
 _       _         ___                 
| |     | |       /   |                
| |_ ___| |__    / /| |_   _ _ __ ___  
| __/ _ \ '_ \  / /_| | | | | '_ ` _ \ 
| ||  __/ | | | \___  | |_| | | | | | |
 \__\___|_| |_|     |_/\__,_|_| |_| |_|
   Version: <?=$this->forum->version?> 
   Date: <?=$this->forum->date?> 
   Author: <?=$this->forum->author?> 
-->

   



<html lang="en">
<head>
   <meta charset="utf-8">

 

   <title>4um : <?=$this->title?></title>

   <link rel="icon" type="image/png" href="<?=WEB_BASE_DIR?>favicon.png" />

   <meta name="description" content="teh 4um">

   <meta name="author" content="Dustin Hibbard">

   <meta name="description" content="4um" />

   <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="stylesheet" href="/forum/css/normalize.css">
   <link rel="stylesheet" href="/forum/css/skeleton.css">





   <link rel="stylesheet" href="/forum/css/style-invert.css">

   <link rel="stylesheet" href="/forum/css/buttons.css">

   <link href='http://fonts.googleapis.com/css?family=Open+Sans:700italic,700,400italic,400' rel='stylesheet' type='text/css'>

 

   <!--[if lt IE 9]>

   <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

   <![endif]-->
   
   <script src="<?=WEB_BASE_DIR?>js/jquery-2.1.1.min.js"></script>
   <script src="<?=WEB_BASE_DIR?>js/forum.js"></script>

   

   <!--<script src="js/scripts.js"></script>-->

 </head>

