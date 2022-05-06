<?php
// absolute path
define("ABSPATH", dirname(__FILE__) . "/");

// header json
Header("Content-Type: application/json;");

// session
session_start();

include_once ABSPATH . "includes/database.php";
include ABSPATH . 'includes/func.php';
include ABSPATH . 'includes/token.php';

// set $db to global variable
global $db;
$db = new Database();// init the Database class

// set 'social' as dbname
$db->set_dbname('social');

// connect
$db->connect();

// verify if has any error
if ($db->hasErrors()) die( json_encode($db->get_errors()) );

// done connecting.