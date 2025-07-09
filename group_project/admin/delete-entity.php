<?php
$required_role = 'admin';
include_once '../session.php';
header('Content-Type: application/json');
include_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$type = $data['type'] ?? null;

if (!$id || !$type) {
    http_response_code(400);
    exit(json_encode(["error" => "Missing id or type"]));
}

$success = false;

if ($type === 'student') {
    // delete from applications first
    $conn->query("DELETE FROM applications WHERE user_id = $id");
    $success = $conn->query("DELETE FROM users WHERE id = $id");
} elseif ($type === 'employer') {
    // delete employer's jobs and then the employer
    $conn->query("DELETE FROM jobs WHERE employer_id = $id");
    $success = $conn->query("DELETE FROM employers WHERE id = $id");
} elseif ($type === 'job') {
    // delete job + any job applications
    $conn->query("DELETE FROM applications WHERE job_id = $id");
    $success = $conn->query("DELETE FROM jobs WHERE id = $id");
}

echo json_encode(["success" => $success]);
$conn->close();
?>
