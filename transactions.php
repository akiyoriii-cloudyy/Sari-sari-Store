<?php
require_once 'api/session_config.php';
session_start();
require_once 'api/config.php';

$database = new Database();
$db = $database->getConnection();

// Rest of the code... 