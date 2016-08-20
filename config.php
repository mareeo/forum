<?php

define('WEB_BASE_DIR',      '/forum/');
define('FS_BASE_DIR',       __DIR__ . '/');
define('THUMB_MAX_WIDTH',   200);
define('THUMB_MAX_HEIGHT',  200);
define('IMG_DIR' ,          WEB_BASE_DIR . 'i/');
define('THUMB_DIR',         IMG_DIR . 't/');

define('FS_IMG_DIR',    FS_BASE_DIR . 'i/');
define('FS_THUMB_DIR',  FS_IMG_DIR . 't/');

define('DB_HOST', 'localhost');
define('DB_NAME', 'forum');
define('DB_USER', 'root');
define('DB_PASS', 'root');

define('ADMIN_PASSWORD', 'adminPassword');

define('FORUM_VERSION', '1.0.1');
define('FORUM_DATE',    '5/23/2016');
define('FORUM_AUTHOR',  'Dustin Hibbard <dustinhibbard@gmail.com>');
