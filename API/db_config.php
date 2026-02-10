<?php
// db_config.php
$host = "localhost";
$dbname = "COP4331";
$username = "karel";
$password = "group9";

# PHP Data Object for the database
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

?>