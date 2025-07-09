<?php
$required_role = 'employer';
include_once '../session.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once '../db.php';

// Get data from POST
$title = $_POST['title'];
$location = $_POST['location'];
$tags = $_POST['tags'];
$employer_id = $_SESSION['user_id'];

// Insert into DB
$stmt = $conn->prepare("INSERT INTO jobs (title, location, tags, employer_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $title, $location, $tags, $employer_id);

if ($stmt->execute()) {
    header('Location: home.html?job_posted=1');
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
