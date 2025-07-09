<?php
$required_role = 'employer';
include_once '../session.php';
include_once '../db.php';

header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

$job_id = $data['job_id'];
$reviewer_id = $data['reviewer_id'];
$reviewer_type = $data['reviewer_type'];
$reviewee_id = $data['reviewee_id'];
$reviewee_type = $data['reviewee_type'];
$rating = $data['rating'];
$comment = $data['comment'];

$stmt = $conn->prepare("INSERT INTO reviews (job_id, reviewer_id, reviewer_type, reviewee_id, reviewee_type, rating, comment) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisisis", $job_id, $reviewer_id, $reviewer_type, $reviewee_id, $reviewee_type, $rating, $comment);
$stmt->execute();

echo json_encode(["status" => "success"]);
?>
