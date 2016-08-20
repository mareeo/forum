<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Forum\Forum\PostService;


require 'vendor/autoload.php';
require 'config.php';

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');




/** @var ActiveRecord\Config $cfg */
$cfg = ActiveRecord\Config::instance();
$cfg->set_model_directory('src/Forum/Models');
$cfg->set_connections(array(
        'production' => 'mysql://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.'/'.DB_NAME.'?charset=utf8mb4'
    )
);
$cfg->set_default_connection('production');
ActiveRecord\Connection::$datetime_format = 'Y-m-d H:i:s';

session_start();



$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ]
];

$app = new \Slim\App($configuration);

$forum = new Forum\Forum\Forum($app);
$forum->registerServices();
$forum->buildRoutes();
$app->run();
