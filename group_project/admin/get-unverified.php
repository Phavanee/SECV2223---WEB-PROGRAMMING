<?php
$required_role = 'admin';
include_once '../session.php';

header('Content-Type: application/json');

include_once '../db.php';

$type = $_GET['type'] ?? '';
$table = $type === 'student' ? 'users' : ($type === 'employer' ? 'employers' : '');

if (!$table) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid type"]);
    exit;
}

$query = "SELECT id, name, phone_number, email, location FROM $table WHERE is_verified = 0";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
