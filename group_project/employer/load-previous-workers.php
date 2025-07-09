<?php
$required_role = 'employer';
include_once '../session.php';
include_once '../db.php';

$employer_id = $_GET['employer_id'];

$sql = "
    SELECT a.id AS application_id, u.name AS worker_name, j.title AS job_title, j.id AS job_id, u.id AS worker_id
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON a.user_id = u.id
    LEFT JOIN reviews r
    ON r.job_id = j.id
    AND r.reviewer_id = j.employer_id
    AND r.reviewer_type = 'employer'
    AND r.reviewee_id = u.id
    AND r.reviewee_type = 'user'
    WHERE j.employer_id = ?
    AND a.status = 'completed'
    AND r.review_id IS NULL;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();

$workers = [];
while ($row = $result->fetch_assoc()) {
    $workers[] = $row;
}

echo json_encode($workers);
?>
