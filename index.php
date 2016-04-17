<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Forum\Forum\PostService;
use League\CommonMark\CommonMarkConverter;
use League\Plates\Engine;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ]
];

require 'vendor/autoload.php';
require 'config.php';

$container = new \Slim\Container($configuration);
$container['cookie'] = function(Slim\Container $c){
    /** @var Slim\Http\Request $request */
    $request = $c->get('request');
    return new \Slim\Http\Cookies($request->getCookieParams());
};
$app = new \Slim\App($container);

$app->get('/', function(Request $request, Response $response, $args) {

    global $FORUM;

    $afterPost = $request->getParam('after');

    $posts  = PostService::getPosts($afterPost);
    foreach($posts as $key => $post) {
        $posts[$key] = PostService::generateTree($post);
    }

    $templates = new Engine("templates");
    $output = $templates->render('home.plates', [
        'forum' => $FORUM,
        'title' => 'Home',
        'posts' => $posts
    ]);

    $response->getBody()->write($output);
//    $response->getBody()->write($tpl->getOutput('views/home.tpl.php'));
    return $response;
});

$app->get('/view/{id}', function(Request $request, Response $response, $args) {
    /** @var $this \Slim\Container */

    /** @var \Slim\Http\Cookies $cookies */
    $cookies = $this->get('cookie');

    global $FORUM;
    $id = intval($args['id']);

    $post  = PostService::getPostByID($id);

    if($post === null) {
        return $response->withRedirect(WEB_BASE_DIR);
    }

    // Find the root post of this thread (for the thread display)
    $root = $post->find_root();

    // Get this post's images
    $images = $post->getImages();

    $converter = new CommonMarkConverter([
        'safe' => true,
    ]);

    // Process message
    $message = $post->post;
    $message = $converter->convertToHtml($message);
    $message = \Forum\Forum\Util::youtube_embeds($message);
    $message = \Forum\Forum\Util::linkify($message);

    $name = null;

    // Used stored name if it was stored
    $name = $cookies->get('author');

    $templates = new Engine("templates");
    $output = $templates->render('post.plates', [
        'forum' => $FORUM,
        'title' => 'View',
        'post' => $post,
        'root' => $root,
        'name' => $name,
        'images' => $images,
        'message' => $message
    ]);

    $response->getBody()->write($output);
    return $response;
});

$app->get('/new', function(Request $request, Response $response, $args) {
    /** @var $this \Slim\Container */

    /** @var \Slim\Http\Cookies $cookies */
    $cookies = $this->get('cookie');


    global $FORUM;

    $name = null;

    $name = $cookies->get('author');

    $templates = new Engine("templates");
    $output = $templates->render('new.plates', [
        'title' => 'New Post',
        'forum' => $FORUM,
        'name' => $name
    ]);

    $response->getBody()->write($output);
    return $response;
});

$app->post('/new', function(Request $request, Response $response, $args) {

    /** @var $this \Slim\Container */

    /** @var \Slim\Http\Cookies $cookies */
    $cookies = $this->get('cookie');

    /** @var $this \Slim\App $cookies */

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

    $id  = PostService::createPost($request->getParsedBody(), $token, $ip);
    PostService::processImages($id);

    if($id !== null) {
        return $response->withRedirect(WEB_BASE_DIR . "view/$id")
            ->withHeader('Set-Cookie', $cookies->toHeaders());
    } else {
        $response->getBody()->write('Error creating post');
        return $response->withStatus(500);
    }

});

$app->get('/edit/{id}', function(Request $request, Response $response, $args) {
    /** @var $this \Slim\Container */

    /** @var \Slim\Http\Cookies $cookies */
    $cookies = $this->get('cookie');

    global $FORUM;
    $id = intval($args['id']);

    /** @var Models\Post $post */
    $post  = PostService::getPostByID($id);

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
        'forum' => $FORUM,
        'title' => 'Edit Post',
        'post' => $post,
    ]);

    $response->getBody()->write($output);
    return $response;
});

$app->post('/edit/{id}', function(Request $request, Response $response, $args) {

    /** @var $this \Slim\Container */

    /** @var \Slim\Http\Cookies $cookies */
    $cookies = $this->get('cookie');

    $action = $request->getparam('action');

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

    /** @var \Slim\Http\Uri $uri */
    $uri = $this->request->getUri();

    if($action == 'Update') {
        $success  = PostService::editPost($request->getParsedBody(), $token, $ip);

        if($success) {
            return $response->withRedirect(WEB_BASE_DIR . "view/$id");
        } else {
            $response->getBody()->write("Error updating post");
            return $response->withStatus(500);
        }
    } elseif ($action == 'Delete') {

        PostService::deletePost($request->getParsedBody(), $token);
        return $response->withRedirect(WEB_BASE_DIR);


    } else {
        $response->getBody()->write("Undefined action");
        return $response->withStatus(500);
    }



});

$app->run();
