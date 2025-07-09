<?php
$required_role = 'student';
include_once '../session.php';
include_once '../db.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$userId = $data["userId"];
$jobId = $data["jobId"];

// Insert application
$sql = "INSERT INTO applications (user_id, job_id) VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $jobId);

$response = [];

if ($stmt->execute()) {
    $response["status"] = "success";
} else {
    $response["status"] = "error";
    $response["message"] = $conn->error;
}

echo json_encode($response);
$conn->close();
?>
