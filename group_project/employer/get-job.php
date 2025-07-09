<?php
$required_role = 'employer';
include_once '../session.php';
header('Content-Type: application/json');

include_once '../db.php';

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'Job ID is required']);
    exit;
}

// Get the job details and verify ownership
$stmt = $conn->prepare("SELECT id, title, location, tags FROM jobs WHERE id = ? AND employer_id = ?");
$stmt->bind_param("ii", $job_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Job not found or access denied']);
    exit;
}

$job = $result->fetch_assoc();
echo json_encode(['success' => true, 'job' => $job]);

$stmt->close();
$conn->close();
?> 