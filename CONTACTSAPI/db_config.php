<?php
// db_config.php
$host = "localhost";
$dbname = "PROJECT1";
$username = "God";
$password = "verygoodpassword";

# PHP Data Object for the database
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

?>