<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Forum\PostService;

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

    // Pass data to templates
    $tpl = new Savant3();
    $tpl->forum = $FORUM;
    $tpl->title = "Home";
    $tpl->posts = $posts;


    $response->getBody()->write($tpl->getOutput('views/home.tpl.php'));
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

    // Process message
    $message = youtube_embeds($post->post);
    $message = linkify($message);
    $message = nl2br($message);

    $cookies = $request->getCookieParams();
    $name = null;

    // Used stored name if it was stored
    if(array_key_exists('author', $cookies)) {
        $name = $cookies['author'];
    }

    // Pass data to templates
    $tpl = new Savant3();
    $tpl->title = "View";
    $tpl->forum = $FORUM;
    $tpl->post = $post;
    $tpl->root = $root;
    $tpl->name = $name;
    $tpl->images = $images;
    $tpl->message = $message;

    $response->getBody()->write($tpl->getOutput('views/post.tpl.php'));
    return $response;
});

$app->get('/new', function(Request $request, Response $response, $args) {
    /** @var $this \Slim\Container */

    /** @var \Slim\Http\Cookies $cookies */
    $cookies = $this->get('cookie');


    global $FORUM;

    $name = null;

    $name = $cookies->get('author');

    // Pass data to templates
    $tpl = new Savant3();
    $tpl->title = "New Post";
    $tpl->forum = $FORUM;
    $tpl->name = $name;

    $response->getBody()->write($tpl->getOutput('views/new.tpl.php'));
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
        return $response->withRedirect(WEB_BASE_DIR . "/view/$id")
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

    // Pass data to templates
    $tpl = new Savant3();
    $tpl->title = "Edit Post";
    $tpl->forum = $FORUM;
    $tpl->post = $post;

    $response->getBody()->write($tpl->getOutput('views/edit.tpl.php'));
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

    $basePath = $uri->getBasePath();


    if($action == 'Update') {
        $success  = PostService::editPost($request->getParsedBody(), $token, $ip);

        if($success) {
            return $response->withRedirect($basePath . "/view/$id");
        } else {
            $response->getBody()->write("Error updating post");
            return $response->withStatus(500);
        }
    } elseif ($action == 'Delete') {

        PostService::deletePost($request->getParsedBody(), $token);
        return $response->withRedirect($basePath);


    } else {
        $response->getBody()->write("Undefined action");
        return $response->withStatus(500);
    }



});

$app->run();

