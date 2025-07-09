<?php
$required_role = 'student';
include_once '../session.php';
// Enable error reporting for debugging
ini_set('display_errors', 1);   // Display errors
error_reporting(E_ALL);         // Report all types of errors

include_once '../db.php';

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

// SQL query to fetch all jobs with sorting
$sql = "SELECT *, created_at FROM jobs ORDER BY $sortBy $sortOrder"; 
$result = $conn->query($sql);

// Check if any records were found
if ($result->num_rows > 0) {
    $jobs = [];
    
    // Fetch all rows from the result
    while($row = $result->fetch_assoc()) {
        $jobs[] = $row; // Add each job row to the jobs array
    }
    
    // Return data as JSON
    echo json_encode($jobs);
} else {
    // If no jobs found, return an empty array
    echo json_encode([]);
}

// Close the database connection
$conn->close();
?>
