<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if (isset($required_role) && $_SESSION['user_type'] !== $required_role) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied for this role.']);
    exit;
}
?>