<?php
$host = ''; // replace with database hostname
$user = ''; // replace with database username
$password = ''; // replace with database password
$dbname = ''; // replace with database name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?> 


