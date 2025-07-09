<?php
$required_role = 'employer';
include_once '../session.php';

header('Content-Type: application/json');

// DB connection
include_once '../db.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing employer ID"]);
    exit;
}

$stmt = $conn->prepare("SELECT name, email, phone_number, location, gender FROM employers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Employer not found"]);
} else {
    echo json_encode($result->fetch_assoc());
}

$stmt->close();
$conn->close();
?>