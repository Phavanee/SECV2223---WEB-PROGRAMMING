<?php
$required_role = 'employer';
include_once '../session.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
include_once '../db.php';

// Get employer ID from query string
$employerId = $_GET['employer_id'] ?? null;

// Get sorting parameters
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortOrder = $_GET['sort_order'] ?? 'DESC';

// Validate sort parameters
$allowedSortFields = ['title', 'name', 'email', 'status', 'created_at'];
$allowedSortOrders = ['ASC', 'DESC'];

if (!in_array($sortBy, $allowedSortFields)) {
    $sortBy = 'created_at';
}
if (!in_array(strtoupper($sortOrder), $allowedSortOrders)) {
    $sortOrder = 'DESC';
}

if ($employerId) {
    $sql = "SELECT applications.*, users.name, users.email, jobs.title, applications.created_at
            FROM applications
            JOIN jobs ON applications.job_id = jobs.id
            JOIN users ON applications.user_id = users.id
            WHERE jobs.employer_id = ?
            ORDER BY $sortBy $sortOrder";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employerId);
    $stmt->execute();
    $result = $stmt->get_result();

    $applicants = [];
    while ($row = $result->fetch_assoc()) {
        $applicants[] = $row;
    }

    echo json_encode($applicants);
} else {
    echo json_encode(["error" => "No employer_id provided"]);
}

$conn->close();
?>
