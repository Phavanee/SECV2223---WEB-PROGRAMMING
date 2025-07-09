<?php
$required_role = 'student';
include_once '../session.php';
include_once '../db.php';

$title = isset($_GET['title']) ? $_GET['title'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

$title = $conn->real_escape_string($title);
$location = $conn->real_escape_string($location);

$sql = "SELECT * FROM jobs WHERE title LIKE '%$title%' AND location LIKE '%$location%'";
$result = $conn->query($sql);

$jobs = [];
while($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

$conn->close();
?>