<?php

namespace Forum\Forum;

use PDO;
use Slim\App;
use Slim\Container;
use Slim\Http\Cookies;
use Slim\Http\Request;

class Forum
{
    /** @var App */
    private $app;
    
    public function __construct(App $app) {
        $this->app = $app;
    }
    
    public function registerServices() {
        $container = $this->app->getContainer();

        $container['cookie'] = function(Container $c){
            /** @var Request $request */
            $request = $c->get('request');
            return new Cookies($request->getCookieParams());
        };

        $container['pdo'] = function() {
            return new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
        };

        $container['post'] = function(Container $c) {
            /** @var PDO $pdo */
            $pdo = $c->get('pdo');
            return new PostService($pdo);
        };
        
        $container['appInfo'] = function() {
            $info = new \stdClass;
            $info->version = FORUM_VERSION;
            $info->date = FORUM_DATE;
            $info->author = FORUM_AUTHOR;
            return $info;
        };

    }
    
    public function buildRoutes() {
        $this->app->get('/', '\Forum\Forum\Controller:homePage');

        $this->app->get('/view/{id}', '\Forum\Forum\Controller:viewPost');

        $this->app->get('/new', '\Forum\Forum\Controller:newPost');

        $this->app->post('/new', '\Forum\Forum\Controller:createPost');

        $this->app->get('/edit/{id}', '\Forum\Forum\Controller:editPostPage');

        $this->app->post('/edit/{id}', '\Forum\Forum\Controller:editPost');

        $this->app->get('/admin', '\Forum\Forum\Controller:adminPage');

        $this->app->post('/admin', '\Forum\Forum\Controller:adminLogin');

        $this->app->post('/delete/{id}', '\Forum\Forum\Controller:deletePost');
    }
}
