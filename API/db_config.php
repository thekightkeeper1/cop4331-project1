<?php
// db_config.php
$host = "localhost";
$dbname = "project1";
$username = "kight";
$password = "admin123";

# PHP Data Object for the database
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

?>