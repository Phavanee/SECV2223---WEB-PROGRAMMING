<?php
$required_role = 'employer';
include_once '../session.php';
header('Content-Type: application/json');

include_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);
$job_id = $data['job_id'] ?? null;

if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'Job ID is required']);
    exit;
}

// Verify that the job belongs to the logged-in employer
$stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND employer_id = ?");
$stmt->bind_param("ii", $job_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Job not found or access denied']);
    exit;
}

// Delete related applications first
$stmt = $conn->prepare("DELETE FROM applications WHERE job_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();

// Delete the job
$stmt = $conn->prepare("DELETE FROM jobs WHERE id = ? AND employer_id = ?");
$stmt->bind_param("ii", $job_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Job deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete job']);
}

$stmt->close();
$conn->close();
?> 