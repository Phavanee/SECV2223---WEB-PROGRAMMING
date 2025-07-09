<?php
$required_role = 'student';
include_once '../session.php';
include_once '../db.php';

$userId = $_SESSION['user_id'];
$sql = "SELECT job_id FROM applications WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$appliedJobs = [];

while ($row = $result->fetch_assoc()) {
    $appliedJobs[] = $row["job_id"];
}

echo json_encode($appliedJobs);
$conn->close();
?>
