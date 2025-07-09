<?php
$required_role = 'student';
include_once '../session.php';
header('Content-Type: application/json');

include_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);
$application_id = $data['application_id'] ?? null;

if (!$application_id) {
    echo json_encode(['success' => false, 'message' => 'Application ID is required']);
    exit;
}

// Verify that the application belongs to the logged-in student
$stmt = $conn->prepare("SELECT id FROM applications WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $application_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Application not found or access denied']);
    exit;
}

// Delete the application
$stmt = $conn->prepare("DELETE FROM applications WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $application_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Application removed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove application']);
}

$stmt->close();
$conn->close();
?> 