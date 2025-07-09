<?php
$required_role = 'admin';
include_once '../session.php';
include_once '../db.php';
if ($conn->connect_error) {
    http_response_code(500);
    exit("DB error");
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$type = $data['type'] ?? null;

if ($id && $type) {
    $table = $type === 'student' ? 'users' : 'employers';
    $stmt = $conn->prepare("UPDATE $table SET is_verified = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    echo json_encode(["success" => $success]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing id or type"]);
}
$conn->close();
?>