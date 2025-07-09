<?php
$required_role = 'employer';
include_once '../session.php';
header('Content-Type: application/json');

include_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);
$job_id = $data['job_id'] ?? null;
$title = $data['title'] ?? null;
$location = $data['location'] ?? null;
$tags = $data['tags'] ?? null;

if (!$job_id || !$title || !$location || !$tags) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
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

// Update the job
$stmt = $conn->prepare("UPDATE jobs SET title = ?, location = ?, tags = ? WHERE id = ? AND employer_id = ?");
$stmt->bind_param("sssii", $title, $location, $tags, $job_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Job updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update job']);
}

$stmt->close();
$conn->close();
?> 