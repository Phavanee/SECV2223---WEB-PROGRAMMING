<?php
$required_role = 'student';
include_once '../session.php';
include_once '../db.php';

$user_id = $_GET['user_id'];

$sql = "
    SELECT a.id AS application_id, j.title AS job_title, j.id AS job_id, e.name AS employer_name, e.id AS employer_id
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN employers e ON j.employer_id = e.id
    LEFT JOIN reviews r
        ON r.job_id = j.id
        AND r.reviewer_id = a.user_id
        AND r.reviewer_type = 'user'
        AND r.reviewee_id = j.employer_id
        AND r.reviewee_type = 'employer'
    WHERE a.user_id = ?
    AND a.status = 'completed'
    AND r.review_id IS NULL
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode($jobs);
?>
