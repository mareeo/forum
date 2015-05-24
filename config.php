<?php

require_once 'vendor/php-activerecord/ActiveRecord.php';
require_once 'vendor/savant3/Savant3.php';
require_once 'vendor/functions.php';
require_once 'vendor/Slim/Slim.php';
require_once 'vendor/Forum/PostService.php';
\Slim\Slim::registerAutoloader();



error_reporting(E_ALL);
ini_set('display_errors', '1');


ActiveRecord\Config::initialize(function($cfg)
{
   $cfg->set_model_directory('models');
   $cfg->set_connections(array('production' =>
   'mysql://root:omgomgomg@localhost/deepgame_forum?charset=utf8'));
     
   # default connection is now production
   $cfg->set_default_connection('production');
  
  
});

date_default_timezone_set('America/Chicago');
mb_internal_encoding('UTF-8');

define('WEB_BASE_DIR',      '/forum2/');
define('FS_BASE_DIR',       __DIR__ . '/');
define('THUMB_MAX_WIDTH',   200);
define('THUMB_MAX_HEIGHT',  200);
define('IMG_DIR' ,          WEB_BASE_DIR . 'i/');
define('THUMB_DIR',         IMG_DIR . 't/');

define('FS_IMG_DIR',   FS_BASE_DIR . 'i/');
define('FS_THUMB_DIR',  FS_IMG_DIR . 't/');

$pdo = new PDO('mysql:host=localhost;dbname=deepgame_forum;charset=UTF8', "DBUSERNAME", "DBPASSWORD");

session_start();

$FORUM = new stdClass;
$FORUM->version = 0.8;
$FORUM->date = '9/30/2014';
$FORUM->author = "Dustin Hibbard <dustinhibbard@gmail.com>";
