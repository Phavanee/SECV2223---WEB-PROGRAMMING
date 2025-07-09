<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    echo json_encode(['error' => 'Not authenticated as student']);
    exit;
}

echo json_encode(['student_id' => $_SESSION['user_id']]);
?> 