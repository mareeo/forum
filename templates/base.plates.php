<!doctype html>

<!--
 _       _         ___
| |     | |       /   |
| |_ ___| |__    / /| |_   _ _ __ ___
| __/ _ \ '_ \  / /_| | | | | '_ ` _ \
| ||  __/ | | | \___  | |_| | | | | | |
 \__\___|_| |_|     |_/\__,_|_| |_| |_|
   Version: <?=$forum->version?>
   Date: <?=$forum->date?>
   Author: <?=$forum->author?>
-->

<html lang="en">
<head>
    <meta charset="utf-8">



    <title>4um : <?=$title?></title>

    <link rel="icon" type="image/png" href="<?=WEB_BASE_DIR?>favicon.png" />
    <meta name="description" content="teh 4um">
    <meta name="author" content="Dustin Hibbard">
    <meta name="description" content="4um" />

    <!-- Mobile Specific Metas
   –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?=WEB_BASE_DIR?>css/normalize.css">
    <link rel="stylesheet" href="<?=WEB_BASE_DIR?>css/skeleton.css">

    <link id="stylesheet" rel="stylesheet" href="<?=WEB_BASE_DIR?>css/style.css">
    <link rel="stylesheet" href="<?=WEB_BASE_DIR?>css/buttons.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700italic,700,400italic,400' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script src="<?=WEB_BASE_DIR?>js/jquery-2.1.1.min.js"></script>
    <script src="<?=WEB_BASE_DIR?>js/forum.js"></script>
    <script src="<?=WEB_BASE_DIR?>js/commonmark.min.js"></script>
    <script src="<?=WEB_BASE_DIR?>js/jquery.ba-throttle-debounce.min.js"></script>
    <!--<script src="js/scripts.js"></script>-->
</head>
<body>
    <?php echo $this->section('content'); ?>
</body>
</html>