<?php

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

date_default_timezone_set('America/Chicago');
mb_internal_encoding('UTF-8');

define('WEB_BASE_DIR',      '/forum/');
define('FS_BASE_DIR',       __DIR__ . '/');
define('THUMB_MAX_WIDTH',   200);
define('THUMB_MAX_HEIGHT',  200);
define('IMG_DIR' ,          WEB_BASE_DIR . 'i/');
define('THUMB_DIR',         IMG_DIR . 't/');

define('FS_IMG_DIR',   FS_BASE_DIR . 'i/');
define('FS_THUMB_DIR',  FS_IMG_DIR . 't/');

define('DB_HOST', 'localhost');
define('DB_NAME', 'forum');
define('DB_USER', 'root');
define('DB_PASS', 'root');

define('ADMIN_PASSWORD', 'adminPassword');

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);

/** @var ActiveRecord\Config $cfg */
$cfg = ActiveRecord\Config::instance();
$cfg->set_model_directory('src/Forum/Models');
$cfg->set_connections(array(
     'production' => 'mysql://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.'/'.DB_NAME.'?charset=utf8mb4'
    )
);

$cfg->set_default_connection('production');

ActiveRecord\Connection::$datetime_format = 'Y-m-d H:i:s';

$FORUM = new stdClass;
$FORUM->version = '1.0.1';
$FORUM->date = '5/23/2016';
$FORUM->author = "Dustin Hibbard <dustinhibbard@gmail.com>";
