<?php
$required_role = 'admin';
include_once '../session.php';
header('Content-Type: application/json');
include_once '../db.php';

// Students
$students = [];
$result = $conn->query("SELECT id, name, email, phone_number, location FROM users");
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Employers
$employers = [];
$result = $conn->query("SELECT id, name, email, phone_number, location FROM employers");
while ($row = $result->fetch_assoc()) {
    $employers[] = $row;
}

// Jobs
$jobs = [];
$result = $conn->query("SELECT id, title, location, tags FROM jobs");
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode([
    "students" => $students,
    "employers" => $employers,
    "jobs" => $jobs
]);

$conn->close();
?>
