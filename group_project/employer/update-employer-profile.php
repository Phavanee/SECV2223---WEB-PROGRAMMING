<?php
$required_role = 'employer';
include_once '../session.php';

header('Content-Type: application/json');

// DB connection
include_once '../db.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id         = $data['id'] ?? null;
$name       = $data['full_name'] ?? null;
$email      = $data['email'] ?? null;
$phone      = $data['phone'] ?? null;
$location   = $data['location'] ?? null;
$gender     = $data['gender'] ?? null;

if (!$id || !$name || !$email || !$phone || !$location || !$gender) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$stmt = $conn->prepare("UPDATE employers SET name = ?, email = ?, phone_number = ?, location = ?, gender = ? WHERE id = ?");
$stmt->bind_param("sssssi", $name, $email, $phone, $location, $gender, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Update failed"]);
}

$stmt->close();
$conn->close();
?>