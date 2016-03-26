<?php

require_once 'vendor/php-activerecord/ActiveRecord.php';
require_once 'vendor/savant3/Savant3.php';
require_once 'vendor/functions.php';
require_once 'vendor/Forum/PostService.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');


ActiveRecord\Config::initialize(function($cfg)
{
   $cfg->set_model_directory('models');
   $cfg->set_connections(array('production' =>
   'mysql://root:root@localhost/deepgame_forum?charset=utf8'));
     
   # default connection is now production
   $cfg->set_default_connection('production');
  
  
});

date_default_timezone_set('America/Chicago');
mb_internal_encoding('UTF-8');

define('WEB_BASE_DIR',      '/dustinhibbard.com/forum-redo/');
define('FS_BASE_DIR',       __DIR__ . '/');
define('THUMB_MAX_WIDTH',   200);
define('THUMB_MAX_HEIGHT',  200);
define('IMG_DIR' ,          WEB_BASE_DIR . 'i/');
define('THUMB_DIR',         IMG_DIR . 't/');

define('FS_IMG_DIR',   FS_BASE_DIR . 'i/');
define('FS_THUMB_DIR',  FS_IMG_DIR . 't/');

$pdo = new PDO('mysql:host=localhost;dbname=deepgame_forum;charset=UTF8', "root", "root");

session_start();

$FORUM = new stdClass;
$FORUM->version = 0.9;
$FORUM->date = '12/29/2012';
$FORUM->author = "Dustin Hibbard <dustinhibbard@gmail.com>";
