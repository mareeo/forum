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

    $tpl->display('views/home.tpl.php');

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

    $tpl->display('views/post.tpl.php');


});

$app->get('/new', function(Request $request, Response $response, $args) {
    /** @var $this \Slim\Container */

    /** @var \Slim\Http\Cookies $cookies */
    $cookies = $this->get('cookie');


    global $FORUM;

    $name = null;

    $name = $cookies->get('author');

    var_dump($name);

    var_dump($cookies);exit;

    // Pass data to templates
    $tpl = new Savant3();
    $tpl->title = "New Post";
    $tpl->forum = $FORUM;
    $tpl->name = $name;

    $tpl->display('views/new.tpl.php');
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


$app->run();



