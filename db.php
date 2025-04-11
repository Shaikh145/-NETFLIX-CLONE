<?php
/**
 * Database Connection File
 * 
 * This file establishes a connection to the MySQL database
 * and can be included in other PHP files.
 */

// Database credentials
$db_host = 'localhost';
$db_user = 'uklz9ew3hrop3';
$db_pass = 'zyrbspyjlzjb';
$db_name = 'db9ranxpmtccqq';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8");

// Uncomment the line below to check if connection is successful
// echo "Connected successfully";
?>
