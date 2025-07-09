<?php
$required_role = 'student';
include_once '../session.php';
include_once '../db.php';

// get current student user_id from session or query
$user_id = $_SESSION['user_id'];

// Get sorting parameters
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortOrder = $_GET['sort_order'] ?? 'DESC';

// Validate sort parameters
$allowedSortFields = ['title', 'location', 'tags', 'status', 'created_at'];
$allowedSortOrders = ['ASC', 'DESC'];

if (!in_array($sortBy, $allowedSortFields)) {
    $sortBy = 'created_at';
}
if (!in_array(strtoupper($sortOrder), $allowedSortOrders)) {
    $sortOrder = 'DESC';
}

$sql = "SELECT applications.id as application_id, jobs.title, jobs.location, jobs.tags, applications.status, applications.created_at
  FROM applications
  JOIN jobs ON applications.job_id = jobs.id
  WHERE applications.user_id = ?
  ORDER BY $sortBy $sortOrder";
  
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
