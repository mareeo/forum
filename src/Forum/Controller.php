<?php

namespace Forum\Forum;

use League\CommonMark\CommonMarkConverter;
use League\Plates\Engine;
use Slim\Container;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller
{
    /** @var Container */
    private $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function homePage(Request $request, Response $response, $args) {

        /** @var PostService $postService */
        $postService = $this->container->get('post');

        $forum = $this->container->get('appInfo');

        $afterPost = $request->getParam('after');

        $posts  = $postService->getPosts($afterPost);
        foreach($posts as $key => $post) {
            $posts[$key] = $postService->generateTree($post);
        }

        $templates = new Engine("templates");
        $output = $templates->render('home.plates', [
            'forum' => $forum,
            'title' => 'Home',
            'posts' => $posts
        ]);

        $response->getBody()->write($output);
        return $response;
        
    }
    
    public function viewPost(Request $request, Response $response, $args) {
        /** @var Cookies $cookies */
        $cookies = $this->container->get('cookie');

        /** @var PostService $postService */
        $postService = $this->container->get('post');

        $forum = $this->container->get('appInfo');

        $id = intval($args['id']);

        $post  = $postService->getPostByID($id);

        if($post === null) {
            return $response->withRedirect(WEB_BASE_DIR);
        }

        // Find the root post of this thread (for the thread display)
        $root = $post->find_root();

        // Get this post's images
        $images = $post->getImages();

        $converter = new CommonMarkConverter([
            'safe' => true,
            'nestedLimit' => 15
        ]);

        // Process message
        $message = $post->post;
        $message = $converter->convertToHtml($message);
        $message = Util::youtube_embeds($message);
        $message = Util::linkify($message);

        $name = null;

        // Used stored name if it was stored
        $name = $cookies->get('author');

        $isAdmin = Util::isAdmin();

        $postTree = $postService->generateTree($root);

        $templates = new Engine("templates");
        $output = $templates->render('post.plates', [
            'forum' => $forum,
            'title' => 'View',
            'post' => $post,
            'root' => $root,
            'tree' => $postTree,
            'name' => $name,
            'images' => $images,
            'message' => $message,
            'admin' => $isAdmin
        ]);

        $response->getBody()->write($output);
        return $response;
    }
    
    public function newPost(Request $request, Response $response, $args) {
        /** @var \Slim\Http\Cookies $cookies */
        $cookies = $this->container->get('cookie');

        $forum = $this->container->get('appInfo');

        $name = null;

        $name = $cookies->get('author');

        $templates = new Engine("templates");
        $output = $templates->render('new.plates', [
            'title' => 'New Post',
            'forum' => $forum,
            'name' => $name
        ]);

        $response->getBody()->write($output);
        return $response;
    }
    
    public function createPost(Request $request, Response $response, $args) {
        /** @var \Slim\Http\Cookies $cookies */
        $cookies = $this->container->get('cookie');

        /** @var PostService $postService */
        $postService = $this->container->get('post');

        // Spam check
        if(strlen($request->getParam('author')) > 0) {
            $response->getBody()->write('Forbidden');
            return $response->withStatus(403);
        }

        // Get edit token.  Set it if it doesn't exist.
        $token = $cookies->get('token');
        if($token === null) {
            $token = uniqid();
            $cookies->set('token', [
                'value' => $token,
                'expires' => '1 year'
            ]);
        }


        $ip = $request->getServerParams()['REMOTE_ADDR'];

        // Set the author name
        $cookies->set('author', [
            'value' => $request->getParam('pumpkin'),
            'expires' => '1 year'
        ]);

        $id  = $postService->createPost($request->getParsedBody(), $token, $ip);
        $postService->processImages($id);

        if($id !== null) {
            return $response->withRedirect(WEB_BASE_DIR . "view/$id")
                ->withHeader('Set-Cookie', $cookies->toHeaders());
        } else {
            $response->getBody()->write('Error creating post');
            return $response->withStatus(500);
        }

    }
    
    public function editPostPage(Request $request, Response $response, $args) {
        /** @var \Slim\Http\Cookies $cookies */
        $cookies = $this->container->get('cookie');

        /** @var PostService $postService */
        $postService = $this->container->get('post');

        $forum = $this->container->get('appInfo');

        $id = intval($args['id']);

        /** @var \Forum\Forum\Models\Post $post */
        $post  = $postService->getPostByID($id);

        if($post === null) {
            return $response->withRedirect(WEB_BASE_DIR);
        }

        $token = $cookies->get('token');

        if(!isset($token) || $token !== $post->token) {
            $response->getBody()->write('Forbidden');
            return $response->withStatus(403);
        }

        $templates = new Engine("templates");
        $output = $templates->render('edit.plates', [
            'forum' => $forum,
            'title' => 'Edit Post',
            'post' => $post,
        ]);

        $response->getBody()->write($output);
        return $response;
    }
    
    public function editPost(Request $request, Response $response, $args) {
        /** @var \Slim\Http\Cookies $cookies */
        $cookies = $this->container->get('cookie');

        /** @var PostService $postService */
        $postService = $this->container->get('post');

        $action = $request->getParam('action');

        $id = intval($args['id']);

        // Spam check
        if(strlen($request->getParam('author')) > 0) {
            $response->getBody()->write('Forbidden');
            return $response->withStatus(403);
        }

        $token = $cookies->get('token');

        if($token === null) {
            $response->getBody()->write('Forbidden - No token cookie');
            return $response->withStatus(403);
        }

        $ip = $request->getServerParams()['REMOTE_ADDR'];

        if($action == 'Update') {
            $success  = $postService->editPost($request->getParsedBody(), $token, $ip);

            if($success) {
                return $response->withRedirect(WEB_BASE_DIR . "view/$id");
            } else {
                $response->getBody()->write("Error updating post");
                return $response->withStatus(500);
            }
        } elseif ($action == 'Delete') {

            $postService->deletePost($request->getParsedBody(), $token);
            return $response->withRedirect(WEB_BASE_DIR);


        } else {
            $response->getBody()->write("Undefined action");
            return $response->withStatus(500);
        }



    }
    
    public function adminPage(Request $request, Response $response, $args) {

        $forum = $this->container->get('appInfo');

        $loggedOut = false;
        if(\Forum\Forum\Util::isAdmin()) {
            session_destroy();
            $loggedOut = true;
        }

        $templates = new Engine("templates");

        $output = $templates->render('admin.plates', [
            'forum' => $forum,
            'title' => 'Admin',
            'loggedOut' => $loggedOut
        ]);

        $response->getBody()->write($output);
        return $response;
    }
    
    public function adminLogin(Request $request, Response $response, $args) {

        $password = $request->getParam('password');

        if($password === ADMIN_PASSWORD) {
            $_SESSION['admin'] = true;
            return $response->withRedirect(WEB_BASE_DIR);
        } else {
            return $response->withRedirect(WEB_BASE_DIR . "admin");
        }
    }
    
    public function deletePost(Request $request, Response $response, $args) {

        /** @var PostService $postService */
        $postService = $this->container->get('post');

        $id = intval($args['id']);

        if(!\Forum\Forum\Util::isAdmin()) {
            return $response->withRedirect(WEB_BASE_DIR);
        }

        $success = $postService->adminDeletePost($id);

        if($success) {
            return $response->withRedirect(WEB_BASE_DIR);
        } else {
            $response->getBody()->write('Error deleting post');
            return $response->withStatus(500);
        }

    }
}