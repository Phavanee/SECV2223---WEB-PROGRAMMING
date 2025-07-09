<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employer') {
    echo json_encode(['error' => 'Not authenticated as employer']);
    exit;
}

echo json_encode(['employer_id' => $_SESSION['user_id']]);
?> 