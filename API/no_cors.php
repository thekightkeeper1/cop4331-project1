<?php
// Allow any origin to access this script
header("Access-Control-Allow-Origin: *");
// Allow specific methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Allow the headers your frontend is likely sending
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle the "Preflight" request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}