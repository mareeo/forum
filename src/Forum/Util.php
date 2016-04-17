<?php

namespace Forum\Forum;

use DateTime;

class Util
{

    public static function youtube_embeds($message) {

        preg_match_all(
            '/\nhttps?:\/\/www\.youtube\.com\/watch\?v=([A-Za-z0-9\-_]+)\S*/',
            $message,
            $matches
        );

        $numVideos = count($matches[0]);


        for($i=0; $i < $numVideos; $i++ ) {
            $message = str_replace($matches[0][$i], '<div class="youtube"><iframe width="100%" height="315" src="//www.youtube.com/embed/'.$matches[1][$i].'?rel=0" frameborder="0" allowfullscreen></iframe>'.$matches[0][$i].'</div>', $message);
        }


        preg_match_all(
            '/https?:\/\/youtu\.be\/([A-Za-z0-9\-_]+)\S*/',
            $message,
            $matches
        );

        $numVideos = count($matches[0]);


        for($i=0; $i < $numVideos; $i++ ) {
            $message = str_replace($matches[0][$i], '<div class="youtube"><iframe width="560" height="315" src="//www.youtube.com/embed/'.$matches[1][$i].'?rel=0" frameborder="0" allowfullscreen></iframe>'.$matches[0][$i].'</div>', $message);
        }

        return $message;
    }

    public static function linkify($text) {
        $url_pattern = '/# Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
    # Match http & ftp URL that is not already linkified.
      # Alternative 1: URL delimited by (parentheses).
      (\()                     # $1  "(" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $2: URL.
      (\))                     # $3: ")" end delimiter.
    | # Alternative 2: URL delimited by [square brackets].
      (\[)                     # $4: "[" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $5: URL.
      (\])                     # $6: "]" end delimiter.
    | # Alternative 3: URL delimited by {curly braces}.
      (\{)                     # $7: "{" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $8: URL.
      (\})                     # $9: "}" end delimiter.
    | # Alternative 4: URL delimited by <angle brackets>.
      (<|&(?:lt|\#60|\#x3c);)  # $10: "<" start delimiter (or HTML entity).
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $11: URL.
      (>|&(?:gt|\#62|\#x3e);)  # $12: ">" end delimiter (or HTML entity).
    | # Alternative 5: URL not delimited by (), [], {} or <>.
      (                        # $13: Prefix proving URL not already linked.
        (?: ^                  # Can be a beginning of line or string, or
        | [^=\s\'"\]]          # a non-"=", non-quote, non-"]", followed by
        ) \s*[\'"]?            # optional whitespace and optional quote;
      | [^=\s]\s+              # or... a non-equals sign followed by whitespace.
      )                        # End $13. Non-prelinkified-proof prefix.
      ( \b                     # $14: Other non-delimited URL.
        (?:ht|f)tps?:\/\/      # Required literal http, https, ftp or ftps prefix.
        [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+ # All URI chars except "&" (normal*).
        (?:                    # Either on a "&" or at the end of URI.
          (?!                  # Allow a "&" char only if not start of an...
            &(?:gt|\#0*62|\#x0*3e);                  # HTML ">" entity, or
          | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]); # a [&\'"] entity if
            [.!&\',:?;]?        # followed by optional punctuation then
            (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$)  # a non-URI char or EOS.
          ) &                  # If neg-assertion true, match "&" (special).
          [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]* # More non-& URI chars (normal*).
        )*                     # Unroll-the-loop (special normal*)*.
        [a-z0-9\-_~$()*+=\/#[\]@%]  # Last char can\'t be [.!&\',;:?]
      )                        # End $14. Other non-delimited URL.
    /imx';
        $url_replace = '$1$4$7$10$13<a href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';
        return preg_replace($url_pattern, $url_replace, $text);
    }


    /**
     * Generates a thumbnail for an image
     *
     * @param string $file Filename of image
     * @param string $type Type of image
     */
    public static function makeThumb( $file, $destination, $type ) {

        // Make a new image depending upon file type
        if ( $type == 'jpg' || $type == 'jpeg') {
            $src = imagecreatefromjpeg($file);
        } else if ( $type == 'png' ) {
            $src = imagecreatefrompng($file);
        } else if ( $type == 'gif' ) {
            $src = imagecreatefromgif($file);
        }

        // Get image dimensions
        $width = imagesx($src);
        $height = imagesy($src);

        // Calculate new height and width
        if ( $width < $height ) {
            $newWidth = $width * (THUMB_MAX_WIDTH / $height);
            $newHeight = THUMB_MAX_HEIGHT;
        } else {
            $newWidth = THUMB_MAX_WIDTH;
            $newHeight = $height * (THUMB_MAX_HEIGHT / $width);
        }

        // Copy the image to the new canvas
        $new = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($new, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Make a .jpg with the new image
        imagejpeg($new, $destination, 90);

        // Clean up
        imagedestroy($new);
        imagedestroy($src);
    }

    /**
     * Returns the file type extension
     *
     * @param string $str Filename
     * @return string Extension of file
     */
    public static function getExtension($str) {
        $i = strrpos($str,".");

        if (!$i)
            return "";

        $l = strlen($str) - $i;

        $ext = substr($str,$i+1,$l);

        return strtolower($ext);
    }


    public static function displayPost($post) {

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
        echo "<a href=\"".WEB_BASE_DIR."view/$post->id\">$post->subject</a> ";

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

    public static function recursiveDisplay($root) {
        // Get all children of this post

        // Display this post
        echo "<div class='post'>\n";
        self::displayPost($root);

        // For each child post, do this same process
        foreach($root->children as $child) {
            self::recursiveDisplay($child);
        }
        echo "</div>\n";
    }

    public static function displayTree(Models\Post $post) {
        $tree = PostService::generateTree($post);
        self::recursiveDisplay($tree);
    }

}