<?php
$required_role = 'student';
include_once '../session.php';
include_once '../db.php';

header("Content-Type: application/json");

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["error" => "No user ID provided"]);
    exit;
}

$sql = "SELECT name, is_verified FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$user_name = $row['name'];
$is_verified = $row['is_verified'];

$rating_query = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE reviewee_id = ? AND reviewee_type = 'student'");
$rating_query->bind_param("i", $user_id);
$rating_query->execute();
$rating_result = $rating_query->get_result();
$avg_rating = round($rating_result->fetch_assoc()['avg_rating'], 1);

$avg_rating = $avg_rating !== null ? round($avg_rating, 1) : 'N/A';

echo json_encode([
    "name" => $user_name,
    "rating" => $avg_rating,
    "is_verified" => $is_verified
]);

?>
