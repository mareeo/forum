<?php

require_once 'vendor/php-activerecord/ActiveRecord.php';
require_once 'vendor/functions.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');


ActiveRecord\Config::initialize(function($cfg)
{
   $cfg->set_model_directory('src/Forum/Models');
   $cfg->set_connections(array('production' =>
   'mysql://root:root@localhost/deepgame_forum?charset=utf8mb4'));
     
   # default connection is now production
   $cfg->set_default_connection('production');
  
  
});

date_default_timezone_set('America/Chicago');
mb_internal_encoding('UTF-8');

define('WEB_BASE_DIR',      '/dustinhibbard.com/forum/');
define('FS_BASE_DIR',       __DIR__ . '/');
define('THUMB_MAX_WIDTH',   200);
define('THUMB_MAX_HEIGHT',  200);
define('IMG_DIR' ,          WEB_BASE_DIR . 'i/');
define('THUMB_DIR',         IMG_DIR . 't/');

define('FS_IMG_DIR',   FS_BASE_DIR . 'i/');
define('FS_THUMB_DIR',  FS_IMG_DIR . 't/');

$pdo = new PDO('mysql:host=localhost;dbname=deepgame_forum;charset=utf8mb4', "root", "root");

session_start();

$FORUM = new stdClass;
$FORUM->version = 0.92;
$FORUM->date = '4/17/2016';
$FORUM->author = "Dustin Hibbard <dustinhibbard@gmail.com>";
