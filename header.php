<!DOCTYPE html>
<html>
    <head>
<meta charset="UTF-8">
<title>Flipbook</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/theme-default.min.css" rel="stylesheet" type="text/css" />
<script src="https://use.fontawesome.com/99c8b658dd.js"></script>
<script src="global.js"></script>
<link rel="stylesheet" type="text/css" href="flipbook.css">
<?php
// only files with visualizations need addtional js
if(strpos($_SERVER['PHP_SELF'],"object.php") !== false || strpos($_SERVER['PHP_SELF'],"list_objects.php") !== false) {
    echo "<link type='text/css' href='vis/forcedirected.css' rel='stylesheet' />"; 
    echo "<!--[if IE]><script language='javascript' type='text/javascript' src='vis/excanvas.js'></script><![endif]-->"; 
    echo "<script language='javascript' type='text/javascript' src='vis/jit.js'></script>"; 
    echo "<script language='javascript' type='text/javascript' src='vis/forcedirected.js'></script>"; 
}

set_include_path("/home/ubuntu/workspace/");
include ('config.php');
// not needed yet  - include ('global.php');
?>
        
    </head>
    <body>
        <div id='header'>
            <div id="appname"><a href="index.php">Flipbook</a></div>
            <div id="menu">
                <ul>
                    <li><a href="about.php"><i class="fa fa-info" aria-hidden="true"></i> About</a></li>
                    <li><a href="list_objects.php"><i class="fa fa-list" aria-hidden="true"></i> List All</a></li>
                    <li><a href="definitions.php"><i class="fa fa-book" aria-hidden="true"></i> Admin</a></li>
                    <li><a href="phpmyadmin" target="_blank"><i class="fa fa-database" aria-hidden="true"></i></a></li>
                </ul>
                
            </div>
        </div>
        <div id='content'>
