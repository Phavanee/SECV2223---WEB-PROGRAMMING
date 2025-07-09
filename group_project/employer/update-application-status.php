<?php
$required_role = 'employer';
include_once '../session.php';

// DB setup
include_once '../db.php';

// Get input
$data = json_decode(file_get_contents("php://input"), true);
$appId = $data['applicationId'];
$newStatus = $data['newStatus'];

// Validate status
$validStatuses = ['Applied', 'Seen', 'Approved', 'Rejected', 'Completed'];
if (!in_array($newStatus, $validStatuses)) {
    echo json_encode(["success" => false, "error" => "Invalid status"]);
    exit;
}

// Update DB
$stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
$stmt->bind_param("si", $newStatus, $appId);
$success = $stmt->execute();

echo json_encode(["success" => $success]);

$conn->close();
?>
