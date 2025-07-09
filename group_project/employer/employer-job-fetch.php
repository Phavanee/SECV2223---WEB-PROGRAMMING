<?php
$required_role = 'employer';
include_once '../session.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once '../db.php';

// Match this to JS: ?employerId=1
$employerId = $_SESSION['user_id'];

// Get sorting parameters
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortOrder = $_GET['sort_order'] ?? 'DESC';

// Validate sort parameters
$allowedSortFields = ['title', 'location', 'tags', 'created_at'];
$allowedSortOrders = ['ASC', 'DESC'];

if (!in_array($sortBy, $allowedSortFields)) {
    $sortBy = 'created_at';
}
if (!in_array(strtoupper($sortOrder), $allowedSortOrders)) {
    $sortOrder = 'DESC';
}

$sql = "SELECT id, title, location, tags, employer_id, created_at FROM jobs WHERE employer_id = ? ORDER BY $sortBy $sortOrder";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employerId);
$stmt->execute();
$result = $stmt->get_result();

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode($jobs);
$conn->close();
?>