<?php
/**
 * The front (main) forum page.
 *
 * Gets the 50 newest threads, then display them
 */

 
use Forum\PostService;

// Include config and libraries
require('config.php');

$app = new \Slim\Slim();

$app->get('/', function() use ($app) {
	
	global $FORUM;
	
	$afterPost = $app->request->get('after');

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

$app->get('/view/:id', function($id) use ($app) {
	
	global $FORUM;

	$post  = PostService::getPostByID($id);
	
	if($post === null) {
		$app->redirect($app->request->getRootUri());
	}
	
	// Find the root post of this thread (for the thread display)
	$root = $post->find_root();
	
	// Get this post's images
	$images = $post->getImages();
	
	// Process message
	$message = youtube_embeds($post->post);
	$message = linkify($message);
	$message = nl2br($message);
	
	// Used stored name if it was stored
	$name = $app->getCookie('author');
	
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

$app->get('/new', function() use ($app) {
	
	global $FORUM;
	
	// Used stored name if it was stored
	$name = $app->getCookie('author');
	
	// Pass data to templates
	$tpl = new Savant3();
	$tpl->title = "New Post";
	$tpl->forum = $FORUM;
	$tpl->name = $name;
	
	$tpl->display('views/new.tpl.php');

	
});

$app->post('/new', function() use ($app) {
	
	// Spam check
	if(strlen($app->request->post('author')) > 0) {
		$app->response->setStatus(403);
		$app->response->setBody('Forbidden');
		return;
	}
	
	// Get edit token.  Set it if it doesn't exist.
	$token = $app->getCookie('token');
	
	if($token === null) {
		$token = uniqid();
		$app->setCookie('token', $token, '1 year');
	}
	
	$ip = $app->environment['REMOTE_ADDR'];
	
	// Set the author name
	$app->setCookie('author', $app->request->post('pumpkin'), '1 year');
	
	$id  = PostService::createPost($app->request->post(), $token, $ip);
	PostService::processImages($id);
	
	if($id !== null) {
		$app->redirect($app->request->getRootUri() . "/view/$id");
	} else {
		$app->response->setStatus(500);
		$app->response->setBody('Error creating post');
		return;
	}

});

$app->get('/edit/:id', function($id) use ($app) {
	
	global $FORUM;

	$post  = PostService::getPostByID($id);
	
	if($post === null) {
		$app->redirect($app->request->getRootUri());
	}
	
	$token = $app->getCookie('token');
	
	if(!isset($token) || $token !== $post->token) {
		$app->response->setStatus(403);
		$app->response->setBody('Forbidden');
		return;
	}
	
	// Pass data to templates
	$tpl = new Savant3();
	$tpl->title = "Edit Post";
	$tpl->forum = $FORUM;
	$tpl->post = $post;
	
	$tpl->display('views/edit.tpl.php');
	
});

$app->post('/edit/:id', function($id) use ($app) {
	
	$action = $app->request->post('action');
	
	// Spam check
	if(strlen($app->request->post('author')) > 0) {
		$app->response->setStatus(403);
		$app->response->setBody('Forbidden');
		return;
	}
	
	// Get edit token.  Set it if it doesn't exist.
	$token = $app->getCookie('token');
	
	if($token === null) {
		die("No token set");
	}
	
	$ip = $app->environment['REMOTE_ADDR'];
	
	if($action == 'Update') {
		
		$success  = PostService::editPost($app->request->post(), $token, $ip);
		
		if($success) {
			$app->redirect($app->request->getRootUri() . "/view/$id");
		} else {
			$app->response->setStatus(500);
			$app->response->setBody('Error updating post');
			return;
		}
	} elseif ($action == 'Delete') {
		
		PostService::deletePost($app->request->post(), $token);
		$app->redirect($app->request->getRootUri());
		
		
	} else {
		$app->response->setStatus(500);
		$app->response->setBody('Undefined action');
	}

});

$app->get('/test', function() use ($app) {

	global $FORUM;

	// Pass data to templates
	$tpl = new Savant3();
	$tpl->title = "Testing Page";
	$tpl->forum = $FORUM;

	$tpl->display('views/test.tpl.php');

});

$app->run();
